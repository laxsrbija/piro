<?php

	// 0 - Glavni LED paneli, 1 - Desni LED paneli
	// 2 - Levi LED paneli

	// Vraća stanje uređaja
	function getRelayStatus($a) {
		return intval($GLOBALS['uredjaji'][intval($a)][1]);
	}

	function toggleRelay($a) {
		if ($a != 2) { // TODO Kako relej za leve panele još uvek nije dostupan
					   // koristi se dati kod da preskoči njegove kontrole
			if (getRelayStatus($a) == 1) {
				$GLOBALS['uredjaji'][intval($a)][1] = 0;
				// TODO - Python kod za gašenje uredjaja
			}
			else {
				$GLOBALS['uredjaji'][intval($a)][1] = 1;
				// TODO - Python kod za paljenje uredjaja
			}
		}
		else {
			if (getRelayStatus($a) == 1) $GLOBALS['uredjaji'][intval($a)][1] = 0;
			else $GLOBALS['uredjaji'][intval($a)][1] = 1;
		}

		upis();
	}
	
	function getPCStatus() {
		return intval($GLOBALS['uredjaji'][3][1]);
	}
	
	function togglePC() {
		if (getPCStatus() == 1) 
			$GLOBALS['uredjaji'][3][1] = 0;
		else 			
			$GLOBALS['uredjaji'][3][1] = 1;
		
		// TODO Python kod za računar
		upis();
	}
			
?>
