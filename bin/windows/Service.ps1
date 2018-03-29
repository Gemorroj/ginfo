return Get-WmiObject -Class Win32_Service Name, DisplayName, State, Started | ConvertTo-Json -Compress
