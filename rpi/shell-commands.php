<?php

	// Funkcija za vraćanje trenutne temperature procesora
	function getShellTemp() {
		exec("/opt/vc/bin/vcgencmd measure_temp 2>&1", $tmp);
		return substr($tmp[0], 5, 2);
	}

	// Vraća broj dana besprekidnog rada sistema
	function getUptime() {
		exec("uptime 2>&1", $tmp1);

		if (!strpos($tmp1[0], "day"))
			return 0;

		exec("uptime | awk '{print $3}' 2>&1", $tmp);
		return $tmp[0];
	}

	// Vraća prosečno opterećenje sistema u zadnjih 10 minuta
	function getLoadAvg() {
		exec("uptime | sed 's/.*load average: //' | awk -F\, '{print $2}' 2>&1", $tmp);
        return intval(str_replace(".", "", $tmp[0]));
	}

?>
