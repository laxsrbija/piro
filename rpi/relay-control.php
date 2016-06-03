<?php

	// 0 - Glavni LED paneli
	// 1 - Desni LED paneli
	// 2 - Levi LED paneli

	// Vraća stanje uređaja
	function getRelayStatus($a) {
		switch ($a) {
			case 0:
				return PiroData::$data['rasveta']['led_centar'];
			case 1:
				return PiroData::$data['rasveta']['led_desno'];
			case 2:
				return PiroData::$data['rasveta']['led_levo'];
		}
	}

	function toggleRelay($a) {

		if (getRelayStatus($a) == 1) {
			switch($a) {
				case 0:
					exec("gpio write ".GPIO_LED_GLAVNA." 1  2>&1");
					PiroData::$data['rasveta']['led_centar'] = 0;
					break;
				case 1:
					exec("gpio write ".GPIO_LED_DESNO." 1  2>&1");
					PiroData::$data['rasveta']['led_desno'] = 0;
					break;
				case 2:
					#exec("gpio write ".GPIO_LED_LEVO." 1  2>&1");
					PiroData::$data['rasveta']['led_levo'] = 0;
					break;
			}

			autoTemp();
		} else {
			switch($a) {
				case 0:
					exec("gpio write ".GPIO_LED_GLAVNA." 0  2>&1");
					PiroData::$data['rasveta']['led_centar'] = 1;
					break;
				case 1:
					exec("gpio write ".GPIO_LED_DESNO." 0  2>&1");
					PiroData::$data['rasveta']['led_desno'] = 1;
					break;
				case 2:
					#exec("gpio write ".GPIO_LED_LEVO." 0  2>&1");
					PiroData::$data['rasveta']['led_levo'] = 1;
					break;
			}
		}


		upis();
	}

	function getPCStatus() {
		return PiroData::$data['racunar']['racunar'];
	}

	function togglePC() {
		if (getPCStatus() == 1)
			PiroData::$data['racunar']['racunar'];
		else
			PiroData::$data['racunar']['racunar'];

		exec("gpio write ".GPIO_PC." 0 && sleep 0.2 && gpio write ".GPIO_PC." 1 2>&1");

		upis();
	}

?>
