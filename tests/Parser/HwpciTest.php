<?php

namespace Ginfo\Tests\Parser;

use Ginfo\Parser\Hwpci;
use PHPUnit\Framework\TestCase;

final class HwpciTest extends TestCase
{
    private const IDS_FILE = __DIR__.'/../Fixtures/pci.ids';

    public function testResolveIds(): void
    {
        $method = new \ReflectionMethod(Hwpci::class, 'resolveIds');

        $result = $method->invoke(
            null,
            self::IDS_FILE,
            ['8086' => true, '10de' => true],
            ['8086-1234' => 1, '10de-abcd' => 1],
        );

        self::assertSame([
            '8086-1234' => ['Intel Corporation', 'Some Device'],
            '10de-abcd' => ['NVIDIA Corporation', 'GeForce'],
        ], $result);
    }

    public function testResolveIdsSkipsUnknownVendors(): void
    {
        $method = new \ReflectionMethod(Hwpci::class, 'resolveIds');

        self::assertSame([], $method->invoke(null, self::IDS_FILE, ['ffff' => true], ['ffff-0000' => 1]));
    }

    public function testResolveIdsOnMissingFile(): void
    {
        $method = new \ReflectionMethod(Hwpci::class, 'resolveIds');

        self::assertSame([], $method->invoke(null, __DIR__.'/../Fixtures/does-not-exist.ids', ['8086' => true], ['8086-1234' => 1]));
    }

    public function testExtractDevs(): void
    {
        $method = new \ReflectionMethod(Hwpci::class, 'extractDevs');

        $deviceIds = [
            'vendors' => ['8086' => true],
            'devices' => ['8086-1234' => 2, '8086-9999' => 1],
            'speeds' => ['8086-1234' => 5000, '8086-9999' => null],
        ];

        self::assertSame([
            ['vendor' => 'Intel Corporation', 'device' => 'Some Device', 'count' => 2, 'speed' => 5000],
        ], $method->invoke(null, $deviceIds, self::IDS_FILE));
    }

    public function testMakeResultSortsDevices(): void
    {
        $method = new \ReflectionMethod(Hwpci::class, 'makeResult');

        $devices = [
            ['vendor' => 'NVIDIA Corporation', 'device' => 'GeForce', 'count' => 1, 'speed' => null],
            ['vendor' => 'Intel Corporation', 'device' => 'Some Device', 'count' => 2, 'speed' => 5000],
        ];

        self::assertSame([
            ['vendor' => 'Intel Corporation', 'device' => 'Some Device', 'count' => 2, 'speed' => 5000],
            ['vendor' => 'NVIDIA Corporation', 'device' => 'GeForce', 'count' => 1, 'speed' => null],
        ], $method->invoke(null, $devices));
    }

    public function testLocateActualPath(): void
    {
        $method = new \ReflectionMethod(Hwpci::class, 'locateActualPath');

        self::assertSame(self::IDS_FILE, $method->invoke(null, ['/nonexistent/'.\uniqid(), self::IDS_FILE]));
        self::assertNull($method->invoke(null, ['/nonexistent/'.\uniqid()]));
    }

    public function testUnknownMode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Hwpci())->run('unknown');
    }
}
