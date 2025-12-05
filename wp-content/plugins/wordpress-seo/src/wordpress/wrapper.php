<?php

namespace Yoast\WP\SEO\WordPress;

use wpdb;
use WPSEO_Addon_Manager;
use WPSEO_Admin_Asset_Manager;
use WPSEO_Replace_Vars;
use WPSEO_Shortlinker;
use WPSEO_Utils;

/**
 * Wrapper class for WordPress globals.
 *
 * This consists of factory functions to inject WP globals into the dependency container.
 */
class Wrapper {

	/**
	 * Wrapper method for returning the wpdb object for use in dependency injection.
	 *
	 * @return wpdb The wpdb global.
	 */
	public static function get_wpdb() {
		global $wpdb;

		return $wpdb;
	}

	/**
	 * Factory function for replace vars helper.
	 *
	 * @return WPSEO_Replace_Vars The replace vars helper.
	 */
	public static function get_replace_vars() {
		return new WPSEO_Replace_Vars();
	}

	/**
	 * Factory function for the admin asset manager.
	 *
	 * @return WPSEO_Admin_Asset_Manager The admin asset manager.
	 */
	public static function get_admin_asset_manager() {
		return new WPSEO_Admin_Asset_Manager();
	}

	/**
	 * Factory function for the addon manager.
	 *
	 * @return WPSEO_Addon_Manager The addon manager.
	 */
	public static function get_addon_manager() {
		return new WPSEO_Addon_Manager();
	}

	/**
	 * Factory function for the shortlinker.
	 *
	 * @return WPSEO_Shortlinker
	 */
	public static function get_shortlinker() {
		return new WPSEO_Shortlinker();
	}

	/**
	 * Factory function for the utils class.
	 *
	 * @return WPSEO_Utils
	 */
	public static function get_utils() {
		return new WPSEO_Utils();
	}
}
