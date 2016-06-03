<?php

	// Upis u INI datoteku
	function upis() {
		$res = array();

		foreach(PiroData::$data as $key => $val) {
			if(is_array($val)) {
				$res[] = (!empty($res) ? "\n" : "")."[$key]";

				foreach($val as $skey => $sval)
					$res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
			} else
				$res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
		}

		$ini = fopen("piro.ini", "w");

		fwrite($ini, implode("\r\n", $res));

		fclose($ini);
	}

?>
