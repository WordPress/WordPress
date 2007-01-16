<?php
/**
 * $RCSfile: tinyspell.php,v $
 * $Revision: 1.1 $
 * $Date: 2006/03/14 17:33:47 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

	require_once("config.php");

	$id = sanitize($_POST['id'], "loose");

	if (!$spellCheckerConfig['enabled']) {
		header('Content-type: text/xml; charset=utf-8');
		echo '<?xml version="1.0" encoding="utf-8" ?><res id="' . $id . '" error="true" msg="You must enable the spellchecker by modifying the config.php file." />';
		die;
	}

	// Basic config
	$defaultLanguage = $spellCheckerConfig['default.language'];
	$defaultMode = $spellCheckerConfig['default.mode'];

	// Normaly not required to configure
	$defaultSpelling = $spellCheckerConfig['default.spelling'];
	$defaultJargon = $spellCheckerConfig['default.jargon'];
	$defaultEncoding = $spellCheckerConfig['default.encoding'];
	$outputType = "xml"; // Do not change

	// Get input parameters.

	$check = $_POST['check'];
	$cmd = sanitize($_POST['cmd']);
	$lang = sanitize($_POST['lang'], "strict");
	$mode = sanitize($_POST['mode'], "strict");
	$spelling = sanitize($_POST['spelling'], "strict");
	$jargon = sanitize($_POST['jargon'], "strict");
	$encoding = sanitize($_POST['encoding'], "strict");
	$sg = sanitize($_POST['sg'], "bool");
	$words = array();

	$validRequest = true;

	if (empty($check))
		$validRequest = false;

	if (empty($lang))
		$lang = $defaultLanguage;

	if (empty($mode))
		$mode = $defaultMode;

	if (empty($spelling))
		$spelling = $defaultSpelling;

	if (empty($jargon))
		$jargon = $defaultJargon;

	if (empty($encoding))
		$encoding = $defaultEncoding;

	function sanitize($str, $type="strict") {
		switch ($type) {
			case "strict":
				$str = preg_replace("/[^a-zA-Z0-9_\-]/i", "", $str);
			break;
			case "loose":
				$str = preg_replace("/</i", "&gt;", $str);
				$str = preg_replace("/>/i", "&lt;", $str);
			break;
			case "bool":
				if ($str == "true" || $str == true)
					$str = true;
				else
					$str = false;
			break;
		}

		return $str;
	}

	$result = array();
	$tinyspell = new $spellCheckerConfig['class']($spellCheckerConfig, $lang, $mode, $spelling, $jargon, $encoding);

	if (count($tinyspell->errorMsg) == 0) {
		switch($cmd) {
			case "spell":
				// Space for non-exec version and \n for the exec version.
				$words = preg_split("/ |\n/", $check, -1, PREG_SPLIT_NO_EMPTY);
				$result = $tinyspell->checkWords($words);
			break;
			case "suggest":
				$result = $tinyspell->getSuggestion($check);
			break;
			default:
				// Just use this for now.
				$tinyspell->errorMsg[] = "No command.";
				$outputType = $outputType . "error";
			break;
		}
	} else
		$outputType = $outputType . "error";

	if (!$result)
		$result = array();

	// Output data
	switch($outputType) {
		case "xml":
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			echo "\n";
			if (count($result) == 0)
				echo '<res id="' . $id . '" cmd="'. $cmd .'" />';
			else
				echo '<res id="' . $id . '" cmd="'. $cmd .'">'. utf8_encode(implode(" ", $result)) .'</res>';

		break;
		case "xmlerror";
			header('Content-type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8" ?>';
			echo "\n";
			echo '<res id="' . $id . '" cmd="'. $cmd .'" error="true" msg="'. implode(" ", $tinyspell->errorMsg) .'" />';
		break;
		case "html":
			var_dump($result);
		break;
		case "htmlerror":
			echo "Error";
		break;
	}
?> 
