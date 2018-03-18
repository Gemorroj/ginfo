param([String]$tmpFile="/linfi-DiskPartition.csv")
Get-WmiObject -Class Win32_DiskPartition DiskIndex, Size, DeviceID, Type | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
