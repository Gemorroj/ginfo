return Get-WmiObject -Class Win32_PnPEntity DeviceID, Caption, Manufacturer | ConvertTo-Json -Compress
