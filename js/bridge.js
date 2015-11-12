
//////////////// OSNOVNE FUNKCIJE ////////////////

function piroQuerryAD(q, arg, dv) {
    if (q.length == 0 || arg.length == 0) 
        return;
    else {
        var xmlhttp = new XMLHttpRequest();

        xmlhttp.open("GET", "rpi/piro-querry.php?f=" + q + "&arg=" + arg, true);
        xmlhttp.send();

		xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && dv != "") {
				document.getElementById(dv).innerHTML = xmlhttp.responseText;
            }
        };
    }
}

function piroQuerryA(q, arg) {
    return piroQuerryAD(q, arg, "");
}

function piroQuerry(q) {
    return piroQuerryAD(q, -1, "");
}

function piroQuerryD(q, dv) {
    return piroQuerryAD(q, -1, dv);
}

//////////////////////////////////////////////////

function getTemp() {
	piroQuerryD("getMode", "tst");
}

function setTemp() {
	var t = parseInt(prompt("Unesi novu temperaturu:",""));
	piroQuerryA("setMode", t);
	piroQuerryD("getMode", "tst");
}


