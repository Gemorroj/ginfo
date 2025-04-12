<?php

namespace Ginfo\Info;

use Ginfo\Info\Php\Apcu;
use Ginfo\Info\Php\Fpm;
use Ginfo\Info\Php\Opcache;

final readonly class Php
{
    public function __construct(
        private string $version,
        private string $sapiName,
        private bool $zendThreadSafe,
        private int $memoryLimit,
        /** @var string[] */
        private array $extensions,
        /** @var string[] */
        private array $zendExtensions,
        private string $iniFile,
        private string $includePath,
        private string $openBasedir,
        /** @var string[] */
        private array $disabledFunctions,
        /** @var string[] */
        private array $disabledClasses,
        private Opcache $opcache,
        private Apcu $apcu,
        private Fpm $fpm,
        private float $realpathCacheSizeUsed,
        private ?float $realpathCacheSizeAllowed = null,
    ) {
    }

    public function getMemoryLimit(): int
    {
        return $this->memoryLimit;
    }

    public function isZendThreadSafe(): bool
    {
        return $this->zendThreadSafe;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @return string[]
     */
    public function getZendExtensions(): array
    {
        return $this->zendExtensions;
    }

    public function getIniFile(): string
    {
        return $this->iniFile;
    }

    public function getIncludePath(): string
    {
        return $this->includePath;
    }

    public function getOpenBasedir(): string
    {
        return $this->openBasedir;
    }

    public function getSapiName(): string
    {
        return $this->sapiName;
    }

    public function getOpcache(): Opcache
    {
        return $this->opcache;
    }

    public function getFpm(): Fpm
    {
        return $this->fpm;
    }

    public function getApcu(): Apcu
    {
        return $this->apcu;
    }

    /**
     * @return string[]
     */
    public function getDisabledFunctions(): array
    {
        return $this->disabledFunctions;
    }

    /**
     * @return string[]
     */
    public function getDisabledClasses(): array
    {
        return $this->disabledClasses;
    }

    public function getRealpathCacheSizeUsed(): float
    {
        return $this->realpathCacheSizeUsed;
    }

    public function getRealpathCacheSizeAllowed(): ?float
    {
        return $this->realpathCacheSizeAllowed;
    }
}
