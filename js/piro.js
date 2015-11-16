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
