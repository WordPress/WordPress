<?php
$curpath = dirname(__FILE__).'/';

$locale = '';

// WPLANG is defined in wp-config.
if (defined('WPLANG')) {
    $locale = WPLANG;
}

if (empty($locale)) {
    $locale = 'en_US';
}

$mofile = $curpath . "/languages/$locale.mo";

require($curpath . 'streams.php');
require($curpath . 'gettext.php');

// If the mo file does not exist or is not readable, or if the locale is
// en_US, do not load the mo.
if ( is_readable($mofile) && ($locale != 'en_US') ) {
    $input = new FileReader($mofile);
} else {
    $input = false;
}

$l10n = new gettext_reader($input);

// Return a translated string.    
function __($text) {
    global $l10n;
    return $l10n->translate($text);
}

// Echo a translated string.
function _e($text) {
    global $l10n;
    echo $l10n->translate($text);
}

// Return the plural form.
function __ngettext($single, $plural, $number) {
    global $l10n;
    return $l10n->ngettext($single, $plural, $number);
}

require($curpath . 'locale.php');
?>
