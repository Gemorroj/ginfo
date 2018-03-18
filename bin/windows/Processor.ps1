param([String]$tmpFile="/linfi-Processor.csv")
Get-WmiObject -Class Win32_Processor LoadPercentage, Caption, Name, Manufacturer, CurrentClockSpeed, LoadPercentage, NumberOfCores, Architecture, ThreadCount | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
