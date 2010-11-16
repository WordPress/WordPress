<?php
/**
 * config.php
 *
 * @package MCManager.includes
 */
	// General settings
	$config['general.engine'] = 'GoogleSpell';
	//$config['general.engine'] = 'PSpell';
	//$config['general.engine'] = 'PSpellShell';
	//$config['general.remote_rpc_url'] = 'http://some.other.site/some/url/rpc.php';

	// PSpell settings
	$config['PSpell.mode'] = PSPELL_FAST;
	$config['PSpell.spelling'] = "";
	$config['PSpell.jargon'] = "";
	$config['PSpell.encoding'] = "";

	// PSpellShell settings
	$config['PSpellShell.mode'] = PSPELL_FAST;
	$config['PSpellShell.aspell'] = '/usr/bin/aspell';
	$config['PSpellShell.tmp'] = '/tmp';

	// Windows PSpellShell settings
	//$config['PSpellShell.aspell'] = '"c:\Program Files\Aspell\bin\aspell.exe"';
	//$config['PSpellShell.tmp'] = 'c:/temp';
?>
