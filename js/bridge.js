// Komunikacija sa serverom
// Funkcija za slanje zahteva sa parametrom
function piroQuery(q, arg, callback) {
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.open("GET", "rpi/piro-query.php?f=" + q + "&arg=" + arg, true);

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && typeof callback == "function")
			callback.apply(xmlhttp);
	}

	xmlhttp.send();
}

// Funkcija za slanje zahteva bez parametara
function piroQueryNA(q, callback) {
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.open("GET", "rpi/piro-query.php?f=" + q, true);

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && typeof callback == "function")
			callback.apply(xmlhttp);
	}

	xmlhttp.send();
}

// Funkcija za učitavanje vremena
function ucitajVreme(arg) {
	piroQuery("azurirajVreme", arg, function() {

		piroQueryNA("getUV", function() {
				var uvStepen;
				var uvIndeks = this.responseText;

				if (uvIndeks >= 11)
					uvStepen = 4;
				else if (uvIndeks >= 8)
					uvStepen = 3;
				else if (uvIndeks >= 6)
					uvStepen = 2;
				else if (uvIndeks >= 3)
					uvStepen = 1;
				else
					uvStepen = 0;

				document.getElementById("prognoza-uv").src = "img/uv-" + uvStepen + ".png";

			}
		);

		piroQueryNA("getWTemp", function() {
				document.getElementById("prognoza-vrednost").innerHTML = this.responseText + "°";
			}
		);

		piroQueryNA("getIcon", function() {
				document.getElementById("prognoza-ikona").src = "http://icons.wxug.com/i/c/v2/"
				+ this.responseText + ".svg";
			}
		);

		piroQueryNA("getDesc", function() {
				document.getElementById("prognoza-opis").innerHTML = this.responseText;
			}
		);

		piroQueryNA("getIconDaily", function() {
				document.getElementById("prognoza-ikona-dnevna").src = "http://icons.wxug.com/i/c/v2/"
				+ this.responseText + ".svg";
			}
		);

		piroQueryNA("getNazivDana", function() {
				document.getElementById("prognoza-dan").innerHTML = this.responseText;
			}
		);

		piroQueryNA("getMaxTemp", function() {
				var tmp = this.responseText;

				piroQueryNA("getMinTemp", function() {
						document.getElementById("prognoza-dnevna-vrednost").innerHTML = tmp + "° / " + this.responseText + "°";
					}
				);

			}
		);

		piroQueryNA("getDescDaily", function() {
				document.getElementById("prognoza-dnevna-opis").innerHTML = this.responseText;
			}
		);

		piroQueryNA("getPadavine", function() {
				document.getElementById("prognoza-ic-padavine").innerHTML = this.responseText + "%";
			}
		);

		piroQueryNA("getSubTemp", function() {
				document.getElementById("prognoza-ic-subjektivniosecaj").innerHTML = this.responseText + "°";
			}
		);

		piroQueryNA("getVisibility", function() {
				document.getElementById("prognoza-ic-vidljivost").innerHTML = this.responseText;
			}
		);

	});

}

function lokalniUredjaji() {

	// Učitati status i  temperaturu peći
	ucitajTempStatus();

	// Učitati status računara
	piroQueryNA("getPCStatus", function() {
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
		    piroQuery("getRelayStatus", id, function() {
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
	piroQueryNA("getShellTemp", function() {
			document.getElementById("status-temperatura").innerHTML = "Temperatura servera: " + this.responseText + "°C";
		}
	);

	// Učitava operativno vreme
	piroQueryNA("getUptime", function() {
			if (this.responseText != 0) {
				document.getElementById("status-uptime").innerHTML = "Operativno vreme: " + this.responseText + " dan";

				// X dan(a)
				if (this.responseText[this.responseText.length-1] != "1"
					|| this.responseText.lastIndexOf("11") != -1 && this.responseText.length - 2 == this.responseText.lastIndexOf("11"))
					document.getElementById("status-uptime").innerHTML += "a";
			}
			else
				document.getElementById("status-uptime").innerHTML = "Server je pokrenut danas.";
		}
	);

	piroQueryNA("getLoadAvg", function() {
			document.getElementById("status-opterecenje").innerHTML =
				"Prosečno opterećenje servera: " + parseInt(this.responseText) + "%";
		}
	);

}

function inicijalnoPokretanje() {

	// Poziva funkciju za učitavanje vremena
	ucitajVreme("Redovno");
	
	document.getElementById("footer").innerHTML = "Copyright © " + (new Date()).getUTCFullYear() + " Lazar Stanojević. Sva prava zadržana.";

	lokalniUredjaji();

	// Pokušaj automatskog ažuriranja statusa
	// Nije se pokazao uspešnim
	//setInterval(lokalniUredjaji, 5000);

}
