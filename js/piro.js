// Prikazivanje ikone dok se stranica u potpunosti ne učita
document.onreadystatechange = function () {
	var state = document.readyState
	if (state == 'interactive') {
		document.getElementById('kontejner').style.visibility="hidden";
	} else if (state == 'complete') {
		setTimeout(function() {
			document.getElementById('interactive');
			document.getElementById('load').style.visibility="hidden";
			document.getElementById('kontejner').style.visibility="visible";
		}, 1000);
	}
}

function racunarToggle() {
	piroQuerry("togglePC", "-1", "-1");
	
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
	piroQuerry("toggleRelay", k, "-1");

	if (document.getElementById("rasv-" + k + "-taster").innerHTML == "UKLJUČI") {
		document.getElementById("rasv-" + k + "-taster").innerHTML = "ISKLJUČI";
		document.getElementById("rasv-" + k + "-taster").className = "rasv-" + k + "-taster ukljuceno";
	}
	else {
		document.getElementById("rasv-" + k + "-taster").innerHTML = "UKLJUČI";
		document.getElementById("rasv-" + k + "-taster").className = "rasv-" + k + "-taster iskljuceno";
	}

}
