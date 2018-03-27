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
- minimal php version 7.1


### PHP library usage

```bash
composer require gemorroj/linfo
```

```php
<?php
$linfo = new \Linfo\Linfo();
$linfo->scan();
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
 - Hardware Devices
 - Network devices and stats
 - Uptime/date booted
 - Hostname
 - Memory usage (physical and swap, if possible)
 - Temperatures/voltages/fan speeds
 - RAID arrays
 - Via included extensions:
   - Nvidia GPU temps
   - DHCPD leases
   - Samba status
   - APC UPS status
   - CUPS printer status
   - IPMI
   - libvirt VMs
   - lxd Containers
   - more

## System requirements:
 - At least PHP 7.1
 - If you are using PHP 7.1.9 or lower, you might need to disable the opcache extension.
 - pcre extension

#### Windows
 - You need to have `powershell`
 - Allow execute ps1 scripts `Set-ExecutionPolicy RemoteSigned –Force`

#### Linux
 - /proc and /sys mounted and readable by PHP
 - Tested with the 2.6.x/3.x kernels

### Extensions
 - See a list of php files in src/Extensions/
 - Open them and look at the comment at the top of the file for usage
