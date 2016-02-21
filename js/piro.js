// Prikazivanje ikone dok se stranica u potpunosti ne učita
document.onreadystatechange = function () {
	var state = document.readyState
	if (state == "interactive") {
		document.getElementById("kontejner").style.visibility = "hidden";
	}
	else if (state == "complete") {
		setTimeout(function() {
			document.getElementById("interactive");
			document.getElementById("load").style.visibility = "hidden";
			document.getElementById("kontejner").style.visibility = "visible";
 		}, 1200);
	}
}

function racunarToggle() {
	piroQuery("togglePC", "-1", "-1");

	if (document.getElementById("racunar-status").innerHTML == "Računar je isključen.") {
		document.getElementById("racunar-taster").innerHTML = "ISKLJUČI";
		document.getElementById("racunar-taster").className = "racunar-taster ukljuceno";
		document.getElementById("racunar-kuler").src = "img/fan-rot.gif";
		document.getElementById("racunar-status").innerHTML = "Računar je uključen.";
	}
	else {
		document.getElementById("racunar-taster").innerHTML = "UKLJUČI";
		document.getElementById("racunar-taster").className = "racunar-taster iskljuceno";
		document.getElementById("racunar-kuler").src = "img/fan.png";
		document.getElementById("racunar-status").innerHTML = "Računar je isključen.";
	}
}

function rasvetaToggle(k) {
	piroQuery("toggleRelay", k, "-1");

	if (document.getElementById("rasv-" + k + "-taster").innerHTML == "UKLJUČI") {
		document.getElementById("rasv-" + k + "-taster").innerHTML = "ISKLJUČI";
		document.getElementById("rasv-" + k + "-taster").className = "rasv-" + k + "-taster ukljuceno";
	}
	else {
		document.getElementById("rasv-" + k + "-taster").innerHTML = "UKLJUČI";
		document.getElementById("rasv-" + k + "-taster").className = "rasv-" + k + "-taster iskljuceno";
	}

}

// Učitati status i  temperaturu peći
function ucitajTempStatus() {
	piroQuery("thermalStatus", "-1", function() {
			if (this.responseText == 0) {
				document.getElementById("grejanje-vrednost").innerHTML = "Isklj.";

				for (var i = 1; i <= 5; i++)
					document.getElementById("grejanje-" + i + "-taster").src = "img/grejanje-" + i + ".png";
			}
			else {
				piroQuery("getTemp", "-1", function() {
						document.getElementById("grejanje-vrednost").innerHTML = this.responseText + "°";
						getThermalMode();
					}
				);
			}
		}
	);
}

// Paljenje i gašenje grejnog tela
function grejanjeToggle() {
	piroQuery("toggleThermal", "-1", function() {
		ucitajTempStatus();
	});
}

// Učitavanje režima rada
function getThermalMode() {
	piroQuery("getMode", "-1", function() {
			for (var i = 1; i <= 5; i++)
				document.getElementById("grejanje-" + i + "-taster").src = "img/grejanje-" + i + ".png";

			document.getElementById("grejanje-" + (parseInt(this.responseText) + 1) + "-taster").src = "img/grejanje-" + (parseInt(this.responseText) + 1) + "-s.png";
		}
	);
}

// Postavljanje režima rada
function setThermalMode(arg) {
	if (document.getElementById("grejanje-vrednost").innerHTML != "Isklj.")
		piroQuery("setMode", arg, function() {
			getThermalMode();
			ucitajTempStatus();
		});
}

// Inkrementacija temperature
function povecajTemperaturu() {
	if (document.getElementById("grejanje-vrednost").innerHTML != "Isklj.")
		piroQuery("increment", "-1", function() {
				ucitajTempStatus();
				setThermalMode(1);
			}
		);
}

// Dekrementacija temperature
function smanjiTemperaturu() {
	if (document.getElementById("grejanje-vrednost").innerHTML != "Isklj.")
		piroQuery("decrement", "-1", function() {
				ucitajTempStatus();
				setThermalMode(1);
			}
		);
}
