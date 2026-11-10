<?php

// Minimal SMTP server for the tests. Serves a single connection.
// Usage: php smtp-server.php [--auth=plain|login|login-required|reject|none] [--tls=none|implicit|starttls]
//        [--greeting=220|none] [--ehlo=reject] [--helo=reject]

function opensslConfig(): ?string
{
    // windows php builds ship the config next to the binary
    $configFile = \dirname(\PHP_BINARY).'/extras/ssl/openssl.cnf';

    return \is_file($configFile) ? $configFile : null;
}

function createCertificate(): string
{
    $config = \opensslConfig();

    $keyOptions = ['private_key_type' => \OPENSSL_KEYTYPE_RSA, 'private_key_bits' => 2048];
    $csrOptions = ['digest_alg' => 'sha256'];
    $exportOptions = [];
    if (null !== $config) {
        $keyOptions['config'] = $config;
        $csrOptions['config'] = $config;
        $exportOptions['config'] = $config;
    }

    $key = \openssl_pkey_new($keyOptions);
    if (false === $key) {
        \fwrite(\STDERR, "Can not create a key for the test server\n");
        exit(1);
    }

    $csr = \openssl_csr_new(['commonName' => '127.0.0.1'], $key, $csrOptions);
    $certificate = \is_object($csr) ? \openssl_csr_sign($csr, null, $key, 1, $csrOptions) : false;
    $certificatePem = '';
    $keyPem = '';
    $exported = \is_object($certificate)
        && \openssl_x509_export($certificate, $certificatePem)
        && \openssl_pkey_export($key, $keyPem, null, $exportOptions);
    $file = $exported ? \tempnam(\sys_get_temp_dir(), 'ginfo-smtp-') : false;
    if (false === $file) {
        \fwrite(\STDERR, "Can not create a certificate for the test server\n");
        exit(1);
    }
    \file_put_contents($file, $certificatePem.$keyPem);

    return $file;
}

$arguments = [];
if (isset($_SERVER['argv']) && \is_array($_SERVER['argv'])) {
    foreach ($_SERVER['argv'] as $argument) {
        if (\is_string($argument)) {
            $arguments[] = $argument;
        }
    }
}

$options = [
    'auth' => 'plain',
    'tls' => 'none',
    'greeting' => '220',
    'ehlo' => 'accept',
    'helo' => 'accept',
];
foreach (\array_slice($arguments, 1) as $argument) {
    if (\str_starts_with($argument, '--')) {
        [$key, $value] = \array_pad(\explode('=', \substr($argument, 2), 2), 2, '1');
        $options[$key] = $value;
    }
}

$certificateFile = null;
$socketContext = null;
if ('none' !== $options['tls']) {
    $certificateFile = \createCertificate();
    $socketContext = \stream_context_create([
        'ssl' => [
            'local_cert' => $certificateFile,
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
}

$server = \stream_socket_server(
    ('implicit' === $options['tls'] ? 'ssl' : 'tcp').'://127.0.0.1:0',
    $errorCode,
    $errorMessage,
    \STREAM_SERVER_BIND | \STREAM_SERVER_LISTEN,
    $socketContext
);
if (false === $server) {
    \fwrite(\STDERR, 'Can not start the server: '.$errorMessage."\n");
    exit(1);
}

$address = (string) \stream_socket_get_name($server, false);
$port = (int) \substr($address, \strrpos($address, ':') + 1);
\fwrite(\STDOUT, 'LISTENING '.$port."\n");
\fflush(\STDOUT);

$client = @\stream_socket_accept($server, 20);
if (false === $client) {
    if (null !== $certificateFile) {
        @\unlink($certificateFile);
    }
    exit(0);
}
\stream_set_timeout($client, 5);

$write = static function (string $line) use ($client): void {
    @\fwrite($client, $line."\r\n");
};
$read = static function () use ($client): ?string {
    $line = @\fgets($client);

    return false === $line ? null : \rtrim($line, "\r\n");
};

if ('none' !== $options['greeting']) {
    $write((int) $options['greeting'].' test ESMTP');
}

$authMode = $options['auth'];
$authCapability = match ($authMode) {
    'none' => null,
    'login' => 'LOGIN',
    'login-required' => 'PLAIN LOGIN',
    default => 'PLAIN', // plain, reject
};

while (null !== ($line = $read())) {
    $command = \strtoupper(\trim($line));

    if (\str_starts_with($command, 'EHLO')) {
        if ('reject' === $options['ehlo']) {
            $write('502 5.5.1 Command not implemented');
            continue;
        }

        $write('250-test ESMTP');
        $write('250-SIZE 35882577');
        if ('starttls' === $options['tls']) {
            $write('250-STARTTLS');
        }
        if (null !== $authCapability) {
            $write('250-AUTH '.$authCapability);
        }
        $write('250 HELP');

        continue;
    }

    if ('STARTTLS' === $command && 'starttls' === $options['tls']) {
        $write('220 2.0.0 Ready to start TLS');
        if (!@\stream_socket_enable_crypto($client, true, \STREAM_CRYPTO_METHOD_TLS_SERVER)) {
            break;
        }

        continue;
    }

    if (\str_starts_with($command, 'HELO')) {
        $write('reject' === $options['helo'] ? '502 5.5.1 Command not implemented' : '250 test');

        continue;
    }

    if (\str_starts_with($command, 'AUTH ')) {
        $parts = \preg_split('/\s+/', \trim($line)) ?: [];
        $mechanism = \strtoupper($parts[1] ?? '');

        if ('reject' === $authMode) {
            $write('535 5.7.8 Authentication credentials invalid');
            continue;
        }

        if ('PLAIN' === $mechanism) {
            $token = \base64_decode($parts[2] ?? '', true);
            $accepted = 'plain' === $authMode && "\0user\0pass" === $token;
            $write($accepted ? '235 2.7.0 Authentication successful' : '535 5.7.8 Authentication credentials invalid');
            continue;
        }

        if ('LOGIN' === $mechanism) {
            $write('334 VXNlcm5hbWU6');
            $username = \base64_decode((string) $read(), true);
            $write('334 UGFzc3dvcmQ6');
            $password = \base64_decode((string) $read(), true);
            $accepted = \in_array($authMode, ['login', 'login-required'], true) && 'user' === $username && 'pass' === $password;
            $write($accepted ? '235 2.7.0 Authentication successful' : '535 5.7.8 Authentication credentials invalid');
            continue;
        }

        $write('504 5.5.4 Unrecognized authentication type');

        continue;
    }

    if ('QUIT' === $command) {
        $write('221 2.0.0 Bye');
        break;
    }

    $write('502 5.5.2 Command not recognized');
}

@\fclose($client);
@\fclose($server);
if (null !== $certificateFile) {
    @\unlink($certificateFile);
}
