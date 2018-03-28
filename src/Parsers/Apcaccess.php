<?php

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

namespace Linfo\Parsers;

use Symfony\Component\Process\Process;


class Apcaccess implements Parser
{
    public static function work()
    {
        $process = new Process('apcaccess');
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $result = $process->getOutput();


        $res = [];

        if (\preg_match('/^UPSNAME\s+:\s+(.+)$/m', $result, $m)) {
            $res['name'] = $m[1];
        }
        if (\preg_match('/^MODEL\s+:\s+(.+)$/m', $result, $m)) {
            $res['model'] = $m[1];
        }
        if (\preg_match('/^BATTV\s+:\s+(\d+\.\d+)/m', $result, $m)) {
            $res['volts'] = $m[1];
        }
        if (\preg_match('/^BCHARGE\s+:\s+(\d+(?:\.\d+)?)/m', $result, $m)) {
            $charge = (int)$m[1];
            $res['charge'] = $charge ? $charge . '%' : null;
        }
        if (\preg_match('/^TIMELEFT\s+:\s+([\d\.]+)/m', $result, $m)) {
            $res['timeLeft'] = $m[1] * 60;
        }
        if (\preg_match('/^STATUS\s+:\s+([A-Z]+)/m', $result, $m)) {
            $res['status'] = $m[1] === 'ONBATT' ? 'On Battery' : \ucfirst(\mb_strtolower($m[1]));
        }
        if (\preg_match('/^LOADPCT\s+:\s+(\d+\.\d+)/m', $result, $m)) {
            $load = (int)$m[1];
            $res['load'] = $load ? $load . '%' : null;
        }

        if (isset($load) && \preg_match('/^NOMPOWER\s+:\s+(\d+)/m', $result, $m)) {
            $watts = (int)$m[1];
            $res['wattsUsed'] = $load * \round($watts / 100);
        } else {
            $res['wattsUsed'] = null;
        }

        if (!$res) {
            return null;
        }

        return [
            'name' => $res['name'],
            'model' => $res['model'],
            'batteryVolts' => $res['volts'],
            'batteryCharge' => $res['charge'],
            'timeLeft' => $res['time_left'],
            'currentLoad' => $res['load'],
            'currentUsage' => $res['watts_used'] ? $res['watts_used'] . 'W' : null,
            'status' => $res['status'],
        ];
    }
}
