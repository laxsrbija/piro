<?php

	// 0 - Glavni LED paneli, 1 - Desni LED paneli
	// 2 - Levi LED paneli, 3 - PC

	// Vraća stanje uređaja
	function getRelayStatus($a) {
		return intval($GLOBALS['uredjaji'][intval($a)][1]);
	}

	function toggleRelay($a) {
		if ($a != 1) { // TODO Kako relej za desne panele još uvek nije dostupan
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
			
?>
