<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Network
 */

/**
 * Implements a network settings API for the plugin's multisite settings.
 */
class Yoast_Network_Settings_API {

	/**
	 * Registered network settings.
	 *
	 * @var array
	 */
	private $registered_settings = [];

	/**
	 * Options whitelist, keyed by option group.
	 *
	 * @var array
	 */
	private $whitelist_options = [];

	/**
	 * The singleton instance of this class.
	 *
	 * @var Yoast_Network_Settings_API|null
	 */
	private static $instance = null;

	/**
	 * Registers a network setting and its data.
	 *
	 * @param string $option_group The group the network option is part of.
	 * @param string $option_name  The name of the network option to sanitize and save.
	 * @param array  $args         {
	 *     Optional. Data used to describe the network setting when registered.
	 *
	 *     @type callable $sanitize_callback A callback function that sanitizes the network option's value.
	 *     @type mixed    $default           Default value when calling `get_network_option()`.
	 * }
	 *
	 * @return void
	 */
	public function register_setting( $option_group, $option_name, $args = [] ) {

		$defaults = [
			'group'             => $option_group,
			'sanitize_callback' => null,
		];
		$args     = wp_parse_args( $args, $defaults );

		if ( ! isset( $this->whitelist_options[ $option_group ] ) ) {
			$this->whitelist_options[ $option_group ] = [];
		}

		$this->whitelist_options[ $option_group ][] = $option_name;

		if ( ! empty( $args['sanitize_callback'] ) ) {
			add_filter( "sanitize_option_{$option_name}", [ $this, 'filter_sanitize_option' ], 10, 2 );
		}

		if ( array_key_exists( 'default', $args ) ) {
			add_filter( "default_site_option_{$option_name}", [ $this, 'filter_default_option' ], 10, 2 );
		}

		$this->registered_settings[ $option_name ] = $args;
	}

	/**
	 * Gets the registered settings and their data.
	 *
	 * @return array Array of $option_name => $data pairs.
	 */
	public function get_registered_settings() {
		return $this->registered_settings;
	}

	/**
	 * Gets the whitelisted options for a given option group.
	 *
	 * @param string $option_group Option group.
	 *
	 * @return array List of option names, or empty array if unknown option group.
	 */
	public function get_whitelist_options( $option_group ) {
		if ( ! isset( $this->whitelist_options[ $option_group ] ) ) {
			return [];
		}

		return $this->whitelist_options[ $option_group ];
	}

	/**
	 * Filters sanitization for a network option value.
	 *
	 * This method is added as a filter to `sanitize_option_{$option}` for network options that are
	 * registered with a sanitize callback.
	 *
	 * @param string $value  The sanitized option value.
	 * @param string $option The option name.
	 *
	 * @return string The filtered sanitized option value.
	 */
	public function filter_sanitize_option( $value, $option ) {

		if ( empty( $this->registered_settings[ $option ] ) ) {
			return $value;
		}

		return call_user_func( $this->registered_settings[ $option ]['sanitize_callback'], $value );
	}

	/**
	 * Filters the default value for a network option.
	 *
	 * This function is added as a filter to `default_site_option_{$option}` for network options that
	 * are registered with a default.
	 *
	 * @param mixed  $default_value Existing default value to return.
	 * @param string $option        The option name.
	 *
	 * @return mixed The filtered default value.
	 */
	public function filter_default_option( $default_value, $option ) {

		// If a default value was manually passed to the function, allow it to override.
		if ( $default_value !== false ) {
			return $default_value;
		}

		if ( empty( $this->registered_settings[ $option ] ) ) {
			return $default_value;
		}

		return $this->registered_settings[ $option ]['default'];
	}

	/**
	 * Checks whether the requirements to use this class are met.
	 *
	 * @return bool True if requirements are met, false otherwise.
	 */
	public function meets_requirements() {
		return is_multisite();
	}

	/**
	 * Gets the singleton instance of this class.
	 *
	 * @return Yoast_Network_Settings_API The singleton instance.
	 */
	public static function get() {

		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
