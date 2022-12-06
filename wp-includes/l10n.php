<?php
/**
 * Core Translation API
 *
 * @package WordPress
 * @subpackage i18n
 * @since 1.2.0
 */

/**
 * Retrieves the current locale.
 *
 * If the locale is set, then it will filter the locale in the {@see 'locale'}
 * filter hook and return the value.
 *
 * If the locale is not set already, then the WPLANG constant is used if it is
 * defined. Then it is filtered through the {@see 'locale'} filter hook and
 * the value for the locale global set and the locale is returned.
 *
 * The process to get the locale should only be done once, but the locale will
 * always be filtered using the {@see 'locale'} hook.
 *
 * @since 1.5.0
 *
 * @global string $locale           The current locale.
 * @global string $wp_local_package Locale code of the package.
 *
 * @return string The locale of the blog or from the {@see 'locale'} hook.
 */
function get_locale() {
	global $locale, $wp_local_package;

	if ( isset( $locale ) ) {
		/** This filter is documented in wp-includes/l10n.php */
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
		if ( wp_installing() ) {
			$ms_locale = get_site_option( 'WPLANG' );
		} else {
			$ms_locale = get_option( 'WPLANG' );
			if ( false === $ms_locale ) {
				$ms_locale = get_site_option( 'WPLANG' );
			}
		}

		if ( false !== $ms_locale ) {
			$locale = $ms_locale;
		}
	} else {
		$db_locale = get_option( 'WPLANG' );
		if ( false !== $db_locale ) {
			$locale = $db_locale;
		}
	}

	if ( empty( $locale ) ) {
		$locale = 'en_US';
	}

	/**
	 * Filters the locale ID of the WordPress installation.
	 *
	 * @since 1.5.0
	 *
	 * @param string $locale The locale ID.
	 */
	return apply_filters( 'locale', $locale );
}

/**
 * Retrieves the locale of a user.
 *
 * If the user has a locale set to a non-empty string then it will be
 * returned. Otherwise it returns the locale of get_locale().
 *
 * @since 4.7.0
 *
 * @param int|WP_User $user User's ID or a WP_User object. Defaults to current user.
 * @return string The locale of the user.
 */
function get_user_locale( $user = 0 ) {
	$user_object = false;

	if ( 0 === $user && function_exists( 'wp_get_current_user' ) ) {
		$user_object = wp_get_current_user();
	} elseif ( $user instanceof WP_User ) {
		$user_object = $user;
	} elseif ( $user && is_numeric( $user ) ) {
		$user_object = get_user_by( 'id', $user );
	}

	if ( ! $user_object ) {
		return get_locale();
	}

	$locale = $user_object->locale;

	return $locale ? $locale : get_locale();
}

/**
 * Determines the current locale desired for the request.
 *
 * @since 5.0.0
 *
 * @global string $pagenow The filename of the current screen.
 *
 * @return string The determined locale.
 */
