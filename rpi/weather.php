<?php

	// Argument koji funkcija uzima može biti "force".
	// U tom slucaju se vreme ažurira čak i kada je prošlo manje od 5 minuta od poslednje provere
	function azurirajVreme($a) {

		// WUnderground API URL za trenutne uslove i trodnevnu prognozu
		$apiURL = "http://api.wunderground.com/api/".WU_API_KEY."/conditions/forecast/lang:".WU_LANG."/q/".WU_COUNTRY."/".WU_CITY.".xml";

		// Pošto WU vraća opis na ćirilici, a želim da budem perfekcionista i održim
		// celu aplikaciju na latinici, čekalo me je žestoko (i delimično nepotrebno) kucanje
		$cyr  = array('а','б','в','г','д','ђ','е','ж','з','и','ј','к','л','љ',
				'м','н','њ','о','п','р','с','т','ћ','у','ф','х','ц','ч','џ','ш',
				'А','Б','В','Г','Д','Ђ','Е','Ж','З','И','Ј','К','Л','Љ',
				'М','Н','Њ','О','П','Р','С','Т','Ћ','У','Ф','Х','Ц','Ч','Џ','Ш');

		$lat = array('a','b','v','g','d','đ','e','ž','z','i','j','k','l','lj',
				'm','n','nj','o','p','r','s','t','ć','u','f','h','c','č','dž','š',
				'A','B','V','G','D','Đ','E','Ž','Z','I','J','K','L','Lj',
				'M','N','Nj','O','P','R','S','T','Ć','U','F','H','C','Č','Dž','Š');

		$updated =0;

		// Kako ne bi došlo do prevelikog broja API zahteva,
		// vreme se ažurira jednom u 5 minuta
		if (time() - intval(PiroData::$data['vremenska_prognoza']['vreme_azuriranja']) >= 300 || strcmp($a, "force") == 0) {
			// Maksimalno čekanje rezultata 3 sec
			$wait = stream_context_create(array(
				'http' => array(
					'timeout' => 3
					)
				)
			);
			
			$xml = simplexml_load_string(file_get_contents($apiURL, 0, $wait));

			$updated = 2;

			// Provera da li je vreme ispravno učitano
			if (strcmp($xml->current_observation->temp_c, "") != 0) {

				// Cuvanje trenutnog vremena
				PiroData::$data['vremenska_prognoza']['vreme_azuriranja'] = time();

				// Cuvanje grada za koji se prognoza pikazuje,
				// kao i njegove geografske širine i dužine
				if (strcmp(PiroData::$data['lokacija']['grad'],
					str_replace($cyr, $lat, $xml->current_observation->display_location->city))) {

						PiroData::$data['lokacija']['grad'] =
							str_replace($cyr, $lat, $xml->current_observation->display_location->city);

						PiroData::$data['lokacija']['geo_sirina'] =
							$xml->current_observation->display_location->latitude;

						PiroData::$data['lokacija']['geo_duzina'] =
							$xml->current_observation->display_location->longitude;

				}

				// Cuvanje trenutne temperature
				PiroData::$data['vremenska_prognoza']['trenutna_temperatura'] =
					intval($xml->current_observation->temp_c);

				// Cuvanje stringa sa opisom uslova
				$temp = str_replace($cyr, $lat, $xml->current_observation->weather);
				PiroData::$data['vremenska_prognoza']['trenutna_stanje'] =
					str_replace("Malo", "Mestimično", $temp);
				PiroData::$data['vremenska_prognoza']['trenutna_stanje'] =
					str_replace("oluja sa grmljavinom", "oluja", PiroData::$data['vremenska_prognoza']['trenutna_stanje']);

				// Cuvanje ikone uslova
				PiroData::$data['vremenska_prognoza']['trenutna_ikona'] =
					getSunlightStatus().$xml->current_observation->icon;

				// Cuvanje maksimalne dnevne temperature
				PiroData::$data['vremenska_prognoza']['dnevna_max_temp'] =
					intval($xml->forecast->simpleforecast->forecastdays->forecastday[0]->high->celsius);

				// Cuvanje minimalne dnevne temperature
				PiroData::$data['vremenska_prognoza']['dnevna_min_temp'] =
					intval($xml->forecast->simpleforecast->forecastdays->forecastday[0]->low->celsius);

				// Cuvanje ikone dnevnih uslova
				PiroData::$data['vremenska_prognoza']['dnevna_ikona'] =
					getSunlightStatus().$xml->forecast->simpleforecast->forecastdays->forecastday[0]->icon;

				// Cuvanje dnevne verovatnoce padavina
				PiroData::$data['vremenska_prognoza']['padavine'] =
					$xml->forecast->simpleforecast->forecastdays->forecastday[0]->pop;

				// Cuvanje trenutne vidljivosti
				if (strcmp($xml->current_observation->visibility_km, "N/A")) {
					$vidljivost = floatval($xml->current_observation->visibility_km);

					if (floor($vidljivost) == $vidljivost)
						PiroData::$data['vremenska_prognoza']['vidljivost'] = floor($vidljivost)." km";
					elseif ($vidljivost >= 0.1)
						PiroData::$data['vremenska_prognoza']['vidljivost'] = ($vidljivost * 1000)." m";
					else
						PiroData::$data['vremenska_prognoza']['vidljivost'] = "< 100m";
				}

				// Cuvanje subjektivne temperature
				PiroData::$data['vremenska_prognoza']['subjektivni_osecaj'] =
					$xml->current_observation->feelslike_c;

				// Cuvanje naziva dana
				PiroData::$data['vremenska_prognoza']['dan'] =
					str_replace($cyr, $lat, $xml->forecast->txt_forecast->forecastdays->forecastday[0]->title);

				// Cuvanje opisa dnevnih vremenskih uslova
				PiroData::$data['vremenska_prognoza']['dnevna_stanje'] =
					str_replace($cyr, $lat, $xml->forecast->simpleforecast->forecastdays->forecastday[0]->conditions);
				PiroData::$data['vremenska_prognoza']['dnevna_stanje'] =
					str_replace("oluja sa grmljavinom", "oluja", PiroData::$data['vremenska_prognoza']['dnevna_stanje']);

				// Cuvanje vrednosti UV indeksa
				PiroData::$data['vremenska_prognoza']['uv_indeks'] =
					$xml->current_observation->UV;

				upis();
				$updated = 3;

			}

		}

		return $updated;
	}

	function getSunlightStatus() {
		// Provera da li se trenutno vreme nalazi izmedju vremena izlaska i zalaska sunca.
		// Koristi UNIX timestamp
		$trenutnoVreme = microtime(true);
		$zalazak = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, PiroData::$data['lokacija']['geo_sirina'], PiroData::$data['lokacija']['geo_duzina'], 90, 1);
		$izlazak = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, PiroData::$data['lokacija']['geo_sirina'], PiroData::$data['lokacija']['geo_duzina'], 90, 1);

		if ($trenutnoVreme >= $izlazak && $trenutnoVreme <= $zalazak)
			return "";

		return "nt_";
	}

	function getWTemp() {
		return PiroData::$data['vremenska_prognoza']['trenutna_temperatura'];
	}

	function getDesc() {
		return PiroData::$data['vremenska_prognoza']['trenutna_stanje'];
	}

	function getIcon() {
		return PiroData::$data['vremenska_prognoza']['trenutna_ikona'];
	}

	function getMaxTemp() {
		return PiroData::$data['vremenska_prognoza']['dnevna_max_temp'];
	}

	function getMinTemp() {
		return PiroData::$data['vremenska_prognoza']['dnevna_min_temp'];
	}

	function getIconDaily() {
		return PiroData::$data['vremenska_prognoza']['dnevna_ikona'];
	}

	function getPadavine() {
		return intval(PiroData::$data['vremenska_prognoza']['padavine']);
	}

	function getVisibility() {
    return PiroData::$data['vremenska_prognoza']['vidljivost'];
	}

	function getSubTemp() {
		return intval(PiroData::$data['vremenska_prognoza']['subjektivni_osecaj']);
	}

	function getNazivDana() {
		return PiroData::$data['vremenska_prognoza']['dan'];
	}

	function getDescDaily() {
		return PiroData::$data['vremenska_prognoza']['dnevna_stanje'];
	}

	function getUV() {
		$uv = 0;

		if (PiroData::$data['vremenska_prognoza']['uv_indeks'] >= 11)
			$uv = 4;
		else if (PiroData::$data['vremenska_prognoza']['uv_indeks'] >= 8)
			$uv = 3;
		else if (PiroData::$data['vremenska_prognoza']['uv_indeks'] >= 6)
			$uv = 2;
		else if (PiroData::$data['vremenska_prognoza']['uv_indeks'] >= 3)
			$uv = 1;

		return $uv;
	}

	function getCityName() {
		return PiroData::$data['lokacija']['grad'];
	}

?>
