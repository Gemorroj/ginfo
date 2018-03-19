$tmp = New-TemporaryFile
$tmpFile = $tmp.FullName

Get-WmiObject -Class Win32_DiskDrive Caption, DeviceID, Size, Index | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation

return $tmpFile
