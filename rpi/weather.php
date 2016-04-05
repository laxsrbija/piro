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

		// Kako ne bi došlo do prevelikog broja API zahteva,
		// vreme se ažurira jednom u 5 minuta
		if (time() - intval($GLOBALS['data']['vremenska_prognoza']['vreme_azuriranja']) >= 300 || strcmp($a, "force") == 0) {
			$xml = simplexml_load_string(file_get_contents($apiURL));
			$xml2 = simplexml_load_string(file_get_contents($apiURLDaily));

			// Provera da li je vreme ispravno učitano
			if (strcmp($xml->current_observation->temp_c, "") != 0) {

				// Cuvanje trenutnog vremena
				$GLOBALS['data']['vremenska_prognoza']['vreme_azuriranja'] = time();
				
				// Cuvanje grada za koji se prognoza pikazuje
				$GLOBALS['data']['vremenska_prognoza']['grad'] = 
					str_replace($cyr, $lat, $xml->current_observation->display_location->city);

				// Cuvanje trenutne temperature
				$GLOBALS['data']['vremenska_prognoza']['trenutna_temperatura'] =  
					$xml->current_observation->temp_c;

				// Cuvanje stringa sa opisom uslova
				$temp = str_replace($cyr, $lat, $xml->current_observation->weather);
				$GLOBALS['data']['vremenska_prognoza']['trenutna_stanje'] = 
					str_replace("Malo", "Mestimično", $temp);

				// Cuvanje ikone uslova
				$GLOBALS['data']['vremenska_prognoza']['trenutna_ikona'] = 
					getSunlightStatus().$xml->current_observation->icon;

				// Cuvanje maksimalne dnevne temperature
				$GLOBALS['data']['vremenska_prognoza']['dnevna_max_temp'] = 
					$xml->forecast->simpleforecast->forecastdays->forecastday[0]->high->celsius;

				// Cuvanje minimalne dnevne temperature
				$GLOBALS['data']['vremenska_prognoza']['dnevna_min_temp'] = 
					$xml->forecast->simpleforecast->forecastdays->forecastday[0]->low->celsius;

				// Cuvanje ikone dnevnih uslova
				$GLOBALS['data']['vremenska_prognoza']['dnevna_ikona'] = 
					getSunlightStatus().$xml->forecast->simpleforecast->forecastdays->forecastday[0]->icon;

				// Cuvanje dnevne verovatnoce padavina
				$GLOBALS['data']['vremenska_prognoza']['padavine'] = 
					$xml->forecast->simpleforecast->forecastdays->forecastday[0]->pop;

				// Cuvanje trenutne vidljivosti
				if (strcmp($xml->current_observation->visibility_km, "N/A")) {
					$vidljivost = floatval($xml->current_observation->visibility_km);
					
					if (floor($vidljivost) == $vidljivost)
						$GLOBALS['data']['vremenska_prognoza']['vidljivost'] = floor($vidljivost)." km";
					elseif ($vidljivost >= 0.1)
						$GLOBALS['data']['vremenska_prognoza']['vidljivost'] = ($vidljivost * 1000)." m";
					else 
						$GLOBALS['data']['vremenska_prognoza']['vidljivost'] = "< 100m";
				}

				// Cuvanje subjektivne temperature
				$GLOBALS['data']['vremenska_prognoza']['subjektivni_osecaj'] = 
					$xml->current_observation->feelslike_c;

				// Cuvanje naziva dana
				$GLOBALS['data']['vremenska_prognoza']['dan'] =
					str_replace($cyr, $lat, $xml->forecast->txt_forecast->forecastdays->forecastday[0]->title);

				// Cuvanje opisa dnevnih vremenskih uslova
				$GLOBALS['data']['vremenska_prognoza']['dnevna_stanje'] =
					str_replace($cyr, $lat, $xml->forecast->simpleforecast->forecastdays->forecastday[0]->conditions);

				// Cuvanje vrednosti UV indeksa
				$GLOBALS['data']['vremenska_prognoza']['uv_indeks'] = 
					$xml->current_observation->UV;

				upis();

			}

			return "OK";
		}

		return "GREŠKA: ".((time()-$GLOBALS['data']['vremenska_prognoza']['vreme_azuriranja']) / 60);
	}
	
	function getSunlightStatus() {
		// Provera da li se trenutno vreme nalazi izmedju vremena izlaska i zalaska sunca.
		// Koristi UNIX timestamp
		$trenutnoVreme = microtime(true);
		$zalazak = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,43.3246,21.903,90,1);
		$izlazak = date_sunrise(time(),SUNFUNCS_RET_TIMESTAMP,43.3246,21.903,90,1);
		
		if ($trenutnoVreme >= $izlazak && $trenutnoVreme <= $zalazak)
			return "";
		
		return "nt_";
	}

	function getWTemp() {
		return $GLOBALS['data']['vremenska_prognoza']['trenutna_temperatura'];
	}

	function getDesc() {
		return $GLOBALS['data']['vremenska_prognoza']['trenutna_stanje'];
	}

	function getIcon() {
		return $GLOBALS['data']['vremenska_prognoza']['trenutna_ikona'];
	}

	function getMaxTemp() {
		return $GLOBALS['data']['vremenska_prognoza']['dnevna_max_temp'];
	}

	function getMinTemp() {
		return $GLOBALS['data']['vremenska_prognoza']['dnevna_min_temp'];
	}

	function getIconDaily() {
		return $GLOBALS['data']['vremenska_prognoza']['dnevna_ikona'];
	}

	function getPadavine() {
		return $GLOBALS['data']['vremenska_prognoza']['padavine'];
	}

	function getVisibility() {
		return $GLOBALS['data']['vremenska_prognoza']['vidljivost'];
	}

	function getSubTemp() {
		return $GLOBALS['data']['vremenska_prognoza']['subjektivni_osecaj'];
	}

	function getNazivDana() {
		return $GLOBALS['data']['vremenska_prognoza']['dan'];
	}

	function getDescDaily() {
		return $GLOBALS['data']['vremenska_prognoza']['dnevna_stanje'];
	}

	function getUV() {
		return $GLOBALS['data']['vremenska_prognoza']['uv_indeks'];
	}
	
	function getCityName() {
		return $GLOBALS['data']['vremenska_prognoza']['grad'];
	}

?>
