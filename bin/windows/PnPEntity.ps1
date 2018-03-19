$tmp = New-TemporaryFile
$tmpFile = $tmp.FullName

Get-WmiObject -Class Win32_PnPEntity DeviceID, Caption, Manufacturer | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation

return $tmpFile
