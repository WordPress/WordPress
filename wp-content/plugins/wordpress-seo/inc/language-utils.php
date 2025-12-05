<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals
 * @since   5.9.0
 */

/**
 * Group of language utility methods for use by WPSEO.
 * All methods are static, this is just a sort of namespacing class wrapper.
 */
class WPSEO_Language_Utils {

	/**
	 * Returns the language part of a given locale, defaults to english when the $locale is empty.
	 *
	 * @param string|null $locale The locale to get the language of.
	 *
	 * @return string The language part of the locale.
	 */
	public static function get_language( $locale = null ) {
		$language = 'en';

		if ( empty( $locale ) || ! is_string( $locale ) ) {
			return $language;
		}

		$locale_parts = explode( '_', $locale );

		if ( ! empty( $locale_parts[0] ) && ( strlen( $locale_parts[0] ) === 2 || strlen( $locale_parts[0] ) === 3 ) ) {
			$language = $locale_parts[0];
		}

		return $language;
	}

	/**
	 * Returns the full name for the sites' language.
	 *
	 * @return string The language name.
	 */
	public static function get_site_language_name() {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';

		$translations = wp_get_available_translations();
		$locale       = get_locale();
		$language     = isset( $translations[ $locale ] ) ? $translations[ $locale ]['native_name'] : 'English (US)';

		return $language;
	}
}
