<?php

/**
 * WPML Compatibility class
 * Defines some WPML constants
 * Registers strings in a persistent way as done by WPML
 *
 * @since 1.0.2
 */
class PLL_WPML_Compat {
	static protected $instance; // For singleton
	static protected $strings; // Used for cache
	public $api;

	/**
	 * Constructor
	 *
	 * @since 1.0.2
	 */
	protected function __construct() {
		// Load the WPML API
		require_once PLL_MODULES_INC . '/wpml/wpml-legacy-api.php';
		$this->api = new PLL_WPML_API();

		self::$strings = get_option( 'polylang_wpml_strings', array() );

		add_action( 'pll_language_defined', array( $this, 'define_constants' ) );
		add_action( 'pll_no_language_defined', array( $this, 'define_constants' ) );
		add_filter( 'pll_get_strings', array( $this, 'get_strings' ) );
	}

	/**
	 * Access to the single instance of the class
	 *
	 * @since 1.7
	 *
	 * @return object
	 */
	static public function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Defines two WPML constants once the language has been defined
	 * The compatibility with WPML is not perfect on admin side as the constants are defined
	 * in 'setup_theme' by Polylang ( based on user info ) and 'plugins_loaded' by WPML ( based on cookie )
	 *
	 * @since 0.9.5
	 */
	public function define_constants() {
		if ( ! empty( PLL()->curlang ) ) {
			if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
				define( 'ICL_LANGUAGE_CODE', PLL()->curlang->slug );
			}

			if ( ! defined( 'ICL_LANGUAGE_NAME' ) ) {
				define( 'ICL_LANGUAGE_NAME', PLL()->curlang->name );
			}
		} elseif ( ! PLL() instanceof PLL_Frontend ) {
			if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
				define( 'ICL_LANGUAGE_CODE', 'all' );
			}

			if ( ! defined( 'ICL_LANGUAGE_NAME' ) ) {
				define( 'ICL_LANGUAGE_NAME', '' );
			}
		}
	}

	/**
	 * Unlike pll_register_string, icl_register_string stores the string in database
	 * so we need to do the same as some plugins or themes may expect this
	 * we use a serialized option to do this
	 *
	 * @since 1.0.2
	 *
	 * @param string $context the group in which the string is registered, defaults to 'polylang'
	 * @param string $name    a unique name for the string
	 * @param string $string  the string to register
	 */
	public function register_string( $context, $name, $string ) {
		// Registers the string if it does not exist yet (multiline as in WPML)
		$to_register = array( 'context' => $context, 'name' => $name, 'string' => $string, 'multiline' => true, 'icl' => true );
		if ( ! in_array( $to_register, self::$strings ) && $to_register['string'] ) {
			self::$strings[] = $to_register;
			update_option( 'polylang_wpml_strings', self::$strings );
		}
	}

	/**
	 * Removes a string from the registered strings list
	 *
	 * @since 1.0.2
	 *
	 * @param string $context the group in which the string is registered, defaults to 'polylang'
	 * @param string $name    a unique name for the string
	 */
	public function unregister_string( $context, $name ) {
		foreach ( self::$strings as $key => $string ) {
			if ( $string['context'] == $context && $string['name'] == $name ) {
				unset( self::$strings[ $key ] );
				update_option( 'polylang_wpml_strings', self::$strings );
			}
		}
	}

	/**
	 * Adds strings registered by icl_register_string to those registered by pll_register_string
	 *
	 * @since 1.0.2
	 *
	 * @param array $strings existing registered strings
	 * @return array registered strings with added strings through WPML API
	 */
	public function get_strings( $strings ) {
		return empty( self::$strings ) ? $strings : array_merge( $strings, self::$strings );
	}

	/**
	 * Get a registered string by its context and name
	 *
	 * @since 2.0
	 *
	 * @param string $context the group in which the string is registered
	 * @param string $name    a unique name for the string
	 * @return bool|string the registered string, false if none was found
	 */
	public function get_string_by_context_and_name( $context, $name ) {
		foreach ( self::$strings as $string ) {
			if ( $string['context'] == $context && $string['name'] == $name ) {
				return $string['string'];
			}
		}
		return false;
	}
}
