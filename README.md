# Linfo - Server stats library

### Linfo is a:
 - Extensible, easy (composer) to use PHP Library to get extensive system stats programmatically from your PHP app

### Fork changes:
- drop ui
- drop internationalization
- drop bsd* support (sorry, I will not be able to support)
- drop dhcp3 support
- drop dnsmasq support
- drop php libvirt support
- drop lxd support
- no need `COM` extension on Windows, but need powershell
- minimal windows 10 support
- minor code improvements
- minimal php version 7.1.10


### PHP library usage

```bash
composer require gemorroj/linfo
```

```php
<?php
$linfo = new \Linfo\Linfo();
$info = $linfo->getInfo();

print_r($info);
```



## Runs on
- Linux
- Windows >= 10

## Information reported
- CPU type/speed; Architecture
- Mount point usage
- Hard/optical/flash drives
- Hardware devices (PCI) (linux: need `pciutils`)
- USB devices (linux: need `usbutils`)
- Network devices and stats
- Uptime
- Memory usage (physical and swap, linux: need `free`)
- Temperatures/voltages/fan speeds (linux: need `hddtemp` as daemon, `mbmon` as daemon, `sensord` (part of `lm-sensors`), `hwmon`, `acpi themal zone`, `nvidia-smi`, `ipmitool`)
- RAID arrays (linux: need `mdadm`)
- Motherboard (linux: need `dimedecode`)
- Processes
- Systemd services (linux: need `systemctl`)
- logged users (linux: need `who`)'
- UPS status (linux: need `apcaccess`)
- Printer status (linux: need `lpstat`)
- Samba status (linux: need `smbstatus`)


## System requirements:
- PHP >= 7.1.10
- pcre extension
- proc_open

#### Windows
- You need to have `powershell`
- Allow execute ps1 scripts `Set-ExecutionPolicy RemoteSigned –Force`

#### Linux
- `/proc` and `/sys` mounted and readable by PHP
- Tested with the 2.6.x/3.x kernels
