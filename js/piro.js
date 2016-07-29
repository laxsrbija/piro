function racunarToggle() {
	piroQueryNA("togglePC", "-1");

	if (document.getElementById("racunar-status").innerHTML == "Računar je isključen.") {
		document.getElementById("racunar-taster").innerHTML = "ISKLJUČI";
		document.getElementById("racunar-taster").className = "racunar-taster ukljuceno";
		document.getElementById("racunar-kuler").src = "img/fan-rot.gif";
		document.getElementById("racunar-status").innerHTML = "Računar je uključen.";
	} else {
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
	} else {
		document.getElementById("rasv-" + k + "-taster").innerHTML = "UKLJUČI";
		document.getElementById("rasv-" + k + "-taster").className = "rasv-" + k + "-taster iskljuceno";
	}

}

// Učitatavanje statusa i temperature peći
function ucitajTempStatus() {
	piroQueryNA("thermalStatus", function() {
		if (this.responseText < 1) {
			if (parseInt(this.responseText) === 0)
				document.getElementById("grejanje-vrednost").innerHTML = "Isklj.";
			else
				document.getElementById("grejanje-vrednost").innerHTML =
					"<img class=\"grejanje-nedostupno\" src=\"img/grejanje-nedostupno.png\">";

			for (var i = 1; i <= 5; i++)
				document.getElementById("grejanje-" + i + "-taster").src = "img/grejanje-" + i + ".png";
		} else {
			piroQueryNA("getTemp", function() {
				document.getElementById("grejanje-vrednost").innerHTML = this.responseText + "°";
				getThermalMode();
			});
		}
	});
}

// Paljenje i gašenje grejnog tela
function grejanjeToggle() {
	piroQueryNA("toggleThermal", function() {
		ucitajTempStatus();
	});
}

// Učitavanje režima rada
function getThermalMode() {
	piroQueryNA("getMode", function() {
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

// Inkrementacija i dekrementacija temperature
// 1 - inkrementiraj
// 0 - dekrementiraj
function promeniTemperaturu(arg) {
	if (document.getElementById("grejanje-vrednost").innerHTML != "Isklj.")
		piroQueryNA((arg == 1 ? "increment" : "decrement"), function() {
			ucitajTempStatus();
			setThermalMode(1);
		});
}

function checkMode(a, b) {
	if (a == 1)
		document.getElementById("grejanje-" + b + "-taster").src = "img/grejanje-" + b + "-s.png";
}

function uptimeFormatter(a) {
	if (a > 1 && (a[a.length - 1] != "1" || a.lastIndexOf("11") != -1 && a.length - 2 == a.lastIndexOf("11")))
		document.getElementById("status-uptime").innerHTML += "a";
}

window.addEventListener("focus", function(event) { ucitajVreme("R"); }, false);