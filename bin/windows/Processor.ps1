return Get-WmiObject -Class Win32_Processor LoadPercentage, Caption, Name, SystemName, Manufacturer, CurrentClockSpeed, LoadPercentage, NumberOfCores, Architecture | ConvertTo-Json -Compress
