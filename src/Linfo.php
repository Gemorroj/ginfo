<?php

/**
 * This file is part of Linfo (c) 2014, 2015 Joseph Gillotti.
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
 */

namespace Linfo;

use Linfo\Extension\Extension;
use Linfo\Meta\Settings;
use Linfo\OS\Linux;
use Linfo\OS\OS;
use Linfo\Exceptions\FatalException;
use Linfo\Meta\Errors;
use Linfo\OS\Windows;

/**
 * Linfo.
 *
 * Serve as the script's "controller". Leverages other classes. Loads settings,
 * outputs them in formats, runs extensions, etc.
 *
 * @throws FatalException
 */
class Linfo
{
    protected $info = [];
    /** @var OS */
    protected $os;

    /**
     * Linfo constructor.
     * @param array $userSettings
     * @throws FatalException
     */
    public function __construct(array $userSettings = [])
    {
        Settings::getInstance()->setSettings(\array_merge(
            Settings::getInstance()->getDefaultSettings(),
            $userSettings
        ));

        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->os = new Windows();
        } else {
            // bsd, linux, darwin, solaris
            $this->os = new Linux();
        }
    }

    /**
     * Load everything, while obeying permissions...
     */
    public function scan()
    {
        // Array fields, tied to method names and default values...
        $fields = array(
            'OS' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['os']),
                'default' => '',
                'method' => 'getOS',
            ),

            'Kernel' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['kernel']),
                'default' => '',
                'method' => 'getKernel',
            ),

            'Distro' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['distro']),
                'default' => '',
                'method' => 'getDistro',
            ),
            'CPU' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['cpu']),
                'default' => array(),
                'method' => 'getCPU',
            ),
            'RAM' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['ram']),
                'default' => array(),
                'method' => 'getRam',
            ),

            'HD' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['hd']),
                'default' => array(),
                'method' => 'getHD',
            ),

            'Mounts' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['mounts']),
                'default' => array(),
                'method' => 'getMounts',
            ),

            'Load' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['load']),
                'default' => array(),
                'method' => 'getLoad',
            ),

            'HostName' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['hostname']),
                'default' => '',
                'method' => 'getHostName',
            ),

            'UpTime' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['uptime']),
                'default' => array(),
                'method' => 'getUpTime',
            ),



            'Model' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['model']),
                'default' => array(),
                'method' => 'getModel',
            ),

            'CPUArchitecture' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['cpu']),
                'default' => '',
                'method' => 'getCPUArchitecture',
            ),

            'Network Devices' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['network']),
                'default' => array(),
                'method' => 'getNet',
            ),

            'Devices' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['devices']),
                'default' => array(),
                'method' => 'getDevs',
            ),

            'Temps' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['temps']),
                'default' => array(),
                'method' => 'getTemps',
            ),

            'Battery' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['battery']),
                'default' => array(),
                'method' => 'getBattery',
            ),

            'Raid' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['raid']),
                'default' => array(),
                'method' => 'getRAID',
            ),

            'Wifi' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['wifi']),
                'default' => array(),
                'method' => 'getWifi',
            ),

            'SoundCards' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['sound']),
                'default' => array(),
                'method' => 'getSoundCards',
            ),

            'processStats' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['process_stats']),
                'default' => array(),
                'method' => 'getProcessStats',
            ),

            'services' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['services']),
                'default' => array(),
                'method' => 'getServices',
            ),

            'numLoggedIn' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['numLoggedIn']),
                'default' => false,
                'method' => 'getnumLoggedIn',
            ),

            'virtualization' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['virtualization']),
                'default' => array(),
                'method' => 'getVirtualization',
            ),

            'cpuUsage' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['cpu_usage']),
                'default' => false,
                'method' => 'getCPUUsage',
            ),

            'phpVersion' => array(
                'show' => !empty(Settings::getInstance()->getSettings()['show']['phpversion']),
                'default' => false,
                'method' => 'getPhpVersion',
            ),

            // Extra info such as which fields to not show
            'contains' => array(
                'show' => true,
                'default' => array(),
                'method' => 'getContains',
            ),
        );

        foreach ($fields as $key => $data) {
            if (!$data['show']) {
                $this->info[$key] = $data['default'];
                continue;
            }

            if (method_exists($this->os, $data['method'])) {
                $this->info[$key] = call_user_func(array($this->os, $data['method']));
            } else {
                $this->info[$key] = $data['default'];
            }
        }

        // Add a timestamp
        $this->info['timestamp'] = date('c');

        // Run extra extensions
        $this->runExtensions();
    }

    /**
     * getInfo()
     *
     * Returning reference so extensions can modify result
     * @return array
     */
    public function &getInfo()
    {
        return $this->info;
    }

    /**
     * run extensions
     */
    protected function runExtensions()
    {
        $this->info['extensions'] = array();

        if (!array_key_exists('extensions', Settings::getInstance()->getSettings()) || count(Settings::getInstance()->getSettings()['extensions']) == 0) {
            return;
        }

        // Go through each enabled extension
        foreach ((array)Settings::getInstance()->getSettings()['extensions'] as $ext => $enabled) {

            // Is it really enabled?
            if (empty($enabled)) {
                continue;
            }

            // Anti hack
            if (!preg_match('/^[a-z0-9-_]+$/i', $ext)) {
                Errors::add('Extension Loader', 'Not going to load "' . $ext . '" extension as only characters allowed in name are letters/numbers/-_');
                continue;
            }

            // Support older config files with lowercase
            if (preg_match('/^[a-z]/', $ext)) {
                $ext = ucfirst($ext);
            }

            // Try loading our class..
            $extClassName = '\\Linfo\\Extension\\' . $ext;
            /** @var Extension $extClass */
            $extClass = new $extClassName($this);

            // Deal with it
            $extClass->work();

            // Does this edit the $info directly, instead of creating a separate output table type thing?
            if (!defined($extClassName.'::LINFO_INTEGRATE')) {

                // Result
                $result = $extClass->result();

                // Save result if it's good
                if ($result != false) {
                    $this->info['extensions'][$ext] = $result;
                }
            }
        }
    }
}
