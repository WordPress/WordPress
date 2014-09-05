<?php
/**
 * WordPress Translation API
 *
 * @package WordPress
 * @subpackage i18n
 */

/**
 * Get the current locale.
 *
 * If the locale is set, then it will filter the locale in the 'locale' filter
 * hook and return the value.
 *
 * If the locale is not set already, then the WPLANG constant is used if it is
 * defined. Then it is filtered through the 'locale' filter hook and the value
 * for the locale global set and the locale is returned.
 *
 * The process to get the locale should only be done once, but the locale will
 * always be filtered using the 'locale' hook.
 *
 * @since 1.5.0
 *
 * @return string The locale of the blog or from the 'locale' hook.
 */
function get_locale() {
	global $locale, $wp_local_package;

	if ( isset( $locale ) ) {
		/**
		 * Filter WordPress install's locale ID.
		 *
		 * @since 1.5.0
		 *
		 * @param string $locale The locale ID.
		 */
		return apply_filters( 'locale', $locale );
	}

	if ( isset( $wp_local_package ) ) {
		$locale = $wp_local_package;
	}

	// WPLANG was defined in wp-config.
	if ( defined( 'WPLANG' ) ) {
		$locale = WPLANG;
	}

	// If multisite, check options.
	if ( is_multisite() ) {
		// Don't check blog option when installing.
		if ( defined( 'WP_INSTALLING' ) || ( false === $ms_locale = get_option( 'WPLANG' ) ) ) {
			$ms_locale = get_site_option( 'WPLANG' );
		}

		if ( $ms_locale !== false ) {
			$locale = $ms_locale;
		}
	} else {
		$db_locale = get_option( 'WPLANG' );
		if ( $db_locale !== false ) {
			$locale = $db_locale;
		}
	}

	if ( empty( $locale ) ) {
		$locale = 'en_US';
	}

	/** This filter is documented in wp-includes/l10n.php */
	return apply_filters( 'locale', $locale );
}

/**
 * Retrieve the translation of $text.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * <strong>Note:</strong> Don't use translate() directly, use __() or related functions.
 *
 * @since 2.2.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text
 */
function translate( $text, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translations = $translations->translate( $text );
	/**
	 * Filter text with its translation.
	 *
	 * @since 2.0.11
	 *
	 * @param string $translations Translated text.
	 * @param string $text         Text to translate.
	 * @param string $domain       Text domain. Unique identifier for retrieving translated strings.
	 */
	return apply_filters( 'gettext', $translations, $text, $domain );
}

/**
 * Remove last item on a pipe-delimited string.
 *
 * Meant for removing the last item in a string, such as 'Role name|User role'. The original
 * string will be returned if no pipe '|' characters are found in the string.
 *
 * @since 2.8.0
 *
 * @param string $string A pipe-delimited string.
 * @return string Either $string or everything before the last pipe.
 */
function before_last_bar( $string ) {
	$last_bar = strrpos( $string, '|' );
	if ( false == $last_bar )
		return $string;
	else
		return substr( $string, 0, $last_bar );
}

/**
 * Retrieve the translation of $text in the context defined in $context.
 *
 * If there is no translation, or the text domain isn't loaded the original
 * text is returned.
 *
 * @since 2.8.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text on success, original text on failure.
 */
function translate_with_gettext_context( $text, $context, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translations = $translations->translate( $text, $context );
	/**
	 * Filter text with its translation based on context information.
	 *
	 * @since 2.8.0
	 *
	 * @param string $translations Translated text.
	 * @param string $text         Text to translate.
	 * @param string $context      Context information for the translators.
	 * @param string $domain       Text domain. Unique identifier for retrieving translated strings.
	 */
	return apply_filters( 'gettext_with_context', $translations, $text, $context, $domain );
}

/**
 * Retrieve the translation of $text. If there is no translation,
 * or the text domain isn't loaded, the original text is returned.
 *
 * @since 2.1.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text.
 */
function __( $text, $domain = 'default' ) {
	return translate( $text, $domain );
}

