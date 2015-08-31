<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * This abstract class and it's concrete classes implement defaults and value validation for
 * all WPSEO options and subkeys within options.
 *
 * Some guidelines:
 * [Retrieving options]
 * - Use the normal get_option() to retrieve an option. You will receive a complete array for the option.
 *    Any subkeys which were not set, will have their default values in place.
 * - In other words, you will normally not have to check whether a subkey isset() as they will *always* be set.
 *    They will also *always* be of the correct variable type.
 *    The only exception to this are the options with variable option names based on post_type or taxonomy
 *    as those will not always be available before the taxonomy/post_type is registered.
 *    (they will be available if a value was set, they won't be if it wasn't as the class won't know
 *    that a default needs to be injected).
 *    Oh and the very few options where the default value is null, i.e. wpseo->'theme_has_description'
 *
 * [Updating/Adding options]
 * - For multisite site_options, please use the WPSEO_Options::update_site_option() method.
 * - For normal options, use the normal add/update_option() functions. As long a the classes here
 *   are instantiated, validation for all options and their subkeys will be automatic.
 * - On (succesfull) update of a couple of options, certain related actions will be run automatically.
 *    Some examples:
 *      - on change of wpseo[yoast_tracking], the cron schedule will be adjusted accordingly
 *      - on change of wpseo_permalinks and wpseo_xml, the rewrite rules will be flushed
 *      - on change of wpseo and wpseo_title, some caches will be cleared
 *
 *
 * [Important information about add/updating/changing these classes]
 * - Make sure that option array key names are unique across options. The WPSEO_Options::get_all()
 *    method merges most options together. If any of them have non-unique names, even if they
 *    are in a different option, they *will* overwrite each other.
 * - When you add a new array key in an option: make sure you add proper defaults and add the key
 *    to the validation routine in the proper place or add a new validation case.
 *    You don't need to do any upgrading as any option returned will always be merged with the
 *    defaults, so new options will automatically be available.
 *    If the default value is a string which need translating, add this to the concrete class
 *    translate_defaults() method.
 * - When you remove an array key from an option: if it's important that the option is really removed,
 *    add the WPSEO_Option::clean_up( $option_name ) method to the upgrade run.
 *    This will re-save the option and automatically remove the array key no longer in existance.
 * - When you rename a sub-option: add it to the clean_option() routine and run that in the upgrade run.
 * - When you change the default for an option sub-key, make sure you verify that the validation routine will
 *    still work the way it should.
 *    Example: changing a default from '' (empty string) to 'text' with a validation routine with tests
 *    for an empty string will prevent a user from saving an empty string as the real value. So the
 *    test for '' with the validation routine would have to be removed in that case.
 * - If an option needs specific actions different from defined in this abstract class, you can just overrule
 *    a method by defining it in the concrete class.
 *
 * @todo       - [JRF => testers] double check that validation will not cause errors when called
 *               from upgrade routine (some of the WP functions may not yet be available)
 */
abstract class WPSEO_Option {

	/**
	 * @var  string  Option name - MUST be set in concrete class and set to public.
	 */
	protected $option_name;

	/**
	 * @var  string  Option group name for use in settings forms
	 *               - will be set automagically if not set in concrete class
	 *               (i.e. if it confirm to the normal pattern 'yoast' . $option_name . 'options',
	 *               only set in conrete class if it doesn't)
	 */
	public $group_name;

	/**
	 * @var  bool  Whether to include the option in the return for WPSEO_Options::get_all().
	 *             Also determines which options are copied over for ms_(re)set_blog().
	 */
	public $include_in_all = true;

	/**
	 * @var  bool  Whether this option is only for when the install is multisite.
	 */
	public $multisite_only = false;

	/**
	 * @var  array  Array of defaults for the option - MUST be set in concrete class.
	 *              Shouldn't be requested directly, use $this->get_defaults();
	 */
	protected $defaults;

	/**
	 * @var  array  Array of variable option name patterns for the option - if any -
	 *              Set this when the option contains array keys which vary based on post_type
	 *              or taxonomy
	 */
	protected $variable_array_key_patterns;

	/**
	 * @var array  Array of sub-options which should not be overloaded with multi-site defaults
	 */
	public $ms_exclude = array();

	/**
	 * @var  object  Instance of this class
	 */
	protected static $instance;


	/* *********** INSTANTIATION METHODS *********** */

