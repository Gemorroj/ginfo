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
}
