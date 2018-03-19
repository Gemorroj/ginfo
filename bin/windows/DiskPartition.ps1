return Get-WmiObject -Class Win32_DiskPartition DiskIndex, Size, DeviceID, Type | ConvertTo-Json -Compress
