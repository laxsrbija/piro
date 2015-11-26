<?php

	// Argument koji funkcija uzima može biti "force".
	// U tom slucaju se vreme ažurira čak i kada je prošlo manje od 15 minuta od poslednje provere
	function azurirajVreme($a) {
		// Otvaranje datoteke sa WU API ključem
		$keyfile = fopen("weather.key", "r") or die("<h1>Greška: Nije moguće otvoriti API ključ!</h1>");
		$key = fread($keyfile, filesize("weather.key"));
		fclose($keyfile);

		// WUnderground API URL za trenutne uslove
		$apiURL = "http://api.wunderground.com/api/".intval($key)."/conditions/lang:SR/q/Serbia/Nis.xml";

		// WUnderground API URL za prognozu
		$apiURLDaily = "http://api.wunderground.com/api/".intval($key)."/forecast/lang:SR/q/Serbia/Nis.xml";

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
		
		// Kako ne bi došlo do prekoračenja upotrebe API zahteva,
		// vreme se ažurira jednom u 15 minuta
		if (time() - $GLOBALS['uredjaji'][7][1] >= 900 || strcmp($a, "force") == 0) {
			$xml = simplexml_load_file($apiURL);
			$xml2 = simplexml_load_file($apiURLDaily);

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
				$GLOBALS['uredjaji'][10][1] = $xml->current_observation->icon;

				// Cuvanje maksimalne dnevne temperature
				$GLOBALS['uredjaji'][11][1] = $xml2->forecast->simpleforecast->forecastdays->forecastday[0]->high->celsius;

				// Cuvanje minimalne dnevne temperature
				$GLOBALS['uredjaji'][12][1] = $xml2->forecast->simpleforecast->forecastdays->forecastday[0]->low->celsius;

				// Cuvanje ikone dnevnih uslova
				$GLOBALS['uredjaji'][13][1] = $xml2->forecast->simpleforecast->forecastdays->forecastday[0]->icon;

				// Cuvanje dnevne verovatnoce padavina
				$GLOBALS['uredjaji'][14][1] = $xml2->forecast->simpleforecast->forecastdays->forecastday[0]->pop;

				upis();
			
			}
		}
	
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

?>
