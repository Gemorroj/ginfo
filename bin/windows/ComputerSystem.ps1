param([String]$tmpFile="/linfi-ComputerSystem.csv")
Get-WmiObject -Class Win32_ComputerSystem Manufacturer, Model | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
