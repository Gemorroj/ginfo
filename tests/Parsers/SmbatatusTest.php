<?php

namespace Ginfo\Tests\Parsers;

use Ginfo\Parsers\Smbstatus;
use PHPUnit\Framework\TestCase;

class SmbatatusTest extends TestCase
{
    public function fileStrings(): array
    {
        return [
            ['10196        1000       DENY_NONE  0x100081    RDONLY     NONE             /home/gemorroj/Общедоступные   .   Fri Mar 30 15:48:44 2018', [
                'pid' => 10196,
                'uid' => 1000,
                'denyMode' => 'DENY_NONE',
                'access' => '0x100081',
                'rw' => 'RDONLY',
                'oplock' => 'NONE',
                'sharePath' => '/home/gemorroj/Общедоступные',
                'name' => '.',
                'time' => new \DateTime('Fri Mar 30 15:48:44 2018'),
            ]],
        ];
    }

    public function serviceStrings(): array
    {
        return [
            ['IPC$         10196   192.168.0.102  Fri Mar 30 15:48:34 2018', [
                'service' => 'IPC$',
                'pid' => 10196,
                'machine' => '192.168.0.102',
                'connectedAt' => new \DateTime('Fri Mar 30 15:48:34 2018'),
                'encryption' => null,
                'signing' => null,
            ]],
            ['encrypted    25597   10.10.11.1    Wed Dec  9 01:40:20 PM 2015 CET  AES-128-CCM  AES-128-CMAC', [
                'service' => 'encrypted',
                'pid' => 25597,
                'machine' => '10.10.11.1',
                'connectedAt' => new \DateTime('Wed Dec  9 01:40:20 PM 2015 CET'),
                'encryption' => 'AES-128-CCM',
                'signing' => 'AES-128-CMAC',
            ]], // samba 4.4
            ['clear        25597   10.10.11.1    Wed Dec  9 01:40:17 PM 2015 CET  -            -', [
                'service' => 'clear',
                'pid' => 25597,
                'machine' => '10.10.11.1',
                'connectedAt' => new \DateTime('Wed Dec  9 01:40:17 PM 2015 CET'),
                'encryption' => null,
                'signing' => null,
            ]], // samba 4.4
        ];
    }

    public function connectionStrings(): array
    {
        return [
            ['30042   user          grp           client-name   (10.0.0.1)', [
                'pid' => 30042,
                'user' => 'user',
                'group' => 'grp',
                'host' => 'client-name',
                'ip' => '10.0.0.1',
                'protocolVersion' => null,
                'encryption' => null,
                'signing' => null,
            ]], // samba 3
            ['25540   user         grp       10.0.0.1 (ipv4:10.0.0.1:52269)   NT1               -                    -', [
                'pid' => 25540,
                'user' => 'user',
                'group' => 'grp',
                'host' => '10.0.0.1',
                'ip' => 'ipv4:10.0.0.1:52269',
                'protocolVersion' => 'NT1',
                'encryption' => null,
                'signing' => null,
            ]], // samba 4.4
            ['10196     nobody        nogroup       192.168.0.102 (ipv4:192.168.0.102:4002) Unknown (0x0311)', [
                'pid' => 10196,
                'user' => 'nobody',
                'group' => 'nogroup',
                'host' => '192.168.0.102',
                'ip' => 'ipv4:192.168.0.102:4002',
                'protocolVersion' => 'Unknown (0x0311)',
                'encryption' => null,
                'signing' => null,
            ]], // samba 4
            ['25597   slow         men          10.10.11.1 (ipv4:10.10.11.1:51241)        SMB3_02           partial(AES-128-CCM) partial(AES-128-CMAC)', [
                'pid' => 25597,
                'user' => 'slow',
                'group' => 'men',
                'host' => '10.10.11.1',
                'ip' => 'ipv4:10.10.11.1:51241',
                'protocolVersion' => 'SMB3_02',
                'encryption' => 'partial(AES-128-CCM)',
                'signing' => 'partial(AES-128-CMAC)',
            ]], // samba 4.4
        ];
    }

    /**
     * @dataProvider connectionStrings
     */
    public function testParseConnection(string $data, array $expected): void
    {
        $method = new \ReflectionMethod(Smbstatus::class, 'parseConnection');
        $method->setAccessible(true);

        $actual = $method->invoke(null, $data);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider serviceStrings
     */
    public function testParseService(string $data, array $expected): void
    {
        $method = new \ReflectionMethod(Smbstatus::class, 'parseService');
        $method->setAccessible(true);

        $actual = $method->invoke(null, $data);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider fileStrings
     */
    public function testParseFile(string $data, array $expected): void
    {
        $method = new \ReflectionMethod(Smbstatus::class, 'parseFile');
        $method->setAccessible(true);

        $actual = $method->invoke(null, $data);

        $this->assertEquals($expected, $actual);
    }
}
