param([String]$tmpFile="/linfi-PnPEntity.csv")
Get-WmiObject -Class Win32_PnPEntity DeviceID, Caption, Manufacturer | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
