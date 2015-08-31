<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * @internal Clean routine for 1.5 not needed as values used to be saved as string 'on' and those will convert
 * automatically
 */
class WPSEO_Option_Permalinks extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo_permalinks';

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 */
	protected $defaults = array(
		'cleanpermalinks'                 => false,
		'cleanpermalink-extravars'        => '', // Text field.
		'cleanpermalink-googlecampaign'   => false,
		'cleanpermalink-googlesitesearch' => false,
		'cleanreplytocom'                 => false,
		'cleanslugs'                      => true,
		'hide-feedlinks'                  => false,
		'hide-rsdlink'                    => false,
		'hide-shortlink'                  => false,
		'hide-wlwmanifest'                => false,
		'redirectattachment'              => false,
		'stripcategorybase'               => false,
		'trailingslash'                   => false,
	);


	/**
	 * Add the actions and filters for the option
	 *
	 * @todo [JRF => testers] Check if the extra actions below would run into problems if an option
	 * is updated early on and if so, change the call to schedule these for a later action on add/update
	 * instead of running them straight away
	 *
	 * @return \WPSEO_Option_Permalinks
	 */
	protected function __construct() {
		parent::__construct();
		add_action( 'update_option_' . $this->option_name, array( 'WPSEO_Utils', 'clear_rewrites' ) );
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
	 * @param  array $old   Old value of the option (not used here as all fields will always be in the form).
	 *
	 * @return  array      Validated clean value for the option to be saved to the database
	 */
	protected function validate_option( $dirty, $clean, $old ) {

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {
				/* text fields */
				case 'cleanpermalink-extravars':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = sanitize_text_field( $dirty[ $key ] );
					}
					break;

				/*
				Boolean (checkbox) fields
				*/

				/*
				Covers:
				 * 		'cleanpermalinks'
				 * 		'cleanpermalink-googlesitesearch'
				 *		'cleanpermalink-googlecampaign'
				 *		'cleanreplytocom'
				 *		'cleanslugs'
				 *		'hide-rsdlink'
				 *		'hide-wlwmanifest'
				 *		'hide-shortlink'
				 *		'hide-feedlinks'
				 *		'redirectattachment'
				 *		'stripcategorybase'
				 *		'trailingslash'
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

	/*
	Protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {

			return $option_value;
		}
	*/
}