function determine_locale() {
	/**
	 * Filters the locale for the current request prior to the default determination process.
	 *
	 * Using this filter allows to override the default logic, effectively short-circuiting the function.
	 *
	 * @since 5.0.0
	 *
	 * @param string|null $locale The locale to return and short-circuit. Default null.
	 */
	$determined_locale = apply_filters( 'pre_determine_locale', null );

	if ( ! empty( $determined_locale ) && is_string( $determined_locale ) ) {
		return $determined_locale;
	}

	$determined_locale = get_locale();

	if ( is_admin() ) {
		$determined_locale = get_user_locale();
	}

	if ( isset( $_GET['_locale'] ) && 'user' === $_GET['_locale'] && wp_is_json_request() ) {
		$determined_locale = get_user_locale();
	}

	$wp_lang = '';

	if ( ! empty( $_GET['wp_lang'] ) ) {
		$wp_lang = sanitize_text_field( $_GET['wp_lang'] );
	} elseif ( ! empty( $_COOKIE['wp_lang'] ) ) {
		$wp_lang = sanitize_text_field( $_COOKIE['wp_lang'] );
	}

	if ( ! empty( $wp_lang ) && ! empty( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
		$determined_locale = $wp_lang;
	}

	/**
	 * Filters the locale for the current request.
	 *
	 * @since 5.0.0
	 *
	 * @param string $locale The locale.
	 */
	return apply_filters( 'determine_locale', $determined_locale );
}

/**
 * Retrieves the translation of $text.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * *Note:* Don't use translate() directly, use __() or related functions.
 *
 * @since 2.2.0
 * @since 5.5.0 Introduced `gettext-{$domain}` filter.
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text.
 */
function translate( $text, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate( $text );

	/**
	 * Filters text with its translation.
	 *
	 * @since 2.0.11
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'gettext', $translation, $text, $domain );

	/**
	 * Filters text with its translation for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 * @since 5.5.0
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "gettext_{$domain}", $translation, $text, $domain );

	return $translation;
}

/**
 * Removes last item on a pipe-delimited string.
 *
 * Meant for removing the last item in a string, such as 'Role name|User role'. The original
 * string will be returned if no pipe '|' characters are found in the string.
 *
 * @since 2.8.0
 *
 * @param string $text A pipe-delimited string.
 * @return string Either $text or everything before the last pipe.
 */
function before_last_bar( $text ) {
	$last_bar = strrpos( $text, '|' );
	if ( false === $last_bar ) {
		return $text;
	} else {
		return substr( $text, 0, $last_bar );
	}
}

/**
 * Retrieves the translation of $text in the context defined in $context.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * *Note:* Don't use translate_with_gettext_context() directly, use _x() or related functions.
 *
 * @since 2.8.0
 * @since 5.5.0 Introduced `gettext_with_context-{$domain}` filter.
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated text on success, original text on failure.
 */
function translate_with_gettext_context( $text, $context, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate( $text, $context );

	/**
	 * Filters text with its translation based on context information.
	 *
	 * @since 2.8.0
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'gettext_with_context', $translation, $text, $context, $domain );

	/**
	 * Filters text with its translation based on context information for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 * @since 5.5.0
	 *
	 * @param string $translation Translated text.
	 * @param string $text        Text to translate.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "gettext_with_context_{$domain}", $translation, $text, $context, $domain );

	return $translation;
}

/**
 * Retrieves the translation of $text.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * @since 2.1.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text.
 */
function __( $text, $domain = 'default' ) {
	return translate( $text, $domain );
}

/**
 * Retrieves the translation of $text and escapes it for safe use in an attribute.
 *
 * If there is no translation, or the text domain isn't loaded, the original text is returned.
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text on success, original text on failure.
 */
function esc_attr__( $text, $domain = 'default' ) {
	return esc_attr( translate( $text, $domain ) );
}

/**
 * Retrieves the translation of $text and escapes it for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and returned.
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated text.
 */
function esc_html__( $text, $domain = 'default' ) {
	return esc_html( translate( $text, $domain ) );
}

/**
 * Displays translated text.
 *
 * @since 1.2.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 */
function _e( $text, $domain = 'default' ) {
	echo translate( $text, $domain );
}

/**
 * Displays translated text that has been escaped for safe use in an attribute.
 *
 * Encodes `< > & " '` (less than, greater than, ampersand, double quote, single quote).
 * Will never double encode entities.
 *
 * If you need the value for use in PHP, use esc_attr__().
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 */
function esc_attr_e( $text, $domain = 'default' ) {
	echo esc_attr( translate( $text, $domain ) );
}

/**
 * Displays translated text that has been escaped for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and displayed.
 *
 * If you need the value for use in PHP, use esc_html__().
 *
 * @since 2.8.0
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 */
function esc_html_e( $text, $domain = 'default' ) {
	echo esc_html( translate( $text, $domain ) );
}

/**
 * Retrieves translated string with gettext context.
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
 *                        Default 'default'.
 * @return string Translated context string without pipe.
 */
function _x( $text, $context, $domain = 'default' ) {
	return translate_with_gettext_context( $text, $context, $domain );
}

/**
 * Displays translated string with gettext context.
 *
 * @since 3.0.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 */
function _ex( $text, $context, $domain = 'default' ) {
	echo _x( $text, $context, $domain );
}

/**
 * Translates string with gettext context, and escapes it for safe use in an attribute.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and returned.
 *
 * @since 2.8.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated text.
 */
function esc_attr_x( $text, $context, $domain = 'default' ) {
	return esc_attr( translate_with_gettext_context( $text, $context, $domain ) );
}

/**
 * Translates string with gettext context, and escapes it for safe use in HTML output.
 *
 * If there is no translation, or the text domain isn't loaded, the original text
 * is escaped and returned.
 *
 * @since 2.9.0
 *
 * @param string $text    Text to translate.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string Translated text.
 */
function esc_html_x( $text, $context, $domain = 'default' ) {
	return esc_html( translate_with_gettext_context( $text, $context, $domain ) );
}

/**
 * Translates and retrieves the singular or plural form based on the supplied number.
 *
 * Used when you want to use the appropriate form of a string based on whether a
 * number is singular or plural.
 *
 * Example:
 *
 *     printf( _n( '%s person', '%s people', $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 * @since 2.8.0
 * @since 5.5.0 Introduced `ngettext-{$domain}` filter.
 *
 * @param string $single The text to be used if the number is singular.
 * @param string $plural The text to be used if the number is plural.
 * @param int    $number The number to compare against to use either the singular or plural form.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string The translated singular or plural form.
 */
function _n( $single, $plural, $number, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate_plural( $single, $plural, $number );

	/**
	 * Filters the singular or plural form of a string.
	 *
	 * @since 2.2.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param int    $number      The number to compare against to use either the singular or plural form.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'ngettext', $translation, $single, $plural, $number, $domain );

	/**
	 * Filters the singular or plural form of a string for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 * @since 5.5.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param int    $number      The number to compare against to use either the singular or plural form.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "ngettext_{$domain}", $translation, $single, $plural, $number, $domain );

	return $translation;
}

/**
 * Translates and retrieves the singular or plural form based on the supplied number, with gettext context.
 *
 * This is a hybrid of _n() and _x(). It supports context and plurals.
 *
 * Used when you want to use the appropriate form of a string with context based on whether a
 * number is singular or plural.
 *
 * Example of a generic phrase which is disambiguated via the context parameter:
 *
 *     printf( _nx( '%s group', '%s groups', $people, 'group of people', 'text-domain' ), number_format_i18n( $people ) );
 *     printf( _nx( '%s group', '%s groups', $animals, 'group of animals', 'text-domain' ), number_format_i18n( $animals ) );
 *
 * @since 2.8.0
 * @since 5.5.0 Introduced `ngettext_with_context-{$domain}` filter.
 *
 * @param string $single  The text to be used if the number is singular.
 * @param string $plural  The text to be used if the number is plural.
 * @param int    $number  The number to compare against to use either the singular or plural form.
 * @param string $context Context information for the translators.
 * @param string $domain  Optional. Text domain. Unique identifier for retrieving translated strings.
 *                        Default 'default'.
 * @return string The translated singular or plural form.
 */
function _nx( $single, $plural, $number, $context, $domain = 'default' ) {
	$translations = get_translations_for_domain( $domain );
	$translation  = $translations->translate_plural( $single, $plural, $number, $context );

	/**
	 * Filters the singular or plural form of a string with gettext context.
	 *
	 * @since 2.8.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param int    $number      The number to compare against to use either the singular or plural form.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( 'ngettext_with_context', $translation, $single, $plural, $number, $context, $domain );

	/**
	 * Filters the singular or plural form of a string with gettext context for a domain.
	 *
	 * The dynamic portion of the hook name, `$domain`, refers to the text domain.
	 *
	 * @since 5.5.0
	 *
	 * @param string $translation Translated text.
	 * @param string $single      The text to be used if the number is singular.
	 * @param string $plural      The text to be used if the number is plural.
	 * @param int    $number      The number to compare against to use either the singular or plural form.
	 * @param string $context     Context information for the translators.
	 * @param string $domain      Text domain. Unique identifier for retrieving translated strings.
	 */
	$translation = apply_filters( "ngettext_with_context_{$domain}", $translation, $single, $plural, $number, $context, $domain );

	return $translation;
}

/**
 * Registers plural strings in POT file, but does not translate them.
 *
 * Used when you want to keep structures with translatable plural
 * strings and use them later when the number is known.
 *
 * Example:
 *
 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
 *     ...
 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 * @since 2.5.0
 *
 * @param string $singular Singular form to be localized.
 * @param string $plural   Plural form to be localized.
 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
 *                         Default null.
 * @return array {
 *     Array of translation information for the strings.
 *
 *     @type string      $0        Singular form to be localized. No longer used.
 *     @type string      $1        Plural form to be localized. No longer used.
 *     @type string      $singular Singular form to be localized.
 *     @type string      $plural   Plural form to be localized.
 *     @type null        $context  Context information for the translators.
 *     @type string|null $domain   Text domain.
 * }
 */
function _n_noop( $singular, $plural, $domain = null ) {
	return array(
		0          => $singular,
		1          => $plural,
		'singular' => $singular,
		'plural'   => $plural,
		'context'  => null,
		'domain'   => $domain,
	);
}

/**
 * Registers plural strings with gettext context in POT file, but does not translate them.
 *
 * Used when you want to keep structures with translatable plural
 * strings and use them later when the number is known.
 *
 * Example of a generic phrase which is disambiguated via the context parameter:
 *
 *     $messages = array(
 *          'people'  => _nx_noop( '%s group', '%s groups', 'people', 'text-domain' ),
 *          'animals' => _nx_noop( '%s group', '%s groups', 'animals', 'text-domain' ),
 *     );
 *     ...
 *     $message = $messages[ $type ];
 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 * @since 2.8.0
 *
 * @param string $singular Singular form to be localized.
 * @param string $plural   Plural form to be localized.
 * @param string $context  Context information for the translators.
 * @param string $domain   Optional. Text domain. Unique identifier for retrieving translated strings.
 *                         Default null.
 * @return array {
 *     Array of translation information for the strings.
 *
 *     @type string      $0        Singular form to be localized. No longer used.
 *     @type string      $1        Plural form to be localized. No longer used.
 *     @type string      $2        Context information for the translators. No longer used.
 *     @type string      $singular Singular form to be localized.
 *     @type string      $plural   Plural form to be localized.
 *     @type string      $context  Context information for the translators.
 *     @type string|null $domain   Text domain.
 * }
 */
function _nx_noop( $singular, $plural, $context, $domain = null ) {
	return array(
		0          => $singular,
		1          => $plural,
		2          => $context,
		'singular' => $singular,
		'plural'   => $plural,
		'context'  => $context,
		'domain'   => $domain,
	);
}

/**
 * Translates and returns the singular or plural form of a string that's been registered
 * with _n_noop() or _nx_noop().
 *
 * Used when you want to use a translatable plural string once the number is known.
 *
 * Example:
 *
 *     $message = _n_noop( '%s post', '%s posts', 'text-domain' );
 *     ...
 *     printf( translate_nooped_plural( $message, $count, 'text-domain' ), number_format_i18n( $count ) );
 *
 * @since 3.1.0
 *
 * @param array  $nooped_plural {
 *     Array that is usually a return value from _n_noop() or _nx_noop().
 *
 *     @type string      $singular Singular form to be localized.
 *     @type string      $plural   Plural form to be localized.
 *     @type string|null $context  Context information for the translators.
 *     @type string|null $domain   Text domain.
 * }
 * @param int    $count         Number of objects.
 * @param string $domain        Optional. Text domain. Unique identifier for retrieving translated strings. If $nooped_plural contains
 *                              a text domain passed to _n_noop() or _nx_noop(), it will override this value. Default 'default'.
 * @return string Either $singular or $plural translated text.
 */
function translate_nooped_plural( $nooped_plural, $count, $domain = 'default' ) {
	if ( $nooped_plural['domain'] ) {
		$domain = $nooped_plural['domain'];
	}

	if ( $nooped_plural['context'] ) {
		return _nx( $nooped_plural['singular'], $nooped_plural['plural'], $count, $nooped_plural['context'], $domain );
	} else {
		return _n( $nooped_plural['singular'], $nooped_plural['plural'], $count, $domain );
	}
}

/**
 * Loads a .mo file into the text domain $domain.
 *
 * If the text domain already exists, the translations will be merged. If both
 * sets have the same string, the translation from the original value will be taken.
 *
 * On success, the .mo file will be placed in the $l10n global by $domain
 * and will be a MO object.
 *
 * @since 1.5.0
 * @since 6.1.0 Added the `$locale` parameter.
 *
 * @global MO[]                   $l10n                   An array of all currently loaded text domains.
 * @global MO[]                   $l10n_unloaded          An array of all text domains that have been unloaded again.
 * @global WP_Textdomain_Registry $wp_textdomain_registry WordPress Textdomain Registry.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $mofile Path to the .mo file.
 * @param string $locale Optional. Locale. Default is the current locale.
 * @return bool True on success, false on failure.
 */
function load_textdomain( $domain, $mofile, $locale = null ) {
	/** @var WP_Textdomain_Registry $wp_textdomain_registry */
	global $l10n, $l10n_unloaded, $wp_textdomain_registry;

	$l10n_unloaded = (array) $l10n_unloaded;

	/**
	 * Filters whether to override the .mo file loading.
	 *
	 * @since 2.9.0
	 *
	 * @param bool   $override Whether to override the .mo file loading. Default false.
	 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
	 * @param string $mofile   Path to the MO file.
	 */
	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );

	if ( true === (bool) $plugin_override ) {
		unset( $l10n_unloaded[ $domain ] );

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
	 * Filters MO file path for loading translations for a specific text domain.
	 *
	 * @since 2.9.0
	 *
	 * @param string $mofile Path to the MO file.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$mofile = apply_filters( 'load_textdomain_mofile', $mofile, $domain );

	if ( ! is_readable( $mofile ) ) {
		return false;
	}

	if ( ! $locale ) {
		$locale = determine_locale();
	}

	$mo = new MO();
	if ( ! $mo->import_from_file( $mofile ) ) {
		$wp_textdomain_registry->set( $domain, $locale, false );

		return false;
	}

	if ( isset( $l10n[ $domain ] ) ) {
		$mo->merge_with( $l10n[ $domain ] );
	}

	unset( $l10n_unloaded[ $domain ] );

	$l10n[ $domain ] = &$mo;

	$wp_textdomain_registry->set( $domain, $locale, dirname( $mofile ) );

	return true;
}

/**
 * Unloads translations for a text domain.
 *
 * @since 3.0.0
 * @since 6.1.0 Added the `$reloadable` parameter.
 *
 * @global MO[] $l10n          An array of all currently loaded text domains.
 * @global MO[] $l10n_unloaded An array of all text domains that have been unloaded again.
 *
 * @param string $domain     Text domain. Unique identifier for retrieving translated strings.
 * @param bool   $reloadable Whether the text domain can be loaded just-in-time again.
 * @return bool Whether textdomain was unloaded.
 */
function unload_textdomain( $domain, $reloadable = false ) {
	global $l10n, $l10n_unloaded;

	$l10n_unloaded = (array) $l10n_unloaded;

	/**
	 * Filters whether to override the text domain unloading.
	 *
	 * @since 3.0.0
	 * @since 6.1.0 Added the `$reloadable` parameter.
	 *
	 * @param bool   $override   Whether to override the text domain unloading. Default false.
	 * @param string $domain     Text domain. Unique identifier for retrieving translated strings.
	 * @param bool   $reloadable Whether the text domain can be loaded just-in-time again.
	 */
	$plugin_override = apply_filters( 'override_unload_textdomain', false, $domain, $reloadable );

	if ( $plugin_override ) {
		if ( ! $reloadable ) {
			$l10n_unloaded[ $domain ] = true;
		}

		return true;
	}

	/**
	 * Fires before the text domain is unloaded.
	 *
	 * @since 3.0.0
	 * @since 6.1.0 Added the `$reloadable` parameter.
	 *
	 * @param string $domain     Text domain. Unique identifier for retrieving translated strings.
	 * @param bool   $reloadable Whether the text domain can be loaded just-in-time again.
	 */
	do_action( 'unload_textdomain', $domain, $reloadable );

	if ( isset( $l10n[ $domain ] ) ) {
		unset( $l10n[ $domain ] );

		if ( ! $reloadable ) {
			$l10n_unloaded[ $domain ] = true;
		}

		return true;
	}

	return false;
}

/**
 * Loads default translated strings based on locale.
 *
 * Loads the .mo file in WP_LANG_DIR constant path from WordPress root.
 * The translated (.mo) file is named based on the locale.
 *
 * @see load_textdomain()
 *
 * @since 1.5.0
 *
 * @param string $locale Optional. Locale to load. Default is the value of get_locale().
 * @return bool Whether the textdomain was loaded.
 */
function load_default_textdomain( $locale = null ) {
	if ( null === $locale ) {
		$locale = determine_locale();
	}

	// Unload previously loaded strings so we can switch translations.
	unload_textdomain( 'default' );

	$return = load_textdomain( 'default', WP_LANG_DIR . "/$locale.mo", $locale );

	if ( ( is_multisite() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK ) ) && ! file_exists( WP_LANG_DIR . "/admin-$locale.mo" ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/ms-$locale.mo", $locale );
		return $return;
	}

	if ( is_admin() || wp_installing() || ( defined( 'WP_REPAIRING' ) && WP_REPAIRING ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo", $locale );
	}

	if ( is_network_admin() || ( defined( 'WP_INSTALLING_NETWORK' ) && WP_INSTALLING_NETWORK ) ) {
		load_textdomain( 'default', WP_LANG_DIR . "/admin-network-$locale.mo", $locale );
	}

	return $return;
}

/**
 * Loads a plugin's translated strings.
 *
 * If the path is not given then it will be the root of the plugin directory.
 *
 * The .mo file should be named based on the text domain with a dash, and then the locale exactly.
 *
 * @since 1.5.0
 * @since 4.6.0 The function now tries to load the .mo file from the languages directory first.
 *
 * @param string       $domain          Unique identifier for retrieving translated strings
 * @param string|false $deprecated      Optional. Deprecated. Use the $plugin_rel_path parameter instead.
 *                                      Default false.
 * @param string|false $plugin_rel_path Optional. Relative path to WP_PLUGIN_DIR where the .mo file resides.
 *                                      Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) {
	/** @var WP_Textdomain_Registry $wp_textdomain_registry */
	global $wp_textdomain_registry;

	/**
	 * Filters a plugin's locale.
	 *
	 * @since 3.0.0
	 *
	 * @param string $locale The plugin's current locale.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );

	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile, $locale ) ) {
		return true;
	}

	if ( false !== $plugin_rel_path ) {
		$path = WP_PLUGIN_DIR . '/' . trim( $plugin_rel_path, '/' );
	} elseif ( false !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '2.7.0' );
		$path = ABSPATH . trim( $deprecated, '/' );
	} else {
		$path = WP_PLUGIN_DIR;
	}

	$wp_textdomain_registry->set_custom_path( $domain, $path );

	return load_textdomain( $domain, $path . '/' . $mofile, $locale );
}

/**
 * Loads the translated strings for a plugin residing in the mu-plugins directory.
 *
 * @since 3.0.0
 * @since 4.6.0 The function now tries to load the .mo file from the languages directory first.
 *
 * @global WP_Textdomain_Registry $wp_textdomain_registry WordPress Textdomain Registry.
 *
 * @param string $domain             Text domain. Unique identifier for retrieving translated strings.
 * @param string $mu_plugin_rel_path Optional. Relative to `WPMU_PLUGIN_DIR` directory in which the .mo
 *                                   file resides. Default empty string.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_muplugin_textdomain( $domain, $mu_plugin_rel_path = '' ) {
	/** @var WP_Textdomain_Registry $wp_textdomain_registry */
	global $wp_textdomain_registry;

	/** This filter is documented in wp-includes/l10n.php */
	$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );

	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile, $locale ) ) {
		return true;
	}

	$path = WPMU_PLUGIN_DIR . '/' . ltrim( $mu_plugin_rel_path, '/' );

	$wp_textdomain_registry->set_custom_path( $domain, $path );

	return load_textdomain( $domain, $path . '/' . $mofile, $locale );
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
 * @since 4.6.0 The function now tries to load the .mo file from the languages directory first.
 *
 * @global WP_Textdomain_Registry $wp_textdomain_registry WordPress Textdomain Registry.
 *
 * @param string       $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string|false $path   Optional. Path to the directory containing the .mo file.
 *                             Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_theme_textdomain( $domain, $path = false ) {
	/** @var WP_Textdomain_Registry $wp_textdomain_registry */
	global $wp_textdomain_registry;

	/**
	 * Filters a theme's locale.
	 *
	 * @since 3.0.0
	 *
	 * @param string $locale The theme's current locale.
	 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
	 */
	$locale = apply_filters( 'theme_locale', determine_locale(), $domain );

	$mofile = $domain . '-' . $locale . '.mo';

	// Try to load from the languages directory first.
	if ( load_textdomain( $domain, WP_LANG_DIR . '/themes/' . $mofile, $locale ) ) {
		return true;
	}

	if ( ! $path ) {
		$path = get_template_directory();
	}

	$wp_textdomain_registry->set_custom_path( $domain, $path );

	return load_textdomain( $domain, $path . '/' . $locale . '.mo', $locale );
}

/**
 * Loads the child theme's translated strings.
 *
 * If the current locale exists as a .mo file in the child theme's
 * root directory, it will be included in the translated strings by the $domain.
 *
 * The .mo files must be named based on the locale exactly.
 *
 * @since 2.9.0
 *
 * @param string       $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string|false $path   Optional. Path to the directory containing the .mo file.
 *                             Default false.
 * @return bool True when the theme textdomain is successfully loaded, false otherwise.
 */
function load_child_theme_textdomain( $domain, $path = false ) {
	if ( ! $path ) {
		$path = get_stylesheet_directory();
	}
	return load_theme_textdomain( $domain, $path );
}

/**
 * Loads the script translated strings.
 *
 * @since 5.0.0
 * @since 5.0.2 Uses load_script_translations() to load translation data.
 * @since 5.1.0 The `$domain` parameter was made optional.
 *
 * @see WP_Scripts::set_translations()
 *
 * @param string $handle Name of the script to register a translation domain to.
 * @param string $domain Optional. Text domain. Default 'default'.
 * @param string $path   Optional. The full file path to the directory containing translation files.
 * @return string|false The translated strings in JSON encoding on success,
 *                      false if the script textdomain could not be loaded.
 */
function load_script_textdomain( $handle, $domain = 'default', $path = '' ) {
	$wp_scripts = wp_scripts();

	if ( ! isset( $wp_scripts->registered[ $handle ] ) ) {
		return false;
	}

	$path   = untrailingslashit( $path );
	$locale = determine_locale();

	// If a path was given and the handle file exists simply return it.
	$file_base       = 'default' === $domain ? $locale : $domain . '-' . $locale;
	$handle_filename = $file_base . '-' . $handle . '.json';

	if ( $path ) {
		$translations = load_script_translations( $path . '/' . $handle_filename, $handle, $domain );

		if ( $translations ) {
			return $translations;
		}
	}

	$src = $wp_scripts->registered[ $handle ]->src;

	if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $wp_scripts->content_url && 0 === strpos( $src, $wp_scripts->content_url ) ) ) {
		$src = $wp_scripts->base_url . $src;
	}

	$relative       = false;
	$languages_path = WP_LANG_DIR;

	$src_url     = wp_parse_url( $src );
	$content_url = wp_parse_url( content_url() );
	$plugins_url = wp_parse_url( plugins_url() );
	$site_url    = wp_parse_url( site_url() );

	// If the host is the same or it's a relative URL.
	if (
		( ! isset( $content_url['path'] ) || strpos( $src_url['path'], $content_url['path'] ) === 0 ) &&
		( ! isset( $src_url['host'] ) || ! isset( $content_url['host'] ) || $src_url['host'] === $content_url['host'] )
	) {
		// Make the src relative the specific plugin or theme.
		if ( isset( $content_url['path'] ) ) {
			$relative = substr( $src_url['path'], strlen( $content_url['path'] ) );
		} else {
			$relative = $src_url['path'];
		}
		$relative = trim( $relative, '/' );
		$relative = explode( '/', $relative );

		$languages_path = WP_LANG_DIR . '/' . $relative[0];

		$relative = array_slice( $relative, 2 ); // Remove plugins/<plugin name> or themes/<theme name>.
		$relative = implode( '/', $relative );
	} elseif (
		( ! isset( $plugins_url['path'] ) || strpos( $src_url['path'], $plugins_url['path'] ) === 0 ) &&
		( ! isset( $src_url['host'] ) || ! isset( $plugins_url['host'] ) || $src_url['host'] === $plugins_url['host'] )
	) {
		// Make the src relative the specific plugin.
		if ( isset( $plugins_url['path'] ) ) {
			$relative = substr( $src_url['path'], strlen( $plugins_url['path'] ) );
		} else {
			$relative = $src_url['path'];
		}
		$relative = trim( $relative, '/' );
		$relative = explode( '/', $relative );

		$languages_path = WP_LANG_DIR . '/plugins';

		$relative = array_slice( $relative, 1 ); // Remove <plugin name>.
		$relative = implode( '/', $relative );
	} elseif ( ! isset( $src_url['host'] ) || ! isset( $site_url['host'] ) || $src_url['host'] === $site_url['host'] ) {
		if ( ! isset( $site_url['path'] ) ) {
			$relative = trim( $src_url['path'], '/' );
		} elseif ( ( strpos( $src_url['path'], trailingslashit( $site_url['path'] ) ) === 0 ) ) {
			// Make the src relative to the WP root.
			$relative = substr( $src_url['path'], strlen( $site_url['path'] ) );
			$relative = trim( $relative, '/' );
		}
	}

	/**
	 * Filters the relative path of scripts used for finding translation files.
	 *
	 * @since 5.0.2
	 *
	 * @param string|false $relative The relative path of the script. False if it could not be determined.
	 * @param string       $src      The full source URL of the script.
	 */
	$relative = apply_filters( 'load_script_textdomain_relative_path', $relative, $src );

	// If the source is not from WP.
	if ( false === $relative ) {
		return load_script_translations( false, $handle, $domain );
	}

	// Translations are always based on the unminified filename.
	if ( substr( $relative, -7 ) === '.min.js' ) {
		$relative = substr( $relative, 0, -7 ) . '.js';
	}

	$md5_filename = $file_base . '-' . md5( $relative ) . '.json';

	if ( $path ) {
		$translations = load_script_translations( $path . '/' . $md5_filename, $handle, $domain );

		if ( $translations ) {
			return $translations;
		}
	}

	$translations = load_script_translations( $languages_path . '/' . $md5_filename, $handle, $domain );

	if ( $translations ) {
		return $translations;
	}

	return load_script_translations( false, $handle, $domain );
}

