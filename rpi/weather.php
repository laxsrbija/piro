<?php

	// Argument koji funkcija uzima može biti "force".
	// U tom slucaju se vreme ažurira čak i kada je prošlo manje od 5 minuta od poslednje provere
	function azurirajVreme($a) {

		// WUnderground API URL za trenutne uslove i trodnevnu prognozu
		$apiURL = "http://api.wunderground.com/api/".WU_API_KEY."/conditions/forecast/lang:SR/q/Serbia/Nis.xml";

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
		if (time() - intval($GLOBALS['uredjaji'][7][1]) >= 300 || strcmp($a, "force") == 0) {
			$xml = simplexml_load_string(file_get_contents($apiURL));
			$xml2 = simplexml_load_string(file_get_contents($apiURLDaily));

			// Provera da li je vreme ispravno učitano
			if (strcmp($xml->current_observation->temp_c, "") != 0) {

				// Cuvanje trenutnog vremena
				$GLOBALS['uredjaji'][7][1] = time();

				// Cuvanje trenutne temperature
				$GLOBALS['uredjaji'][8][1] =  $xml->current_observation->temp_c;

				// Cuvanje stringa sa opisom uslova
				$temp = str_replace($cyr, $lat, $xml->current_observation->weather);
				$GLOBALS['uredjaji'][9][1] =  str_replace("Malo", "Mestimično", $temp);

				// Cuvanje ikone uslova
				$GLOBALS['uredjaji'][10][1] = getSunlightStatus().$xml->current_observation->icon;

				// Cuvanje maksimalne dnevne temperature
				$GLOBALS['uredjaji'][11][1] = $xml->forecast->simpleforecast->forecastdays->forecastday[0]->high->celsius;

				// Cuvanje minimalne dnevne temperature
				$GLOBALS['uredjaji'][12][1] = $xml->forecast->simpleforecast->forecastdays->forecastday[0]->low->celsius;

				// Cuvanje ikone dnevnih uslova
				$GLOBALS['uredjaji'][13][1] = getSunlightStatus().$xml->forecast->simpleforecast->forecastdays->forecastday[0]->icon;

				// Cuvanje dnevne verovatnoce padavina
				$GLOBALS['uredjaji'][14][1] = $xml->forecast->simpleforecast->forecastdays->forecastday[0]->pop;

				// Cuvanje trenutne vidljivosti
				if (strcmp($xml->current_observation->visibility_km, "N/A")) {
					$vidljivost = floatval($xml->current_observation->visibility_km);
					
					if (floor($vidljivost) == $vidljivost)
						$GLOBALS['uredjaji'][15][1] = floor($vidljivost)." km";
					elseif ($vidljivost >= 0.1)
						$GLOBALS['uredjaji'][15][1] = ($vidljivost * 1000)." m";
					else 
						$GLOBALS['uredjaji'][15][1] = "< 100m";
				}

				// Cuvanje subjektivne temperature
				$GLOBALS['uredjaji'][16][1] = $xml->current_observation->feelslike_c;

				// Cuvanje naziva dana
				$GLOBALS['uredjaji'][17][1] =
					str_replace($cyr, $lat, $xml->forecast->txt_forecast->forecastdays->forecastday[0]->title);

				// Cuvanje opisa dnevnih vremenskih uslova
				$GLOBALS['uredjaji'][18][1] =
					str_replace($cyr, $lat, $xml->forecast->simpleforecast->forecastdays->forecastday[0]->conditions);

				// Cuvanje vrednosti UV indeksa
				$GLOBALS['uredjaji'][19][1] = $xml->current_observation->UV;

				upis();

			}

			return "OK";
		}

		return "GREŠKA: ".((time()-$GLOBALS['uredjaji'][7][1]) / 60);
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
		return $GLOBALS['uredjaji'][8][1];
	}

	function getDesc() {
		return $GLOBALS['uredjaji'][9][1];
	}

	function getIcon() {
		return $GLOBALS['uredjaji'][10][1];
	}

	function getMaxTemp() {
		return $GLOBALS['uredjaji'][11][1];
	}

	function getMinTemp() {
		return $GLOBALS['uredjaji'][12][1];
	}

	function getIconDaily() {
		return $GLOBALS['uredjaji'][13][1];
	}

	function getPadavine() {
		return $GLOBALS['uredjaji'][14][1];
	}

	function getVisibility() {
		return $GLOBALS['uredjaji'][15][1];
	}

	function getSubTemp() {
		return $GLOBALS['uredjaji'][16][1];
	}

	function getNazivDana() {
		return $GLOBALS['uredjaji'][17][1];
	}

	function getDescDaily() {
		return $GLOBALS['uredjaji'][18][1];
	}

	function getUV() {
		return $GLOBALS['uredjaji'][19][1];
	}

?>
