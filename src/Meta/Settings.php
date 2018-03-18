<?php

/**
 * This file is part of Linfo (c) 2010 Joseph Gillotti.
 *
 * Linfo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Linfo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linfo. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Linfo\Meta;

class Settings
{
    /**
     * @var static
     */
    static protected $instance;
    protected $settings = [];

    protected function __clone(){}
    protected function __construct(){}

    /**
     * @return static
     */
    static public function getInstance()
    {
        if (!(static::$instance instanceof static)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getDefaultSettings()
    {
        $settings = array();

        /*
         * Usual configuration
         */
        $settings['byte_notation'] = 1024; // Either 1024 or 1000; defaults to 1024
        $settings['dates'] = 'm/d/y h:i A (T)'; // Format for dates shown. See php.net/date for syntax

        /*
         * Possibly don't show stuff
         */

        // For certain reasons, some might choose to not display all we can
        // Set these to true to enable; false to disable. They default to false.
        $settings['show']['kernel'] = true;
        $settings['show']['ip'] = true;
        $settings['show']['os'] = true;
        $settings['show']['load'] = true;
        $settings['show']['ram'] = true;
        $settings['show']['hd'] = true;
        $settings['show']['mounts'] = true;
        $settings['show']['mounts_options'] = true; // Might be useless/confidential information; disabled by default.
        $settings['show']['webservice'] = false; // Might be dangerous/confidential information; disabled by default.
        $settings['show']['phpversion'] = true; // Might be dangerous/confidential information; disabled by default.
        $settings['show']['network'] = true;
        $settings['show']['uptime'] = true;
        $settings['show']['cpu'] = true;
        $settings['show']['process_stats'] = true;
        $settings['show']['hostname'] = true;
        $settings['show']['distro'] = true; # Attempt finding name and version of distribution on Linux systems
        $settings['show']['devices'] = true; # Slow on old systems
        $settings['show']['model'] = true; # Model of system. Supported on certain OS's. ex: Macbook Pro
        $settings['show']['numLoggedIn'] = true; # Number of unqiue users with shells running (on Linux)
        $settings['show']['virtualization'] = true; # whether this is a VPS/VM and what kind

        // CPU Usage on Linux (per core and overall). This requires running sleep(1) once so it slows
        // the entire page load down. Enable at your own inconvenience, especially since the load averages
        // are more useful.
        $settings['cpu_usage'] = true;

        // Sometimes a filesystem mount is mounted more than once. Only list the first one I see?
        // (note, duplicates are not shown twice in the file system totals)
        $settings['show']['duplicate_mounts'] = true;

        // Disabled by default as they require extra config below
        $settings['show']['temps'] = true;
        $settings['show']['raid'] = true;

        // Following are probably only useful on laptop/desktop/workstation systems, not servers, although they work just as well
        $settings['show']['battery'] = true;
        $settings['show']['sound'] = true;
        $settings['show']['wifi'] = true; # Not finished

        // Service monitoring
        $settings['show']['services'] = true;

        /*
         * Misc settings pertaining to the above follow below:
         */

        // Hide certain file systems / devices
        $settings['hide']['filesystems'] = array(
            'tmpfs', 'ecryptfs', 'nfsd', 'rpc_pipefs', 'proc', 'sysfs',
            'usbfs', 'devpts', 'fusectl', 'securityfs', 'fuse.truecrypt',
            'cgroup', 'debugfs', 'mqueue', 'hugetlbfs', 'pstore', 'rootfs', 'binfmt_misc'
        );
        $settings['hide']['storage_devices'] = array('gvfs-fuse-daemon', 'none', 'systemd-1', 'udev');

        // filter mountpoints based on PCRE regex, eg '@^/proc@', '@^/sys@', '@^/dev@'
        $settings['hide']['mountpoints_regex'] = array();

        // Hide mount options for these file systems. (very, very suggested, especially the ecryptfs ones)
        $settings['hide']['fs_mount_options'] = array('ecryptfs');

        // Hide hard drives that begin with /dev/sg?. These are duplicates of usual ones, like /dev/sd?
        $settings['hide']['sg'] = true; # Linux only

        // Set to true to not resolve symlinks in the mountpoint device paths. Eg don't convert /dev/mapper/root to /dev/dm-0
        $settings['hide']['dont_resolve_mountpoint_symlinks'] = false; # Linux only

        // Various softraids. Set to true to enable.
        // Only works if it's available on your system; otherwise does nothing
        $settings['raid']['mdadm'] = true;  // For Linux; known to support RAID 1, 5, and 6

        // Various ways of getting temps/voltages/etc. Set to true to enable. Currently these are just for Linux
        $settings['temps']['hwmon'] = true; // Requires no extra config, is fast, and is in /sys :)
        $settings['temps']['thermal_zone'] = true;
        $settings['temps']['hddtemp'] = true;
        $settings['temps']['mbmon'] = true;
        $settings['temps']['sensord'] = true; // Part of lm-sensors; logs periodically to syslog. slow
        $settings['temps_show0rpmfans'] = true; // Set to true to show fans with 0 RPM

        // Configuration for getting temps with hddtemp
        $settings['hddtemp']['mode'] = 'daemon'; // Either daemon or syslog
        $settings['hddtemp']['address'] = array( // Address/Port of hddtemp daemon to connect to
            'host' => 'localhost',
            'port' => 7634
        );
        // Configuration for getting temps with mbmon
        $settings['mbmon']['address'] = array( // Address/Port of mbmon daemon to connect to
            'host' => 'localhost',
            'port' => 411
        );

        /*
         * For the things that require executing external programs, such as non-linux OS's
         * and the extensions, you may specify other paths to search for them here:
         */
        $settings['additional_paths'] = array(//'/opt/bin' # for example
        );


        /*
         * Services. It works by specifying locations to PID files, which then get checked
         * Either that or specifying a path to the executable, which we'll try to find a running
         * process PID entry for. It'll stop on the first it finds.
         */

        // Format: Label => pid file path
        $settings['services']['pidFiles'] = array(
            // 'Apache' => '/var/run/apache2.pid', // uncomment to enable
            // 'SSHd' => '/var/run/sshd.pid'
        );

        // Format: Label => path to executable or array containing arguments to be checked
        $settings['services']['executables'] = array(
            // 'MySQLd' => '/usr/sbin/mysqld' // uncomment to enable
            // 'BuildSlave' => array('/usr/bin/python', // executable
            //						1 => '/usr/local/bin/buildslave') // argv[1]
        );

        // Format: Label => systemd service name
        $settings['services']['systemdServices'] = array(
            // 'Apache' => 'httpd', // uncomment to enable
            // 'SSHd' => 'sshd'
        );

        /*
         * Occasional sudo
         * Sometimes you may want to have one of the external commands here be ran as root with
         * sudo. This requires the web server user be set to "NOPASS" in your sudoers so the sudo
         * command just works without a prompt.
         *
         * Add names of commands to the array if this is what you want. Just the name of the command;
         * not the complete path. This also applies to commands called by extensions.
         *
         * Note: this is extremely dangerous if done wrong
         */
        $settings['sudo_apps'] = array(//'ps' // For example
        );

        $settings['extensions']['ipmi'] = true;
        $settings['extensions']['nvidia'] = true;
        $settings['extensions']['smb'] = true;
        $settings['extensions']['lxd'] = true;
        $settings['extensions']['libvirt'] = true;
        $settings['libvirt_connection'] = array(
            'url' => 'qemu:///system', // For xen do 'xen:///' instead
            'credentials' => null
        );
        $settings['extensions']['Dnsmasq_dhcpd'] = true;
        $settings['dnsmasq_hide_mac'] = false;  // set to false to show mac addresses
        $settings['dnsmasq_leases'] = '/var/lib/libvirt/dnsmasq/default.leases';  // change path to the leases file. defaults to /var/lib/libvirt/dnsmasq/default.leases
        $settings['extensions']['dhcpd3_leases'] = true;
        $settings['extensions']['cups'] = true;
        $settings['extensions']['apcaccess'] = true;

        return $settings;
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }


    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }
}
