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

		if ($a == 2)
			setTemp(floatval(TEMP_DNEVNA));
		else if ($a == 3)
			setTemp(floatval(TEMP_NOCNA));
		else if ($a == 4)
			setTemp(intval(TEMP_ODRZAVANJE));

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

				// Čekanje 0.3 sec
				time_nanosleep(0, 300000000);
			}

		else if ($temp > $arg)
			while ($temp > $arg) {
				decrement();

				$temp = floatval($temp) - 0.5;

				// Čekanje 0.3 sec
				time_nanosleep(0, 300000000);
			}
	}

?>
