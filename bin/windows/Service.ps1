return Get-WmiObject -Class Win32_Service Name, DisplayName, State | ConvertTo-Json -Compress
