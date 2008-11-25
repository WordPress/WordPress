<?php
/**
 * WordPress Translation API
 *
 * @package WordPress
 * @subpackage i18n
 */

/**
 * Gets the current locale.
 *
 * If the locale is set, then it will filter the locale in the 'locale' filter
 * hook and return the value.
 *
 * If the locale is not set already, then the WPLANG constant is used if it is
 * defined. Then it is filtered through the 'locale' filter hook and the value
 * for the locale global set and the locale is returned.
 *
 * The process to get the locale should only be done once but the locale will
 * always be filtered using the 'locale' hook.
 *
 * @since 1.5.0
 * @uses apply_filters() Calls 'locale' hook on locale value.
 * @uses $locale Gets the locale stored in the global.
 *
 * @return string The locale of the blog or from the 'locale' hook.
 */
function get_locale() {
	global $locale;

	if (isset($locale))
		return apply_filters( 'locale', $locale );

	// WPLANG is defined in wp-config.
	if (defined('WPLANG'))
		$locale = WPLANG;

	if (empty($locale))
		$locale = 'en_US';

	$locale = apply_filters('locale', $locale);

	return $locale;
}

/**
 * Retrieve the translated text.
 *
 * If the domain is set in the $l10n global, then the text is run through the
 * domain's translate method. After it is passed to the 'gettext' filter hook,
 * along with the untranslated text as the second parameter.
 *
 * If the domain is not set, the $text is just returned.
 *
 * @since 2.2.0
 * @uses $l10n Gets list of domain translated string (gettext_reader) objects.
 * @uses apply_filters() Calls 'gettext' on domain translated text
 *		with the untranslated text as second parameter.
 *
 * @param string $text Text to translate.
 * @param string $domain Domain to retrieve the translated text.
 * @return string Translated text
 */
function translate($text, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain]))
		return apply_filters('gettext', $l10n[$domain]->translate($text), $text, $domain);
	else
		return apply_filters('gettext', $text, $text, $domain);
}

function before_last_bar( $string ) {
	$last_bar = strrpos( $string, '|' );
	if ( false == $last_bar )
		return $string;
	else
		return substr( $string, 0, $last_bar );
}

/**
 * Retrieve the translated text and strip context.
 *
 * If the domain is set in the $l10n global, then the text is run through the
 * domain's translate method. After it is passed to the 'gettext' filter hook,
 * along with the untranslated text as the second parameter.
 *
 * If the domain is not set, the $text is just returned.
 *
 * @since 2.5
 * @uses translate()
 *
 * @param string $text Text to translate
 * @param string $domain Domain to retrieve the translated text
 * @return string Translated text
 */
function translate_with_context( $text, $domain = 'default' ) {
	return before_last_bar( translate( $text, $domain ) );

}

/**
 * Retrieves the translated string from the translate().
 *
 * @see translate() An alias of translate()
 * @since 2.1.0
 *
 * @param string $text Text to translate
 * @param string $domain Optional. Domain to retrieve the translated text
 * @return string Translated text
 */
function __($text, $domain = 'default') {
	return translate($text, $domain);
}

/**
 * Displays the returned translated text from translate().
 *
 * @see translate() Echos returned translate() string
 * @since 1.2.0
 *
 * @param string $text Text to translate
 * @param string $domain Optional. Domain to retrieve the translated text
 */
function _e($text, $domain = 'default') {
	echo translate($text, $domain);
}

/**
 * Retrieve context translated string.
 *
 * Quite a few times, there will be collisions with similar translatable text
 * found in more than two places but with different translated context.
 *
 * In order to use the separate contexts, the _c() function is used and the
 * translatable string uses a pipe ('|') which has the context the string is in.
 *
 * When the translated string is returned, it is everything before the pipe, not
 * including the pipe character. If there is no pipe in the translated text then
 * everything is returned.
 *
 * @since 2.2.0
 *
 * @param string $text Text to translate
 * @param string $domain Optional. Domain to retrieve the translated text
 * @return string Translated context string without pipe
 */
function _c($text, $domain = 'default') {
	return translate_with_context($text, $domain);
}

/**
 * Retrieve the plural or single form based on the amount.
 *
 * If the domain is not set in the $l10n list, then a comparsion will be made
 * and either $plural or $single parameters returned.
 *
 * If the domain does exist, then the parameters $single, $plural, and $number
 * will first be passed to the domain's ngettext method. Then it will be passed
 * to the 'ngettext' filter hook along with the same parameters. The expected
 * type will be a string.
 *
 * @since 1.2.0
 * @uses $l10n Gets list of domain translated string (gettext_reader) objects
 * @uses apply_filters() Calls 'ngettext' hook on domains text returned,
 *		along with $single, $plural, and $number parameters. Expected to return string.
 *
 * @param string $single The text that will be used if $number is 1
 * @param string $plural The text that will be used if $number is not 1
 * @param int $number The number to compare against to use either $single or $plural
 * @param string $domain Optional. The domain identifier the text should be retrieved in
 * @return string Either $single or $plural translated text
 */
