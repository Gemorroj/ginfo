<?php

namespace Ginfo\Info\Mail;

use Ginfo\Info\InfoInterface;

final readonly class Smtp implements InfoInterface
{
    public const ENCRYPTION_NONE = 'none';
    public const ENCRYPTION_TLS = 'tls';
    public const ENCRYPTION_STARTTLS = 'starttls';

    /**
     * @param array<string, string|null> $capabilities EHLO extensions, e.g. ['SIZE' => '35882577', 'STARTTLS' => null]
     * @param float                      $time         check duration in milliseconds
     * @param string|null                $error        null when the server is available
     */
    public function __construct(
        private string $host,
        private int $port,
        private string $encryption = self::ENCRYPTION_NONE,
        private ?string $banner = null,
        private bool $tls = false,
        private array $capabilities = [],
        private ?bool $authenticated = null,
        private float $time = 0.0,
        private ?string $error = null,
    ) {
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string (none|tls|starttls)
     */
    public function getEncryption(): string
    {
        return $this->encryption;
    }

    /**
     * Greeting line sent by the server right after the connection is established.
     */
    public function getBanner(): ?string
    {
        return $this->banner;
    }

    /**
     * Whether a TLS connection was really established.
     */
    public function isTls(): bool
    {
        return $this->tls;
    }

    /**
     * @return array<string, string|null>
     */
    public function getCapabilities(): array
    {
        return $this->capabilities;
    }

    public function hasCapability(string $capability): bool
    {
        return \array_key_exists(\strtoupper($capability), $this->capabilities);
    }

    /**
     * Authentication result. null when authentication was not attempted.
     */
    public function isAuthenticated(): ?bool
    {
        return $this->authenticated;
    }

    /**
     * @return float check duration in milliseconds
     */
    public function getTime(): float
    {
        return $this->time;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function isAvailable(): bool
    {
        return null === $this->error;
    }
}
