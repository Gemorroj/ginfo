return Get-WmiObject -Class Win32_Volume Automount, BootVolume, IndexingEnabled, Compressed, Label, DriveType, FileSystem, Capacity, FreeSpace, Caption | ConvertTo-Json -Compress
