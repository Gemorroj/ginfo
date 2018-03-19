return Get-WmiObject -Class Win32_ComputerSystem Manufacturer, Model | ConvertTo-Json -Compress
