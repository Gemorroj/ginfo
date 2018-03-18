param([String]$tmpFile="/linfi-NetworkAdapter.csv")
Get-WmiObject -Class Win32_NetworkAdapter Name, AdapterType, NetConnectionStatus, GUID, PhysicalAdapter | Where-Object PhysicalAdapter -eq True | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
