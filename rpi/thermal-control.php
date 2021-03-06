<?php

	// Vraća stanje grejnog tela
	// Ukoliko peć nije povezana, rezultat je -1
	function thermalStatus() {
		if (!TERM_DOSTUPNO && intval(PiroData::$data['grejanje']['status_peci']) != -1) {
			PiroData::$data['grejanje']['status_peci'] = -1;
			upis();
		}
		else if (TERM_DOSTUPNO && intval(PiroData::$data['grejanje']['status_peci']) == -1) {
			PiroData::$data['grejanje']['status_peci'] = 0;
			upis();
		}

		return PiroData::$data['grejanje']['status_peci'];
	}

	// Vraća trenutnu temperaturu
	function getTemp() {
		return PiroData::$data['grejanje']['temperatura_peci'];
	}

	// Vraća režim rada grejnog tela
	// 0 - Autonoman rad
	// 1 - Ručni režim
	// 2 - Dan,
	// 3 - Noć
	// 4 - Zaštita od zamrzavanja

	// Režim postavljen na "A" služi za pozive unutar
	// Androida, nakon čega vraća trenutne parametre

	function getMode() {
		return PiroData::$data['grejanje']['rezim_peci'];
	}

	// Postavljanja režima rada grejnog tela
	function setMode($a, $mode = "R") {

		if (thermalStatus() > 0) {
			PiroData::$data['grejanje']['rezim_peci'] = intval($a);

			switch($a) {
				case 0:
					autoTemp();
					break;
				case 2:
					setTemp(floatval(TEMP_DNEVNA));
					break;
				case 3:
					setTemp(floatval(TEMP_NOCNA));
					break;
				case 4:
					setTemp(floatval(TEMP_ODRZAVANJE));
					break;
			}
			upis();
		}

		if (strcmp($mode, "A") == 0)
			return getJSONThermal();
	}

	// NOTE: Shodno mogućnostima grejnog tela,
	// inkrementacija i dekrementacija se vrše u intervalima od 0.5°C!

	function increment($mode = "R") {
		if (thermalStatus() > 0 && floatval(getTemp()) < 27) {
			PiroData::$data['grejanje']['temperatura_peci'] = floatval(getTemp()) + 0.5;
			exec("gpio write ".GPIO_TERMO_INC." 0 && sleep 0.1 && gpio write ".GPIO_TERMO_INC." 1 2>&1");
			upis();
		}

		if (strcmp($mode, "A") == 0) {
			setMode(1);
			return getJSONThermal();
		}
	}

	function decrement($mode = "R") {
		if (thermalStatus() > 0 && floatval(getTemp()) > 7) {
			PiroData::$data['grejanje']['temperatura_peci'] = floatval(getTemp()) - 0.5;
			exec("gpio write ".GPIO_TERMO_DEC." 0 && sleep 0.1 && gpio write ".GPIO_TERMO_DEC." 1 2>&1");
			upis();
		}

		if (strcmp($mode, "A") == 0) {
			setMode(1);
			return getJSONThermal();
		}
	}

	// Paljenje i gašenje grejnog tela
	function toggleThermal($mode = "R") {
		if (thermalStatus() != -1) {
			if (thermalStatus() == 1)
				PiroData::$data['grejanje']['status_peci'] = 0;
			else
				PiroData::$data['grejanje']['status_peci'] = 1;

			exec("gpio write ".GPIO_TERMO_PWR." 0 && sleep 0.1 && gpio write ".GPIO_TERMO_PWR." 1 2>&1");
			upis();
		}

		if (strcmp($mode, "A") == 0)
			return getJSONThermal();
	}

	// Postavlja temperaturu na određenu vrednost
	function setTemp($arg) {
		$temp = getTemp();

		if ($temp == $arg || thermalStatus() < 1)
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
		if (thermalStatus() < 1)
			return;

		if (getMode() == 0 && getRelayStatus(0) == 0 && getRelayStatus(1) == 0 && getRelayStatus(2) == 0 && (date("G") == 23 || date("G") <= 6))
			setTemp(floatval(TEMP_NOCNA));
		else if (getMode() == 0 && getTemp() == TEMP_NOCNA)
			setTemp(floatval(TEMP_DNEVNA));
	}

?>
