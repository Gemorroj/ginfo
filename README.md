# Linfo - Server stats library

### Linfo is a:
 - Extensible, easy (composer) to use PHP Library to get extensive system stats programmatically from your PHP app

### Fork changes:
- drop ui
- drop internationalization
- drop bsd* support (sorry, I will not be able to support)
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

print_r($info); // and a whole lot more
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
 - Temperatures/voltages/fan speeds (linux: need `hddtemp` as daemon, `mbmon` as daemon, `sensord` (part of `lm-sensors`), `hwmon`, `acpi themal zone`)
 - RAID arrays (linux: need `mdadm`)
 - Motherboard (linux: need `dimedecode`)
 - Processes
 - Systemd services
 - logged users (linux: need `who`)
 - Via included extensions:
   - Nvidia GPU temps (linux: need `nvidia-smi`)
   - DHCPD leases
   - Samba status
   - UPS status (linux: need `apcaccess`)
   - Printer status (linux: need `lpstat`)
   - IPMI
   - libvirt VMs
   - lxd Containers
   - more

## System requirements:
 - PHP >= 7.1.10
 - pcre extension
 - proc_open

#### Windows
 - You need to have `powershell`
 - Allow execute ps1 scripts `Set-ExecutionPolicy RemoteSigned –Force`

#### Linux
 - /proc and /sys mounted and readable by PHP
 - Tested with the 2.6.x/3.x kernels

### Extensions
 - See a list of php files in src/Extensions/
 - Open them and look at the comment at the top of the file for usage
