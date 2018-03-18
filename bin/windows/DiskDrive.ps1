param([String]$tmpFile="/linfi-DiskDrive.csv")
Get-WmiObject -Class Win32_DiskDrive Caption, DeviceID, Size, Index | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
