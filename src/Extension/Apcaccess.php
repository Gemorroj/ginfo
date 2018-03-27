<?php

/*
This lets you view the command output of the APC program apcaccess.

Make sure that you have your UPS connected correctly, the apc package installed, and that
running apcaccess produces output you find interesting enough for Linfo to display.

Installation: 
 - The following lines must be added to your settings:
   $settings['extensions']['apcaccess'] = true;
*/

/**
 * This file is part of Linfo (c) 2011 Joseph Gillotti.
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

namespace Linfo\Extension;

use Linfo\Linfo;
use Linfo\Common;
use Linfo\Meta\Errors;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Get status on apcaccess volumes. 
 */
class Apcaccess implements Extension
{
    private $_res;

    // Localize important classes
    public function __construct(Linfo $linfo)
    {
    }

    // call apcaccess and parse it
    private function _call()
    {
        // Deal with calling it
        try {
            $process = new Process('apcaccess');
            $process->mustRun();
            $result = $process->getOutput();
        } catch (ProcessFailedException $e) {
            // messed up somehow
            Errors::add('apcaccess Extension', $e->getMessage());
            $this->_res = false;

            // Don't bother going any further
            return false;
        }

        // Store them here
        $this->_res = array();

        // Get name
        if (preg_match('/^UPSNAME\s+:\s+(.+)$/m', $result, $m)) {
            $this->_res['name'] = $m[1];
        }

        // Get model
        if (preg_match('/^MODEL\s+:\s+(.+)$/m', $result, $m)) {
            $this->_res['model'] = $m[1];
        }

        // Get battery voltage
        if (preg_match('/^BATTV\s+:\s+(\d+\.\d+)/m', $result, $m)) {
            $this->_res['volts'] = $m[1];
        }

        // Get charge percentage, and get it cool
        if (preg_match('/^BCHARGE\s+:\s+(\d+(?:\.\d+)?)/m', $result, $m)) {
            $charge = (int)$m[1];
            $this->_res['charge'] = $charge ? $charge . '%' : '?';
        }

        // Get time remaning
        if (preg_match('/^TIMELEFT\s+:\s+([\d\.]+)/m', $result, $m)) {
            $this->_res['time_left'] = Common::secondsConvert($m[1] * 60);
        }

        // Get status
        if (preg_match('/^STATUS\s+:\s+([A-Z]+)/m', $result, $m)) {
            $this->_res['status'] = $m[1] == 'ONBATT' ? 'On Battery' : ucfirst(\mb_strtolower($m[1]));
        }

        // Load percentage looking cool
        if (preg_match('/^LOADPCT\s+:\s+(\d+\.\d+)/m', $result, $m)) {
            $load = (int)$m[1];
            $this->_res['load'] = $load ? $load . '%' : '?';
        }

        // Attempt getting wattage 
        if (isset($load) && preg_match('/^NOMPOWER\s+:\s+(\d+)/m', $result, $m)) {
            $watts = (int)$m['1'];
            $this->_res['watts_used'] = $load * round($watts / 100);
        } else {
            $this->_res['watts_used'] = false;
        }

        // Apparent success
        return true;
    }

    // Called to get working
    public function work()
    {
        $this->_call();
    }

    // Get result. Essentially take results and make it usable by the Common::createTable function
    public function result()
    {

        // Don't bother if it didn't go well
        if ($this->_res === false) {
            return false;
        }

        // Store rows here
        $rows = array();

        // Start showing connections
        $rows[] = array(
            'type' => 'header',
            'columns' => array(
                'UPS Name',
                'Model',
                'Battery Volts',
                'Battery Charge',
                'Time Left',
                'Current Load',
                $this->_res['watts_used'] ? 'Current Usage' : false,
                'Status',
            ),
        );

        // And all the values
        $rows[] = array(
            'type' => 'values',
            'columns' => array(
                $this->_res['name'],
                $this->_res['model'],
                $this->_res['volts'],
                $this->_res['charge'],
                $this->_res['time_left'],
                $this->_res['load'],
                $this->_res['watts_used'] ? $this->_res['watts_used'] . 'W' : false,
                $this->_res['status'],
            ),
        );

        // Give it off
        return array(
            'root_title' => 'APC UPS Status',
            'rows' => $rows,
        );
    }
}