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

namespace Linfo\Parsers;

use Linfo\Common;
use Linfo\Linfo;
use Exception;
use Linfo\Meta\Settings;

/**
 * Class used to call external programs.
 */
class CallExt
{
    /**
     * Maintain a count of how many external programs we call.
     *
     * @var int
     */
    public static $callCount = 0;

    /**
     * Store results of commands here to avoid calling them more than once.
     *
     * @var array
     */
    protected $cliCache = array();

    /**
     * Store paths to look for executables here.
     *
     * @var array
     */
    protected $searchPaths = array();

    /**
     * Say where we'll search for execs.
     *
     * @param array $paths list of paths
     */
    public function setSearchPaths(array $paths)
    {
        // Merge in possible custom paths
        if (array_key_exists('additional_paths', Settings::getInstance()->getSettings()) &&
            is_array(Settings::getInstance()->getSettings()['additional_paths']) &&
            count(Settings::getInstance()->getSettings()['additional_paths']) > 0) {

            $paths = array_merge(Settings::getInstance()->getSettings()['additional_paths'], $paths);
        }

        // Make sure they all have a trailing slash
        foreach ($paths as $k => $v) {
            $paths[$k] .= substr($v, -1) == '/' ? '' : '/';
        }

        // Save them
        $this->searchPaths = $paths;
    }

    /**
     * Run a command and cache its output for later.
     *
     * @throws Exception
     *
     * @param string $name name of executable to call
     * @param string $switches command arguments
     * @return string
     */
    public function exec($name, $switches = '')
    {
        // Sometimes it is necessary to call a program with sudo
        $attempt_sudo = array_key_exists('sudo_apps', Settings::getInstance()->getSettings()) && in_array($name, Settings::getInstance()->getSettings()['sudo_apps']);

        // Have we gotten it before?
        if (array_key_exists($name . $switches, $this->cliCache)) {
            return $this->cliCache[$name . $switches];
        }

        // Try finding the exec
        foreach ($this->searchPaths as $path) {

            // Found it; run it
            if (is_file($path . $name) && is_executable($path . $name)) {

                // Complete command, path switches and all
                $command = "$path$name $switches";

                // Sudoing?
                $command = $attempt_sudo ? Common::locateActualPath(Common::arrayAppendString($this->searchPaths, 'sudo', '%2s%1s')) . ' ' . $command : $command;

                // Result of command
                $result = shell_exec($command);

                // Increment call count
                ++self::$callCount;

                // Cache that
                $this->cliCache[$name . $switches] = $result;

                // Give result
                return $result;
            }
        }

        // Never got it
        throw new Exception('Exec `' . $name . '` not found');
    }
}