/**
 * Loads the translation data for the given script handle and text domain.
 *
 * @since 5.0.2
 *
 * @param string|false $file   Path to the translation file to load. False if there isn't one.
 * @param string       $handle Name of the script to register a translation domain to.
 * @param string       $domain The text domain.
 * @return string|false The JSON-encoded translated strings for the given script handle and text domain.
 *                      False if there are none.
 */
function load_script_translations( $file, $handle, $domain ) {
	/**
	 * Pre-filters script translations for the given file, script handle and text domain.
	 *
	 * Returning a non-null value allows to override the default logic, effectively short-circuiting the function.
	 *
	 * @since 5.0.2
	 *
	 * @param string|false|null $translations JSON-encoded translation data. Default null.
	 * @param string|false      $file         Path to the translation file to load. False if there isn't one.
	 * @param string            $handle       Name of the script to register a translation domain to.
	 * @param string            $domain       The text domain.
	 */
	$translations = apply_filters( 'pre_load_script_translations', null, $file, $handle, $domain );

	if ( null !== $translations ) {
		return $translations;
	}

	/**
	 * Filters the file path for loading script translations for the given script handle and text domain.
	 *
	 * @since 5.0.2
	 *
	 * @param string|false $file   Path to the translation file to load. False if there isn't one.
	 * @param string       $handle Name of the script to register a translation domain to.
	 * @param string       $domain The text domain.
	 */
	$file = apply_filters( 'load_script_translation_file', $file, $handle, $domain );

	if ( ! $file || ! is_readable( $file ) ) {
		return false;
	}

	$translations = file_get_contents( $file );

	/**
	 * Filters script translations for the given file, script handle and text domain.
	 *
	 * @since 5.0.2
	 *
	 * @param string $translations JSON-encoded translation data.
	 * @param string $file         Path to the translation file that was loaded.
	 * @param string $handle       Name of the script to register a translation domain to.
	 * @param string $domain       The text domain.
	 */
	return apply_filters( 'load_script_translations', $translations, $file, $handle, $domain );
}

