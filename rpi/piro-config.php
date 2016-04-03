<?php

	// TODO: Omogućiti jednokratno podešavanje grada, 
	// na osnovu koga se kasnije vade podaci o koordinatama i
	// nazivu grada koji se koriste u drugim delovima sistema.

	// WeatherUnderground API Key
	define("WU_API_KEY", "WeatherUnderground API Key");

	// Konstante za dnevnu, noćnu i temperaturu održavanja
	define("TEMP_DNEVNA", "21.5");
	define("TEMP_NOCNA", "19.5");
	define("TEMP_ODRZAVANJE", "7");

	// GPIO pinovi za kontrolu rasvete
	define("GPIO_LED_GLAVNA", "2");
	define("GPIO_LED_DESNO", "0");
	#define("GPIO_LED_LEVO", ""); Nije u upotrebi

	// GPIO pinovi za kontrolu grejnog tela
	define("GPIO_TERMO_PWR", "5");
	define("GPIO_TERMO_INC", "1");
	define("GPIO_TERMO_DEC", "4");

	// GPIO pinovi za kontrolu računara
	define("GPIO_PC", "6");
	
?>
