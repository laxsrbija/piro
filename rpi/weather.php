<?php

	function azururajVreme() {
		// WUnderground API key
		$key = "API";
		$apiURL = "http://api.wunderground.com/api/$key/conditions/lang:SR/q/Serbia/Nis.xml";

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
		
		// Kako ne bi došlo da prekoračenja upotrebe API zahteva,
		// vreme se ažurira jednom u 15 minuta
		if (time() - $GLOBALS['uredjaji'][7][1] >= 900) {
			$xml = simplexml_load_file($apiURL);
			
			// Cuvanje trenutnog vremena
			$GLOBALS['uredjaji'][7][1] = time();

			// Cuvanje trenutne temperature
			$GLOBALS['uredjaji'][8][1] = $xml->current_observation->temp_c;
			
			// Cuvanje stringa sa opisom uslova
			$GLOBALS['uredjaji'][9][1] = str_replace($cyr, $lat, $xml->current_observation->weather);

			// Cuvanje ikone uslova
			$GLOBALS['uredjaji'][10][1] = $xml->current_observation->icon;

			upis();
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
		
?>