/**
 * Loads plugin and theme text domains just-in-time.
 *
 * When a textdomain is encountered for the first time, we try to load
 * the translation file from `wp-content/languages`, removing the need
 * to call load_plugin_textdomain() or load_theme_textdomain().
 *
 * @since 4.6.0
 * @access private
 *
 * @global MO[]                   $l10n_unloaded          An array of all text domains that have been unloaded again.
 * @global WP_Textdomain_Registry $wp_textdomain_registry WordPress Textdomain Registry.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool True when the textdomain is successfully loaded, false otherwise.
 */
function _load_textdomain_just_in_time( $domain ) {
	/** @var WP_Textdomain_Registry $wp_textdomain_registry */
	global $l10n_unloaded, $wp_textdomain_registry;

	$l10n_unloaded = (array) $l10n_unloaded;

	// Short-circuit if domain is 'default' which is reserved for core.
	if ( 'default' === $domain || isset( $l10n_unloaded[ $domain ] ) ) {
		return false;
	}

	if ( ! $wp_textdomain_registry->has( $domain ) ) {
		return false;
	}

	$locale = determine_locale();
	$path   = $wp_textdomain_registry->get( $domain, $locale );
	if ( ! $path ) {
		return false;
	}
	// Themes with their language directory outside of WP_LANG_DIR have a different file name.
	$template_directory   = trailingslashit( get_template_directory() );
	$stylesheet_directory = trailingslashit( get_stylesheet_directory() );
	if ( str_starts_with( $path, $template_directory ) || str_starts_with( $path, $stylesheet_directory ) ) {
		$mofile = "{$path}{$locale}.mo";
	} else {
		$mofile = "{$path}{$domain}-{$locale}.mo";
	}

	return load_textdomain( $domain, $mofile, $locale );
}

