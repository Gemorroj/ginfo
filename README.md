# Ginfo - Server stats library

[![License](https://poser.pugx.org/gemorroj/ginfo/license)](https://packagist.org/packages/gemorroj/ginfo)
[![Latest Stable Version](https://poser.pugx.org/gemorroj/ginfo/v/stable)](https://packagist.org/packages/gemorroj/ginfo)
[![Continuous Integration](https://github.com/Gemorroj/ginfo/workflows/Continuous%20Integration/badge.svg)](https://github.com/Gemorroj/ginfo/actions?query=workflow%3A%22Continuous+Integration%22)


### Ginfo is a:
 - Extensible PHP library to get extensive system stats programmatically from your PHP app

### Fork changes:
- drop ui
- drop internationalization
- drop bsd* support (sorry, I will not be able to support)
- drop dhcp3 support
- drop dnsmasq support
- drop php libvirt support
- drop lxd support
- no need `COM` extension on Windows, but need powershell
- support Windows >= 10
- adapt the code to modern standards
- minimal php version 8.2
- add selinux status info
- add php info (basic, opcache, apcu)
- add web-servers info (nginx, angie, httpd, caddy)
- allow add custom parsers


### Installation:
```bash
composer require gemorroj/ginfo
```

### Example:
```php
<?php
use Ginfo\Ginfo;

$ginfo = new Ginfo();
$info = $ginfo->getInfo();

print_r($info->getGeneral());
print_r($info->getPhp());
print_r($info->getCpu());
print_r($info->getMemory());
print_r($info->getSoundCard());
print_r($info->getUsb());
print_r($info->getUps());
print_r($info->getPci());
print_r($info->getNetwork());
print_r($info->getDisk());
print_r($info->getBattery());
print_r($info->getSensors());
print_r($info->getProcesses());
print_r($info->getServices());
print_r($info->getPrinters());
print_r($info->getSamba());
print_r($info->getSelinux());
print_r($info->getNginx());
print_r($info->getAngie('http://localhost/status/'));
print_r($info->getHttpd());
print_r($info->getCaddy());
```

### Custom parser example:
```php
<?php
use Ginfo\Ginfo;
use Ginfo\Info\InfoInterface;
use Ginfo\InfoParserInterface;

// class for parsed data
final readonly class SwooleInfo implements InfoInterface
{
    public function __construct(private array $domeData)
    {
    }
    
    public function getSomeData(): array
    {
        return $this->someData;
    }
}

// parser
final readonly class SwooleParser implements InfoParserInterface
{
    public function run(): ?InfoInterface
    {
        // ... some job
        $someData = ['some', 'data'];
        return new SwooleInfo($someData);
    }
}

$swooleParser = new SwooleParser();

$ginfo = new Ginfo();
$info = $ginfo->getInfo($swooleParser);
/** @var SwooleInfo $data */
$data = $info->getCustomParser(SwooleParser::class);
print_r($data->getSomeData());
```


### Information reported
- CPU type/speed; Architecture
- Mount point usage
- Hard/optical/flash drives
- Hardware devices (PCI) (linux: need `pciutils`)
- USB devices (linux: need `usbutils`)
- Network devices and stats
- Uptime
- Memory usage (physical and swap, linux: need `free`)
- Temperatures/voltages/fan speeds (linux: need `hddtemp` as daemon, `mbmon` as daemon, `sensors` (part of `lm-sensors`), `hwmon`, `acpi themal zone`, `nvidia-smi`, `ipmitool`)
- RAID arrays (linux: need `mdadm`)
- Motherboard (linux: need `dimedecode`)
- Processes
- Systemd services (linux: need `systemctl`)
- logged users (linux: need `who`)
- UPS status (linux: need `apcaccess`)
- Printer status (linux: need `lpstat`)
- Samba status (linux: need `smbstatus`)
- Selinux status (linux: need `sestatus`)
- PHP (basic info, `opcache`, `apcu`)
- Web-Servers (`angie`, `nginx`, `httpd`, `caddy`)


### System requirements:
- PHP >= 8.2
- pcre extension
- proc_open
- Linux/Windows

#### Windows
- Windows >= 10
- You need to have `powershell`
- Allow execute ps1 scripts `Set-ExecutionPolicy RemoteSigned –Force`

#### Linux
- `/proc` and `/sys` mounted and readable by PHP
- Tested with the 2.6.x/3.x/4.x/5.x/6.x kernels
