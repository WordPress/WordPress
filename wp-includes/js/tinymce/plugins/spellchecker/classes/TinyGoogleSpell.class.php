<?php
/* * 
 * Tiny Spelling Interface for TinyMCE Spell Checking.
 *
 * Copyright © 2006 Moxiecode Systems AB
 */

require_once("HttpClient.class.php");

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
			$words[] = substr($wordstr, $matches[$i][1], $matches[$i][2]);

		return $words;
	}

	// Returns array with suggestions or false if failed.
	function getSuggestion($word) {
		$sug = array();

		$matches = $this->_getMatches($word);

		if (count($matches) > 0)
			$sug = explode("\t", $matches[0][4]);

		return $sug;
	}

	function _getMatches($word_list) {
		$xml = "";

		// Setup HTTP Client
		$client = new HttpClient('www.google.com');
		$client->setUserAgent('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR');
		$client->setHandleRedirects(false);
		$client->setDebug(false);

		// Setup XML request
		$xml .= '<?xml version="1.0" encoding="utf-8" ?>';
		$xml .= '<spellrequest textalreadyclipped="0" ignoredups="0" ignoredigits="1" ignoreallcaps="1">';
		$xml .= '<text>' . htmlentities($word_list) . '</text></spellrequest>';

		// Execute HTTP Post to Google
		if (!$client->post('/tbproxy/spell?lang=' . $this->lang, $xml)) {
			$this->errorMsg[] = 'An error occurred: ' . $client->getError();
			return array();
		}

		// Grab and parse content
		$xml = $client->getContent();
		preg_match_all('/<c o="([^"]*)" l="([^"]*)" s="([^"]*)">([^<]*)<\/c>/', $xml, $matches, PREG_SET_ORDER);

		return $matches;
	}
}

// Setup classname, should be the same as the name of the spellchecker class
$spellCheckerConfig['class'] = "TinyGoogleSpell";

?>
