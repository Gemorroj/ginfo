return Get-WmiObject -Class Win32_Process ThreadCount | ConvertTo-Json -Compress
