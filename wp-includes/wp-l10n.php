<?php
$parentpath = dirname(dirname(__FILE__));
 
require_once($parentpath.'/wp-config.php');

$curpath = dirname(__FILE__).'/';

$locale = '';

// WPLANG is defined in wp-config.
if (defined('WPLANG')) {
    $locale = WPLANG;
}

if (empty($locale)) {
    $locale = 'en_US';
}

$mofile = $curpath . "languages/$locale.mo";

require($curpath . 'streams.php');
require($curpath . 'gettext.php');

// If the mo file does not exist or is not readable, or if the locale is
// en_US, do not load the mo.
if ( is_readable($mofile) && ($locale != 'en_US') ) {
    $input = new FileReader($mofile);
} else {
    $input = false;
}

$l10n['default'] = new gettext_reader($input);

// Return a translated string.    
function __($text, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		return $l10n[$domain]->translate($text);
	} else {
		return $text;
	}
}

// Echo a translated string.
function _e($text, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		echo $l10n[$domain]->translate($text);
	} else {
		echo $text;
	}
}

// Return the plural form.
function __ngettext($single, $plural, $number, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		return $l10n[$domain]->ngettext($single, $plural, $number);
	} else {
		return $text;
	}
}

function load_textdomain($domain, $mofile) {
	global $l10n;

	if ( is_readable($mofile)) {
    $input = new FileReader($mofile);
	}	else {
		return;
	}

	$l10n[$domain] = new gettext_reader($input);
}

function load_plugin_textdomain($domain) {
	global $locale;
	
	$mofile = ABSPATH . "wp-content/plugins/$domain-$locale.mo";
	load_textdomain($domain, $mofile);
}

function load_theme_textdomain($domain) {
	global $locale;
	
	$mofile = get_template_directory() . "/$locale.mo";
	load_textdomain($domain, $mofile);
}

require($curpath . 'locale.php');
?>
