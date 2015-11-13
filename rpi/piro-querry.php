<?php
	require ("manipulator.php");
	require ("thermal-control.php");
	require ("relay-control.php");

	// piro-querry.php?f=ASD&arg=99

	// Preuzimanje parametra i argumenta iz URL adrese
	$q = $_REQUEST["f"];
	$arg = intval($_REQUEST["arg"]);

	// NOTE: Svaka izmena funkcija u posebnim 
	// modulima mora biti primenjena i ovde!

	// Odabir funkcije
	switch ($q) {
		case "thermalStatus":
			echo thermalStatus();
			break;
		case "getTemp":
			echo getTemp();
			break;
		case "getMode":
			echo getMode();
			break;
		case "setMode":
			setMode($arg);
			break;
		case "increment":
			increment();
			break;
		case "decrement":
			decrement();
			break;
		case "toggle":
			toggle();
			break;
		case "getRelayStatus":
			getRelayStatus($arg);
			break;
		case "toggleRelay":
			toggleRelay($arg);
			break;
		case "azurirajVreme":
			azurirajVreme($arg);
			break;
		case "getWTemp":
			getWTemp();
			break;
		case "getDesc":
			getDesc();
			break;
		case "getIcon":
			getIcon();
			break;
	}
?>
