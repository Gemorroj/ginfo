<?php

namespace Ginfo\Info;

final class Selinux
{
    private bool $enabled;
    private ?string $mode = null;
    private ?string $policy = null;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

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
     * @return string|null (enforcing|permissive|disabled)
     */
    public function getMode(): ?string
    {
        return $this->mode;
    }

    /**
     * enforcing – This indicates that SELinux security policy is enforced (i.e SELinux is enabled)
     * permissive – This indicates that SELinux prints warnings instead of enforcing. This is helpful during debugging purpose when you want to know what would SELinux potentially block (without really blocking it) by looking at the SELinux logs.
     * disabled – No SELinux policy is loaded.
     *
     * @param string $mode (enforcing|permissive|disabled)
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
     * @return string|null (targeted|minimum|mls)
     */
    public function getPolicy(): ?string
    {
        return $this->policy;
    }

    /**
     * targeted – This means that only targeted processes are protected by SELinux
     * minimum – This is a slight modification of targeted policy. Only few selected processes are protected in this case.
     * mls – This is for Multi Level Security protection. MLS is pretty complex and pretty much not used in most situations.
     *
     * @param string $policy (targeted|minimum|mls)
     */
    public function setPolicy(string $policy): self
    {
        $this->policy = $policy;

        return $this;
    }
}
