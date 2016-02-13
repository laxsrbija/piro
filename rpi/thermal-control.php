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
	// 0 - Autonoman rad, 1 - Ručno, 2 - Dan,
	// 3 - Noć, 4 - Zaštita od zamrzavanja
	function getMode() {
		return intval($GLOBALS['uredjaji'][6][1]);
	}

	// Postavljanja režima rada grejnog tela
	function setMode($a) {
		
		switch($a) {
			case 1:
				autoTemp();
				break;
			case 2:
				setTemp(floatval(TEMP_DNEVNA));
				break;
			case 3:
				setTemp(floatval(TEMP_NOCNA));
				break;
			case 4:
				setTemp(intval(TEMP_ODRZAVANJE));
				break;
		}

		$GLOBALS['uredjaji'][6][1] = intval($a);
		upis();
	}

	// NOTE: Shodno mogućnostima hardvera, inkrementacija i dekrementacija
	// se vrše u intervalima od 0.5°C!

	function increment() {
		if (thermalStatus() == 1 && floatval(getTemp()) < 27) {
			$GLOBALS['uredjaji'][5][1] = getTemp() + 0.5;
			exec("gpio write ".GPIO_TERMO_INC." 0 && sleep 0.1 && gpio write ".GPIO_TERMO_INC." 1 2>&1");
			upis();
		}
	}

	function decrement() {
		if (thermalStatus() == 1 && floatval(getTemp()) > 7) {
			$GLOBALS['uredjaji'][5][1] = getTemp() - 0.5;
			exec("gpio write ".GPIO_TERMO_DEC." 0 && sleep 0.1 && gpio write ".GPIO_TERMO_DEC." 1 2>&1");
			upis();
		}
	}

	// Paljenje i gašenje grejnog tela
	function toggleThermal() {
		if (thermalStatus() == 1)
			$GLOBALS['uredjaji'][4][1] = 0;
		else
			$GLOBALS['uredjaji'][4][1] = 1;

		exec("gpio write ".GPIO_TERMO_PWR." 0 && sleep 0.1 && gpio write ".GPIO_TERMO_PWR." 1 2>&1");

		upis();
	}

	// Postavlja temperaturu na određenu vrednost
	function setTemp($arg) {
		$temp = getTemp();

		if ($temp == $arg || thermalStatus() == 0)
			return;

		else if ($temp < $arg)
			while ($temp < $arg) {
				increment();

				$temp = floatval($temp) + 0.5;

				// Čekanje 0.15 sec
				time_nanosleep(0, 150000000);
			}

		else if ($temp > $arg)
			while ($temp > $arg) {
				decrement();

				$temp = floatval($temp) - 0.5;

				// Čekanje 0.15 sec
				time_nanosleep(0, 150000000);
			}
	}
	
	// Funkcija za automatizaciju grejnog tela
	function autoTemp() {
		if (getMode() == 0 && getRelayStatus(0) == 0 && getRelayStatus(1) == 0 && getRelayStatus(2) == 0 && (date("G") >= 11 || date("G") <= 6))
			setTemp(floatval(TEMP_NOCNA));
		else if (getMode() == 0 && date("G") == 8)
			setTemp(floatval(TEMP_DNEVNA));
	}

?>
