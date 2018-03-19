return Get-WmiObject -Class Win32_OperatingSystem Caption, Version, BuildNumber, CSName, TotalVisibleMemorySize, FreePhysicalMemory, LastBootUpTime | ConvertTo-Json -Compress