/**
 * Returns the Translations instance for a text domain.
 *
 * If there isn't one, returns empty Translations instance.
 *
 * @since 2.8.0
 *
 * @global MO[] $l10n An array of all currently loaded text domains.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return Translations|NOOP_Translations A Translations instance.
 */
function get_translations_for_domain( $domain ) {
	global $l10n;
	if ( isset( $l10n[ $domain ] ) || ( _load_textdomain_just_in_time( $domain ) && isset( $l10n[ $domain ] ) ) ) {
		return $l10n[ $domain ];
	}

	static $noop_translations = null;
	if ( null === $noop_translations ) {
		$noop_translations = new NOOP_Translations();
	}

	return $noop_translations;
}

/**
 * Determines whether there are translations for the text domain.
 *
 * @since 3.0.0
 *
 * @global MO[] $l10n An array of all currently loaded text domains.
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @return bool Whether there are translations.
 */
function is_textdomain_loaded( $domain ) {
	global $l10n;
	return isset( $l10n[ $domain ] );
}

/**
 * Translates role name.
 *
 * Since the role names are in the database and not in the source there
 * are dummy gettext calls to get them into the POT file and this function
 * properly translates them back.
 *
 * The before_last_bar() call is needed, because older installations keep the roles
 * using the old context format: 'Role name|User role' and just skipping the
 * content after the last bar is easier than fixing them in the DB. New installations
 * won't suffer from that problem.
 *
 * @since 2.8.0
 * @since 5.2.0 Added the `$domain` parameter.
 *
 * @param string $name   The role name.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *                       Default 'default'.
 * @return string Translated role name on success, original name on failure.
 */
