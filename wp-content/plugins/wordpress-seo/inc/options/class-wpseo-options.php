<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals\Options
 */

/**
 * Overall Option Management class.
 *
 * Instantiates all the options and offers a number of utility methods to work with the options.
 */
class WPSEO_Options {

	/**
	 * The option values.
	 *
	 * @var array|null
	 */
	protected static $option_values = null;

	/**
	 * Options this class uses.
	 *
	 * @var array Array format: (string) option_name  => (string) name of concrete class for the option.
	 */
	public static $options = [
		'wpseo'               => 'WPSEO_Option_Wpseo',
		'wpseo_titles'        => 'WPSEO_Option_Titles',
		'wpseo_social'        => 'WPSEO_Option_Social',
		'wpseo_ms'            => 'WPSEO_Option_MS',
		'wpseo_taxonomy_meta' => 'WPSEO_Taxonomy_Meta',
		'wpseo_llmstxt'       => 'WPSEO_Option_Llmstxt',
	];

	/**
	 * Array of instantiated option objects.
	 *
	 * @var array
	 */
	protected static $option_instances = [];

	/**
	 * Array with the option names.
	 *
	 * @var array
	 */
	protected static $option_names = [];

	/**
	 * Instance of this class.
	 *
	 * @var WPSEO_Options
	 */
	protected static $instance;

	/**
	 * Instantiate all the WPSEO option management classes.
	 */
	protected function __construct() {
		$this->register_hooks();

		foreach ( static::$options as $option_class ) {
			static::register_option( call_user_func( [ $option_class, 'get_instance' ] ) );
		}
	}

	/**
	 * Register our hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'registered_taxonomy', [ $this, 'clear_cache' ] );
		add_action( 'unregistered_taxonomy', [ $this, 'clear_cache' ] );
		add_action( 'registered_post_type', [ $this, 'clear_cache' ] );
		add_action( 'unregistered_post_type', [ $this, 'clear_cache' ] );
	}

	/**
	 * Get the singleton instance of this class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( static::$instance instanceof self ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Registers an option to the options list.
	 *
	 * @param WPSEO_Option $option_instance Instance of the option.
	 *
	 * @return void
	 */
	public static function register_option( WPSEO_Option $option_instance ) {
		$option_name = $option_instance->get_option_name();

		if ( $option_instance->multisite_only && ! static::is_multisite() ) {
			unset( static::$options[ $option_name ], static::$option_names[ $option_name ] );

			return;
		}

		$is_already_registered = array_key_exists( $option_name, static::$options );
		if ( ! $is_already_registered ) {
			static::$options[ $option_name ] = get_class( $option_instance );
		}

		if ( $option_instance->include_in_all === true ) {
			static::$option_names[ $option_name ] = $option_name;
		}

		static::$option_instances[ $option_name ] = $option_instance;

		if ( ! $is_already_registered ) {
			static::clear_cache();
		}
	}

	/**
	 * Get the group name of an option for use in the settings form.
	 *
	 * @param string $option_name The option for which you want to retrieve the option group name.
	 *
	 * @return string|bool
	 */
	public static function get_group_name( $option_name ) {
		if ( isset( static::$option_instances[ $option_name ] ) ) {
			return static::$option_instances[ $option_name ]->group_name;
		}

		return false;
	}

	/**
	 * Get a specific default value for an option.
	 *
	 * @param string $option_name The option for which you want to retrieve a default.
	 * @param string $key         The key within the option who's default you want.
	 *
	 * @return mixed
	 */
	public static function get_default( $option_name, $key ) {
		if ( isset( static::$option_instances[ $option_name ] ) ) {
			$defaults = static::$option_instances[ $option_name ]->get_defaults();
			if ( isset( $defaults[ $key ] ) ) {
				return $defaults[ $key ];
			}
		}

		return null;
	}

	/**
	 * Update a site_option.
	 *
	 * @param string $option_name The option name of the option to save.
	 * @param mixed  $value       The new value for the option.
	 *
	 * @return bool
	 */
	public static function update_site_option( $option_name, $value ) {
		if ( is_multisite() && isset( static::$option_instances[ $option_name ] ) ) {
			return static::$option_instances[ $option_name ]->update_site_option( $value );
		}

		return false;
	}

