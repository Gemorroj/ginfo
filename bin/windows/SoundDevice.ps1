param([String]$tmpFile="/linfi-SoundDevice.csv")
Get-WmiObject -Class Win32_SoundDevice Manufacturer, Caption | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
