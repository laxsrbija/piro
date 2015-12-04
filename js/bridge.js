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
	piroQuerry("azurirajVreme", arg, "-1");

	// Čekanje 1000ms zbog WU API
	setTimeout(function(){
    
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

	}, 750);
	

}

// Proverava da li je zašlo sunce
// Vraća "nt_" ukoliko jeste, ili "" ako nije
function jsonSS() {
	var xmlHttp = new XMLHttpRequest();

	if (xmlHttp != null) {
		xmlHttp.open("GET", "http://api.sunrise-sunset.org/json?lat=43.3209022&lng=21.8957589", false);
		xmlHttp.send();
	}
	
	var json = JSON.parse(xmlHttp.responseText);
	var izlazak = json.results.sunrise;
	var zalazak = json.results.sunset;
	
	var izlazakSat, izlazakMinut, zalazakSat, zalazakMinut;
	izlazakSat = izlazakMinut = zalazakSat = zalazakMinut = "";
	
	var i = 0;
	while (i < izlazak.length) {
		if (izlazak[i] == ':') {
			i++;
			break;
		}
		else
			izlazakSat += izlazak[i];
		i++;
	}	
	
	while (i < izlazak.length) {
		if (izlazak[i] == ':')
			break;
		else
			izlazakMinut += izlazak[i];
		i++;
	}
	
	i = 0;
	while (i < zalazak.length) {
		if (zalazak[i] == ':') {
			i++;
			break;
		}
		else
			zalazakSat += zalazak[i];
		i++;
	}	
	
	while (i < zalazak.length) {
		if (zalazak[i] == ':')
			break;
		else
			zalazakMinut += zalazak[i];
		i++;
	}
	
	var trenutnoVreme = new Date();
	
	// Konstruisanje Date objekta na osnovu dobijenih podataka za izlazak i zalazak
	// Dodaje se jedan sat zbog GMT+1 vremenske zone
	var izlaznoVreme = new Date();
	izlaznoVreme.setHours(parseInt(izlazakSat)+1);
	izlaznoVreme.setMinutes(parseInt(izlazakMinut));
	
	var zalaznoVreme = new Date();
	zalaznoVreme.setHours(parseInt(zalazakSat)+1);
	zalaznoVreme.setMinutes(parseInt(zalazakMinut));
	
	if (trenutnoVreme.getHours() >= izlaznoVreme.getHours() && trenutnoVreme.getHours() <= zalaznoVreme.getHours() + 12)
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
				if (this.responseText[this.responseText.length-1] != "0")
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
