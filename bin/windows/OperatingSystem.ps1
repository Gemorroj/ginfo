$tmp = New-TemporaryFile
$tmpFile = $tmp.FullName

Get-WmiObject -Class Win32_OperatingSystem Caption, Version, BuildNumber, CSName, TotalVisibleMemorySize, FreePhysicalMemory, LastBootUpTime | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation

return $tmpFile
