<?php

namespace Ginfo\Info;

use Ginfo\Info\Php\Apcu;
use Ginfo\Info\Php\Fpm;
use Ginfo\Info\Php\Opcache;

class Php
{
    /** @var string */
    private $version;
    /** @var string[] */
    private $extensions;
    /** @var string[] */
    private $zendExtensions;
    /** @var string */
    private $iniFile;
    /** @var string */
    private $includePath;
    /** @var string */
    private $openBasedir;
    /** @var string */
    private $sapiName;
    /** @var Opcache */
    private $opcache;
    /** @var Apcu */
    private $apcu;
    /** @var string[] */
    private $disabledFunctions;
    /** @var string[] */
    private $disabledClasses;
    /** @var float */
    private $realpathCacheSizeUsed;
    /** @var float|null */
    private $realpathCacheSizeAllowed;
    /** @var bool */
    private $zendThreadSafe;
    /** @var int */
    private $memoryLimit;
    /** @var Fpm */
    private $fpm;

    public function getMemoryLimit(): int
    {
        return $this->memoryLimit;
    }

    /**
     * @return $this
     */
    public function setMemoryLimit(int $memoryLimit): self
    {
        $this->memoryLimit = $memoryLimit;

        return $this;
    }

    public function isZendThreadSafe(): bool
    {
        return $this->zendThreadSafe;
    }

    /**
     * @return $this
     */
    public function setZendThreadSafe(bool $zendThreadSafe): self
    {
        $this->zendThreadSafe = $zendThreadSafe;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return $this
     */
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
     *
     * @return $this
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
     *
     * @return $this
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

    /**
     * @return $this
     */
    public function setIniFile(string $iniFile): self
    {
        $this->iniFile = $iniFile;

        return $this;
    }

    public function getIncludePath(): string
    {
        return $this->includePath;
    }

    /**
     * @return $this
     */
    public function setIncludePath(string $includePath): self
    {
        $this->includePath = $includePath;

        return $this;
    }

    public function getOpenBasedir(): string
    {
        return $this->openBasedir;
    }

    /**
     * @return $this
     */
    public function setOpenBasedir(string $openBasedir): self
    {
        $this->openBasedir = $openBasedir;

        return $this;
    }

    public function getSapiName(): string
    {
        return $this->sapiName;
    }

    /**
     * @return $this
     */
    public function setSapiName(string $sapiName): self
    {
        $this->sapiName = $sapiName;

        return $this;
    }

    public function getOpcache(): Opcache
    {
        return $this->opcache;
    }

    /**
     * @return $this
     */
    public function setOpcache(Opcache $opcache): self
    {
        $this->opcache = $opcache;

        return $this;
    }

    public function getFpm(): Fpm
    {
        return $this->fpm;
    }

    /**
     * @return $this
     */
    public function setFpm(Fpm $fpm): self
    {
        $this->fpm = $fpm;

        return $this;
    }

    public function getApcu(): Apcu
    {
        return $this->apcu;
    }

    /**
     * @return $this
     */
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
     *
     * @return $this
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
     *
     * @return $this
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

    /**
     * @return $this
     */
    public function setRealpathCacheSizeUsed(float $realpathCacheSizeUsed): self
    {
        $this->realpathCacheSizeUsed = $realpathCacheSizeUsed;

        return $this;
    }

    public function getRealpathCacheSizeAllowed(): ?float
    {
        return $this->realpathCacheSizeAllowed;
    }

    /**
     * @return $this
     */
    public function setRealpathCacheSizeAllowed(?float $realpathCacheSizeAllowed): self
    {
        $this->realpathCacheSizeAllowed = $realpathCacheSizeAllowed;

        return $this;
    }
}