	/**
	 * Add all the actions and filters for the option
	 *
	 * @return \WPSEO_Option
	 */
	protected function __construct() {

		/* Add filters which get applied to the get_options() results */
		$this->add_default_filters(); // Return defaults if option not set.
		$this->add_option_filters(); // Merge with defaults if option *is* set.


		if ( $this->multisite_only !== true ) {
			/**
			 * The option validation routines remove the default filters to prevent failing
			 * to insert an option if it's new. Let's add them back afterwards.
			 */
			add_action( 'add_option', array( $this, 'add_default_filters' ) ); // Adding back after INSERT.

			if ( version_compare( $GLOBALS['wp_version'], '3.7', '!=' ) ) { // Adding back after non-WP 3.7 UPDATE.
				add_action( 'update_option', array( $this, 'add_default_filters' ) );
			}
			else { // Adding back after WP 3.7 UPDATE.
				add_filter( 'pre_update_option_' . $this->option_name, array( $this, 'wp37_add_default_filters' ) );
			}
		}
		else if ( is_multisite() ) {
			/*
			The option validation routines remove the default filters to prevent failing
			   to insert an option if it's new. Let's add them back afterwards.

			   For site_options, this method is not foolproof as these actions are not fired
			   on an insert/update failure. Please use the WPSEO_Options::update_site_option() method
			   for updating site options to make sure the filters are in place.
			*/
			add_action( 'add_site_option_' . $this->option_name, array( $this, 'add_default_filters' ) );
			add_action( 'update_site_option_' . $this->option_name, array( $this, 'add_default_filters' ) );

		}


		/*
		Make sure the option will always get validated, independently of register_setting()
			   (only available on back-end)
		*/
		add_filter( 'sanitize_option_' . $this->option_name, array( $this, 'validate' ) );

		/* Register our option for the admin pages */
		add_action( 'admin_init', array( $this, 'register_setting' ) );


		/* Set option group name if not given */
		if ( ! isset( $this->group_name ) || $this->group_name === '' ) {
			$this->group_name = 'yoast_' . $this->option_name . '_options';
		}

		/* Translate some defaults as early as possible - textdomain is loaded in init on priority 1 */
		if ( method_exists( $this, 'translate_defaults' ) ) {
			add_action( 'init', array( $this, 'translate_defaults' ), 2 );
		}

		/**
		 * Enrich defaults once custom post types and taxonomies have been registered
		 * which is normally done on the init action.
		 *
		 * @todo - [JRF/testers] verify that none of the options which are only available after
		 * enrichment are used before the enriching
		 */
		if ( method_exists( $this, 'enrich_defaults' ) ) {
			add_action( 'init', array( $this, 'enrich_defaults' ), 99 );
		}
	}

// @codingStandardsIgnoreStart

	/**
	 * All concrete classes *must* contain the get_instance method
	 * @internal Unfortunately I can't define it as an abstract as it also *has* to be static....
	 */
	// abstract protected static function get_instance();


	/**
	 * Concrete classes *may* contain a translate_defaults method
	 */
	// abstract public function translate_defaults();


	/**
	 * Concrete classes *may* contain a enrich_defaults method to add additional defaults once
	 * all post_types and taxonomies have been registered
	 */
	// abstract public function enrich_defaults();

// @codingStandardsIgnoreEnd

	/* *********** METHODS INFLUENCING get_option() *********** */

	/**
	 * Add filters to make sure that the option default is returned if the option is not set
	 *
	 * @return  void
	 */
	public function add_default_filters() {
		// Don't change, needs to check for false as could return prio 0 which would evaluate to false.
		if ( has_filter( 'default_option_' . $this->option_name, array( $this, 'get_defaults' ) ) === false ) {
			add_filter( 'default_option_' . $this->option_name, array( $this, 'get_defaults' ) );
		}
	}


	/**
	 * Abusing a filter to re-add our default filters
	 * WP 3.7 specific as update_option action hook was in the wrong place temporarily
	 *
	 * @see http://core.trac.wordpress.org/ticket/25705
	 *
	 * @param   mixed $new_value
	 *
	 * @return  mixed   unchanged value
	 */
	public function wp37_add_default_filters( $new_value ) {
		$this->add_default_filters();

		return $new_value;
	}

