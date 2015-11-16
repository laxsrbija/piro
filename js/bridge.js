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

	if (trenutnoVreme >= izlaznoVreme && trenutnoVreme <= zalaznoVreme)
		return "";
	else
		return "nt_";
}

function inicijalnoPokretanje() {
	
	// Poziva funkciju za učitavanje vremena
	ucitajVreme("Redovno");
	
	/*// Učitati status, temperaturu i režim rada peći
	piroQuerry("thermalStatus", "-1", function() {
			document.getElementById("termoStatus").innerHTML = "Status grejnog tela: " + this.responseText;
		}
	);

	piroQuerry("getTemp", "-1", function() {
			document.getElementById("termoTemperatura").innerHTML = "Temperatura grejnog tela: " + this.responseText;
		}
	);
	
	piroQuerry("getMode", "-1", function() {
			document.getElementById("termoMod").innerHTML = "Režim grejnog tela: " + this.responseText;
		}
	);
	
	// Učitati status računara
	piroQuerry("getRelayStatus", "3", function() {
			document.getElementById("racunarStatus").innerHTML = "Status racunara: " + this.responseText;
		}
	);
	
	// Učitavanje statusa rasvete
	piroQuerry("getRelayStatus", "0", function() {
			document.getElementById("svetloGlavno").innerHTML = "Status glavnog svetla: " + this.responseText;
		}
	);

	piroQuerry("getRelayStatus", "1", function() {
			document.getElementById("svetloDesno").innerHTML = "Status desnog svetla: " + this.responseText;
		}
	);
	
	piroQuerry("getRelayStatus", "2", function() {
			document.getElementById("svetloLevo").innerHTML = "Status levog svetla: " + this.responseText;
		}
	);*/
	
}
