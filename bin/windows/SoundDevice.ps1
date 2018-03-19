return Get-WmiObject -Class Win32_SoundDevice Manufacturer, Caption | ConvertTo-Json -Compress