/**
 * Retrieve the translation of $text and escapes it for safe use in an attribute.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text on success, original text on failure.
 */
function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( translate( $text, $domain ) );
}

/**
 * Retrieve the translation of $text and escapes it for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text
 */
function esc_html__( $text, $domain = 'default' ) {
	return esc_html( translate( $text, $domain ) );
}

/**
 * Display translated text.
 *
 * @since 1.2.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 */
function _e( $text, $domain = 'default' ) {
	echo translate( $text, $domain );
}

/**
 * Display translated text that has been escaped for safe use in an attribute.
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 */
function esc_attr_e( $text, $domain = 'default' ) {
	echo esc_attr( translate( $text, $domain ) );
}

/**
 * Display translated text that has been escaped for safe use in HTML output.
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 */
function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html( translate( $text, $domain ) );
}

/**
 * Retrieve translated string with gettext context.
 *
 * Quite a few times, there will be collisions with similar translatable text
 * found in more than two places, but with different translated context.
 *
 * By including the context in the pot file, translators can translate the two
 * strings differently.
 *
 * @since 2.8.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated context string without pipe.
 */
function _x( $text, $context, $domain = 'default' ) {
	return translate_with_gettext_context( $text, $context, $domain );
}

/**
 * Display translated string with gettext context.
 *
 * @since 3.0.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated context string without pipe.
 */
function _ex( $text, $context, $domain = 'default' ) {
	echo _x( $text, $context, $domain );
}

/**
 * Translate string with gettext context, and escapes it for safe use in an attribute.
 *
 * @since 2.8.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text
 */
function esc_attr_x( $text, $context, $domain = 'default' ) {
	return esc_attr( translate_with_gettext_context( $text, $context, $domain ) );
}

/**
 * Translate string with gettext context, and escapes it for safe use in HTML output.
 *
 * @since 2.9.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Translated text.
 */
function esc_html_x( $text, $context, $domain = 'default' ) {
	return esc_html( translate_with_gettext_context( $text, $context, $domain ) );
}

/**
 * Retrieve the plural or single form based on the supplied amount.
 *
 * If the text domain is not set in the $l10n list, then a comparison will be made
 * and either $plural or $single parameters returned.
 *
 * If the text domain does exist, then the parameters $single, $plural, and $number
 * will first be passed to the text domain's ngettext method. Then it will be passed
 * to the 'ngettext' filter hook along with the same parameters. The expected
 * type will be a string.
 *
 * @since 2.8.0
 *
 * @param string $single The text that will be used if $number is 1.
 * @param string $plural The text that will be used if $number is not 1.
 * @param int    $number The number to compare against to use either $single or $plural.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Either $single or $plural translated text.
 */
