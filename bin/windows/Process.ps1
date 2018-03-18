param([String]$tmpFile="/linfi-Process.csv")
Get-WmiObject -Class Win32_Process ThreadCount | Export-Csv -Path $tmpFile -Delimiter "," -Encoding "utf8" -NoTypeInformation