function __ngettext($single, $plural, $number, $domain = 'default') {
	global $l10n;

	if (isset($l10n[$domain])) {
		return apply_filters('ngettext', $l10n[$domain]->ngettext($single, $plural, $number), $single, $plural, $number);
	} else {
		if ($number != 1)
			return $plural;
		else
			return $single;
	}
}

/**
 * @see __ngettext() An alias of __ngettext
 *
 */
function _n() {
	$args = func_get_args();
	return call_user_func_array('__ngettext', $args);
}

/**
 * @see _n() A version of _n(), which supports contexts --
 * strips everything from the translation after the last bar
 *
 */
function _nc( $single, $plural, $number, $domain = 'default' ) {
	return before_last_bar( __ngettext( $single, $plural, $number, $domain ) );
}

/**
 * Register plural strings in POT file, but don't translate them.
 *
 * Used when you want do keep structures with translatable plural strings and
 * use them later.
 *
 * Example:
 *  $messages = array(
 *  	'post' => ngettext_noop('%s post', '%s posts'),
 *  	'page' => ngettext_noop('%s pages', '%s pages')
 *  );
 *  ...
 *  $message = $messages[$type];
 *  $usable_text = sprintf(__ngettext($message[0], $message[1], $count), $count);
 *
 * @since 2.5
 * @param $single Single form to be i18ned
 * @param $plural Plural form to be i18ned
 * @param $number Not used, here for compatibility with __ngettext, optional
 * @param $domain Not used, here for compatibility with __ngettext, optional
 * @return array array($single, $plural)
 */
function __ngettext_noop($single, $plural, $number=1, $domain = 'default') {
	return array($single, $plural);
}

/**
 * @see __ngettext_noop() An alias of __ngettext_noop()
 *
 */
function _n_noop() {
	$args = func_get_args();
	return call_user_func_array('__ngettext_noop', $args);
}

/**
 * Loads MO file into the list of domains.
 *
 * If the domain already exists, the inclusion will fail. If the MO file is not
 * readable, the inclusion will fail.
 *
 * On success, the mofile will be placed in the $l10n global by $domain and will
 * be an gettext_reader object.
 *
 * @since 1.5.0
 * @uses $l10n Gets list of domain translated string (gettext_reader) objects
 * @uses CacheFileReader Reads the MO file
 * @uses gettext_reader Allows for retrieving translated strings
 *
 * @param string $domain Unique identifier for retrieving translated strings
 * @param string $mofile Path to the .mo file
 * @return null On failure returns null and also on success returns nothing.
 */
function load_textdomain($domain, $mofile) {
	global $l10n;

	if ( is_readable($mofile))
		$input = new CachedFileReader($mofile);
	else
		return;

	$gettext = new gettext_reader($input);

	if (isset($l10n[$domain])) {
		$l10n[$domain]->load_tables();
		$gettext->load_tables();
		$l10n[$domain]->cache_translations = array_merge($gettext->cache_translations, $l10n[$domain]->cache_translations);
	} else
		$l10n[$domain] = $gettext;

	unset($input, $gettext);
}

/**
 * Loads default translated strings based on locale.
 *
 * Loads the .mo file in WP_LANG_DIR constant path from WordPress root. The
 * translated (.mo) file is named based off of the locale.
 *
 * @since 1.5.0
 */
function load_default_textdomain() {
	$locale = get_locale();

	$mofile = WP_LANG_DIR . "/$locale.mo";

	load_textdomain('default', $mofile);
}

/**
 * Loads the plugin's translated strings.
 *
 * If the path is not given then it will be the root of the plugin directory.
 * The .mo file should be named based on the domain with a dash followed by a
 * dash, and then the locale exactly.
 *
 * @since 1.5.0
 *
 * @param string $domain Unique identifier for retrieving translated strings
 * @param string $abs_rel_path Optional. Relative path to ABSPATH of a folder,
 * 	where the .mo file resides. Deprecated, but still functional until 2.7
 * @param string $plugin_rel_path Optional. Relative path to WP_PLUGIN_DIR. This is the preferred argument to use. It takes precendence over $abs_rel_path
 */
function load_plugin_textdomain($domain, $abs_rel_path = false, $plugin_rel_path = false) {
	$locale = get_locale();

	if ( false !== $plugin_rel_path	)
		$path = WP_PLUGIN_DIR . '/' . trim( $plugin_rel_path, '/');
	else if ( false !== $abs_rel_path)
		$path = ABSPATH . trim( $abs_rel_path, '/');
	else
		$path = WP_PLUGIN_DIR;

	$mofile = $path . '/'. $domain . '-' . $locale . '.mo';
	load_textdomain($domain, $mofile);
}

/**
 * Loads the theme's translated strings.
 *
 * If the current locale exists as a .mo file in the theme's root directory, it
 * will be included in the translated strings by the $domain.
 *
 * The .mo files must be named based on the locale exactly.
 *
 * @since 1.5.0
 *
 * @param string $domain Unique identifier for retrieving translated strings
 */
function load_theme_textdomain($domain, $path = false) {
	$locale = get_locale();

	$path = ( empty( $path ) ) ? get_template_directory() : $path;

	$mofile = "$path/$locale.mo";
	load_textdomain($domain, $mofile);
}

?>
