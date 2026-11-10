<?php

namespace Ginfo\Tests\Os;

use Ginfo\Info\Disk\Drive\Partition;
use Ginfo\Os\Linux;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LinuxTest extends TestCase
{
    public static function providePercent(): \Generator
    {
        yield [50.0, 200.0, 25.0];
        yield [1.0, 3.0, 33.33];
        yield [200.0, 200.0, 100.0];
        yield [null, 100.0, null];
        yield [10.0, null, null];
        yield [10.0, 0.0, null];
    }

    #[DataProvider('providePercent')]
    public function testPercent(?float $part, ?float $total, ?float $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'percent');

        self::assertSame($expected, $method->invoke(null, $part, $total));
    }

    public static function provideSpeed(): \Generator
    {
        yield ['1000', 1000000000.0];
        yield ['100', 100000000.0];
        yield ['4294967295', null];
        yield ['-1', null];
        yield ['0', null];
        yield ['abc', null];
        yield ['', null];
        yield [null, null];
    }

    #[DataProvider('provideSpeed')]
    public function testParseSpeed(?string $raw, ?float $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseSpeed');

        self::assertSame($expected, $method->invoke(null, $raw));
    }

    public static function provideSoundCards(): \Generator
    {
        yield 'two cards' => [
            [
                ' 0 [PCH            ]: HDA-Intel - HDA Intel PCH',
                '                      HDA Intel PCH at 0xf7f10000 irq 47',
                ' 1 [HDMI           ]: HDA-Intel - HDA HDMI',
                '                      HDA HDMI at 0xf7f20000 irq 48',
            ],
            [
                ['vendor' => 'HDA Intel PCH', 'name' => 'HDA-Intel - HDA Intel PCH'],
                ['vendor' => 'HDA HDMI', 'name' => 'HDA-Intel - HDA HDMI'],
            ],
        ];

        yield 'no soundcards single line' => [
            ['--- no soundcards ---'],
            [],
        ];

        yield 'dangling last line is ignored' => [
            [
                ' 0 [PCH            ]: HDA-Intel - HDA Intel PCH',
                '                      HDA Intel PCH at 0xf7f10000 irq 47',
                'dangling',
            ],
            [
                ['vendor' => 'HDA Intel PCH', 'name' => 'HDA-Intel - HDA Intel PCH'],
            ],
        ];
    }

    /**
     * @param string[]                                        $lines
     * @param array<int, array{vendor: string, name: string}> $expected
     */
    #[DataProvider('provideSoundCards')]
    public function testParseSoundCards(array $lines, array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseSoundCards');

        $cards = $method->invoke(null, $lines);

        self::assertCount(\count($expected), $cards);
        foreach ($expected as $i => $card) {
            self::assertSame($card['vendor'], $cards[$i]->getVendor());
            self::assertSame($card['name'], $cards[$i]->getName());
        }
    }

    public static function provideUptime(): \Generator
    {
        yield ['12345.67 99999.99', 12345];
        yield ["42.5 100.0\n", 42];
        yield ['', null];
        yield ["  \n", null];
    }

    #[DataProvider('provideUptime')]
    public function testParseUptime(string $content, ?int $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseUptime');

        self::assertSame($expected, $method->invoke(null, $content));
    }

    public static function provideLoad(): \Generator
    {
        yield ['0.11 0.17 0.18 1/234 5678', [0.11, 0.17, 0.18]];
        yield ['1.00 2.00 3.00', [1.0, 2.0, 3.0]];
        yield ['0.5 0.6', null];
        yield ['', null];
        yield ['a b c', null];
    }

    /**
     * @param float[]|null $expected
     */
    #[DataProvider('provideLoad')]
    public function testParseLoad(string $content, ?array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseLoad');

        self::assertSame($expected, $method->invoke(null, $content));
    }

    public static function provideModel(): \Generator
    {
        yield 'vendor and product' => [
            ['B450 TOMAHAWK', 'Micro-Star International Co., Ltd.', 'MS-7B89'],
            'MS-7B89 (Micro-Star International Co., Ltd. B450 TOMAHAWK)',
        ];

        yield 'product is already in the name' => [
            ['Dell Inc. XPS 13 9370', 'Dell Inc.', 'XPS 13 9370'],
            'Dell Inc. XPS 13 9370',
        ];

        yield 'placeholder product' => [
            ['B450 TOMAHAWK', 'MSI', 'Filled by O.E.M.'],
            'MSI B450 TOMAHAWK',
        ];

        yield 'without vendor and product' => [
            ['B450 TOMAHAWK', null, null],
            'B450 TOMAHAWK',
        ];

        yield 'without name' => [
            [null, 'MSI', 'MS-7B89'],
            null,
        ];

        yield 'empty name' => [
            ['', 'MSI', 'MS-7B89'],
            null,
        ];
    }

    /**
     * @param array{0: string|null, 1: string|null, 2: string|null} $input
     */
    #[DataProvider('provideModel')]
    public function testParseModel(array $input, ?string $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseModel');

        self::assertSame($expected, $method->invoke(null, ...$input));
    }

    public static function provideMountLines(): \Generator
    {
        yield 'common mounts' => [
            <<<'TXT'
                /dev/sda1 / ext4 rw,relatime,errors=remount-ro 0 0
                proc /proc proc rw,nosuid,nodev,noexec,relatime 0 0
                /dev/sdb1 /mnt/my\040disk ext4 rw,relatime 0 0
                TXT,
            [
                ['device' => '/dev/sda1', 'mount' => '/', 'fstype' => 'ext4', 'options' => ['rw', 'relatime', 'errors=remount-ro']],
                ['device' => 'proc', 'mount' => '/proc', 'fstype' => 'proc', 'options' => ['rw', 'nosuid', 'nodev', 'noexec', 'relatime']],
                ['device' => '/dev/sdb1', 'mount' => '/mnt/my disk', 'fstype' => 'ext4', 'options' => ['rw', 'relatime']],
            ],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<int, array{device: string, mount: string, fstype: string, options: string[]}>|null $expected
     */
    #[DataProvider('provideMountLines')]
    public function testParseMountLines(string $contents, ?array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseMountLines');

        self::assertSame($expected, $method->invoke(null, $contents));
    }

    public static function providePartitions(): \Generator
    {
        yield 'sata and nvme partitions' => [
            <<<'TXT'
                major minor  #blocks  name

                   8        0  976762584 sda
                   8        1     524288 sda1
                   8        2  976237568 sda2
                 259        0  500107862 nvme0n1
                 259        1     524288 nvme0n1p1
                TXT,
            [
                'sda' => [
                    new Partition(536870912.0, 'sda1'),
                    new Partition(999667269632.0, 'sda2'),
                ],
                'nvme0n1' => [
                    new Partition(536870912.0, 'nvme0n1p1'),
                ],
            ],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<string, Partition[]> $expected
     */
    #[DataProvider('providePartitions')]
    public function testParsePartitions(string $contents, array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parsePartitions');

        self::assertEquals($expected, $method->invoke(null, $contents));
    }

    public static function provideNetworkState(): \Generator
    {
        yield ['up', false, null, 'up'];
        yield ['down', false, null, 'down'];
        yield ['unknown', true, '1', 'up'];
        yield ['unknown', true, '0', 'down'];
        yield ['unknown', true, '', 'down'];
        yield ['unknown', false, null, null];
        yield [null, true, '1', 'up'];
    }

    #[DataProvider('provideNetworkState')]
    public function testParseNetworkState(?string $operstate, bool $carrierExists, ?string $carrier, ?string $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseNetworkState');

        self::assertSame($expected, $method->invoke(null, $operstate, $carrierExists, $carrier));
    }

    public static function provideNetworkType(): \Generator
    {
        yield 'loopback takes precedence' => ['772', 'PCI:1234', 'ethernet', 'e1000e', false, false, 'Loopback'];
        yield 'tunnel' => ['65534', '', null, null, false, false, 'Tunnel'];
        yield 'ipv6 in ipv4' => ['776', '', null, null, false, false, 'IPv6 in IPv4'];
        yield 'pci device type and driver' => ['1', 'PCI:1234', 'ethernet', 'e1000e', false, false, 'Ethernet (PCI) (e1000e)'];
        yield 'usb device type' => ['1', 'USB:abcd', 'wlan', null, false, false, 'Wlan (USB)'];
        yield 'pci device with driver' => ['1', 'PCI:1234', null, 'e1000e', false, false, 'Ethernet (PCI) (e1000e)'];
        yield 'pci device without driver' => ['1', 'PCI:1234', null, null, false, false, 'Ethernet (PCI)'];
        yield 'virtio' => ['1', 'VIRTIO:1234', null, null, false, false, 'VirtIO'];
        yield 'xen' => ['1', 'XEN:VIF', null, null, false, false, 'Xen (VIF)'];
        yield 'xen backend' => ['1', 'XEN-BACKEND:VIF', null, null, false, false, 'Xen Backend (VIF)'];
        yield 'bridge' => ['1', '', null, null, true, false, 'Bridge'];
        yield 'bond' => ['1', '', null, null, false, true, 'Bond'];
        yield 'unknown' => ['1', '', null, null, false, false, null];
    }

    #[DataProvider('provideNetworkType')]
    public function testParseNetworkType(
        ?string $typeCode,
        string $typeContents,
        ?string $deviceType,
        ?string $driver,
        bool $hasBridge,
        bool $hasBonding,
        ?string $expected,
    ): void {
        $method = new \ReflectionMethod(Linux::class, 'parseNetworkType');

        self::assertSame($expected, $method->invoke(null, $typeCode, $typeContents, $deviceType, $driver, $hasBridge, $hasBonding));
    }

    public static function provideProcess(): \Generator
    {
        $status = "Name:\tnginx\nState:\tS (sleeping)\nPid:\t1234\nThreads:\t4\nVmPeak:\t  263452 kB\nVmSize:\t  263248 kB\nUid:\t1000\t1000\t1000\t1000\nGid:\t1000\t1000\t1000\t1000\n";
        $io = "rchar: 12345\nwchar: 67890\nread_bytes: 4096\nwrite_bytes: 8192\n";

        yield 'full' => [
            $status,
            "nginx\0-g\0daemon off;\0",
            $io,
            [
                'name' => 'nginx',
                'pid' => 1234,
                'uid' => 1000,
                'cmdline' => 'nginx -g daemon off; ',
                'threads' => 4,
                'state' => 'S (sleeping)',
                'vmSize' => 269565952.0,
                'vmPeak' => 269774848.0,
                'readBytes' => 4096.0,
                'writeBytes' => 8192.0,
            ],
        ];

        yield 'without memory, cmdline and io' => [
            "Name:\tnginx\nState:\tS (sleeping)\nPid:\t1234\nThreads:\t4\nUid:\t1000\t1000\t1000\t1000\n",
            null,
            null,
            [
                'name' => 'nginx',
                'pid' => 1234,
                'uid' => 1000,
                'cmdline' => null,
                'threads' => 4,
                'state' => 'S (sleeping)',
                'vmSize' => null,
                'vmPeak' => null,
                'readBytes' => null,
                'writeBytes' => null,
            ],
        ];

        yield 'missing required fields' => [
            "Name:\tnginx\nPid:\t1234\n",
            null,
            null,
            null,
        ];

        yield 'empty status' => [
            '',
            null,
            null,
            null,
        ];
    }

    /**
     * @param array<string, mixed>|null $expected
     */
    #[DataProvider('provideProcess')]
    public function testParseProcess(string $statusContents, ?string $cmdlineContents, ?string $ioContents, ?array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseProcess');

        self::assertSame($expected, $method->invoke(null, $statusContents, $cmdlineContents, $ioContents));
    }

    public static function provideContainer(): \Generator
    {
        yield 'openvz' => [null, false, false, false, null, true, false, 'OpenVZ'];
        yield 'openvz has priority' => ['docker', true, true, true, '4.15.0-microsoft', true, true, 'OpenVZ'];
        yield 'wsl by osrelease' => [null, false, false, false, '5.15.153.1-microsoft-standard-WSL2', false, false, 'WSL'];
        yield 'wsl by manager' => ['wsl', false, false, false, null, false, false, 'WSL'];
        yield 'systemd-nspawn' => ['systemd-nspawn', false, false, false, null, false, false, 'systemd-nspawn'];
        yield 'lxc' => ['lxc', false, false, false, null, false, false, 'LXC'];
        yield 'lxc-libvirt' => ['lxc-libvirt', false, false, false, null, false, false, 'LXC'];
        yield 'docker manager' => ['docker', false, false, false, null, false, false, 'Docker'];
        yield 'podman manager' => ['podman', false, false, false, null, false, false, 'Podman'];
        yield 'rkt' => ['rkt', false, false, false, null, false, false, 'RKT'];
        yield 'pouch' => ['pouch', false, false, false, null, false, false, 'Pouch'];
        yield 'proot' => ['proot', false, false, false, null, false, false, 'PRoot'];
        yield 'unknown manager' => ['containerd', false, false, false, null, false, false, 'Container (other)'];
        yield 'manager is case insensitive' => ['Docker', false, false, false, null, false, false, 'Docker'];
        yield 'oci with podman env' => ['oci', true, false, false, null, false, false, 'Podman'];
        yield 'oci without files' => ['oci', false, false, false, null, false, false, 'Container (other)'];
        yield 'kubernetes' => [null, false, false, true, null, false, false, 'Kubernetes'];
        yield 'kubernetes beats docker' => [null, false, true, true, null, false, false, 'Kubernetes'];
        yield 'podman env' => [null, true, false, false, null, false, false, 'Podman'];
        yield 'docker env' => [null, false, true, false, null, false, false, 'Docker'];
        yield 'lxcfs' => [null, false, false, false, null, false, true, 'LXC'];
        yield 'none' => [null, false, false, false, null, false, false, null];
    }

    #[DataProvider('provideContainer')]
    public function testDetectContainer(
        ?string $containerManager,
        bool $hasPodmanEnv,
        bool $hasDockerEnv,
        bool $hasKubernetes,
        ?string $osRelease,
        bool $hasOpenVz,
        bool $hasLxcFs,
        ?string $expected,
    ): void {
        $method = new \ReflectionMethod(Linux::class, 'detectContainer');

        self::assertSame($expected, $method->invoke(null, $containerManager, $hasPodmanEnv, $hasDockerEnv, $hasKubernetes, $osRelease, $hasOpenVz, $hasLxcFs));
    }

    public static function provideVm(): \Generator
    {
        yield 'kvm' => ['KVM', null, null, null, null, null, false, [], 'Qemu/KVM'];
        yield 'openstack' => ['OpenStack Nova', null, null, null, null, null, false, [], 'OpenStack'];
        yield 'kubevirt' => ['KubeVirt', null, null, null, null, null, false, [], 'KubeVirt'];
        yield 'alibaba cloud' => [null, 'Alibaba Cloud', null, null, null, null, false, [], 'Alibaba Cloud'];
        yield 'amazon ec2' => ['r6i.large', 'Amazon EC2', null, null, null, null, false, [], 'Amazon EC2'];
        yield 'amazon ec2 bare metal' => ['r6i.metal', 'Amazon EC2', null, null, null, null, false, [], null];
        yield 'qemu' => ['QEMU', null, null, null, null, null, false, [], 'Qemu/KVM'];
        yield 'vmware' => [null, 'VMware, Inc.', null, null, null, null, false, [], 'VMWare'];
        yield 'vmw prefix' => [null, 'VMW', null, null, null, null, false, [], 'VMWare'];
        yield 'virtualbox innotek' => [null, null, null, 'innotek GmbH', null, null, false, [], 'VirtualBox'];
        yield 'virtualbox oracle corporation' => [null, 'Oracle Corporation', null, null, null, null, false, [], 'VirtualBox'];
        yield 'oracle cloud' => [null, 'OracleCloud', null, null, null, null, false, [], 'Oracle Cloud'];
        yield 'xen dmi' => [null, null, null, 'Xen', null, null, false, [], 'Xen'];
        yield 'bochs' => ['Bochs', null, null, null, null, null, false, [], 'Bochs'];
        yield 'parallels' => [null, null, null, 'Parallels Software International Inc.', null, null, false, [], 'Parallels'];
        yield 'bhyve' => ['BHYVE', null, null, null, null, null, false, [], 'bhyve'];
        yield 'hyper-v product version' => [null, null, null, null, 'Hyper-V', null, false, [], 'Hyper-V'];
        yield 'microsoft' => [null, 'Microsoft Corporation', null, null, null, null, false, [], 'Hyper-V'];
        yield 'apple virtualization' => ['Apple Virtualization', null, null, null, null, null, false, [], 'Apple Virtualization'];
        yield 'google compute engine' => ['Google Compute Engine', null, null, null, null, null, false, [], 'Google Compute Engine'];
        yield 'digitalocean' => [null, 'DigitalOcean', null, null, null, null, false, [], 'DigitalOcean'];
        yield 'veertu' => [null, null, null, 'Veertu', null, null, false, [], 'Veertu'];
        yield 'xen hypervisor type' => [null, null, null, null, null, 'xen', false, [], 'Xen'];
        yield 'xen directory' => [null, null, null, null, null, null, true, [], 'Xen'];
        yield 'virtualbox module' => [null, null, null, null, null, null, false, ['vboxguest'], 'VirtualBox'];
        yield 'vmware module' => [null, null, null, null, null, null, false, ['vmwgfx'], 'VMWare'];
        yield 'xen module' => [null, null, null, null, null, null, false, ['xen_gntdev'], 'Xen'];
        yield 'hyper-v module' => [null, null, null, null, null, null, false, ['hv_vmbus'], 'Hyper-V'];
        yield 'qemu/kvm module' => [null, null, null, null, null, null, false, ['virtio_pci'], 'Qemu/KVM'];
        yield 'dmi has priority over modules' => ['QEMU', null, null, null, null, null, false, ['vboxguest'], 'Qemu/KVM'];
        yield 'none' => [null, null, null, null, null, null, false, [], null];
    }

    /**
     * @param string[] $modules
     */
    #[DataProvider('provideVm')]
    public function testDetectVm(
        ?string $productName,
        ?string $sysVendor,
        ?string $boardVendor,
        ?string $biosVendor,
        ?string $productVersion,
        ?string $hypervisorType,
        bool $hasXenDir,
        array $modules,
        ?string $expected,
    ): void {
        $method = new \ReflectionMethod(Linux::class, 'detectVm');

        self::assertSame($expected, $method->invoke(null, $productName, $sysVendor, $boardVendor, $biosVendor, $productVersion, $hypervisorType, $hasXenDir, $modules));
    }

    public static function provideOsName(): \Generator
    {
        yield 'release file wins' => [
            ['CentOS Linux release 7.9', ['PRETTY_NAME' => 'Some Linux 1.0'], ['DISTRIB_DESCRIPTION' => 'Ubuntu 24.04.1 LTS'], ['openSUSE Leap 15.5'], '12.5'],
            'CentOS Linux release 7.9',
        ];

        yield 'os-release pretty name' => [
            [null, ['ID' => 'custom', 'PRETTY_NAME' => 'Custom Linux 1.0'], ['DISTRIB_DESCRIPTION' => 'Ubuntu 24.04.1 LTS'], ['openSUSE Leap 15.5'], '12.5'],
            'Custom Linux 1.0',
        ];

        yield 'os-release without pretty name falls back to lsb' => [
            [null, ['ID' => 'custom'], ['DISTRIB_DESCRIPTION' => 'Ubuntu 24.04.1 LTS'], ['openSUSE Leap 15.5'], '12.5'],
            'Ubuntu 24.04.1 LTS',
        ];

        yield 'lsb release' => [
            [null, [], ['DISTRIB_DESCRIPTION' => 'Ubuntu 24.04.1 LTS'], ['openSUSE Leap 15.5'], '12.5'],
            'Ubuntu 24.04.1 LTS',
        ];

        yield 'lsb release without description' => [
            [null, [], ['DISTRIB_ID' => 'Ubuntu'], ['openSUSE Leap 15.5'], '12.5'],
            'openSUSE Leap 15.5',
        ];

        yield 'suse release' => [
            [null, [], null, ['openSUSE Leap 15.5'], '12.5'],
            'openSUSE Leap 15.5',
        ];

        yield 'debian via os-release' => [
            [null, ['ID' => 'debian', 'PRETTY_NAME' => 'Debian GNU/Linux 12 (bookworm)'], null, null, '12.5'],
            'Debian GNU/Linux 12 (bookworm)',
        ];

        yield 'debian version' => [
            [null, [], null, null, '12.5'],
            'Debian 12.5',
        ];

        yield 'fallback' => [
            [null, [], null, null, null],
            \PHP_OS,
        ];
    }

    /**
     * @param array{0: string|null, 1: array<string, string>, 2: array<string, mixed>|null, 3: string[]|null, 4: string|null} $input
     */
    #[DataProvider('provideOsName')]
    public function testParseOsName(array $input, string $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseOsName');

        self::assertSame($expected, $method->invoke(null, ...$input));
    }

    public static function provideOsRelease(): \Generator
    {
        yield 'typical file' => [
            <<<'TXT'
                NAME="Astra Linux"
                ID=astra
                ID_LIKE=debian
                PRETTY_NAME="Astra Linux (Orel)"
                VERSION_ID="1.7"
                ANSI_COLOR="1;31"
                # a comment
                EMPTY=
                INVALID LINE
                TXT,
            [
                'NAME' => 'Astra Linux',
                'ID' => 'astra',
                'ID_LIKE' => 'debian',
                'PRETTY_NAME' => 'Astra Linux (Orel)',
                'VERSION_ID' => '1.7',
                'ANSI_COLOR' => '1;31',
                'EMPTY' => '',
            ],
        ];

        yield 'empty' => ['', []];
    }

    /**
     * @param array<string, string> $expected
     */
    #[DataProvider('provideOsRelease')]
    public function testParseOsRelease(string $contents, array $expected): void
    {
        $method = new \ReflectionMethod(Linux::class, 'parseOsRelease');

        self::assertSame($expected, $method->invoke(null, $contents));
    }
}
