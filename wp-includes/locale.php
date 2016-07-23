<?php
/**
 * Locale API
 *
 * @package WordPress
 * @subpackage i18n
 * @since 1.2.0
 */

/** WP_Locale class */
require_once ABSPATH . WPINC . '/class-wp-locale.php';

/**
 * Checks if current locale is RTL.
 *
 * @since 3.0.0
 *
 * @global WP_Locale $wp_locale
 *
 * @return bool Whether locale is RTL.
 */
function is_rtl() {
	global $wp_locale;
	return $wp_locale->is_rtl();
}
