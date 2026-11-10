<?php

namespace Ginfo\Parser\Mail;

use Ginfo\Parser\ParserInterface;

final readonly class Smtp implements ParserInterface
{
    private const ENCRYPTION_NONE = 'none';
    private const ENCRYPTION_TLS = 'tls';
    private const ENCRYPTION_STARTTLS = 'starttls';
    private const ENCRYPTIONS = [self::ENCRYPTION_NONE, self::ENCRYPTION_TLS, self::ENCRYPTION_STARTTLS];

    /**
     * Check an SMTP server: TCP/TLS connection, greeting, EHLO capabilities, STARTTLS and AUTH.
     * No mail is sent, the session is always closed with QUIT.
     *
     * @param string      $host       host or ip address without scheme
     * @param int         $port       port
     * @param string      $encryption one of none, tls (implicit TLS, aka smtps), starttls
     * @param string|null $username   authenticate when not empty
     * @param string|null $password   authentication password
     * @param float       $timeout    connect and read timeout in seconds
     * @param bool        $verifyPeer verify the server certificate and its name
     *
     * @return array{
     *     host: string,
     *     port: int,
     *     encryption: string,
     *     banner: string|null,
     *     tls: bool,
     *     capabilities: array<string, string|null>,
     *     authenticated: bool|null,
     *     time: float,
     *     error: string|null,
     * }
     */
    public function run(
        string $host = 'localhost',
        int $port = 25,
        string $encryption = self::ENCRYPTION_NONE,
        ?string $username = null,
        ?string $password = null,
        float $timeout = 3.0,
        bool $verifyPeer = true,
    ): array {
        $startedAt = \microtime(true);

        $result = [
            'host' => $host,
            'port' => $port,
            'encryption' => $encryption,
            'banner' => null,
            'tls' => false,
            'capabilities' => [],
            'authenticated' => null,
            'time' => 0.0,
            'error' => null,
        ];
        $finish = static function (?string $error) use (&$result, $startedAt): array {
            $result['time'] = \round((\microtime(true) - $startedAt) * 1000, 1);
            $result['error'] = $error;

            return $result;
        };

        if (!\in_array($encryption, self::ENCRYPTIONS, true)) {
            return $finish('Unsupported encryption: '.$encryption);
        }

        if (self::ENCRYPTION_NONE !== $encryption && !\extension_loaded('openssl')) {
            return $finish('The openssl extension is required for TLS');
        }

        $remote = (self::ENCRYPTION_TLS === $encryption ? 'ssl' : 'tcp').'://'.$host.':'.$port;
        $context = \stream_context_create([
            'ssl' => [
                'verify_peer' => $verifyPeer,
                'verify_peer_name' => $verifyPeer,
                'SNI_enabled' => true,
                'peer_name' => \trim($host, '[]'),
            ],
        ]);

        $errorCode = 0;
        $errorMessage = '';
        $socket = @\stream_socket_client($remote, $errorCode, $errorMessage, $timeout, \STREAM_CLIENT_CONNECT, $context);
        if (false === $socket) {
            return $finish('' !== \trim($errorMessage) ? \trim($errorMessage) : 'Connection failed (errno '.$errorCode.')');
        }

        \stream_set_timeout($socket, (int) \max(1, \ceil($timeout)));
        $result['tls'] = self::ENCRYPTION_TLS === $encryption;

        try {
            $reply = self::readReply($socket);
            if (null === $reply || 220 !== $reply['code']) {
                return $finish('Unexpected greeting: '.(null === $reply ? 'no response' : $reply['code'].' '.\implode(' ', $reply['lines'])));
            }
            $result['banner'] = \implode(' ', $reply['lines']);

            $capabilities = self::handshake($socket);
            if (null === $capabilities) {
                return $finish('EHLO command rejected');
            }
            $result['capabilities'] = $capabilities;

            if (self::ENCRYPTION_STARTTLS === $encryption) {
                if (!\array_key_exists('STARTTLS', $capabilities)) {
                    return $finish('STARTTLS is not supported by the server');
                }

                self::writeLine($socket, 'STARTTLS');
                $reply = self::readReply($socket);
                if (null === $reply || 220 !== $reply['code']) {
                    return $finish('STARTTLS command rejected');
                }

                $methods = \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | \STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                if (!@\stream_socket_enable_crypto($socket, true, $methods)) {
                    return $finish('TLS handshake failed');
                }
                $result['tls'] = true;

                $capabilities = self::handshake($socket);
                if (null === $capabilities) {
                    return $finish('EHLO command rejected after STARTTLS');
                }
                $result['capabilities'] = $capabilities;
            }

            if (null !== $username && '' !== $username) {
                $result['authenticated'] = self::authenticate($socket, $capabilities, $username, (string) $password);
                if (false === $result['authenticated']) {
                    return $finish('SMTP authentication failed');
                }
            }
        } finally {
            self::writeLine($socket, 'QUIT');
            @\fclose($socket);
        }

        return $finish(null);
    }

    /**
     * @param resource $socket
     *
     * @return array<string, string|null>|null
     */
    private static function handshake($socket): ?array
    {
        foreach (['EHLO', 'HELO'] as $command) {
            self::writeLine($socket, $command.' '.(\gethostname() ?: 'localhost'));
            $reply = self::readReply($socket);
            if (null !== $reply && 250 === $reply['code']) {
                if ('HELO' === $command) {
                    return [];
                }

                \array_shift($reply['lines']); // the first line is the server identification

                return self::parseCapabilities($reply['lines']);
            }
        }

        return null;
    }

    /**
     * @param resource                   $socket
     * @param array<string, string|null> $capabilities
     */
    private static function authenticate($socket, array $capabilities, string $username, string $password): bool
    {
        $mechanisms = \array_map('strtoupper', \preg_split('/\s+/', \trim($capabilities['AUTH'] ?? '')) ?: []);

        if (\in_array('PLAIN', $mechanisms, true) && self::authPlain($socket, $username, $password)) {
            return true;
        }

        if (\in_array('LOGIN', $mechanisms, true) && self::authLogin($socket, $username, $password)) {
            return true;
        }

        return false;
    }

    /**
     * @param resource $socket
     */
    private static function authPlain($socket, string $username, string $password): bool
    {
        self::writeLine($socket, 'AUTH PLAIN '.\base64_encode("\0".$username."\0".$password));
        $reply = self::readReply($socket);

        return null !== $reply && 235 === $reply['code'];
    }

    /**
     * @param resource $socket
     */
    private static function authLogin($socket, string $username, string $password): bool
    {
        self::writeLine($socket, 'AUTH LOGIN');
        $reply = self::readReply($socket);
        if (null === $reply || 334 !== $reply['code']) {
            return false;
        }

        self::writeLine($socket, \base64_encode($username));
        $reply = self::readReply($socket);
        if (null === $reply || 334 !== $reply['code']) {
            return false;
        }

        self::writeLine($socket, \base64_encode($password));
        $reply = self::readReply($socket);

        return null !== $reply && 235 === $reply['code'];
    }

    /**
     * @param resource $socket
     *
     * @return array{code: int, lines: string[]}|null
     */
    private static function readReply($socket): ?array
    {
        $code = null;
        $lines = [];
        $complete = false;

        while (false !== ($line = \fgets($socket, 2048))) {
            if (!\preg_match('/^(\d{3})([ -])(.*)$/', \rtrim($line, "\r\n"), $matches)) {
                return null;
            }

            $code = (int) $matches[1];
            $lines[] = $matches[3];

            if (' ' === $matches[2]) {
                $complete = true;
                break;
            }
        }

        return $complete ? ['code' => $code, 'lines' => $lines] : null;
    }

    /**
     * @param string[] $lines
     *
     * @return array<string, string|null>
     */
    private static function parseCapabilities(array $lines): array
    {
        $capabilities = [];
        foreach ($lines as $line) {
            $line = \trim($line);
            if ('' === $line) {
                continue;
            }

            [$name, $value] = \array_pad(\explode(' ', $line, 2), 2, null);
            $capabilities[\strtoupper((string) $name)] = $value;
        }

        return $capabilities;
    }

    /**
     * @param resource $socket
     */
    private static function writeLine($socket, string $line): bool
    {
        return false !== @\fwrite($socket, $line."\r\n");
    }
}
