param([String]$tmpFile="/linfi-PerfRawData_Tcpip_NetworkAdapter.csv")
Get-WmiObject -Class Win32_PerfRawData_Tcpip_NetworkAdapter Name, BytesReceivedPersec, PacketsReceivedErrors, PacketsReceivedPersec, BytesSentPersec, PacketsSentPersec | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
