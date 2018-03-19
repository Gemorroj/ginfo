$tmp = New-TemporaryFile
$tmpFile = $tmp.FullName

Get-WmiObject -Class Win32_Processor LoadPercentage, Caption, Name, Manufacturer, CurrentClockSpeed, LoadPercentage, NumberOfCores, Architecture | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation

return $tmpFile