	/**
	 * Get the instantiated option instance.
	 *
	 * @param string $option_name The option for which you want to retrieve the instance.
	 *
	 * @return object|bool
	 */
	public static function get_option_instance( $option_name ) {
		if ( isset( static::$option_instances[ $option_name ] ) ) {
			return static::$option_instances[ $option_name ];
		}

		return false;
	}

	/**
	 * Retrieve an array of the options which should be included in get_all() and reset().
	 *
	 * @return array Array of option names.
	 */
	public static function get_option_names() {
		$option_names = array_values( static::$option_names );
		if ( $option_names === [] ) {
			foreach ( static::$option_instances as $option_name => $option_object ) {
				if ( $option_object->include_in_all === true ) {
					$option_names[] = $option_name;
				}
			}
		}

		/**
		 * Filter: wpseo_options - Allow developers to change the option name to include.
		 *
		 * @param array $option_names The option names to include in get_all and reset().
		 */
		return apply_filters( 'wpseo_options', $option_names );
	}

	/**
	 * Retrieve all the options for the SEO plugin in one go.
	 *
	 * @param array<string> $specific_options The option groups of the option you want to get.
	 *
	 * @return array Array combining the values of all the options.
	 */
	public static function get_all( $specific_options = [] ) {
		$option_names          = ( empty( $specific_options ) ) ? static::get_option_names() : $specific_options;
		static::$option_values = static::get_options( $option_names );

		return static::$option_values;
	}

	/**
	 * Retrieve one or more options for the SEO plugin.
	 *
	 * @param array $option_names An array of option names of the options you want to get.
	 *
	 * @return array Array combining the values of the requested options.
	 */
	public static function get_options( array $option_names ) {
		$options      = [];
		$option_names = array_filter( $option_names, 'is_string' );
		foreach ( $option_names as $option_name ) {
			if ( isset( static::$option_instances[ $option_name ] ) ) {
				$option = static::get_option( $option_name );

				if ( $option !== null ) {
					$options = array_merge( $options, $option );
				}
			}
		}

		return $options;
	}

	/**
	 * Retrieve a single option for the SEO plugin.
	 *
	 * @param string $option_name The name of the option you want to get.
	 *
	 * @return array Array containing the requested option.
	 */
	public static function get_option( $option_name ) {
		$option = null;
		if ( is_string( $option_name ) && ! empty( $option_name ) ) {
			if ( isset( static::$option_instances[ $option_name ] ) ) {
				if ( static::$option_instances[ $option_name ]->multisite_only !== true ) {
					$option = get_option( $option_name );
				}
				else {
					$option = get_site_option( $option_name );
				}
			}
		}

		return $option;
	}

	/**
	 * Retrieve a single field from any option for the SEO plugin. Keys are always unique.
	 *
	 * @param string        $key           The key it should return.
	 * @param mixed         $default_value The default value that should be returned if the key isn't set.
	 * @param array<string> $option_groups The option groups to retrieve the option from.
	 *
	 * @return mixed Returns value if found, $default_value if not.
	 */
	public static function get( $key, $default_value = null, $option_groups = [] ) {
		if ( ! isset( static::$option_values[ $key ] ) ) {
			static::prime_cache( $option_groups );
		}
		if ( isset( static::$option_values[ $key ] ) ) {
			return static::$option_values[ $key ];
		}

		return $default_value;
	}

	/**
	 * Resets the cache to null.
	 *
	 * @return void
	 */
	public static function clear_cache() {
		static::$option_values = null;
	}

	/**
	 * Primes our cache.
	 *
	 * @param array<string> $option_groups The option groups to prime the cache with.
	 *
	 * @return void
	 */
	private static function prime_cache( $option_groups = [] ) {
		static::$option_values = static::get_all( $option_groups );
		static::$option_values = static::add_ms_option( static::$option_values );
	}

