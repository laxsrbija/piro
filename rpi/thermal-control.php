<?php

	// Vraća stanje grejnog tela
	function thermalStatus() {
		return intval($GLOBALS['uredjaji'][4][1]);
	}

	// Vraća trenutnu temperaturu
	function getTemp() {
		return floatval($GLOBALS['uredjaji'][5][1]);
	}

	// Vraća režim rada grejnog tela
	// 0 - Ručno, 1 - Dan, 2 - Noć, 3 - Zaštita od zamrzavanja
	function getMode() {
		return intval($GLOBALS['uredjaji'][6][1]);
	}

	// Postavljanja režima rada grejnog tela
	function setMode($a) {
		$GLOBALS['uredjaji'][6][1] = intval($a);
		upis();
	}

	// NOTE: Shodno mogućnostima hardvera, inkrementacija i dekrementacija
	// se vrše u intervalima od 0.5°C!

	function increment() {
		if (thermalStatus() == 1) {
			$GLOBALS['uredjaji'][5][1] = getTemp() + 0.5;
			// TODO - Python kod za aktuelnu inkrementaciju
			upis();
		}
	}

	function decrement() {
		if (thermalStatus() == 1) {
			$GLOBALS['uredjaji'][5][1] = getTemp() - 0.5;
			// TODO - Python kod za aktuelnu dekrementaciju
			upis();
		}
	}

	// Paljenje i gašenje grejnog tela
	function toggle() {
		if (thermalStatus() == 1) {
			$GLOBALS['uredjaji'][4][1] = 0;
			// TODO - Python kod za gašenje uredjaja
		}
		else {
			$GLOBALS['uredjaji'][4][1] = 1;
			// TODO - Python kod za paljenje uredjaja
		}

		upis();
	}	
			
?>