function _n( $single, $plural, $number, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation = $translations->translate_plural( $single, $plural, $number );
	/**
	 * Filter text with its translation when plural option is available.
	 *
	 * @since 2.2.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text that will be used if $number is 1.
	 * @param string $plural      The text that will be used if $number is not 1.
	 * @param string $number      The number to compare against to use either $single or $plural.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	return apply_filters( 'ngettext', $translation, $single, $plural, $number, $domain );
}

/**
 * Retrieve the plural or single form based on the supplied amount with gettext context.
 *
 * This is a hybrid of _n() and _x(). It supports contexts and plurals.
 *
 * @since 2.8.0
 *
 * @param string $single  The text that will be used if $number is 1.
 * @param string $plural  The text that will be used if $number is not 1.
 * @param int    $number  The number to compare against to use either $single or $plural.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return string Either $single or $plural translated text with context.
 */
function _nx($single, $plural, $number, $context, $domain = 'default') {
	$translations = get_translations_for_domain( $domain );
	$translation = $translations->translate_plural( $single, $plural, $number, $context );
	/**
	 * Filter text with its translation while plural option and context are available.
	 *
	 * @since 2.8.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text that will be used if $number is 1.
	 * @param string $plural      The text that will be used if $number is not 1.
	 * @param string $number      The number to compare against to use either $single or $plural.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	return apply_filters( 'ngettext_with_context', $translation, $single, $plural, $number, $context, $domain );
}

/**
 * Register plural strings in POT file, but don't translate them.
 *
 * Used when you want to keep structures with translatable plural
 * strings and use them later.
 *
 * Example:
 * <code>
 * $messages = array(
 *  	'post' => _n_noop('%s post', '%s posts'),
 *  	'page' => _n_noop('%s pages', '%s pages')
 * );
 * ...
 * $message = $messages[$type];
 * $usable_text = sprintf( translate_nooped_plural( $message, $count ), $count );
 * </code>
 *
 * @since 2.5.0
 *
 * @param string $singular Single form to be i18ned.
 * @param string $plural   Plural form to be i18ned.
 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
 * @return array array($singular, $plural)
 */
function _n_noop( $singular, $plural, $domain = null ) {
	return array( 0 => $singular, 1 => $plural, 'singular' => $singular, 'plural' => $plural, 'context' => null, 'domain' => $domain );
}

/**
 * Register plural strings with context in POT file, but don't translate them.
 *
 * @since 2.8.0
 */
function _nx_noop( $singular, $plural, $context, $domain = null ) {
	return array( 0 => $singular, 1 => $plural, 2 => $context, 'singular' => $singular, 'plural' => $plural, 'context' => $context, 'domain' => $domain );
}

/**
 * Translate the result of _n_noop() or _nx_noop().
 *
 * @since 3.1.0
 *
 * @param array  $nooped_plural Array with singular, plural and context keys, usually the result of _n_noop() or _nx_noop()
 * @param int    $count         Number of objects
 * @param string $domain        Optional. Text domain. Unique identifier for retrieving translated strings. If $nooped_plural contains
 *                              a text domain passed to _n_noop() or _nx_noop(), it will override this value.
 * @return string Either $single or $plural translated text.
 */
function translate_nooped_plural( $nooped_plural, $count, $domain = 'default' ) {
	if ( $nooped_plural['domain'] )
		$domain = $nooped_plural['domain'];

	if ( $nooped_plural['context'] )
		return _nx( $nooped_plural['singular'], $nooped_plural['plural'], $count, $nooped_plural['context'], $domain );
	else
		return _n( $nooped_plural['singular'], $nooped_plural['plural'], $count, $domain );
}

/**
 * Load a .mo file into the text domain $domain.
 *
 * If the text domain already exists, the translations will be merged. If both
 * sets have the same string, the translation from the original value will be taken.
 *
 * On success, the .mo file will be placed in the $l10n global by $domain
 * and will be a MO object.
 *
 * @since 1.5.0
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $mofile Path to the .mo file.
 * @return bool True on success, false on failure.
 */
function load_textdomain( $domain, $mofile ) {
	global $l10n;

	/**
	 * Filter text domain and/or MO file path for loading translations.
	 *
	 * @since 2.9.0
	 *
	 * @param bool   $override Whether to override the text domain. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile   Path to the MO file.
	 */
	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );

	if ( true == $plugin_override ) {
		return true;
	}

	/**
	 * Fires before the MO translation file is loaded.
	 *
	 * @since 2.9.0
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile Path to the .mo file.
	 */
	do_action( 'load_textdomain', $domain, $mofile );

	/**
	 * Filter MO file path for loading translations for a specific text domain.
	 *
	 * @since 2.9.0
	 *
	 * @param string $mofile Path to the MO file.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$mofile = apply_filters( 'load_textdomain_mofile', $mofile, $domain );

	if ( !is_readable( $mofile ) ) return false;

	$mo = new MO();
	if ( !$mo->import_from_file( $mofile ) ) return false;

	if ( isset( $l10n[$domain] ) )
		$mo->merge_with( $l10n[$domain] );

	$l10n[$domain] = &$mo;

	return true;
}

/**
 * Unload translations for a text domain.
 *
 * @since 3.0.0
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool Whether textdomain was unloaded.
 */
function unload_textdomain( $domain ) {
	global $l10n;

	/**
	 * Filter the text domain for loading translation.
	 *
	 * @since 3.0.0
	 *
	 * @param bool   $override Whether to override unloading the text domain. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 */
	$plugin_override = apply_filters( 'override_unload_textdomain', false, $domain );

	if ( $plugin_override )
		return true;

	/**
	 * Fires before the text domain is unloaded.
	 *
	 * @since 3.0.0
	 *
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	do_action( 'unload_textdomain', $domain );

	if ( isset( $l10n[$domain] ) ) {
		unset( $l10n[$domain] );
		return true;
	}

	return false;
}

/**
 * Load default translated strings based on locale.
 *
 * Loads the .mo file in WP_LANG_DIR constant path from WordPress root.
 * The translated (.mo) file is named based on the locale.
 *
 * @see load_textdomain()
 *
 * @since 1.5.0
 *
 * @param string $locale Optional. Locale to load. Defaults to get_locale().
 */
function load_default_textdomain( $locale = null ) {
	if ( null === $locale ) {
		$locale = get_locale();
	}

	// Unload previously loaded strings so we can switch translations.
	unload_textdomain( 'default' );

	$return = load_textdomain( 'default', WP_LANG_DIR . "/$locale.mo" );

	if ( ( is_multisite() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK ) ) && ! file_exists(  WP_LANG_DIR . "/admin-$locale.mo" ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/ms-$locale.mo" );
		return $return;
	}

	if ( is_admin() || defined( 'WP_INSTALLING' ) || ( defined( 'WP_REPAIRING' ) && WP_REPAIRING ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" );
	}

	if ( is_network_admin() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK ) )
		load_textdomain( 'default', WP_LANG_DIR . "/admin-network-$locale.mo" );

	return $return;
}

/**
 * Load a plugin's translated strings.
 *
 * If the path is not given then it will be the root of the plugin directory.
 *
 * The .mo file should be named based on the text domain with a dash, and then the locale exactly.
 *
 * @since 1.5.0
 *
 * @param string $domain          Unique identifier for retrieving translated strings
 * @param string $deprecated      Use the $plugin_rel_path parameter instead.
 * @param string $plugin_rel_path Optional. Relative path to WP_PLUGIN_DIR where the .mo file resides.
 *                                Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) {
	$locale = get_locale();
	/**
	 * Filter a plugin's locale.
	 *
	 * @since 3.0.0
	 *
	 * @param string $locale The plugin's current locale.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$locale = apply_filters( 'plugin_locale', $locale, $domain );

	if ( false !== $plugin_rel_path	) {
		$path = WP_PLUGIN_DIR . '/' . trim( $plugin_rel_path, '/' );
	} else if ( false !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '2.7' );
		$path = ABSPATH . trim( $deprecated, '/' );
	} else {
		$path = WP_PLUGIN_DIR;
	}

	// Load the textdomain according to the plugin first
	$mofile = $domain . '-' . $locale . '.mo';
	if ( $loaded = load_textdomain( $domain, $path . '/'. $mofile ) )
		return $loaded;

	// Otherwise, load from the languages directory
	$mofile = WP_LANG_DIR . '/plugins/' . $mofile;
	return load_textdomain( $domain, $mofile );
}

/**
 * Load the translated strings for a plugin residing in the mu-plugins directory.
 *
 * @since 3.0.0
 *
 * @param string $domain             Text domain. Unique identifier for retrieving translated strings.
 * @param string $mu_plugin_rel_path Relative to WPMU_PLUGIN_DIR directory in which the .mo file resides.
 *                                   Default empty string.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_muplugin_textdomain( $domain, $mu_plugin_rel_path = '' ) {
	/** This filter is documented in wp-includes/l10n.php */
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	$path = trailingslashit( WPMU_PLUGIN_DIR . '/' . ltrim( $mu_plugin_rel_path, '/' ) );

	// Load the textdomain according to the plugin first
	$mofile = $domain . '-' . $locale . '.mo';
	if ( $loaded = load_textdomain( $domain, $path . $mofile ) )
		return $loaded;

	// Otherwise, load from the languages directory
	$mofile = WP_LANG_DIR . '/plugins/' . $mofile;
	return load_textdomain( $domain, $mofile );
}

/**
 * Load the theme's translated strings.
 *
 * If the current locale exists as a .mo file in the theme's root directory, it
 * will be included in the translated strings by the $domain.
 *
 * The .mo files must be named based on the locale exactly.
 *
 * @since 1.5.0
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $path   Optional. Path to the directory containing the .mo file.
 *                       Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_theme_textdomain( $domain, $path = false ) {
	$locale = get_locale();
	/**
	 * Filter a theme's locale.
	 *
	 * @since 3.0.0
	 *
	 * @param string $locale The theme's current locale.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$locale = apply_filters( 'theme_locale', $locale, $domain );

	if ( ! $path )
		$path = get_template_directory();

	// Load the textdomain according to the theme
	$mofile = "{$path}/{$locale}.mo";
	if ( $loaded = load_textdomain( $domain, $mofile ) )
		return $loaded;

	// Otherwise, load from the languages directory
	$mofile = WP_LANG_DIR . "/themes/{$domain}-{$locale}.mo";
	return load_textdomain( $domain, $mofile );
}

/**
 * Load the child themes translated strings.
 *
 * If the current locale exists as a .mo file in the child themes
 * root directory, it will be included in the translated strings by the $domain.
 *
 * The .mo files must be named based on the locale exactly.
 *
 * @since 2.9.0
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $path   Optional. Path to the directory containing the .mo file.
 *                       Default false.
 * @return bool True when the theme textdomain is successfully loaded, false otherwise.
 */
function load_child_theme_textdomain( $domain, $path = false ) {
	if ( ! $path )
		$path = get_stylesheet_directory();
	return load_theme_textdomain( $domain, $path );
}

/**
 * Return the Translations instance for a text domain.
 *
 * If there isn't one, returns empty Translations instance.
 *
 * @since 2.8.0
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return Translations A Translations instance.
 */
function get_translations_for_domain( $domain ) {
	global $l10n;
	if ( !isset( $l10n[$domain] ) ) {
		$l10n[$domain] = new NOOP_Translations;
	}
	return $l10n[$domain];
}

/**
 * Whether there are translations for the text domain.
 *
 * @since 3.0.0
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool Whether there are translations.
 */
function is_textdomain_loaded( $domain ) {
	global $l10n;
	return isset( $l10n[$domain] );
}

/**
 * Translates role name.
 *
 * Since the role names are in the database and not in the source there
 * are dummy gettext calls to get them into the POT file and this function
 * properly translates them back.
 *
 * The before_last_bar() call is needed, because older installs keep the roles
 * using the old context format: 'Role name|User role' and just skipping the
 * content after the last bar is easier than fixing them in the DB. New installs
 * won't suffer from that problem.
 *
 * @since 2.8.0
 *
 * @param string $name The role name.
 * @return string Translated role name on success, original name on failure.
 */
function translate_user_role( $name ) {
	return translate_with_gettext_context( before_last_bar($name), 'User role' );
}

/**
 * Get all available languages based on the presence of *.mo files in a given directory.
 *
 * The default directory is WP_LANG_DIR.
 *
 * @since 3.0.0
 *
 * @param string $dir A directory to search for language files.
 *                    Default WP_LANG_DIR.
 * @return array An array of language codes or an empty array if no languages are present. Language codes are formed by stripping the .mo extension from the language file names.
 */
function get_available_languages( $dir = null ) {
	$languages = array();

	foreach( (array)glob( ( is_null( $dir) ? WP_LANG_DIR : $dir ) . '/*.mo' ) as $lang_file ) {
		$lang_file = basename($lang_file, '.mo');
		if ( 0 !== strpos( $lang_file, 'continents-cities' ) && 0 !== strpos( $lang_file, 'ms-' ) &&
			0 !== strpos( $lang_file, 'admin-' ))
			$languages[] = $lang_file;
	}

	return $languages;
}

/**
 * Get installed translations.
 *
 * Looks in the wp-content/languages directory for translations of
 * plugins or themes.
 *
 * @since 3.7.0
 *
 * @param string $type What to search for. Accepts 'plugins', 'themes', 'core'.
 * @return array Array of language data.
 */
function wp_get_installed_translations( $type ) {
	if ( $type !== 'themes' && $type !== 'plugins' && $type !== 'core' )
		return array();

	$dir = 'core' === $type ? '' : "/$type";

	if ( ! is_dir( WP_LANG_DIR ) )
		return array();

	if ( $dir && ! is_dir( WP_LANG_DIR . $dir ) )
		return array();

	$files = scandir( WP_LANG_DIR . $dir );
	if ( ! $files )
		return array();

	$language_data = array();

	foreach ( $files as $file ) {
		if ( '.' === $file[0] || is_dir( $file ) )
			continue;
		if ( substr( $file, -3 ) !== '.po' )
			continue;
		if ( ! preg_match( '/(?:(.+)-)?([A-Za-z_]{2,6}).po/', $file, $match ) )
			continue;

		list( , $textdomain, $language ) = $match;
		if ( '' === $textdomain )
			$textdomain = 'default';
		$language_data[ $textdomain ][ $language ] = wp_get_pomo_file_data( WP_LANG_DIR . "$dir/$file" );
	}
	return $language_data;
}

/**
 * Extract headers from a PO file.
 *
 * @since 3.7.0
 *
 * @param string $po_file Path to PO file.
 * @return array PO file headers.
 */
function wp_get_pomo_file_data( $po_file ) {
	$headers = get_file_data( $po_file, array(
		'POT-Creation-Date'  => '"POT-Creation-Date',
		'PO-Revision-Date'   => '"PO-Revision-Date',
		'Project-Id-Version' => '"Project-Id-Version',
		'X-Generator'        => '"X-Generator',
	) );
	foreach ( $headers as $header => $value ) {
		// Remove possible contextual '\n' and closing double quote.
		$headers[ $header ] = preg_replace( '~(\\\n)?"$~', '', $value );
	}
	return $headers;
}

/**
 * Language selector.
 *
 * @since 4.0.0
 *
 * @see get_available_languages()
 * @see wp_get_available_translations()
 *
 * @param array $args Optional arguments. Default empty array.
 */
function wp_dropdown_languages( $args = array() ) {
	require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

	$args = wp_parse_args( $args, array(
		'id'        => '',
		'name'      => '',
		'languages' => array(),
		'selected'  => ''
	) );

	if ( empty( $args['languages'] ) ) {
		return false;
	}

	$translations = wp_get_available_translations();

	/*
	 * $args['languages'] should only contain the locales. Find the locale in
	 * $translations to get the native name. Fall back to locale.
	 */
	$languages = array();
	foreach ( $args['languages'] as $locale ) {
		if ( isset( $translations[ $locale ] ) ) {
			$translation = $translations[ $locale ];
			$languages[] = array(
				'language'    => $translation['language'],
				'native_name' => $translation['native_name'],
				'lang'        => $translation['iso'][1],
			);
		} else {
			$languages[] = array(
				'language'    => $locale,
				'native_name' => $locale,
				'lang'        => '',
			);
		}
	}

	printf( '<select name="%s" id="%s">', esc_attr( $args['name'] ), esc_attr( $args['id'] ) );

	// List installed languages.
	echo '<option value="" lang="en">English (United States)</option>';
	foreach ( $languages as $language ) {
		$selected = selected( $language['language'], $args['selected'], false );
		printf(
			'<option value="%s" lang="%s"%s>%s</option>',
			esc_attr( $language['language'] ),
			esc_attr( $language['lang'] ),
			$selected,
			esc_html( $language['native_name'] )
		);
	}

	echo '</select>';
}
