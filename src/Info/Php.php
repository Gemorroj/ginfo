<?php

namespace Ginfo\Info;

use Ginfo\Info\Php\Apcu;
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

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
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
     * @return $this
     */
    public function setZendExtensions(array $zendExtensions): self
    {
        $this->zendExtensions = $zendExtensions;
        return $this;
    }

    /**
     * @return string
     */
    public function getIniFile(): string
    {
        return $this->iniFile;
    }

    /**
     * @param string $iniFile
     * @return $this
     */
    public function setIniFile(string $iniFile): self
    {
        $this->iniFile = $iniFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getIncludePath(): string
    {
        return $this->includePath;
    }

    /**
     * @param string $includePath
     * @return $this
     */
    public function setIncludePath(string $includePath): self
    {
        $this->includePath = $includePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getSapiName(): string
    {
        return $this->sapiName;
    }

    /**
     * @param string $sapiName
     * @return $this
     */
    public function setSapiName(string $sapiName): self
    {
        $this->sapiName = $sapiName;
        return $this;
    }

    /**
     * @return Opcache
     */
    public function getOpcache(): Opcache
    {
        return $this->opcache;
    }

    /**
     * @param Opcache $opcache
     * @return $this
     */
    public function setOpcache(Opcache $opcache): self
    {
        $this->opcache = $opcache;
        return $this;
    }

    /**
     * @return Apcu
     */
    public function getApcu(): Apcu
    {
        return $this->apcu;
    }

    /**
     * @param Apcu $apcu
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
     * @return $this
     */
    public function setDisabledClasses(array $disabledClasses): self
    {
        $this->disabledClasses = $disabledClasses;
        return $this;
    }

    /**
     * @return float
     */
    public function getRealpathCacheSizeUsed(): float
    {
        return $this->realpathCacheSizeUsed;
    }

    /**
     * @param float $realpathCacheSizeUsed
     * @return $this
     */
    public function setRealpathCacheSizeUsed(float $realpathCacheSizeUsed): self
    {
        $this->realpathCacheSizeUsed = $realpathCacheSizeUsed;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getRealpathCacheSizeAllowed(): ?float
    {
        return $this->realpathCacheSizeAllowed;
    }

    /**
     * @param float|null $realpathCacheSizeAllowed
     * @return $this
     */
    public function setRealpathCacheSizeAllowed(?float $realpathCacheSizeAllowed): self
    {
        $this->realpathCacheSizeAllowed = $realpathCacheSizeAllowed;
        return $this;
    }
}
