<?php

	// WeatherUnderground API Key
	define("WU_API_KEY", "WeatherUnderground API Key");
	
	// Ime zemlje i naziv grada za koji se prikazuje vremenska prognoza
	define("WU_CITY", "Nis");
	define("WU_COUNTRY", "Serbia");
	
	// Jezik vremenske prognoze
	define("WU_LANG", "SR");

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
