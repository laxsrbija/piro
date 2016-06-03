// Komunikacija sa serverom
// Funkcija za slanje zahteva sa parametrom
function piroQuery(q, arg, callback) {
	var xmlhttp = new XMLHttpRequest();

	xmlhttp.open("GET", "rpi/piro-query.php?f=" + q + (arg !== "" ? "&arg=" + arg : ""), true);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && typeof callback == "function")
			callback.apply(xmlhttp);
	};
	xmlhttp.send();
}

// Funkcija za slanje zahteva bez parametara
function piroQueryNA(q, callback) {
	piroQuery(q, "", callback);
}

// Funkcija za učitavanje vremena
function ucitajVreme(arg) {

	if (arg == "force")
		document.getElementById("osvezi-str").className = "osvezi-rot";

	piroQuery("azurirajVreme", arg, function() {

		if (parseInt(this.responseText) > 0) {

			piroQueryNA("getUV", function() {
					document.getElementById("prognoza-uv").src = "img/uv-" + this.responseText + ".png";
				}
			);

			piroQueryNA("getWTemp", function() {
					document.getElementById("prognoza-vrednost").innerHTML = this.responseText + "°";
				}
			);

			piroQueryNA("getIcon", function() {
					document.getElementById("prognoza-ikona").src =
						"img/weather/svg/" + this.responseText + ".svg";
				}
			);

			piroQueryNA("getDesc", function() {
					document.getElementById("prognoza-opis").innerHTML = this.responseText;
				}
			);

			piroQueryNA("getIconDaily", function() {
					document.getElementById("prognoza-ikona-dnevna").src =
						"img/weather/svg/" + this.responseText + ".svg";
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

		}

		if (arg == "force")
			document.getElementById("osvezi-str").className = "";

	});

}

function inicijalnoPokretanje() {

	// Poziva funkciju za učitavanje vremena
	ucitajVreme("R");

	// Pokušaj automatskog ažuriranja statusa
	// Nije se pokazao uspešnim
	//setInterval(lokalniUredjaji, 5000);

}
