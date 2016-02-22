<?php

	// 0 - Glavni LED paneli
	// 1 - Desni LED paneli
	// 2 - Levi LED paneli

	// Vraća stanje uređaja
	function getRelayStatus($a) {
		return $GLOBALS['uredjaji'][$a][1];
	}

	function toggleRelay($a) {
		
		// TODO Kako relej za leve panele još uvek nije dostupan
		// koristi se dati kod da preskoči njegove kontrole
		if ($a != 2) { 
		
			if (getRelayStatus($a) == 1) {
				$GLOBALS['uredjaji'][$a][1] = 0;
				
				switch($a) {
					case 0:
						exec("gpio write ".GPIO_LED_GLAVNA." 1  2>&1");
						break;
					case 1:
						exec("gpio write ".GPIO_LED_DESNO." 1  2>&1");
						break;
				}
				
				autoTemp();
			}
			else {
				$GLOBALS['uredjaji'][$a][1] = 1;
				
				switch($a) {
					case 0:
						exec("gpio write ".GPIO_LED_GLAVNA." 0  2>&1");
						break;
					case 1:
						exec("gpio write ".GPIO_LED_DESNO." 0  2>&1");
						break;
				}
			}
		}
		else {
			if (getRelayStatus($a) == 1) 
				$GLOBALS['uredjaji'][$a][1] = 0;
			else 
				$GLOBALS['uredjaji'][$a][1] = 1;
		}

		upis();
	}

	function getPCStatus() {
		return $GLOBALS['uredjaji'][3][1];
	}

	function togglePC() {
		if (getPCStatus() == 1)
			$GLOBALS['uredjaji'][3][1] = 0;
		else
			$GLOBALS['uredjaji'][3][1] = 1;

		exec("gpio write ".GPIO_PC." 0 && sleep 0.2 && gpio write ".GPIO_PC." 1 2>&1");

		upis();
	}

?>
