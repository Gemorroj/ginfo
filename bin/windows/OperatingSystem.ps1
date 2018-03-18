param([String]$tmpFile="/linfi-ComputerSystem.csv")
Get-WmiObject -Class Win32_ComputerSystem TotalPhysicalMemory, FreePhysicalMemory | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
