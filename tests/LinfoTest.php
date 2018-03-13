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
namespace Linfo\Tests;

use Linfo\Linfo;
use Linfo\Meta\Errors;
use Linfo\OS\Windows;

class LinfoTest extends \PHPUnit_Framework_TestCase
{
    public function testTodo()
    {
        $linfo = new Linfo();
        $linfo->scan();
        $info = $linfo->getInfo();

        //$errors = Errors::show();
        //print_r($errors);
        //$this->assertEmpty($errors);

        $this->assertInternalType('array', $info);
        //self::markTestSkipped('not implemented');
    }


    /**
     * @return array
     */
    public function wmicListDataProvider()
    {
        return array(
            array('

Access=
Availability=
BlockSize=512
Bootable=TRUE
BootPartition=TRUE
Caption=Диск #0, раздел #0
ConfigManagerErrorCode=
ConfigManagerUserConfig=
CreationClassName=Win32_DiskPartition
Description=GPT: система
DeviceID=Disk #0, Partition #0
DiskIndex=0
ErrorCleared=
ErrorDescription=
ErrorMethodology=
HiddenSectors=
Index=0
InstallDate=
LastErrorCode=
Name=Диск #0, раздел #0
NumberOfBlocks=612289
PNPDeviceID=
PowerManagementCapabilities=
PowerManagementSupported=
PrimaryPartition=TRUE
Purpose=
RewritePartition=
Size=313491968
StartingOffset=2129408
Status=
StatusInfo=
SystemCreationClassName=Win32_ComputerSystem
SystemName=GEMORPC
Type=GPT: System


Access=
Availability=
BlockSize=512
Bootable=FALSE
BootPartition=FALSE
Caption=Диск #0, раздел #1
ConfigManagerErrorCode=
ConfigManagerUserConfig=
CreationClassName=Win32_DiskPartition
Description=GPT: основные данные
DeviceID=Disk #0, Partition #1
DiskIndex=0
ErrorCleared=
ErrorDescription=
ErrorMethodology=
HiddenSectors=
Index=1
InstallDate=
LastErrorCode=
Name=Диск #0, раздел #1
NumberOfBlocks=248528896
PNPDeviceID=
PowerManagementCapabilities=
PowerManagementSupported=
PrimaryPartition=TRUE
Purpose=
RewritePartition=
Size=127246794752
StartingOffset=315621376
Status=
StatusInfo=
SystemCreationClassName=Win32_ComputerSystem
SystemName=GEMORPC
Type=GPT: Basic Data


Access=
Availability=
BlockSize=512
Bootable=FALSE
BootPartition=FALSE
Caption=Диск #0, раздел #2
ConfigManagerErrorCode=
ConfigManagerUserConfig=
CreationClassName=Win32_DiskPartition
Description=GPT: основные данные
DeviceID=Disk #0, Partition #2
DiskIndex=0
ErrorCleared=
ErrorDescription=
ErrorMethodology=
HiddenSectors=
Index=2
InstallDate=
LastErrorCode=
Name=Диск #0, раздел #2
NumberOfBlocks=921600
PNPDeviceID=
PowerManagementCapabilities=
PowerManagementSupported=
PrimaryPartition=TRUE
Purpose=
RewritePartition=
Size=471859200
StartingOffset=127562416128
Status=
StatusInfo=
SystemCreationClassName=Win32_ComputerSystem
SystemName=GEMORPC
Type=GPT: Basic Data


Access=
Availability=
BlockSize=512
Bootable=FALSE
BootPartition=FALSE
Caption=Диск #1, раздел #0
ConfigManagerErrorCode=
ConfigManagerUserConfig=
CreationClassName=Win32_DiskPartition
Description=GPT: основные данные
DeviceID=Disk #1, Partition #0
DiskIndex=1
ErrorCleared=
ErrorDescription=
ErrorMethodology=
HiddenSectors=
Index=0
InstallDate=
LastErrorCode=
Name=Диск #1, раздел #0
NumberOfBlocks=236676214
PNPDeviceID=
PowerManagementCapabilities=
PowerManagementSupported=
PrimaryPartition=TRUE
Purpose=
RewritePartition=
Size=121178221568
StartingOffset=17408
Status=
StatusInfo=
SystemCreationClassName=Win32_ComputerSystem
SystemName=GEMORPC
Type=GPT: Basic Data


Access=
Availability=
BlockSize=512
Bootable=FALSE
BootPartition=FALSE
Caption=Диск #1, раздел #1
ConfigManagerErrorCode=
ConfigManagerUserConfig=
CreationClassName=Win32_DiskPartition
Description=GPT: основные данные
DeviceID=Disk #1, Partition #1
DiskIndex=1
ErrorCleared=
ErrorDescription=
ErrorMethodology=
HiddenSectors=
Index=1
InstallDate=
LastErrorCode=
Name=Диск #1, раздел #1
NumberOfBlocks=4131370481
PNPDeviceID=
PowerManagementCapabilities=
PowerManagementSupported=
PrimaryPartition=TRUE
Purpose=
RewritePartition=
Size=2115261686272
StartingOffset=121178238976
Status=
StatusInfo=
SystemCreationClassName=Win32_ComputerSystem
SystemName=GEMORPC
Type=GPT: Basic Data


Access=
Availability=
BlockSize=512
Bootable=FALSE
BootPartition=FALSE
Caption=Диск #1, раздел #2
ConfigManagerErrorCode=
ConfigManagerUserConfig=
CreationClassName=Win32_DiskPartition
Description=GPT: основные данные
DeviceID=Disk #1, Partition #2
DiskIndex=1
ErrorCleared=
ErrorDescription=
ErrorMethodology=
HiddenSectors=
Index=2
InstallDate=
LastErrorCode=
Name=Диск #1, раздел #2
NumberOfBlocks=1492486405
PNPDeviceID=
PowerManagementCapabilities=
PowerManagementSupported=
PrimaryPartition=TRUE
Purpose=
RewritePartition=
Size=764153039360
StartingOffset=2236439925248
Status=
StatusInfo=
SystemCreationClassName=Win32_ComputerSystem
SystemName=GEMORPC
Type=GPT: Basic Data


', array (
                0 =>
                    array (
                        'Access' => '',
                        'Availability' => '',
                        'BlockSize' => '512',
                        'Bootable' => true,
                        'BootPartition' => true,
                        'Caption' => 'Диск #0, раздел #0',
                        'ConfigManagerErrorCode' => '',
                        'ConfigManagerUserConfig' => '',
                        'CreationClassName' => 'Win32_DiskPartition',
                        'Description' => 'GPT: система',
                        'DeviceID' => 'Disk #0, Partition #0',
                        'DiskIndex' => '0',
                        'ErrorCleared' => '',
                        'ErrorDescription' => '',
                        'ErrorMethodology' => '',
                        'HiddenSectors' => '',
                        'Index' => '0',
                        'InstallDate' => '',
                        'LastErrorCode' => '',
                        'Name' => 'Диск #0, раздел #0',
                        'NumberOfBlocks' => '612289',
                        'PNPDeviceID' => '',
                        'PowerManagementCapabilities' => '',
                        'PowerManagementSupported' => '',
                        'PrimaryPartition' => true,
                        'Purpose' => '',
                        'RewritePartition' => '',
                        'Size' => '313491968',
                        'StartingOffset' => '2129408',
                        'Status' => '',
                        'StatusInfo' => '',
                        'SystemCreationClassName' => 'Win32_ComputerSystem',
                        'SystemName' => 'GEMORPC',
                        'Type' => 'GPT: System',
                    ),
                2 =>
                    array (
                        'Access' => '',
                        'Availability' => '',
                        'BlockSize' => '512',
                        'Bootable' => false,
                        'BootPartition' => false,
                        'Caption' => 'Диск #0, раздел #1',
                        'ConfigManagerErrorCode' => '',
                        'ConfigManagerUserConfig' => '',
                        'CreationClassName' => 'Win32_DiskPartition',
                        'Description' => 'GPT: основные данные',
                        'DeviceID' => 'Disk #0, Partition #1',
                        'DiskIndex' => '0',
                        'ErrorCleared' => '',
                        'ErrorDescription' => '',
                        'ErrorMethodology' => '',
                        'HiddenSectors' => '',
                        'Index' => '1',
                        'InstallDate' => '',
                        'LastErrorCode' => '',
                        'Name' => 'Диск #0, раздел #1',
                        'NumberOfBlocks' => '248528896',
                        'PNPDeviceID' => '',
                        'PowerManagementCapabilities' => '',
                        'PowerManagementSupported' => '',
                        'PrimaryPartition' => true,
                        'Purpose' => '',
                        'RewritePartition' => '',
                        'Size' => '127246794752',
                        'StartingOffset' => '315621376',
                        'Status' => '',
                        'StatusInfo' => '',
                        'SystemCreationClassName' => 'Win32_ComputerSystem',
                        'SystemName' => 'GEMORPC',
                        'Type' => 'GPT: Basic Data',
                    ),
                4 =>
                    array (
                        'Access' => '',
                        'Availability' => '',
                        'BlockSize' => '512',
                        'Bootable' => false,
                        'BootPartition' => false,
                        'Caption' => 'Диск #0, раздел #2',
                        'ConfigManagerErrorCode' => '',
                        'ConfigManagerUserConfig' => '',
                        'CreationClassName' => 'Win32_DiskPartition',
                        'Description' => 'GPT: основные данные',
                        'DeviceID' => 'Disk #0, Partition #2',
                        'DiskIndex' => '0',
                        'ErrorCleared' => '',
                        'ErrorDescription' => '',
                        'ErrorMethodology' => '',
                        'HiddenSectors' => '',
                        'Index' => '2',
                        'InstallDate' => '',
                        'LastErrorCode' => '',
                        'Name' => 'Диск #0, раздел #2',
                        'NumberOfBlocks' => '921600',
                        'PNPDeviceID' => '',
                        'PowerManagementCapabilities' => '',
                        'PowerManagementSupported' => '',
                        'PrimaryPartition' => true,
                        'Purpose' => '',
                        'RewritePartition' => '',
                        'Size' => '471859200',
                        'StartingOffset' => '127562416128',
                        'Status' => '',
                        'StatusInfo' => '',
                        'SystemCreationClassName' => 'Win32_ComputerSystem',
                        'SystemName' => 'GEMORPC',
                        'Type' => 'GPT: Basic Data',
                    ),
                6 =>
                    array (
                        'Access' => '',
                        'Availability' => '',
                        'BlockSize' => '512',
                        'Bootable' => false,
                        'BootPartition' => false,
                        'Caption' => 'Диск #1, раздел #0',
                        'ConfigManagerErrorCode' => '',
                        'ConfigManagerUserConfig' => '',
                        'CreationClassName' => 'Win32_DiskPartition',
                        'Description' => 'GPT: основные данные',
                        'DeviceID' => 'Disk #1, Partition #0',
                        'DiskIndex' => '1',
                        'ErrorCleared' => '',
                        'ErrorDescription' => '',
                        'ErrorMethodology' => '',
                        'HiddenSectors' => '',
                        'Index' => '0',
                        'InstallDate' => '',
                        'LastErrorCode' => '',
                        'Name' => 'Диск #1, раздел #0',
                        'NumberOfBlocks' => '236676214',
                        'PNPDeviceID' => '',
                        'PowerManagementCapabilities' => '',
                        'PowerManagementSupported' => '',
                        'PrimaryPartition' => true,
                        'Purpose' => '',
                        'RewritePartition' => '',
                        'Size' => '121178221568',
                        'StartingOffset' => '17408',
                        'Status' => '',
                        'StatusInfo' => '',
                        'SystemCreationClassName' => 'Win32_ComputerSystem',
                        'SystemName' => 'GEMORPC',
                        'Type' => 'GPT: Basic Data',
                    ),
                8 =>
                    array (
                        'Access' => '',
                        'Availability' => '',
                        'BlockSize' => '512',
                        'Bootable' => false,
                        'BootPartition' => false,
                        'Caption' => 'Диск #1, раздел #1',
                        'ConfigManagerErrorCode' => '',
                        'ConfigManagerUserConfig' => '',
                        'CreationClassName' => 'Win32_DiskPartition',
                        'Description' => 'GPT: основные данные',
                        'DeviceID' => 'Disk #1, Partition #1',
                        'DiskIndex' => '1',
                        'ErrorCleared' => '',
                        'ErrorDescription' => '',
                        'ErrorMethodology' => '',
                        'HiddenSectors' => '',
                        'Index' => '1',
                        'InstallDate' => '',
                        'LastErrorCode' => '',
                        'Name' => 'Диск #1, раздел #1',
                        'NumberOfBlocks' => '4131370481',
                        'PNPDeviceID' => '',
                        'PowerManagementCapabilities' => '',
                        'PowerManagementSupported' => '',
                        'PrimaryPartition' => true,
                        'Purpose' => '',
                        'RewritePartition' => '',
                        'Size' => '2115261686272',
                        'StartingOffset' => '121178238976',
                        'Status' => '',
                        'StatusInfo' => '',
                        'SystemCreationClassName' => 'Win32_ComputerSystem',
                        'SystemName' => 'GEMORPC',
                        'Type' => 'GPT: Basic Data',
                    ),
                10 =>
                    array (
                        'Access' => '',
                        'Availability' => '',
                        'BlockSize' => '512',
                        'Bootable' => false,
                        'BootPartition' => false,
                        'Caption' => 'Диск #1, раздел #2',
                        'ConfigManagerErrorCode' => '',
                        'ConfigManagerUserConfig' => '',
                        'CreationClassName' => 'Win32_DiskPartition',
                        'Description' => 'GPT: основные данные',
                        'DeviceID' => 'Disk #1, Partition #2',
                        'DiskIndex' => '1',
                        'ErrorCleared' => '',
                        'ErrorDescription' => '',
                        'ErrorMethodology' => '',
                        'HiddenSectors' => '',
                        'Index' => '2',
                        'InstallDate' => '',
                        'LastErrorCode' => '',
                        'Name' => 'Диск #1, раздел #2',
                        'NumberOfBlocks' => '1492486405',
                        'PNPDeviceID' => '',
                        'PowerManagementCapabilities' => '',
                        'PowerManagementSupported' => '',
                        'PrimaryPartition' => true,
                        'Purpose' => '',
                        'RewritePartition' => '',
                        'Size' => '764153039360',
                        'StartingOffset' => '2236439925248',
                        'Status' => '',
                        'StatusInfo' => '',
                        'SystemCreationClassName' => 'Win32_ComputerSystem',
                        'SystemName' => 'GEMORPC',
                        'Type' => 'GPT: Basic Data',
                    ),
            ))
        );
    }

    /**
     * @dataProvider wmicListDataProvider
     */
    public function testParseWmicListData($str, $expectedData)
    {
        $object = new Windows(array());
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod('parseWmicListData');
        $method->setAccessible(true);

        $parsedData = $method->invokeArgs($object, array($str));

        $this->assertEquals($expectedData, $parsedData);
    }
}