function translate_user_role( $name, $domain = 'default' ) {
	return translate_with_gettext_context( before_last_bar( $name ), 'User role', $domain );
}

/**
 * Gets all available languages based on the presence of *.mo files in a given directory.
 *
 * The default directory is WP_LANG_DIR.
 *
 * @since 3.0.0
 * @since 4.7.0 The results are now filterable with the {@see 'get_available_languages'} filter.
 *
 * @param string $dir A directory to search for language files.
 *                    Default WP_LANG_DIR.
 * @return string[] An array of language codes or an empty array if no languages are present.
 *                  Language codes are formed by stripping the .mo extension from the language file names.
 */
function get_available_languages( $dir = null ) {
	$languages = array();

	$lang_files = glob( ( is_null( $dir ) ? WP_LANG_DIR : $dir ) . '/*.mo' );
	if ( $lang_files ) {
		foreach ( $lang_files as $lang_file ) {
			$lang_file = basename( $lang_file, '.mo' );
			if ( 0 !== strpos( $lang_file, 'continents-cities' ) && 0 !== strpos( $lang_file, 'ms-' ) &&
				0 !== strpos( $lang_file, 'admin-' ) ) {
				$languages[] = $lang_file;
			}
		}
	}

	/**
	 * Filters the list of available language codes.
	 *
	 * @since 4.7.0
	 *
	 * @param string[] $languages An array of available language codes.
	 * @param string   $dir       The directory where the language files were found.
	 */
	return apply_filters( 'get_available_languages', $languages, $dir );
}

