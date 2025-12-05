<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor settings validations.
 *
 * Elementor settings validations handler class is responsible for validating settings
 * fields.
 *
 * @since 1.0.0
 */
class Settings_Validations {

	/**
	 * Validate HTML field.
	 *
	 * Sanitize content for allowed HTML tags and remove backslashes before quotes.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $input Input field.
	 *
	 * @return string Input field.
	 */
	public static function html( $input ) {
		return stripslashes( wp_filter_post_kses( addslashes( $input ) ) );
	}

	/**
	 * Validate checkbox list.
	 *
	 * Make sure that an empty checkbox list field will return an array.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param mixed $input Input field.
	 *
	 * @return mixed Input field.
	 */
	public static function checkbox_list( $input ) {
		if ( empty( $input ) ) {
			$input = [];
		}

		return $input;
	}

	/**
	 * Current Time
	 *
	 * Used to return current time
	 *
	 * @since 2.5.0
	 * @access public
	 * @static
	 *
	 * @param mixed $input Input field.
	 *
	 * @return int
	 */
	public static function current_time( $input ) {
		return time();
	}

	/**
	 * Clear cache.
	 *
	 * Delete post meta containing the post CSS file data. And delete the actual
	 * CSS files from the upload directory.
	 *
	 * @since 1.4.8
	 * @access public
	 * @static
	 *
	 * @param mixed $input Input field.
	 *
	 * @return mixed Input field.
	 */
	public static function clear_cache( $input ) {
		Plugin::$instance->files_manager->clear_cache();

		return $input;
	}
}
