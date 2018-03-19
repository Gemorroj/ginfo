return Get-WmiObject -Class Win32_DiskDrive Caption, DeviceID, Size, Index | ConvertTo-Json -Compress
