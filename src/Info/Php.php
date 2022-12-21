<?php

namespace Ginfo\Info;

use Ginfo\Info\Php\Apcu;
use Ginfo\Info\Php\Fpm;
use Ginfo\Info\Php\Opcache;

class Php
{
    private string $version;
    /** @var string[] */
    private array $extensions;
    /** @var string[] */
    private array $zendExtensions;
    private string $iniFile;
    private string $includePath;
    private string $openBasedir;
    private string $sapiName;
    private Opcache $opcache;
    private Apcu $apcu;
    /** @var string[] */
    private array $disabledFunctions;
    /** @var string[] */
    private array $disabledClasses;
    private float $realpathCacheSizeUsed;
    private ?float $realpathCacheSizeAllowed = null;
    private bool $zendThreadSafe;
    private int $memoryLimit;
    private Fpm $fpm;

    public function getMemoryLimit(): int
    {
        return $this->memoryLimit;
    }

    public function setMemoryLimit(int $memoryLimit): self
    {
        $this->memoryLimit = $memoryLimit;

        return $this;
    }

    public function isZendThreadSafe(): bool
    {
        return $this->zendThreadSafe;
    }

    public function setZendThreadSafe(bool $zendThreadSafe): self
    {
        $this->zendThreadSafe = $zendThreadSafe;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param string[] $extensions
     */
    public function setExtensions(array $extensions): self
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getZendExtensions(): array
    {
        return $this->zendExtensions;
    }

    /**
     * @param string[] $zendExtensions
     */
    public function setZendExtensions(array $zendExtensions): self
    {
        $this->zendExtensions = $zendExtensions;

        return $this;
    }

    public function getIniFile(): string
    {
        return $this->iniFile;
    }

    public function setIniFile(string $iniFile): self
    {
        $this->iniFile = $iniFile;

        return $this;
    }

    public function getIncludePath(): string
    {
        return $this->includePath;
    }

    public function setIncludePath(string $includePath): self
    {
        $this->includePath = $includePath;

        return $this;
    }

    public function getOpenBasedir(): string
    {
        return $this->openBasedir;
    }

    public function setOpenBasedir(string $openBasedir): self
    {
        $this->openBasedir = $openBasedir;

        return $this;
    }

    public function getSapiName(): string
    {
        return $this->sapiName;
    }

    public function setSapiName(string $sapiName): self
    {
        $this->sapiName = $sapiName;

        return $this;
    }

    public function getOpcache(): Opcache
    {
        return $this->opcache;
    }

    public function setOpcache(Opcache $opcache): self
    {
        $this->opcache = $opcache;

        return $this;
    }

    public function getFpm(): Fpm
    {
        return $this->fpm;
    }

    public function setFpm(Fpm $fpm): self
    {
        $this->fpm = $fpm;

        return $this;
    }

    public function getApcu(): Apcu
    {
        return $this->apcu;
    }

    public function setApcu(Apcu $apcu): self
    {
        $this->apcu = $apcu;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDisabledFunctions(): array
    {
        return $this->disabledFunctions;
    }

    /**
     * @param string[] $disabledFunctions
     */
    public function setDisabledFunctions(array $disabledFunctions): self
    {
        $this->disabledFunctions = $disabledFunctions;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getDisabledClasses(): array
    {
        return $this->disabledClasses;
    }

    /**
     * @param string[] $disabledClasses
     */
    public function setDisabledClasses(array $disabledClasses): self
    {
        $this->disabledClasses = $disabledClasses;

        return $this;
    }

    public function getRealpathCacheSizeUsed(): float
    {
        return $this->realpathCacheSizeUsed;
    }

    public function setRealpathCacheSizeUsed(float $realpathCacheSizeUsed): self
    {
        $this->realpathCacheSizeUsed = $realpathCacheSizeUsed;

        return $this;
    }

    public function getRealpathCacheSizeAllowed(): ?float
    {
        return $this->realpathCacheSizeAllowed;
    }

    public function setRealpathCacheSizeAllowed(?float $realpathCacheSizeAllowed): self
    {
        $this->realpathCacheSizeAllowed = $realpathCacheSizeAllowed;

        return $this;
    }
}
