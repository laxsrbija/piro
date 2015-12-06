// Globalna promenljive koja čuva rezultat funkcije jsonSS()
var statusDana = jsonSS();

// Osnovna funkcija za komuniciranje sa PHP datotekama
function piroQuerry(q, arg, callback) {
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.open("GET", "rpi/piro-querry.php?f=" + q + "&arg=" + arg, true);
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && typeof callback == "function")
			callback.apply(xmlhttp);
	}

	xmlhttp.send();
}

// Funkcija za učitavanje vremena
function ucitajVreme(arg) {
	piroQuerry("azurirajVreme", arg, function() {
    
		piroQuerry("getWTemp", "-1", function() {
				document.getElementById("prognoza-vrednost").innerHTML = this.responseText + "°";
			}
		);

		piroQuerry("getIcon", "-1", function() {
				document.getElementById("prognoza-ikona").src = "http://icons.wxug.com/i/c/v2/" + statusDana + this.responseText + ".svg";
			}
		);

		piroQuerry("getDesc", "-1", function() {
				document.getElementById("prognoza-opis").innerHTML = this.responseText;
			}
		);

		piroQuerry("getIconDaily", "-1", function() {
				document.getElementById("prognoza-ikona-dnevna").src = "http://icons.wxug.com/i/c/v2/" + statusDana + this.responseText + ".svg";
			}
		);

		piroQuerry("getMaxTemp", "-1", function() {
				document.getElementById("prognoza-max").innerHTML = "Maksimalna: " + this.responseText + "°";
			}
		);

		piroQuerry("getMinTemp", "-1", function() {
				document.getElementById("prognoza-min").innerHTML = "Minimalna: " + this.responseText + "°";
			}
		);

		piroQuerry("getPadavine", "-1", function() {
				document.getElementById("prognoza-padavine").innerHTML = "Mogućnost padavina: " + this.responseText + "%";
			}
		);
		
	});	

}

// Proverava da li je zašlo sunce
// Vraća "nt_" ukoliko jeste, ili "" ako nije
function jsonSS() {
	var trenutnoVreme = new Date();
	var objekatSS = new SunriseSunset(trenutnoVreme.getUTCFullYear(), trenutnoVreme.getUTCMonth(), trenutnoVreme.getUTCDate(), 43.310383, 21.868767);
	
	if (trenutnoVreme.getHours() >= objekatSS.sunriseLocalHours(1) && trenutnoVreme.getHours() <= objekatSS.sunsetLocalHours(1))
		return "";
	else
		return "nt_";
}

function lokalniUredjaji() {

	// Učitati status i  temperaturu peći 
	ucitajTempStatus();
	
	// Učitava režim rada peći
	piroQuerry("getMode", "-1", function() {
			for (var i = 1; i <= 5; i++)
				document.getElementById("grejanje-" + i + "-taster").src = "img/grejanje-" + i + ".png";

			document.getElementById("grejanje-" + (parseInt(this.responseText) + 1) + "-taster").src = "img/grejanje-" + (parseInt(this.responseText) + 1) + "-s.png";
		}
	);
	
	// Učitati status računara
	piroQuerry("getPCStatus", "-1", function() {
			if (this.responseText == 1) {
				document.getElementById("racunar-taster").innerHTML = "ISKLJUČI";
				document.getElementById("racunar-kuler").src = "img/fan-rot.gif";
				document.getElementById("racunar-status").innerHTML = "Računar je uključen.";
				document.getElementById("racunar-taster").className = "racunar-taster ukljuceno";
			}
			else {
				document.getElementById("racunar-taster").innerHTML = "UKLJUČI";
				document.getElementById("racunar-kuler").src = "img/fan.png";
				document.getElementById("racunar-status").innerHTML = "Računar je isključen.";
				document.getElementById("racunar-taster").className = "racunar-taster iskljuceno";
			}
		}
	);
	
	// Učitavanje statusa rasvete
	// Kako se radi o asinhronoj funkciji, neophodno je "zamrznuti" brojač tokom trenutnog zahteva
	// Zato se poziva funkcija sa vrednošću i kao parametrom 
	for (var i = 0; i < 3; i++) {
		(function(id) {
		    piroQuerry("getRelayStatus", id, function() {
					if (this.responseText == 1) {
						document.getElementById("rasv-" + id + "-taster").innerHTML = "ISKLJUČI";
						document.getElementById("rasv-" + id + "-taster").className = "rasv-" + id + "-taster ukljuceno";
					}
					else {
						document.getElementById("rasv-" + id + "-taster").innerHTML = "UKLJUČI";
						document.getElementById("rasv-" + id + "-taster").className = "rasv-" + id + "-taster iskljuceno";
					}
				}
			);
    	})(i);
	}

	// Učitava temperaturu sistema
	piroQuerry("getShellTemp", "-1", function() {
			document.getElementById("status-temperatura").innerHTML = "Temperatura servera: " + this.responseText + "°C";
		}
	);

	// Učitava temperaturu sistema
	piroQuerry("getUptime", "-1", function() {
			if (this.responseText.lastIndexOf(":") == -1) {
				document.getElementById("status-uptime").innerHTML = "Operativno vreme: " + this.responseText + " dan";
				if (this.responseText[this.responseText.length-1] != "1") 
					document.getElementById("status-uptime").innerHTML += "a"; 
			}
			else
				document.getElementById("status-uptime").innerHTML = "Server je pokrenut danas.";
		}
	);

	piroQuerry("getLoadAvg", "-1", function() {
			document.getElementById("status-opterecenje").innerHTML = "Prosečno opterećenje servera: " + parseInt(this.responseText) + "%";
		}
	);

}

function inicijalnoPokretanje() {
	
	// Poziva funkciju za učitavanje vremena
	ucitajVreme("Redovno");

	lokalniUredjaji();

	// Pokušaj automatskog ažuriranja statusa
	// Nije se pokazao uspešnim
	//setInterval(lokalniUredjaji, 5000);
	
}
