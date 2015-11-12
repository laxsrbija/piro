<?php
	$xml = simplexml_load_file("piro.xml") or die ("<h1>Greška: Ne postoji datoteka sa konfiguracijama!</h1>");
	
	// Globalne promenljive
	$uredjaji = array();
	$xml_len = 0;

	foreach ($xml->uredjaj as $i) {
		$uredjaji[$xml_len][0] = $i->id;
		$uredjaji[$xml_len++][1] = $i->status;
	}

	function upis() {
		$xml_upis = fopen("piro.xml","w");

		$podaci = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<piro>";		
		for($i = 0; $i < $GLOBALS['xml_len']; $i++) {
			$podaci .= "\n\t<uredjaj>\n\t\t<id>".$GLOBALS['uredjaji'][$i][0]."</id>\n";
			$podaci .= "\t\t<status>".$GLOBALS['uredjaji'][$i][1]."</status>\n";
			$podaci .= "\t</uredjaj>";
		}
		$podaci .= "\n</piro>";

		fwrite($xml_upis, $podaci);
		fclose($xml_upis);
	}
		
?>
