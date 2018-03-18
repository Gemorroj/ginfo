param([String]$tmpFile="/linfi-cpu.csv")
Get-WmiObject -Class Win32_Processor LoadPercentage, Caption, Name, Manufacturer, CurrentClockSpeed, LoadPercentage, NumberOfCores, ThreadCount | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
