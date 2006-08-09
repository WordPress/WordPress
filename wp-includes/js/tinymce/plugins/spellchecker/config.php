<?php
	$spellCheckerConfig = array();

	// General settings
	$spellCheckerConfig['enabled'] = true;

	// Pspell shell specific settings
	$spellCheckerConfig['tinypspellshell.aspell'] = '/usr/bin/aspell';
	$spellCheckerConfig['tinypspellshell.tmp'] = '/tmp/tinyspell/0';

	// Default settings
	$spellCheckerConfig['default.language'] = 'en';
	$spellCheckerConfig['default.mode'] = PSPELL_FAST;

	// Normaly not required to configure
	$spellCheckerConfig['default.spelling'] = "";
	$spellCheckerConfig['default.jargon'] = "";
	$spellCheckerConfig['default.encoding'] = "";

	// Spellchecker class use
	if ( function_exists('pspell_new') )
		require_once("classes/TinyPspell.class.php"); // Internal PHP version

	elseif ( file_exists($spellCheckerConfig['tinypspellshell.aspell']) )
		require_once("classes/TinyPspellShell.class.php"); // Command line pspell

	else
		require_once("classes/TinyGoogleSpell.class.php"); // Google web service
?>
