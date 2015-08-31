<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo
 */
class WPSEO_Option_Wpseo extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo';

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 */
	protected $defaults = array(
		// Non-form fields, set via (ajax) function.
		'blocking_files'                  => array(),
		'ignore_blog_public_warning'      => false,
		'ignore_meta_description_warning' => null, // Overwrite in __construct().
		'ignore_page_comments'            => false,
		'ignore_permalink'                => false,
		'ms_defaults_set'                 => false,
		'theme_description_found'         => null, // Overwrite in __construct().
		'theme_has_description'           => null, // Overwrite in __construct().
		// Non-form field, should only be set via validation routine.
		'version'                         => '', // Leave default as empty to ensure activation/upgrade works.
		// Form fields:
		'alexaverify'                     => '', // Text field.
		'company_logo'                    => '',
		'company_name'                    => '',
		'company_or_person'               => '',
		'disableadvanced_meta'            => true,
		'googleverify'                    => '', // Text field.
		'msverify'                        => '', // Text field.
		'person_name'                     => '',
		'website_name'                    => '',
		'alternate_website_name'          => '',
		'yandexverify'                    => '',
	);

	/**
	 * @var array  Array of description related defaults
	 */
	public static $desc_defaults = array(
		'ignore_meta_description_warning' => false,
		'theme_description_found'         => '', // Text string description.
		'theme_has_description'           => null,
	);

	/**
	 * @var array  Array of sub-options which should not be overloaded with multi-site defaults
	 */
	public $ms_exclude = array(
		'ignore_blog_public_warning',
		'ignore_meta_description_warning',
		'ignore_page_comments',
		'ignore_permalink',
		/* theme dependent */
		'theme_description_found',
		'theme_has_description',
		/* privacy */
		'alexaverify',
		'googleverify',
		'msverify',
		'yandexverify',
	);


	/**
	 * Add the actions and filters for the option
	 *
	 * @todo [JRF => testers] Check if the extra actions below would run into problems if an option
	 * is updated early on and if so, change the call to schedule these for a later action on add/update
	 * instead of running them straight away
	 *
	 * @return \WPSEO_Option_Wpseo
	 */
	protected function __construct() {
		/*
		Dirty fix for making certain defaults available during activation while still only
			   defining them once
		*/
		foreach ( self::$desc_defaults as $key => $value ) {
			$this->defaults[ $key ] = $value;
		}

		parent::__construct();

		/* Clear the cache on update/add */
		add_action( 'add_option_' . $this->option_name, array( 'WPSEO_Utils', 'clear_cache' ) );
		add_action( 'update_option_' . $this->option_name, array( 'WPSEO_Utils', 'clear_cache' ) );
	}


	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Validate the option
	 *
	 * @param  array $dirty New value for the option.
	 * @param  array $clean Clean value for the option, normally the defaults.
	 * @param  array $old   Old value of the option.
	 *
	 * @return  array      Validated clean value for the option to be saved to the database
	 */
	protected function validate_option( $dirty, $clean, $old ) {

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {
				case 'version':
					$clean[ $key ] = WPSEO_VERSION;
					break;


				case 'blocking_files':
					/**
					 * @internal [JRF] to really validate this we should also do a file_exists()
					 * on each array entry and remove files which no longer exist, but that might be overkill
					 */
					if ( isset( $dirty[ $key ] ) && is_array( $dirty[ $key ] ) ) {
						$clean[ $key ] = array_unique( $dirty[ $key ] );
					}
					elseif ( isset( $old[ $key ] ) && is_array( $old[ $key ] ) ) {
						$clean[ $key ] = array_unique( $old[ $key ] );
					}
					break;


				case 'theme_description_found':
					if ( isset( $dirty[ $key ] ) && is_string( $dirty[ $key ] ) ) {
						$clean[ $key ] = $dirty[ $key ]; // @todo [JRF/whomever] maybe do wp_kses ?
					}
					elseif ( isset( $old[ $key ] ) && is_string( $old[ $key ] ) ) {
						$clean[ $key ] = $old[ $key ];
					}
					break;

				case 'company_or_person':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						if ( in_array( $dirty[ $key ], array( 'company', 'person' ) ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
					}
					break;

				/* text fields */
				case 'company_name':
				case 'person_name':
				case 'website_name':
				case 'alternate_website_name':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = sanitize_text_field( $dirty[ $key ] );
					}
					break;

				case 'company_logo':
					$this->validate_url( $key, $dirty, $old, $clean );
					break;

				/* verification strings */
				case 'alexaverify':
				case 'googleverify':
				case 'msverify':
				case 'yandexverify':
					$this->validate_verification_string( $key, $dirty, $old, $clean );
					break;


				/* boolean|null fields - if set a check was done, if null, it hasn't */
				case 'theme_has_description':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::validate_bool( $dirty[ $key ] );
					}
					elseif ( isset( $old[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::validate_bool( $old[ $key ] );
					}
					break;


				/*
				Boolean dismiss warnings - not fields - may not be in form
					   (and don't need to be either as long as the default is false)
				 */
				case 'ignore_blog_public_warning':
				case 'ignore_meta_description_warning':
				case 'ignore_page_comments':
				case 'ignore_permalink':
				case 'ms_defaults_set':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::validate_bool( $dirty[ $key ] );
					}
					elseif ( isset( $old[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::validate_bool( $old[ $key ] );
					}
					break;


				/*
				Boolean (checkbox) fields
				*/

				/*
				Covers
				 * 		'disableadvanced_meta'
				 * 		'yoast_tracking'
				 */
				default:
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;
			}
		}

		return $clean;
	}


	/**
	 * Clean a given option value
	 *
	 * @param  array  $option_value          Old (not merged with defaults or filtered) option value to
	 *                                       clean according to the rules for this option.
	 * @param  string $current_version       (optional) Version from which to upgrade, if not set,
	 *                                       version specific upgrades will be disregarded.
	 * @param  array  $all_old_option_values (optional) Only used when importing old options to have
	 *                                       access to the real old values, in contrast to the saved ones.
	 *
	 * @return  array            Cleaned option
	 */
	protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {
		// Deal with renaming of some options without losing the settings.
		$rename = array(
			'meta_description_warning' => 'ignore_meta_description_warning',
		);
		foreach ( $rename as $old => $new ) {
			if ( isset( $option_value[ $old ] ) && ! isset( $option_value[ $new ] ) ) {
				$option_value[ $new ] = $option_value[ $old ];
				unset( $option_value[ $old ] );
			}
		}
		unset( $rename, $old, $new );


		// Change a array sub-option to two straight sub-options.
		if ( isset( $option_value['theme_check']['description'] ) && ! isset( $option_value['theme_has_description'] ) ) {
			// @internal the negate is by design!
			$option_value['theme_has_description'] = ! $option_value['theme_check']['description'];
		}
		if ( isset( $option_values['theme_check']['description_found'] ) && ! isset( $option_value['theme_description_found'] ) ) {
			$option_value['theme_description_found'] = $option_value['theme_check']['description_found'];
		}


		// Deal with value change from text string to boolean.
		$value_change = array(
			'ignore_blog_public_warning',
			'ignore_meta_description_warning',
			'ignore_page_comments',
			'ignore_permalink',
			// 'disableadvanced_meta', => not needed as is 'on' which will auto-convert to true.
		);
		foreach ( $value_change as $key ) {
			if ( isset( $option_value[ $key ] ) && in_array( $option_value[ $key ], array(
					'ignore',
					'done',
				), true )
			) {
				$option_value[ $key ] = true;
			}
		}

		return $option_value;
	}
}
