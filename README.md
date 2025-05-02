# Ginfo - Server stats library

[![License](https://poser.pugx.org/gemorroj/ginfo/license)](https://packagist.org/packages/gemorroj/ginfo)
[![Latest Stable Version](https://poser.pugx.org/gemorroj/ginfo/v/stable)](https://packagist.org/packages/gemorroj/ginfo)
[![Continuous Integration](https://github.com/Gemorroj/ginfo/workflows/Continuous%20Integration/badge.svg)](https://github.com/Gemorroj/ginfo/actions?query=workflow%3A%22Continuous+Integration%22)


### Requirements:
- PHP >= 8.2
- pcre extension
- proc_open
- Linux

#### Linux
- `/proc`, `/sys` and `/etc` mounted and readable by PHP
- Tested with the 2.6.x/3.x/4.x/5.x/6.x kernels


### Installation:
```bash
composer require gemorroj/ginfo
```


### Example:
```php
<?php
use Ginfo\Ginfo;

$ginfo = new Ginfo();

print_r($ginfo->getGeneral()); // kernel, uptime, virtualization, load, etc...
print_r($ginfo->getPhp()); // version, extensions, Opcache, FPM, APCU, etc...
print_r($ginfo->getCpu()); // cores, speed, cache, etc...
print_r($ginfo->getMemory()); // total memory, used, free, cached, swap, etc...
print_r($ginfo->getSoundCard()); // vendor, name
print_r($ginfo->getUsb()); // vendor, name, speed
print_r($ginfo->getUps()); // vendor, time, status, charge, etc...
print_r($ginfo->getPci()); // vendor, name
print_r($ginfo->getNetwork()); // name, speed, state, stats, etc...
print_r($ginfo->getDisk()); // mounts, drives, raids, size, type, stats, etc...
print_r($ginfo->getBattery()); // model, status, voltage, charge, etc...
print_r($ginfo->getSensors()); // name, value, unit, path
print_r($ginfo->getProcesses()); // name, pid, commandLine, memory, state, stats, etc...
print_r($ginfo->getServices()); // name, state, type, etc...
print_r($ginfo->getPrinters()); // name, enabled
print_r($ginfo->getSamba()); // files, services, connections, etc...
print_r($ginfo->getSelinux()); // enabled, mode, policy
print_r($ginfo->getNginx()); // version, status, etc...
print_r($ginfo->getAngie('http://localhost/status/')); // version, status, etc...
print_r($ginfo->getHttpd()); // version, status, etc...
print_r($ginfo->getCaddy()); // version, status, etc...
print_r($ginfo->getMysql(new \PDO('mysql:host=127.0.0.1', 'root', ''))); // variables, performance, status, etc...
print_r($ginfo->getPostgres(new \PDO('pgsql:host=127.0.0.1', 'postgres', 'postgres'))); // pg_stat_activity, pg_stat_statements, etc...
print_r($ginfo->getManticore(new \PDO('mysql:host=127.0.0.1;port=9306', 'root', ''))); // status, variables, etc...
print_r($ginfo->getRedis(new \Redis(['host' => '127.0.0.1', 'port' => 6379]))); // status, memory, cpu, etc...
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
    public function __construct(private array $stats)
    {
    }
    
    public function getStats(): array
    {
        return $this->stats;
    }
}

// parser
final readonly class SwooleParser implements InfoParserInterface
{
    public function run(): ?InfoInterface
    {
        $stats = \app('Swoole\Http\Server')->stats(); // laravel
        return new SwooleInfo($stats);
    }
}

$swooleParser = new SwooleParser();

$ginfo = new Ginfo(customParsers: [$swooleParser]);
/** @var SwooleInfo $data */
$data = $ginfo->getCustomParser(SwooleParser::class);
print_r($data->getStats());
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
- Databases (`mysql/mariadb`, `postgres`, `manticore`, `redis/valkey`)


### Fork changes:
- drop windows support
- drop bsd* support
- drop ui
- drop internationalization
- drop dhcp3 support
- drop dnsmasq support
- drop php libvirt support
- drop lxd support
- adapt the code to modern standards
- minimal php version 8.2
- add selinux status info
- add php info (basic, opcache, apcu)
- add web-servers info (nginx, angie, httpd, caddy)
- add databases info (mysql/mariadb, postgres, manticore, redis/valkey)
- allow add custom parsers
