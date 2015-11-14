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

	setTimeout(function(){
    
		piroQuerry("getWTemp", "-1", function() {
				document.getElementById("temperatura").innerHTML = this.responseText;
			}
		);

		piroQuerry("getIcon", "-1", function() {
				document.getElementById("slikaTemperatura").src = "img/weather-icons/" + this.responseText + ".gif";
			}
		);

		piroQuerry("getDesc", "-1", function() {
				document.getElementById("tekst").innerHTML = this.responseText;
			}
		);

	}, 650);
	

}