/**
 * Gets installed translations.
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
	if ( 'themes' !== $type && 'plugins' !== $type && 'core' !== $type ) {
		return array();
	}

	$dir = 'core' === $type ? '' : "/$type";

	if ( ! is_dir( WP_LANG_DIR ) ) {
		return array();
	}

	if ( $dir && ! is_dir( WP_LANG_DIR . $dir ) ) {
		return array();
	}

	$files = scandir( WP_LANG_DIR . $dir );
	if ( ! $files ) {
		return array();
	}

	$language_data = array();

	foreach ( $files as $file ) {
		if ( '.' === $file[0] || is_dir( WP_LANG_DIR . "$dir/$file" ) ) {
			continue;
		}
		if ( substr( $file, -3 ) !== '.po' ) {
			continue;
		}
		if ( ! preg_match( '/(?:(.+)-)?([a-z]{2,3}(?:_[A-Z]{2})?(?:_[a-z0-9]+)?).po/', $file, $match ) ) {
			continue;
		}
		if ( ! in_array( substr( $file, 0, -3 ) . '.mo', $files, true ) ) {
			continue;
		}

		list( , $textdomain, $language ) = $match;
		if ( '' === $textdomain ) {
			$textdomain = 'default';
		}
		$language_data[ $textdomain ][ $language ] = wp_get_pomo_file_data( WP_LANG_DIR . "$dir/$file" );
	}
	return $language_data;
}

/**
 * Extracts headers from a PO file.
 *
 * @since 3.7.0
 *
 * @param string $po_file Path to PO file.
 * @return string[] Array of PO file header values keyed by header name.
 */
function wp_get_pomo_file_data( $po_file ) {
	$headers = get_file_data(
		$po_file,
		array(
			'POT-Creation-Date'  => '"POT-Creation-Date',
			'PO-Revision-Date'   => '"PO-Revision-Date',
			'Project-Id-Version' => '"Project-Id-Version',
			'X-Generator'        => '"X-Generator',
		)
	);
	foreach ( $headers as $header => $value ) {
		// Remove possible contextual '\n' and closing double quote.
		$headers[ $header ] = preg_replace( '~(\\\n)?"$~', '', $value );
	}
	return $headers;
}

/**
 * Displays or returns a Language selector.
 *
 * @since 4.0.0
 * @since 4.3.0 Introduced the `echo` argument.
 * @since 4.7.0 Introduced the `show_option_site_default` argument.
 * @since 5.1.0 Introduced the `show_option_en_us` argument.
 * @since 5.9.0 Introduced the `explicit_option_en_us` argument.
 *
 * @see get_available_languages()
 * @see wp_get_available_translations()
 *
 * @param string|array $args {
 *     Optional. Array or string of arguments for outputting the language selector.
 *
 *     @type string   $id                           ID attribute of the select element. Default 'locale'.
 *     @type string   $name                         Name attribute of the select element. Default 'locale'.
 *     @type array    $languages                    List of installed languages, contain only the locales.
 *                                                  Default empty array.
 *     @type array    $translations                 List of available translations. Default result of
 *                                                  wp_get_available_translations().
 *     @type string   $selected                     Language which should be selected. Default empty.
 *     @type bool|int $echo                         Whether to echo the generated markup. Accepts 0, 1, or their
 *                                                  boolean equivalents. Default 1.
 *     @type bool     $show_available_translations  Whether to show available translations. Default true.
 *     @type bool     $show_option_site_default     Whether to show an option to fall back to the site's locale. Default false.
 *     @type bool     $show_option_en_us            Whether to show an option for English (United States). Default true.
 *     @type bool     $explicit_option_en_us        Whether the English (United States) option uses an explicit value of en_US
 *                                                  instead of an empty value. Default false.
 * }
 * @return string HTML dropdown list of languages.
 */
