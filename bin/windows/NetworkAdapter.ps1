return Get-WmiObject -Class Win32_NetworkAdapter Name, AdapterType, NetConnectionStatus, GUID, PhysicalAdapter | ConvertTo-Json -Compress