	/**
	 * Retrieve a single field from an option for the SEO plugin.
	 *
	 * @param string $key          The key to set.
	 * @param mixed  $value        The value to set.
	 * @param string $option_group The lookup table which represents the option_group where the key is stored.
	 *
	 * @return mixed|null Returns value if found, $default if not.
	 */
	public static function set( $key, $value, $option_group = '' ) {
		$lookup_table = static::get_lookup_table( $option_group );

		if ( isset( $lookup_table[ $key ] ) ) {
			return static::save_option( $lookup_table[ $key ], $key, $value );
		}

		$patterns = static::get_pattern_table();
		foreach ( $patterns as $pattern => $option ) {
			if ( strpos( $key, $pattern ) === 0 ) {
				return static::save_option( $option, $key, $value );
			}
		}

		static::$option_values[ $key ] = $value;
	}

	/**
	 * Get an option only if it's been auto-loaded.
	 *
	 * @param string $option        The option to retrieve.
	 * @param mixed  $default_value A default value to return.
	 *
	 * @return mixed
	 */
	public static function get_autoloaded_option( $option, $default_value = false ) {
		$value = wp_cache_get( $option, 'options' );
		if ( $value === false ) {
			$passed_default = func_num_args() > 1;

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Using WP native filter.
			return apply_filters( "default_option_{$option}", $default_value, $option, $passed_default );
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Using WP native filter.
		return apply_filters( "option_{$option}", maybe_unserialize( $value ), $option );
	}

	/**
	 * Run the clean up routine for one or all options.
	 *
	 * @param array|string|null $option_name     Optional. the option you want to clean or an array of
	 *                                           option names for the options you want to clean.
	 *                                           If not set, all options will be cleaned.
	 * @param string|null       $current_version Optional. Version from which to upgrade, if not set,
	 *                                           version specific upgrades will be disregarded.
	 *
	 * @return void
	 */
	public static function clean_up( $option_name = null, $current_version = null ) {
		if ( isset( $option_name ) && is_string( $option_name ) && $option_name !== '' ) {
			if ( isset( static::$option_instances[ $option_name ] ) ) {
				static::$option_instances[ $option_name ]->clean( $current_version );
			}
		}
		elseif ( isset( $option_name ) && is_array( $option_name ) && $option_name !== [] ) {
			foreach ( $option_name as $option ) {
				if ( isset( static::$option_instances[ $option ] ) ) {
					static::$option_instances[ $option ]->clean( $current_version );
				}
			}
			unset( $option );
		}
		else {
			foreach ( static::$option_instances as $instance ) {
				$instance->clean( $current_version );
			}
			unset( $instance );

			// If we've done a full clean-up, we can safely remove this really old option.
			delete_option( 'wpseo_indexation' );
		}
	}

	/**
	 * Check that all options exist in the database and add any which don't.
	 *
	 * @return void
	 */
	public static function ensure_options_exist() {
		foreach ( static::$option_instances as $instance ) {
			$instance->maybe_add_option();
		}
	}

	/**
	 * Initialize some options on first install/activate/reset.
	 *
	 * @return void
	 */
	public static function initialize() {
		/* Force WooThemes to use Yoast SEO data. */
		if ( function_exists( 'woo_version_init' ) ) {
			update_option( 'seo_woo_use_third_party_data', 'true' );
		}
	}

	/**
	 * Reset all options to their default values and rerun some tests.
	 *
	 * @return void
	 */
	public static function reset() {
		if ( ! is_multisite() ) {
			$option_names = static::get_option_names();
			if ( is_array( $option_names ) && $option_names !== [] ) {
				foreach ( $option_names as $option_name ) {
					delete_option( $option_name );
					update_option( $option_name, get_option( $option_name ) );
				}
			}
			unset( $option_names );
		}
		else {
			// Reset MS blog based on network default blog setting.
			static::reset_ms_blog( get_current_blog_id() );
		}

		static::initialize();
	}

	/**
	 * Initialize default values for a new multisite blog.
	 *
	 * @param bool $force_init Whether to always do the initialization routine (title/desc test).
	 *
	 * @return void
	 */
	public static function maybe_set_multisite_defaults( $force_init = false ) {
		$option = get_option( 'wpseo' );

		if ( is_multisite() ) {
			if ( $option['ms_defaults_set'] === false ) {
				static::reset_ms_blog( get_current_blog_id() );
				static::initialize();
			}
			elseif ( $force_init === true ) {
				static::initialize();
			}
		}
	}

	/**
	 * Reset all options for a specific multisite blog to their default values based upon a
	 * specified default blog if one was chosen on the network page or the plugin defaults if it was not.
	 *
	 * @param int|string $blog_id Blog id of the blog for which to reset the options.
	 *
	 * @return void
	 */
	public static function reset_ms_blog( $blog_id ) {
		if ( is_multisite() ) {
			$options      = get_site_option( 'wpseo_ms' );
			$option_names = static::get_option_names();

			if ( is_array( $option_names ) && $option_names !== [] ) {
				$base_blog_id = $blog_id;
				if ( $options['defaultblog'] !== '' && $options['defaultblog'] !== 0 ) {
					$base_blog_id = $options['defaultblog'];
				}

				foreach ( $option_names as $option_name ) {
					delete_blog_option( $blog_id, $option_name );

					$new_option = get_blog_option( $base_blog_id, $option_name );

					/* Remove sensitive, theme dependent and site dependent info. */
					if ( isset( static::$option_instances[ $option_name ] ) && static::$option_instances[ $option_name ]->ms_exclude !== [] ) {
						foreach ( static::$option_instances[ $option_name ]->ms_exclude as $key ) {
							unset( $new_option[ $key ] );
						}
					}

					if ( $option_name === 'wpseo' ) {
						$new_option['ms_defaults_set'] = true;
					}

					update_blog_option( $blog_id, $option_name, $new_option );
				}
			}
		}
	}

	/**
	 * Saves the option to the database.
	 *
	 * @param string $wpseo_options_group_name The name for the wpseo option group in the database.
	 * @param string $option_name              The name for the option to set.
	 * @param mixed  $option_value             The value for the option.
	 *
	 * @return bool Returns true if the option is successfully saved in the database.
	 */
	public static function save_option( $wpseo_options_group_name, $option_name, $option_value ) {
		$options                 = static::get_option( $wpseo_options_group_name );
		$options[ $option_name ] = $option_value;

		if ( isset( static::$option_instances[ $wpseo_options_group_name ] ) && static::$option_instances[ $wpseo_options_group_name ]->multisite_only === true ) {
			static::update_site_option( $wpseo_options_group_name, $options );
		}
		else {
			update_option( $wpseo_options_group_name, $options );
		}

		// Check if everything got saved properly.
		$saved_option = static::get_option( $wpseo_options_group_name );

		// Clear our cache.
		static::clear_cache();

		return $saved_option[ $option_name ] === $options[ $option_name ];
	}

	/**
	 * Adds the multisite options to the option stack if relevant.
	 *
	 * @param array $option The currently present options settings.
	 *
	 * @return array Options possibly including multisite.
	 */
	protected static function add_ms_option( $option ) {
		if ( ! is_multisite() ) {
			return $option;
		}

		$ms_option = static::get_option( 'wpseo_ms' );
		if ( $ms_option === null ) {
			return $option;
		}

		return array_merge( $option, $ms_option );
	}

	/**
	 * Checks if installation is multisite.
	 *
	 * @return bool True when is multisite.
	 */
	protected static function is_multisite() {
		static $is_multisite;

		if ( $is_multisite === null ) {
			$is_multisite = is_multisite();
		}

		return $is_multisite;
	}

	/**
	 * Retrieves a lookup table to find in which option_group a key is stored.
	 *
	 * @param string $option_group The option_group where the key is stored.
	 *
	 * @return array The lookup table.
	 */
	private static function get_lookup_table( $option_group = '' ) {
		$lookup_table  = [];
		$option_groups = ( $option_group === '' ) ? static::$options : [ $option_group => static::$options[ $option_group ] ];

		foreach ( array_keys( $option_groups ) as $option_name ) {
			$full_option = static::get_option( $option_name );
			foreach ( $full_option as $key => $value ) {
				$lookup_table[ $key ] = $option_name;
			}
		}

		return $lookup_table;
	}

	/**
	 * Retrieves a lookup table to find in which option_group a key is stored.
	 *
	 * @return array The lookup table.
	 */
	private static function get_pattern_table() {
		$pattern_table = [];
		foreach ( static::$options as $option_name => $option_class ) {
			$instance = call_user_func( [ $option_class, 'get_instance' ] );
			foreach ( $instance->get_patterns() as $key ) {
				$pattern_table[ $key ] = $option_name;
			}
		}

		return $pattern_table;
	}
}
