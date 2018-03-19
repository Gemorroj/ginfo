return Get-WmiObject -Class Win32_PerfFormattedData_PerfOS_Processor Name, Caption, PercentProcessorTime | Where-Object {$_.Name -ne '_Total'} | ConvertTo-Json -Compress
