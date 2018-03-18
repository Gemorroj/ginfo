param([String]$tmpFile="/linfi-OperatingSystem.csv")
Get-WmiObject -Class Win32_OperatingSystem Caption, Version, BuildNumber, CSName, TotalVisibleMemorySize, FreePhysicalMemory, LastBootUpTime | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
