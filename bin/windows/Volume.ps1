param([String]$tmpFile="/linfi-Volume.csv")
Get-WmiObject -Class Win32_Volume Automount, BootVolume, IndexingEnabled, Compressed, Label, DriveType, FileSystem, Capacity, FreeSpace, Caption | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
