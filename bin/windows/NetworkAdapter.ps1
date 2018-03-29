return Get-WmiObject -Class Win32_NetworkAdapter Name, AdapterType, NetConnectionStatus, GUID, PhysicalAdapter, Speed | Where-Object {$_.PhysicalAdapter -eq 'True'} | ConvertTo-Json -Compress
