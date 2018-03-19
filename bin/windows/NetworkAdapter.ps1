return Get-WmiObject -Class Win32_NetworkAdapter Name, AdapterType, NetConnectionStatus, GUID, PhysicalAdapter | Where-Object {$_.PhysicalAdapter -eq 'True'} | ConvertTo-Json -Compress
