$tmp = New-TemporaryFile
$tmpFile = $tmp.FullName

Get-WmiObject -Class Win32_Volume Automount, BootVolume, IndexingEnabled, Compressed, Label, DriveType, FileSystem, Capacity, FreeSpace, Name | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation

return $tmpFile
