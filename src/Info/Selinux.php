<?php

namespace Ginfo\Info;

final readonly class Selinux implements InfoInterface
{
    public function __construct(
        private bool $enabled,
        private ?string $mode = null,
        private ?string $policy = null
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
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
}
