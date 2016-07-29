<?php
	ini_set('display_errors', 1);

	class PiroData {
		public static $data;
	}
	PiroData::$data = parse_ini_file("rpi/piro.ini", true) or die ("<h1>Greška: Ne postoji datoteka sa konfiguracijama!</h1>");

	require ("rpi/piro-config.php");
	require ("rpi/dbrw.php");
	require ("rpi/thermal-control.php");
	require ("rpi/relay-control.php");
	require ("rpi/weather.php");
	require ("rpi/shell-commands.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="title" content="PIRO | Kontrolni panel">
		<meta name="author" content="Lazar Stanojević">
		<meta name="robots" content="noindex, nofollow">
		<title>PIRO | Kontrolni panel</title>
		<link rel="stylesheet" type="text/css" href="css/piro-desktop.css">
		<script type="text/javascript" src="js/piro.js"></script>
		<script type="text/javascript" src="js/bridge.js"></script>
		<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
	</head>
	<body>
		<div id="nav">
			<img src="img/logo.png" id="logo-nav">
			<a href="#" id="podesavanja-nav" title="Podešavanja"><img src="img/podesavanja.png"></a>
			<a href="https://www.wunderground.com/?apiref=bd0f471813b6ae6d" id="wu-nav" title="Weather Underground" target="_blank"><img src="img/wundergroundLogo_4c_rev.png"></a>
		</div>
		<div id="kontejner"><br>
			<div class="razmak"></div>
			<div>
				<span class="prognoza">
					<img title="UV indeks" src="img/uv-<?php echo getUV() ?>.png" id="prognoza-uv">
					<img id="prognoza-ikona" src="img/weather/svg/<?php echo getIcon() ?>.svg">
					<span id="prognoza-vrednost"><?php echo getWTemp(); ?>°</span>
					<span id="prognoza-opis"><?php echo getDesc(); ?></span>
					<a href="http://www.wunderground.com/weather-forecast/zmw:00000.1.13388?apiref=bd0f471813b6ae6d" target="_blank" id="prognoza-grad"><?php echo getCityName() ?></a>
					<a href="javascript:ucitajVreme('force')" id="prognoza-osvezi" title="Osveži"><img src="img/weather-refresh.png" id="osvezi-str"></a>
					<span class="prognoza-ic-kontejner">
						<span class="prognoza-ic" id="prognoza-ic-subjektivniosecaj" title="Subjektivni osećaj"><?php echo getSubTemp() ?>°</span>
						<span class="prognoza-ic" id="prognoza-ic-vidljivost" title="Vidljivost"><?php echo getVisibility() ?></span>
						<span class="prognoza-ic" id="prognoza-ic-padavine" title="Mogućnost padavina"><?php echo getPadavine() ?>%</span>
					</span>
					<hr>
					<span id="prognoza-dan"><?php echo getNazivDana() ?></span>
					<img id="prognoza-ikona-dnevna" src="img/weather/svg/<?php echo getIconDaily() ?>.svg">
					<span id="prognoza-dnevna-vrednost"><?php echo getMaxTemp() ?>° / <?php echo getMinTemp() ?>°</span>
					<span id="prognoza-dnevna-opis"><?php echo getDescDaily() ?></span>
				</span>
			</div>
			<div>
				<span class="racunar" id="racunar-div">
					<img id="racunar-kuler" src="img/fan<?php echo (getPCStatus() ? "-rot.gif" : ".png") ?>">
					<a href="javascript:racunarToggle()" id="racunar-taster" class="racunar-taster <?php echo (getPCStatus() ? "ukljuceno" : "iskljuceno") ?>"><?php echo (getPCStatus() ? "ISKLJUČI" : "UKLJUČI") ?></a>
					<span id="racunar-status" class="dugme-labela">Računar je <?php echo (getPCStatus() ? "u" : "is") ?>ključen.</span>
				</span>
				<br>
				<span class="rasveta">
					<img class="rasveta-centralno" src="img/rasveta-centralno.png">
					<a href="javascript:rasvetaToggle(0)" id="rasv-0-taster" class="rasv-0-taster <?php echo (getRelayStatus(0) ? "u" : "is") ?>kljuceno"><?php echo (getRelayStatus(0) ? "ISKLJUČI" : "UKLJUČI") ?></a>
					<span class="rasv-cent-status dugme-labela">Centralni LED paneli</span>
					<img class="rasveta-desno" src="img/rasveta-desno.png">
					<a href="javascript:rasvetaToggle(1)" id="rasv-1-taster" class="rasv-1-taster <?php echo (getRelayStatus(1) ? "u" : "is") ?>kljuceno"><?php echo (getRelayStatus(1) ? "ISKLJUČI" : "UKLJUČI") ?></a>
					<span class="rasv-desn-status dugme-labela">Desna LED rasveta</span>
					<img class="rasveta-levo" src="img/rasveta-levo.png">
					<a href="javascript:rasvetaToggle(2)" id="rasv-2-taster" class="rasv-2-taster <?php echo (getRelayStatus(2) ? "u" : "is") ?>kljuceno"><?php echo (getRelayStatus(2) ? "ISKLJUČI" : "UKLJUČI") ?></a>
					<span class="rasv-lev-status dugme-labela">Leva LED rasveta</span>
				</span>
			</div>
			<div>
				<span class="grejanje">
					<span id="grejanje-vrednost"><?php
						echo (thermalStatus() > 0 ? getTemp()."°" :
							((thermalStatus() == 0) ? "Isklj." : "<img class=\"grejanje-nedostupno\" src=\"img/grejanje-nedostupno.png\">"));
					?></span>
					<span id="grejanje-tasteri">
						<a href="javascript:setThermalMode(0)"><img id="grejanje-1-taster" src="img/grejanje-1.png"></a>
						<a><img id="grejanje-2-taster" src="img/grejanje-2.png"></a>
						<a href="javascript:setThermalMode(2)"><img id="grejanje-3-taster" src="img/grejanje-3.png"></a>
						<a href="javascript:setThermalMode(3)"><img id="grejanje-4-taster" src="img/grejanje-4.png"></a>
						<a href="javascript:setThermalMode(4)"><img id="grejanje-5-taster" src="img/grejanje-5.png"></a>
					</span>
					<script>checkMode(<?php echo thermalStatus() ?>, <?php echo getMode() + 1 ?>)</script>
					<a class="grejanje-pojacaj" href="javascript:promeniTemperaturu(1)"><img src="img/grejanje-inkrementacija.png"></a>
					<a class="grejanje-snaga" href="javascript:grejanjeToggle()"><img src="img/grejanje-pwr.png"></a>
					<a class="grejanje-smanji" href="javascript:promeniTemperaturu(0)"><img src="img/grejanje-dekrementacija.png"></a>
				</span>
				<br>
				<span class="status">
					<img id="status-icon" src="img/ok.png">
					<span id="status-status">Svi servisi su dostupni</span>
					<span id="status-uptime" class="status-tekst"><?php echo getUptime() ? "Operativno vreme: ".getUptime()." dan" :  "Server je pokrenut danas." ?></span>
					<span id="status-temperatura" class="status-tekst">Temperatura servera: <?php echo getShellTemp() ?>°C</span>
					<span id="status-opterecenje" class="status-tekst">Prosečno opterećenje servera: <?php echo getLoadAvg() ?>%</span>
					<script>uptimeFormatter(<?php echo getUptime() ?>)</script>
				</span>
			</div>
		</div>
		<a href="https://github.com/laxsrbija/piro" target="_blank" id="footer">Copyright © <?php echo date("Y") ?> Lazar Stanojević. Sva prava zadržana.</a>
	</body>
</html>
