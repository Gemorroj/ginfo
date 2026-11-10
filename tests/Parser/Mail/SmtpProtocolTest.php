<?php

namespace Ginfo\Tests\Parser\Mail;

use Ginfo\Info\Mail\Smtp;
use Ginfo\Parser\Mail\Smtp as SmtpParser;
use Ginfo\Tests\Fixtures\SmtpServer;
use PHPUnit\Framework\TestCase;

final class SmtpProtocolTest extends TestCase
{
    /**
     * @var SmtpServer[]
     */
    private array $servers = [];

    protected function tearDown(): void
    {
        foreach ($this->servers as $server) {
            $server->stop();
        }
        $this->servers = [];
    }

    public function testStartTls(): void
    {
        $this->requireOpenssl();
        $port = $this->startServer('--tls=starttls');

        $data = $this->runParser('127.0.0.1', $port, encryption: Smtp::ENCRYPTION_STARTTLS, verifyPeer: false);

        self::assertNull($data['error']);
        self::assertTrue($data['tls']);
        self::assertSame('test ESMTP', $data['banner']);
    }

    public function testStartTlsCertificateIsNotVerified(): void
    {
        $this->requireOpenssl();
        $port = $this->startServer('--tls=starttls');

        $data = $this->runParser('127.0.0.1', $port, encryption: Smtp::ENCRYPTION_STARTTLS);

        self::assertSame('TLS handshake failed', $data['error']);
        self::assertFalse($data['tls']);
    }

    public function testTls(): void
    {
        $this->requireOpenssl();
        $port = $this->startServer('--tls=implicit');

        $data = $this->runParser('127.0.0.1', $port, encryption: Smtp::ENCRYPTION_TLS, verifyPeer: false);

        self::assertNull($data['error']);
        self::assertTrue($data['tls']);
        self::assertSame('test ESMTP', $data['banner']);
    }

    public function testTlsCertificateIsNotVerified(): void
    {
        $this->requireOpenssl();
        $port = $this->startServer('--tls=implicit');

        $data = $this->runParser('127.0.0.1', $port, encryption: Smtp::ENCRYPTION_TLS);

        self::assertNotNull($data['error']);
        self::assertFalse($data['tls']);
        self::assertNull($data['banner']);
    }

    public function testAuthLogin(): void
    {
        $port = $this->startServer('--auth=login');

        $data = $this->runParser('127.0.0.1', $port, username: 'user', password: 'pass');

        self::assertNull($data['error']);
        self::assertTrue($data['authenticated']);
        self::assertSame('LOGIN', $data['capabilities']['AUTH']);
    }

    public function testAuthPlainFallbackToLogin(): void
    {
        $port = $this->startServer('--auth=login-required');

        $data = $this->runParser('127.0.0.1', $port, username: 'user', password: 'pass');

        self::assertNull($data['error']);
        self::assertTrue($data['authenticated']);
        self::assertSame('PLAIN LOGIN', $data['capabilities']['AUTH']);
    }

    public function testAuthFailed(): void
    {
        $port = $this->startServer('--auth=reject');

        $data = $this->runParser('127.0.0.1', $port, username: 'user', password: 'wrong');

        self::assertSame('SMTP authentication failed', $data['error']);
        self::assertFalse($data['authenticated']);
    }

    public function testAuthIsNotSupported(): void
    {
        $port = $this->startServer('--auth=none');

        $data = $this->runParser('127.0.0.1', $port, username: 'user', password: 'pass');

        self::assertSame('SMTP authentication failed', $data['error']);
        self::assertFalse($data['authenticated']);
        self::assertArrayNotHasKey('AUTH', $data['capabilities']);
    }

    public function testStartTlsIsNotSupported(): void
    {
        $port = $this->startServer();

        $data = $this->runParser('127.0.0.1', $port, encryption: Smtp::ENCRYPTION_STARTTLS);

        self::assertSame('STARTTLS is not supported by the server', $data['error']);
        self::assertFalse($data['tls']);
    }

    public function testUnexpectedGreeting(): void
    {
        $port = $this->startServer('--greeting=554');

        $data = $this->runParser('127.0.0.1', $port);

        self::assertSame('Unexpected greeting: 554 test ESMTP', $data['error']);
        self::assertNull($data['banner']);
    }

    public function testHeloFallback(): void
    {
        $port = $this->startServer('--ehlo=reject');

        $data = $this->runParser('127.0.0.1', $port);

        self::assertNull($data['error']);
        self::assertSame([], $data['capabilities']);
    }

    public function testEhloAndHeloRejected(): void
    {
        $port = $this->startServer('--ehlo=reject', '--helo=reject');

        $data = $this->runParser('127.0.0.1', $port);

        self::assertSame('EHLO command rejected', $data['error']);
    }

    public function testUnsupportedEncryption(): void
    {
        $data = $this->runParser('127.0.0.1', 25, encryption: 'bogus');

        self::assertSame('Unsupported encryption: bogus', $data['error']);
    }

    public function testNoResponse(): void
    {
        $port = $this->startServer('--greeting=none');

        $data = $this->runParser('127.0.0.1', $port, timeout: 0.5);

        self::assertSame('Unexpected greeting: no response', $data['error']);
        self::assertNull($data['banner']);
    }

    public function testConnectionRefused(): void
    {
        $port = $this->startServer();
        foreach ($this->servers as $server) {
            $server->stop();
        }
        $this->servers = [];

        $data = $this->runParser('127.0.0.1', $port);

        self::assertNotNull($data['error']);
        self::assertNull($data['banner']);
        self::assertFalse($data['tls']);
    }

    public function testParseCapabilities(): void
    {
        $method = new \ReflectionMethod(SmtpParser::class, 'parseCapabilities');

        self::assertSame(
            [
                'SIZE' => '35882577',
                'STARTTLS' => null,
                'AUTH' => 'PLAIN LOGIN',
            ],
            $method->invoke(null, ['SIZE 35882577', 'starttls', '', 'AUTH PLAIN LOGIN']),
        );
    }

    /**
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
    private function runParser(
        string $host,
        int $port,
        string $encryption = Smtp::ENCRYPTION_NONE,
        ?string $username = null,
        ?string $password = null,
        float $timeout = 2.0,
        bool $verifyPeer = true,
    ): array {
        return (new SmtpParser())->run($host, $port, $encryption, $username, $password, $timeout, $verifyPeer);
    }

    private function requireOpenssl(): void
    {
        if (!\extension_loaded('openssl')) {
            self::markTestSkipped('The openssl extension is required');
        }
    }

    private function startServer(string ...$arguments): int
    {
        $server = new SmtpServer($arguments);
        $this->servers[] = $server;

        return $server->getPort();
    }
}
