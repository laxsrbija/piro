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
				document.getElementById("tempVrednost").innerHTML = this.responseText;
			}
		);

		piroQuerry("getIcon", "-1", function() {
				document.getElementById("tempSlika").src = "img/weather-icons/" + this.responseText + ".gif";
			}
		);

		piroQuerry("getDesc", "-1", function() {
				document.getElementById("tempOpis").innerHTML = this.responseText;
			}
		);

	}, 750);
	

}

function inicijalnoPokretanje() {
	
	// Poziva funkciju za učitavanje vremena
	ucitajVreme("NULL");
	
	// Učitati status, temperaturu i režim rada peći
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
	);
	
}
