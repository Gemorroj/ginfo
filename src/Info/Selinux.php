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

namespace Linfo\Info;

class Selinux
{
    /** @var bool */
    private $enabled;
    /** @var string|null */
    private $mode;
    /** @var string|null */
    private $policy;

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * enforcing – This indicates that SELinux security policy is enforced (i.e SELinux is enabled)
     * permissive – This indicates that SELinux prints warnings instead of enforcing. This is helpful during debugging purpose when you want to know what would SELinux potentially block (without really blocking it) by looking at the SELinux logs.
     * disabled – No SELinux policy is loaded.
     *
     * @return string (enforcing|permissive|disabled)
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * enforcing – This indicates that SELinux security policy is enforced (i.e SELinux is enabled)
     * permissive – This indicates that SELinux prints warnings instead of enforcing. This is helpful during debugging purpose when you want to know what would SELinux potentially block (without really blocking it) by looking at the SELinux logs.
     * disabled – No SELinux policy is loaded.
     *
     * @param string $mode (enforcing|permissive|disabled)
     * @return $this
     */
    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * targeted – This means that only targeted processes are protected by SELinux
     * minimum – This is a slight modification of targeted policy. Only few selected processes are protected in this case.
     * mls – This is for Multi Level Security protection. MLS is pretty complex and pretty much not used in most situations.
     *
     * @return string (targeted|minimum|mls)
     */
    public function getPolicy(): string
    {
        return $this->policy;
    }

    /**
     * targeted – This means that only targeted processes are protected by SELinux
     * minimum – This is a slight modification of targeted policy. Only few selected processes are protected in this case.
     * mls – This is for Multi Level Security protection. MLS is pretty complex and pretty much not used in most situations.
     *
     * @param string $policy (targeted|minimum|mls)
     * @return $this
     */
    public function setPolicy(string $policy): self
    {
        $this->policy = $policy;
        return $this;
    }
}
