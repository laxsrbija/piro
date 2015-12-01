<?php
	
	// Funkcija za vraćanje trenutne temperature procesora
	function getShellTemp() {
		exec("/opt/vc/bin/vcgencmd measure_temp 2>&1", $tmp);
		return $tmp[0];
	}
	
	// Vraćanje broja dana besprekidnog rada sistema	
	function getUptime() {
		exec("uptime 2>&1", $tmp);
		return $tmp[0];
	}
	
	// Vraća prosečno opterećenje sistema
	function getLoadAvg() {
		$opt = exec("cat /proc/loadavg | awk ' {print $1, $2, $3} ' 2>&1");
        $optTemp = array("prosecno opterecenje:");
        return str_replace($optTemp, "", $opt);
	}
	
?>
