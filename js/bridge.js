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

		var statusDana = jsonSS();

		piroQuerry("getUV", "-1", function() {
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

		piroQuerry("getWTemp", "-1", function() {
				document.getElementById("prognoza-vrednost").innerHTML = this.responseText + "°";
			}
		);

		piroQuerry("getIcon", "-1", function() {
				document.getElementById("prognoza-ikona").src = "http://icons.wxug.com/i/c/v2/"
				+ statusDana + this.responseText + ".svg";
			}
		);

		piroQuerry("getDesc", "-1", function() {
				document.getElementById("prognoza-opis").innerHTML = this.responseText;
			}
		);

		piroQuerry("getIconDaily", "-1", function() {
				document.getElementById("prognoza-ikona-dnevna").src = "http://icons.wxug.com/i/c/v2/"
				+ statusDana + this.responseText + ".svg";
			}
		);

		piroQuerry("getNazivDana", "-1", function() {
				document.getElementById("prognoza-dan").innerHTML = this.responseText;
			}
		);

		piroQuerry("getMaxTemp", "-1", function() {
				var tmp = this.responseText;

				piroQuerry("getMinTemp", "-1", function() {
						document.getElementById("prognoza-dnevna-vrednost").innerHTML = tmp + "° / " + this.responseText + "°";
					}
				);

			}
		);

		piroQuerry("getDescDaily", "-1", function() {
				document.getElementById("prognoza-dnevna-opis").innerHTML = this.responseText;
			}
		);

		piroQuerry("getPadavine", "-1", function() {
				document.getElementById("prognoza-ic-padavine").innerHTML = this.responseText + "%";
			}
		);

		piroQuerry("getSubTemp", "-1", function() {
				document.getElementById("prognoza-ic-subjektivniosecaj").innerHTML = this.responseText + "°";
			}
		);

		piroQuerry("getVisibility", "-1", function() {
				var t = this.responseText;

				// Provera da li je vidljivost ceo broj
				if (t % 1 === 0)
					t = parseInt(t);

				document.getElementById("prognoza-ic-vidljivost").innerHTML = t + " km";
			}
		);

	});

}

// Proverava da li je zašlo sunce
// Vraća "nt_" ukoliko jeste, ili "" ako nije
function jsonSS() {
	var trenutnoVreme = new Date();
	var objekatSS = new SunriseSunset(trenutnoVreme.getUTCFullYear(),
		trenutnoVreme.getUTCMonth(), trenutnoVreme.getUTCDate(), 43.310383, 21.868767);

	// Učitavanje footera o istom trošku stvaranja Date objekta
	var d = new Date();
	document.getElementById("footer").innerHTML = "Copyright © " + d.getUTCFullYear() + " Lazar Stanojević. Sva prava zadržana.";

	if (trenutnoVreme.getHours() >= objekatSS.sunriseLocalHours(1)
		&& trenutnoVreme.getHours() <= objekatSS.sunsetLocalHours(1))
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

			document.getElementById("grejanje-" + (parseInt(this.responseText) + 1)
				+ "-taster").src = "img/grejanje-" + (parseInt(this.responseText) + 1) + "-s.png";
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
			if (this.responseText != 0) {
				document.getElementById("status-uptime").innerHTML = "Operativno vreme: " + this.responseText + " dan";
				if (this.responseText[this.responseText.length-1] != "1" || this.responseText.length - 2 == this.responseText.lastIndexOf("11"))
					document.getElementById("status-uptime").innerHTML += "a";
			}
			else
				document.getElementById("status-uptime").innerHTML = "Server je pokrenut danas.";
		}
	);

	piroQuerry("getLoadAvg", "-1", function() {
			document.getElementById("status-opterecenje").innerHTML =
				"Prosečno opterećenje servera: " + parseInt(this.responseText) + "%";
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
