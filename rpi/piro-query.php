<?php
	// ini_set('display_errors', 1);

	require ("piro-config.php");
	require ("manipulator.php");
	require ("thermal-control.php");
	require ("relay-control.php");
	require ("weather.php");
	require ("shell-commands.php");

	// piro-query.php?f=funkcija&arg=parametar

	// NOTE: Svaka izmena funkcija u posebnim
	// modulima mora biti primenjena i ovde!

	// Odabir funkcije na osnovu parametra iz URL-a
	switch ($_REQUEST["f"]) {
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
			setMode($_REQUEST["arg"]);
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
			echo getRelayStatus($_REQUEST["arg"]);
			break;
		case "toggleRelay":
			toggleRelay($_REQUEST["arg"]);
			break;
		case "azurirajVreme":
			echo azurirajVreme($_REQUEST["arg"]);
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
		case "getDescDaily":
			echo getDescDaily();
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
		case "getUV":
			echo getUV();
			break;
		case "getNazivDana":
			echo getNazivDana();
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
		case "autoTemp":
			autoTemp();
			break;
	}
	
?>