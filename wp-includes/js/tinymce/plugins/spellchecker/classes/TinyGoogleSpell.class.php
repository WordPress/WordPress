<?php
/* *
 * Tiny Spelling Interface for TinyMCE Spell Checking.
 *
 * Copyright © 2006 Moxiecode Systems AB
 */

class TinyGoogleSpell {
	var $lang;

	function TinyGoogleSpell(&$config, $lang, $mode, $spelling, $jargon, $encoding) {
		$this->lang = $lang;
	}

	// Returns array with bad words or false if failed.
	function checkWords($word_array) {
		$words = array();
		$wordstr = implode(' ', $word_array);

		$matches = $this->_getMatches($wordstr);

		for ($i=0; $i<count($matches); $i++)
			$words[] = $this->unhtmlentities(mb_substr($wordstr, $matches[$i][1], $matches[$i][2], "UTF-8"));

		return $words;
	}

	function unhtmlentities($string) {
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);

		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);

		return strtr($string, $trans_tbl);
	}

	// Returns array with suggestions or false if failed.
	function getSuggestion($word) {
		$sug = array();

		$matches = $this->_getMatches($word);

		if (count($matches) > 0)
			$sug = explode("\t", utf8_encode($this->unhtmlentities($matches[0][4])));

		return $sug;
	}

	function _xmlChars($string) {
	   $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
	
	   foreach ($trans as $k => $v)
			$trans[$k] = "&#".ord($k).";";

	   return strtr($string, $trans);
	}

	function _getMatches($word_list) {
        $server = "www.google.com";
        $port = 443;
        $path = "/tbproxy/spell?lang=" . $this->lang . "&hl=en";
        $host = "www.google.com";
        $url = "https://" . $server;

		// Setup XML request
		$xml = '<?xml version="1.0" encoding="utf-8" ?><spellrequest textalreadyclipped="0" ignoredups="0" ignoredigits="1" ignoreallcaps="1"><text>' . $word_list . '</text></spellrequest>';

        $header  = "POST ".$path." HTTP/1.0 \r\n";
        $header .= "MIME-Version: 1.0 \r\n";
        $header .= "Content-type: application/PTI26 \r\n";
        $header .= "Content-length: ".strlen($xml)." \r\n";
        $header .= "Content-transfer-encoding: text \r\n";
        $header .= "Request-number: 1 \r\n";
        $header .= "Document-type: Request \r\n";
        $header .= "Interface-Version: Test 1.4 \r\n";
        $header .= "Connection: close \r\n\r\n";
        $header .= $xml;
		//$this->_debugData($xml);

		// Use raw sockets
		$fp = fsockopen("ssl://" . $server, $port, $errno, $errstr, 30);
		if ($fp) {
			// Send request
			fwrite($fp, $header);

			// Read response
			$xml = "";
			while (!feof($fp))
				$xml .= fgets($fp, 128);

			fclose($fp);
		} else {
			// Use curl
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$xml = curl_exec($ch);
			curl_close($ch);
		}

		//$this->_debugData($xml);

		// Grab and parse content
		preg_match_all('/<c o="([^"]*)" l="([^"]*)" s="([^"]*)">([^<]*)<\/c>/', $xml, $matches, PREG_SET_ORDER);

		return $matches;
	}

	function _debugData($data) {
		$fh = @fopen("debug.log", 'a+');
		@fwrite($fh, $data);
		@fclose($fh);
	}
}

// Setup classname, should be the same as the name of the spellchecker class
$spellCheckerConfig['class'] = "TinyGoogleSpell";

?>