	/**
	 * Validate webmaster tools & Pinterest verification strings
	 *
	 * @param string $key
	 * @param array  $dirty
	 * @param array  $old
	 * @param array  $clean (passed by reference).
	 */
	public function validate_verification_string( $key, $dirty, $old, &$clean ) {
		if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
			$meta = $dirty[ $key ];
			if ( strpos( $meta, 'content=' ) ) {
				// Make sure we only have the real key, not a complete meta tag.
				preg_match( '`content=([\'"])?([^\'"> ]+)(?:\1|[ />])`', $meta, $match );
				if ( isset( $match[2] ) ) {
					$meta = $match[2];
				}
				unset( $match );
			}

			$meta = sanitize_text_field( $meta );
			if ( $meta !== '' ) {
				$regex   = '`^[A-Fa-f0-9_-]+$`';
				$service = '';

				switch ( $key ) {
					case 'googleverify':
						$regex   = '`^[A-Za-z0-9_-]+$`';
						$service = 'Google Webmaster tools';
						break;

					case 'msverify':
						$service = 'Bing Webmaster tools';
						break;

					case 'pinterestverify':
						$service = 'Pinterest';
						break;

					case 'yandexverify':
						$service = 'Yandex Webmaster tools';
						break;

					case 'alexaverify':
						$regex   = '`^[A-Za-z0-9_-]{20,}$`';
						$service = 'Alexa ID';
				}

				if ( preg_match( $regex, $meta ) ) {
					$clean[ $key ] = $meta;
				}
				else {
					if ( isset( $old[ $key ] ) && preg_match( $regex, $old[ $key ] ) ) {
						$clean[ $key ] = $old[ $key ];
					}
					if ( function_exists( 'add_settings_error' ) ) {
						add_settings_error(
							$this->group_name, // Slug title of the setting.
							'_' . $key, // Suffix-id for the error message box.
							sprintf( __( '%s does not seem to be a valid %s verification string. Please correct.', 'wordpress-seo' ), '<strong>' . esc_html( $meta ) . '</strong>', $service ), // The error message.
							'error' // Error type, either 'error' or 'updated'.
						);
					}
				}
			}
		}
	}

	/**
	 *
	 *
	 * @param string $key
	 * @param array  $dirty
	 * @param array  $old
	 * @param array  $clean (passed by reference).
	 */
	public function validate_url( $key, $dirty, $old, &$clean ) {
		if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
			$url = WPSEO_Utils::sanitize_url( $dirty[ $key ] );
			if ( $url !== '' ) {
				$clean[ $key ] = $url;
			}
			else {
				if ( isset( $old[ $key ] ) && $old[ $key ] !== '' ) {
					$url = WPSEO_Utils::sanitize_url( $old[ $key ] );
					if ( $url !== '' ) {
						$clean[ $key ] = $url;
					}
				}
				if ( function_exists( 'add_settings_error' ) ) {
					$url = WPSEO_Utils::sanitize_url( $dirty[ $key ] );
					add_settings_error(
						$this->group_name, // Slug title of the setting.
						'_' . $key, // Suffix-id for the error message box.
						sprintf( __( '%s does not seem to be a valid url. Please correct.', 'wordpress-seo' ), '<strong>' . esc_html( $url ) . '</strong>' ), // The error message.
						'error' // Error type, either 'error' or 'updated'.
					);
				}
			}
		}
	}

	/**
	 * Remove the default filters.
	 * Called from the validate() method to prevent failure to add new options
	 *
	 * @return  void
	 */
	public function remove_default_filters() {
		remove_filter( 'default_option_' . $this->option_name, array( $this, 'get_defaults' ) );
	}


	/**
	 * Get the enriched default value for an option
	 *
	 * Checks if the concrete class contains an enrich_defaults() method and if so, runs it.
	 *
	 * @internal the enrich_defaults method is used to set defaults for variable array keys in an option,
	 * such as array keys depending on post_types and/or taxonomies
	 *
	 * @return  array
	 */
	public function get_defaults() {
		if ( method_exists( $this, 'translate_defaults' ) ) {
			$this->translate_defaults();
		}

		if ( method_exists( $this, 'enrich_defaults' ) ) {
			$this->enrich_defaults();
		}

		return apply_filters( 'wpseo_defaults', $this->defaults, $this->option_name );
	}


	/**
	 * Add filters to make sure that the option is merged with its defaults before being returned
	 *
	 * @return  void
	 */
	public function add_option_filters() {
		// Don't change, needs to check for false as could return prio 0 which would evaluate to false.
		if ( has_filter( 'option_' . $this->option_name, array( $this, 'get_option' ) ) === false ) {
			add_filter( 'option_' . $this->option_name, array( $this, 'get_option' ) );
		}
	}


	/**
	 * Remove the option filters.
	 * Called from the clean_up methods to make sure we retrieve the original old option
	 *
	 * @return  void
	 */
	public function remove_option_filters() {
		remove_filter( 'option_' . $this->option_name, array( $this, 'get_option' ) );
	}


	/**
	 * Merge an option with its default values
	 *
	 * This method should *not* be called directly!!! It is only meant to filter the get_option() results
	 *
	 * @param   mixed $options Option value.
	 *
	 * @return  mixed        Option merged with the defaults for that option
	 */
	public function get_option( $options = null ) {
		$filtered = $this->array_filter_merge( $options );

		/*
		If the option contains variable option keys, make sure we don't remove those settings
			   - even if the defaults are not complete yet.
			   Unfortunately this means we also won't be removing the settings for post types or taxonomies
			   which are no longer in the WP install, but rather that than the other way around
		*/
		if ( isset( $this->variable_array_key_patterns ) ) {
			$filtered = $this->retain_variable_keys( $options, $filtered );
		}

		return $filtered;
	}


	/* *********** METHODS influencing add_uption(), update_option() and saving from admin pages *********** */

	/**
	 * Register (whitelist) the option for the configuration pages.
	 * The validation callback is already registered separately on the sanitize_option hook,
	 * so no need to double register.
	 *
	 * @return void
	 */
	public function register_setting() {
		if ( WPSEO_Utils::grant_access() ) {
			register_setting( $this->group_name, $this->option_name );
		}
	}


	/**
	 * Validate the option
	 *
	 * @param  mixed $option_value The unvalidated new value for the option.
	 *
	 * @return  array          Validated new value for the option
	 */
	public function validate( $option_value ) {
		$clean = $this->get_defaults();

		/* Return the defaults if the new value is empty */
		if ( ! is_array( $option_value ) || $option_value === array() ) {
			return $clean;
		}


		$option_value = array_map( array( 'WPSEO_Utils', 'trim_recursive' ), $option_value );
		if ( $this->multisite_only !== true ) {
			$old = get_option( $this->option_name );
		}
		else {
			$old = get_site_option( $this->option_name );
		}
		$clean = $this->validate_option( $option_value, $clean, $old );

		/* Retain the values for variable array keys even when the post type/taxonomy is not yet registered */
		if ( isset( $this->variable_array_key_patterns ) ) {
			$clean = $this->retain_variable_keys( $option_value, $clean );
		}

		$this->remove_default_filters();

		return $clean;
	}


	/**
	 * All concrete classes must contain a validate_option() method which validates all
	 * values within the option
	 *
	 * @param  array $dirty New value for the option.
	 * @param  array $clean Clean value for the option, normally the defaults.
	 * @param  array $old   Old value of the option.
	 */
	abstract protected function validate_option( $dirty, $clean, $old );


	/* *********** METHODS for ADDING/UPDATING/UPGRADING the option *********** */

	/**
	 * Retrieve the real old value (unmerged with defaults)
	 *
	 * @return array|bool the original option value (which can be false if the option doesn't exist)
	 */
	protected function get_original_option() {
		$this->remove_default_filters();
		$this->remove_option_filters();

		// Get (unvalidated) array, NOT merged with defaults.
		if ( $this->multisite_only !== true ) {
			$option_value = get_option( $this->option_name );
		}
		else {
			$option_value = get_site_option( $this->option_name );
		}

		$this->add_option_filters();
		$this->add_default_filters();

		return $option_value;
	}

	/**
	 * Add the option if it doesn't exist for some strange reason
	 *
	 * @uses WPSEO_Option::get_original_option()
	 *
	 * @return void
	 */
	public function maybe_add_option() {
		if ( $this->get_original_option() === false ) {
			if ( $this->multisite_only !== true ) {
				update_option( $this->option_name, $this->get_defaults() );
			}
			else {
				$this->update_site_option( $this->get_defaults() );
			}
		}
	}


	/**
	 * Update a site_option
	 *
	 * @internal This special method is only needed for multisite options, but very needed indeed there.
	 * The order in which certain functions and hooks are run is different between get_option() and
	 * get_site_option() which means in practice that the removing of the default filters would be
	 * done too late and the re-adding of the default filters might not be done at all.
	 * Aka: use the WPSEO_Options::update_site_option() method (which calls this method) for
	 * safely adding/updating multisite options.
	 *
	 * @param mixed $value The new value for the option.
	 *
	 * @return bool whether the update was succesfull
	 */
	public function update_site_option( $value ) {
		if ( $this->multisite_only === true && is_multisite() ) {
			$this->remove_default_filters();
			$result = update_site_option( $this->option_name, $value );
			$this->add_default_filters();

			return $result;
		}
		else {
			return false;
		}
	}


	/**
	 * Retrieve the real old value (unmerged with defaults), clean and re-save the option
	 *
	 * @uses WPSEO_Option::get_original_option()
	 * @uses WPSEO_Option::import()
	 *
	 * @param  string $current_version (optional) Version from which to upgrade, if not set, version specific upgrades will be disregarded.
	 *
	 * @return void
	 */
	public function clean( $current_version = null ) {
		$option_value = $this->get_original_option();
		$this->import( $option_value, $current_version );
	}


	/**
	 * Clean and re-save the option
	 *
	 * @uses clean_option() method from concrete class if it exists
	 *
	 * @todo [JRF/whomever] Figure out a way to show settings error during/after the upgrade - maybe
	 * something along the lines of:
	 * -> add them to a property in this class
	 * -> if that property isset at the end of the routine and add_settings_error function does not exist,
	 *    save as transient (or update the transient if one already exists)
	 * -> next time an admin is in the WP back-end, show the errors and delete the transient or only delete it
	 *    once the admin has dismissed the message (add ajax function)
	 * Important: all validation routines which add_settings_errors would need to be changed for this to work
	 *
	 * @param  array  $option_value          Option value to be imported.
	 * @param  string $current_version       (optional) Version from which to upgrade, if not set, version specific upgrades will be disregarded.
	 * @param  array  $all_old_option_values (optional) Only used when importing old options to have access to the real old values, in contrast to the saved ones.
	 *
	 * @return void
	 */
	public function import( $option_value, $current_version = null, $all_old_option_values = null ) {
		if ( $option_value === false ) {
			$option_value = $this->get_defaults();
		}
		elseif ( is_array( $option_value ) && method_exists( $this, 'clean_option' ) ) {
			$option_value = $this->clean_option( $option_value, $current_version, $all_old_option_values );
		}

		/*
		Save the cleaned value - validation will take care of cleaning out array keys which
			   should no longer be there
		*/
		if ( $this->multisite_only !== true ) {
			update_option( $this->option_name, $option_value );
		}
		else {
			$this->update_site_option( $this->option_name, $option_value );
		}
	}


	/**
	 * Concrete classes *may* contain a clean_option method which will clean out old/renamed
	 * values within the option
	 */
	// abstract public function clean_option( $option_value, $current_version = null, $all_old_option_values = null );
	/* *********** HELPER METHODS for internal use *********** */

	/**
	 * Helper method - Combines a fixed array of default values with an options array
	 * while filtering out any keys which are not in the defaults array.
	 *
	 * @todo [JRF] - shouldn't this be a straight array merge ? at the end of the day, the validation
	 * removes any invalid keys on save
	 *
	 * @param  array $options (Optional) Current options. If not set, the option defaults for the $option_key will be returned.
	 *
	 * @return  array  Combined and filtered options array.
	 */
	protected function array_filter_merge( $options = null ) {

		$defaults = $this->get_defaults();

		if ( ! isset( $options ) || $options === false || $options === array() ) {
			return $defaults;
		}

		$options = (array) $options;

		/*
		$filtered = array();

			if ( $defaults !== array() ) {
				foreach ( $defaults as $key => $default_value ) {
					// @todo should this walk through array subkeys ?
					$filtered[ $key ] = ( isset( $options[ $key ] ) ? $options[ $key ] : $default_value );
				}
			}
		*/
		$filtered = array_merge( $defaults, $options );

		return $filtered;
	}


	/**
	 * Make sure that any set option values relating to post_types and/or taxonomies are retained,
	 * even when that post_type or taxonomy may not yet have been registered.
	 *
	 * @internal The wpseo_titles concrete class overrules this method. Make sure that any changes
	 * applied here, also get ported to that version.
	 *
	 * @param  array $dirty Original option as retrieved from the database.
	 * @param  array $clean Filtered option where any options which shouldn't be in our option
	 *                      have already been removed and any options which weren't set
	 *                      have been set to their defaults.
	 *
	 * @return  array
	 */
	protected function retain_variable_keys( $dirty, $clean ) {
		if ( ( is_array( $this->variable_array_key_patterns ) && $this->variable_array_key_patterns !== array() ) && ( is_array( $dirty ) && $dirty !== array() ) ) {
			foreach ( $dirty as $key => $value ) {

				// Do nothing if already in filtered options.
				if ( isset( $clean[ $key ] ) ) {
					continue;
				}

				foreach ( $this->variable_array_key_patterns as $pattern ) {

					if ( strpos( $key, $pattern ) === 0 ) {
						$clean[ $key ] = $value;
						break;
					}
				}
			}
		}

		return $clean;
	}


	/**
	 * Check whether a given array key conforms to one of the variable array key patterns for this option
	 *
	 * @usedby validate_option() methods for options with variable array keys
	 *
	 * @param  string $key Array key to check.
	 *
	 * @return string      Pattern if it conforms, original array key if it doesn't or if the option
	 *              does not have variable array keys
	 */
	protected function get_switch_key( $key ) {
		if ( ! isset( $this->variable_array_key_patterns ) || ( ! is_array( $this->variable_array_key_patterns ) || $this->variable_array_key_patterns === array() ) ) {
			return $key;
		}

		foreach ( $this->variable_array_key_patterns as $pattern ) {
			if ( strpos( $key, $pattern ) === 0 ) {
				return $pattern;
			}
		}

		return $key;
	}


	/* *********** DEPRECATED METHODS *********** */

	/**
	 * Emulate the WP native sanitize_text_field function in a %%variable%% safe way
	 *
	 * @see        https://core.trac.wordpress.org/browser/trunk/src/wp-includes/formatting.php for the original
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::sanitize_text_field()
	 * @see        WPSEO_Utils::sanitize_text_field()
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function sanitize_text_field( $value ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::sanitize_text_field()' );

		return WPSEO_Utils::sanitize_text_field( $value );
	}


	/**
	 * Sanitize a url for saving to the database
	 * Not to be confused with the old native WP function
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::sanitize_url()
	 * @see        WPSEO_Utils::sanitize_url()
	 *
	 * @param  string $value
	 * @param  array  $allowed_protocols
	 *
	 * @return  string
	 */
	public static function sanitize_url( $value, $allowed_protocols = array( 'http', 'https' ) ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::sanitize_url()' );

		return WPSEO_Utils::sanitize_url( $value, $allowed_protocols );
	}

	/**
	 * Validate a value as boolean
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::validate_bool()
	 * @see        WPSEO_Utils::validate_bool()
	 *
	 * @static
	 *
	 * @param  mixed $value
	 *
	 * @return  bool
	 */
	public static function validate_bool( $value ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::validate_bool()' );

		return WPSEO_Utils::validate_bool( $value );
	}

	/**
	 * Cast a value to bool
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::emulate_filter_bool()
	 * @see        WPSEO_Utils::emulate_filter_bool()
	 *
	 * @static
	 *
	 * @param    mixed $value Value to cast.
	 *
	 * @return    bool
	 */
	public static function emulate_filter_bool( $value ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::emulate_filter_bool()' );

		return WPSEO_Utils::emulate_filter_bool( $value );
	}


	/**
	 * Validate a value as integer
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::validate_int()
	 * @see        WPSEO_Utils::validate_int()
	 *
	 * @static
	 *
	 * @param  mixed $value
	 *
	 * @return  mixed  int or false in case of failure to convert to int
	 */
	public static function validate_int( $value ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::validate_int()' );

		return WPSEO_Utils::validate_int( $value );
	}

	/**
	 * Cast a value to integer
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::emulate_filter_int()
	 * @see        WPSEO_Utils::emulate_filter_int()
	 *
	 * @static
	 *
	 * @param    mixed $value Value to cast.
	 *
	 * @return    int|bool
	 */
	public static function emulate_filter_int( $value ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::emulate_filter_int()' );

		return WPSEO_Utils::emulate_filter_int( $value );
	}


	/**
	 * Recursively trim whitespace round a string value or of string values within an array
	 * Only trims strings to avoid typecasting a variable (to string)
	 *
	 * @deprecated 1.5.6.1
	 * @deprecated use WPSEO_Utils::trim_recursive()
	 * @see        WPSEO_Utils::trim_recursive()
	 *
	 * @static
	 *
	 * @param   mixed $value Value to trim or array of values to trim.
	 *
	 * @return  mixed      Trimmed value or array of trimmed values
	 */
	public static function trim_recursive( $value ) {
		_deprecated_function( __FUNCTION__, 'WPSEO 1.5.6.1', 'WPSEO_Utils::trim_recursive()' );

		return WPSEO_Utils::trim_recursive( $value );
	}
}
