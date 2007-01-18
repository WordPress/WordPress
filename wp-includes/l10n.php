<?php
function get_locale() {
	global $locale;

	if (isset($locale))
		return apply_filters( 'locale', $locale );

	// WPLANG is defined in wp-config.
	if (defined('WPLANG'))
		$locale = WPLANG;

	if (empty($locale))
		$locale = '';

	$locale = apply_filters('locale', $locale);

	return $locale;
}

// Return a translated string.
function __($text, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain]))
		return apply_filters('gettext', $l10n[$domain]->translate($text), $text);
	else
		return $text;
}

// Echo a translated string.
function _e($text, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain]))
		echo apply_filters('gettext', $l10n[$domain]->translate($text), $text);
	else
		echo $text;
}

// Return the plural form.
function __ngettext($single, $plural, $number, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		return $l10n[$domain]->ngettext($single, $plural, $number);
	} else {
		if ($number != 1)
			return $plural;
		else
			return $single;
	}
}

function load_textdomain($domain, $mofile) {
	global $l10n;

	if (isset($l10n[$domain]))
		return;

	if ( is_readable($mofile))
		$input = new CachedFileReader($mofile);
	else
		return;

	$l10n[$domain] = new gettext_reader($input);
}

function load_default_textdomain() {
	global $l10n;

	$locale = get_locale();
	if ( empty($locale) )
		$locale = 'en_US';

	$mofile = ABSPATH . LANGDIR . "/$locale.mo";

	load_textdomain('default', $mofile);
}

function load_plugin_textdomain($domain, $path = false) {
	$locale = get_locale();
	if ( empty($locale) )
		$locale = 'en_US';

	if ( false === $path )
		$path = PLUGINDIR;

	$mofile = ABSPATH . "$path/$domain-$locale.mo";
	load_textdomain($domain, $mofile);
}

function load_theme_textdomain($domain) {
	$locale = get_locale();
	if ( empty($locale) )
		$locale = 'en_US';

	$mofile = get_template_directory() . "/$locale.mo";
	load_textdomain($domain, $mofile);
}

?>