function wp_dropdown_languages( $args = array() ) {

	$parsed_args = wp_parse_args(
		$args,
		array(
			'id'                          => 'locale',
			'name'                        => 'locale',
			'languages'                   => array(),
			'translations'                => array(),
			'selected'                    => '',
			'echo'                        => 1,
			'show_available_translations' => true,
			'show_option_site_default'    => false,
			'show_option_en_us'           => true,
			'explicit_option_en_us'       => false,
		)
	);

	// Bail if no ID or no name.
	if ( ! $parsed_args['id'] || ! $parsed_args['name'] ) {
		return;
	}

	// English (United States) uses an empty string for the value attribute.
	if ( 'en_US' === $parsed_args['selected'] && ! $parsed_args['explicit_option_en_us'] ) {
		$parsed_args['selected'] = '';
	}

	$translations = $parsed_args['translations'];
	if ( empty( $translations ) ) {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		$translations = wp_get_available_translations();
	}

	/*
	 * $parsed_args['languages'] should only contain the locales. Find the locale in
	 * $translations to get the native name. Fall back to locale.
	 */
	$languages = array();
	foreach ( $parsed_args['languages'] as $locale ) {
		if ( isset( $translations[ $locale ] ) ) {
			$translation = $translations[ $locale ];
			$languages[] = array(
				'language'    => $translation['language'],
				'native_name' => $translation['native_name'],
				'lang'        => current( $translation['iso'] ),
			);

			// Remove installed language from available translations.
			unset( $translations[ $locale ] );
		} else {
			$languages[] = array(
				'language'    => $locale,
				'native_name' => $locale,
				'lang'        => '',
			);
		}
	}

	$translations_available = ( ! empty( $translations ) && $parsed_args['show_available_translations'] );

	// Holds the HTML markup.
	$structure = array();

	// List installed languages.
	if ( $translations_available ) {
		$structure[] = '<optgroup label="' . esc_attr_x( 'Installed', 'translations' ) . '">';
	}

	// Site default.
	if ( $parsed_args['show_option_site_default'] ) {
		$structure[] = sprintf(
			'<option value="site-default" data-installed="1"%s>%s</option>',
			selected( 'site-default', $parsed_args['selected'], false ),
			_x( 'Site Default', 'default site language' )
		);
	}

	if ( $parsed_args['show_option_en_us'] ) {
		$value       = ( $parsed_args['explicit_option_en_us'] ) ? 'en_US' : '';
		$structure[] = sprintf(
			'<option value="%s" lang="en" data-installed="1"%s>English (United States)</option>',
			esc_attr( $value ),
			selected( '', $parsed_args['selected'], false )
		);
	}

	// List installed languages.
	foreach ( $languages as $language ) {
		$structure[] = sprintf(
			'<option value="%s" lang="%s"%s data-installed="1">%s</option>',
			esc_attr( $language['language'] ),
			esc_attr( $language['lang'] ),
			selected( $language['language'], $parsed_args['selected'], false ),
			esc_html( $language['native_name'] )
		);
	}
	if ( $translations_available ) {
		$structure[] = '</optgroup>';
	}

	// List available translations.
	if ( $translations_available ) {
		$structure[] = '<optgroup label="' . esc_attr_x( 'Available', 'translations' ) . '">';
		foreach ( $translations as $translation ) {
			$structure[] = sprintf(
				'<option value="%s" lang="%s"%s>%s</option>',
				esc_attr( $translation['language'] ),
				esc_attr( current( $translation['iso'] ) ),
				selected( $translation['language'], $parsed_args['selected'], false ),
				esc_html( $translation['native_name'] )
			);
		}
		$structure[] = '</optgroup>';
	}

	// Combine the output string.
	$output  = sprintf( '<select name="%s" id="%s">', esc_attr( $parsed_args['name'] ), esc_attr( $parsed_args['id'] ) );
	$output .= implode( "\n", $structure );
	$output .= '</select>';

	if ( $parsed_args['echo'] ) {
		echo $output;
	}

	return $output;
}

/**
 * Determines whether the current locale is right-to-left (RTL).
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 3.0.0
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @return bool Whether locale is RTL.
 */
function is_rtl() {
	global $wp_locale;
	if ( ! ( $wp_locale instanceof WP_Locale ) ) {
		return false;
	}
	return $wp_locale->is_rtl();
}

/**
 * Switches the translations according to the given locale.
 *
 * @since 4.7.0
 *
 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
 *
 * @param string $locale The locale.
 * @return bool True on success, false on failure.
 */
function switch_to_locale( $locale ) {
	/* @var WP_Locale_Switcher $wp_locale_switcher */
	global $wp_locale_switcher;

	return $wp_locale_switcher->switch_to_locale( $locale );
}

/**
 * Restores the translations according to the previous locale.
 *
 * @since 4.7.0
 *
 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
 *
 * @return string|false Locale on success, false on error.
 */
function restore_previous_locale() {
	/* @var WP_Locale_Switcher $wp_locale_switcher */
	global $wp_locale_switcher;

	return $wp_locale_switcher->restore_previous_locale();
}

/**
 * Restores the translations according to the original locale.
 *
 * @since 4.7.0
 *
 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
 *
 * @return string|false Locale on success, false on error.
 */
function restore_current_locale() {
	/* @var WP_Locale_Switcher $wp_locale_switcher */
	global $wp_locale_switcher;

	return $wp_locale_switcher->restore_current_locale();
}

/**
 * Determines whether switch_to_locale() is in effect.
 *
 * @since 4.7.0
 *
 * @global WP_Locale_Switcher $wp_locale_switcher WordPress locale switcher object.
 *
 * @return bool True if the locale has been switched, false otherwise.
 */
function is_locale_switched() {
	/* @var WP_Locale_Switcher $wp_locale_switcher */
	global $wp_locale_switcher;

	return $wp_locale_switcher->is_switched();
}

/**
 * Translates the provided settings value using its i18n schema.
 *
 * @since 5.9.0
 * @access private
 *
 * @param string|string[]|array[]|object $i18n_schema I18n schema for the setting.
 * @param string|string[]|array[]        $settings    Value for the settings.
 * @param string                         $textdomain  Textdomain to use with translations.
 *
 * @return string|string[]|array[] Translated settings.
 */
function translate_settings_using_i18n_schema( $i18n_schema, $settings, $textdomain ) {
	if ( empty( $i18n_schema ) || empty( $settings ) || empty( $textdomain ) ) {
		return $settings;
	}

	if ( is_string( $i18n_schema ) && is_string( $settings ) ) {
		return translate_with_gettext_context( $settings, $i18n_schema, $textdomain );
	}
	if ( is_array( $i18n_schema ) && is_array( $settings ) ) {
		$translated_settings = array();
		foreach ( $settings as $value ) {
			$translated_settings[] = translate_settings_using_i18n_schema( $i18n_schema[0], $value, $textdomain );
		}
		return $translated_settings;
	}
	if ( is_object( $i18n_schema ) && is_array( $settings ) ) {
		$group_key           = '*';
		$translated_settings = array();
		foreach ( $settings as $key => $value ) {
			if ( isset( $i18n_schema->$key ) ) {
				$translated_settings[ $key ] = translate_settings_using_i18n_schema( $i18n_schema->$key, $value, $textdomain );
			} elseif ( isset( $i18n_schema->$group_key ) ) {
				$translated_settings[ $key ] = translate_settings_using_i18n_schema( $i18n_schema->$group_key, $value, $textdomain );
			} else {
				$translated_settings[ $key ] = $value;
			}
		}
		return $translated_settings;
	}
	return $settings;
}

/**
 * Retrieves the list item separator based on the locale.
 *
 * @since 6.0.0
 *
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @return string Locale-specific list item separator.
 */
function wp_get_list_item_separator() {
	global $wp_locale;

	return $wp_locale->get_list_item_separator();
}
