<?php
	require ("piro-config.php");
	require ("manipulator.php");
	require ("thermal-control.php");
	require ("relay-control.php");
	require ("weather.php");
	require ("shell-commands.php");

	// piro-querry.php?f=ASD&arg=99

	// Preuzimanje parametra i argumenta iz URL adrese
	$q = $_REQUEST["f"];
	$arg = $_REQUEST["arg"];

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
		case "toggleThermal":
			toggleThermal();
			break;
		case "getRelayStatus":
			echo getRelayStatus($arg);
			break;
		case "toggleRelay":
			toggleRelay($arg);
			break;
		case "azurirajVreme":
			echo azurirajVreme($arg);
			break;
		case "getWTemp":
			echo getWTemp();
			break;
		case "getDesc":
			echo getDesc();
			break;
		case "getIcon":
			echo getIcon();
			break;
		case "getMaxTemp":
			echo getMaxTemp();
			break;
		case "getMinTemp":
			echo getMinTemp();
			break;
		case "getIconDaily":
			echo getIconDaily();
			break;
		case "getPadavine":
			echo getPadavine();
			break;
		case "getVisibility":
			echo getVisibility();
			break;
		case "getSubTemp":
			echo getSubTemp();
			break;
		case "getPCStatus":
			echo getPCStatus();
			break;
		case "togglePC":
			togglePC();
			break;
		case "getShellTemp":
			echo getShellTemp();
			break;
		case "getUptime":
			echo getUptime();
			break;
		case "getLoadAvg":
			echo getLoadAvg();
			break;
	}
?>
