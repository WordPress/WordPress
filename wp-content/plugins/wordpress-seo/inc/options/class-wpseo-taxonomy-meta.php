<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_taxonomy_meta
 */
class WPSEO_Taxonomy_Meta extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo_taxonomy_meta';

	/**
	 * @var  bool  whether to include the option in the return for WPSEO_Options::get_all()
	 */
	public $include_in_all = false;

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 * @internal  Important: in contrast to most defaults, the below array format is
	 *        very bare. The real option is in the format [taxonomy_name][term_id][...]
	 *        where [...] is any of the $defaults_per_term options shown below.
	 *        This is of course taken into account in the below methods.
	 */
	protected $defaults = array();


	/**
	 * @var  string  Option name - same as $option_name property, but now also available to static methods
	 * @static
	 */
	public static $name;

	/**
	 * @var  array  Array of defaults for individual taxonomy meta entries
	 * @static
	 */
	public static $defaults_per_term = array(
		'wpseo_title'           => '',
		'wpseo_desc'            => '',
		'wpseo_metakey'         => '',
		'wpseo_canonical'       => '',
		'wpseo_bctitle'         => '',
		'wpseo_noindex'         => 'default',
		'wpseo_sitemap_include' => '-',
	);

	/**
	 * @var  array  Available index options
	 *        Used for form generation and input validation
	 *
	 * @static
	 *
	 * @internal  Labels (translation) added on admin_init via WPSEO_Taxonomy::translate_meta_options()
	 */
	public static $no_index_options = array(
		'default' => '',
		'index'   => '',
		'noindex' => '',
	);

	/**
	 * @var  array  Available sitemap include options
	 *        Used for form generation and input validation
	 *
	 * @static
	 *
	 * @internal  Labels (translation) added on admin_init via WPSEO_Taxonomy::translate_meta_options()
	 */
	public static $sitemap_include_options = array(
		'-'      => '',
		'always' => '',
		'never'  => '',
	);


	/**
	 * Add the actions and filters for the option
	 *
	 * @todo [JRF => testers] Check if the extra actions below would run into problems if an option
	 * is updated early on and if so, change the call to schedule these for a later action on add/update
	 * instead of running them straight away
	 *
	 * @return \WPSEO_Taxonomy_Meta
	 */
	protected function __construct() {
		parent::__construct();

		/* On succesfull update/add of the option, flush the W3TC cache */
		add_action( 'add_option_' . $this->option_name, array( 'WPSEO_Utils', 'flush_w3tc_cache' ) );
		add_action( 'update_option_' . $this->option_name, array( 'WPSEO_Utils', 'flush_w3tc_cache' ) );
	}


	/**
	 * Get the singleton instance of this class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
			self::$name     = self::$instance->option_name;
		}

		return self::$instance;
	}


	/**
	 * Add extra default options received from a filter
	 */
	public function enrich_defaults() {
		$extra_defaults_per_term = apply_filters( 'wpseo_add_extra_taxmeta_term_defaults', array() );
		if ( is_array( $extra_defaults_per_term ) ) {
			self::$defaults_per_term = array_merge( $extra_defaults_per_term, self::$defaults_per_term );
		}
	}


	/**
	 * Helper method - Combines a fixed array of default values with an options array
	 * while filtering out any keys which are not in the defaults array.
	 *
	 * @static
	 *
	 * @param  string $option_key Option name of the option we're doing the merge for.
	 * @param  array  $options    (Optional) Current options. If not set, the option defaults for the $option_key will be returned.
	 *
	 * @return  array  Combined and filtered options array.
	 */

	/*
	Public function array_filter_merge( $option_key, $options = null ) {

			$defaults = $this->get_defaults( $option_key );

			if ( ! isset( $options ) || $options === false ) {
				return $defaults;
			}

			/ *
			@internal Adding the defaults to all taxonomy terms each time the option is retrieved
			will be quite inefficient if there are a lot of taxonomy terms
			As long as taxonomy_meta is only retrieved via methods in this class, we shouldn't need this

			$options  = (array) $options;
			$filtered = array();

			if ( $options !== array() ) {
				foreach ( $options as $taxonomy => $terms ) {
					if ( is_array( $terms ) && $terms !== array() ) {
						foreach ( $terms as $id => $term_meta ) {
							foreach ( self::$defaults_per_term as $name => $default ) {
								if ( isset( $options[ $taxonomy ][ $id ][ $name ] ) ) {
									$filtered[ $taxonomy ][ $id ][ $name ] = $options[ $taxonomy ][ $id ][ $name ];
								}
								else {
									$filtered[ $name ] = $default;
								}
							}
						}
					}
				}
				unset( $taxonomy, $terms, $id, $term_meta, $name, $default );
			}
			// end of may be remove.

			return $filtered;
			* /

			return (array) $options;
		}
	*/


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
		/*
		Prevent complete validation (which can be expensive when there are lots of terms)
			   if only one item has changed and has already been validated
		*/
		if ( isset( $dirty['wpseo_already_validated'] ) && $dirty['wpseo_already_validated'] === true ) {
			unset( $dirty['wpseo_already_validated'] );

			return $dirty;
		}


		foreach ( $dirty as $taxonomy => $terms ) {
			/* Don't validate taxonomy - may not be registered yet and we don't want to remove valid ones */
			if ( is_array( $terms ) && $terms !== array() ) {
				foreach ( $terms as $term_id => $meta_data ) {
					/* Only validate term if the taxonomy exists */
					if ( taxonomy_exists( $taxonomy ) && get_term_by( 'id', $term_id, $taxonomy ) === false ) {
						/* Is this term id a special case ? */
						if ( has_filter( 'wpseo_tax_meta_special_term_id_validation_' . $term_id ) !== false ) {
							$clean[ $taxonomy ][ $term_id ] = apply_filters( 'wpseo_tax_meta_special_term_id_validation_' . $term_id, $meta_data, $taxonomy, $term_id );
						}
						continue;
					}

					if ( is_array( $meta_data ) && $meta_data !== array() ) {
						/* Validate meta data */
						$old_meta  = self::get_term_meta( $term_id, $taxonomy );
						$meta_data = self::validate_term_meta_data( $meta_data, $old_meta );
						if ( $meta_data !== array() ) {
							$clean[ $taxonomy ][ $term_id ] = $meta_data;
						}
					}

					// Deal with special cases (for when taxonomy doesn't exist yet).
					if ( ! isset( $clean[ $taxonomy ][ $term_id ] ) && has_filter( 'wpseo_tax_meta_special_term_id_validation_' . $term_id ) !== false ) {
						$clean[ $taxonomy ][ $term_id ] = apply_filters( 'wpseo_tax_meta_special_term_id_validation_' . $term_id, $meta_data, $taxonomy, $term_id );
					}
				}
			}
		}

		return $clean;
	}


	/**
	 * Validate the meta data for one individual term and removes default values (no need to save those)
	 *
	 * @static
	 *
	 * @param  array $meta_data New values.
	 * @param  array $old_meta  The original values.
	 *
	 * @return  array        Validated and filtered value
	 */
	public static function validate_term_meta_data( $meta_data, $old_meta ) {

		$clean     = self::$defaults_per_term;
		$meta_data = array_map( array( 'WPSEO_Utils', 'trim_recursive' ), $meta_data );

		if ( ! is_array( $meta_data ) || $meta_data === array() ) {
			return $clean;
		}

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {

				case 'wpseo_noindex':
					if ( isset( $meta_data[ $key ] ) ) {
						if ( isset( self::$no_index_options[ $meta_data[ $key ] ] ) ) {
							$clean[ $key ] = $meta_data[ $key ];
						}
					}
					elseif ( isset( $old_meta[ $key ] ) ) {
						// Retain old value if field currently not in use.
						$clean[ $key ] = $old_meta[ $key ];
					}
					break;

				case 'wpseo_sitemap_include':
					if ( isset( $meta_data[ $key ], self::$sitemap_include_options[ $meta_data[ $key ] ] ) ) {
						$clean[ $key ] = $meta_data[ $key ];
					}
					break;

				case 'wpseo_canonical':
					if ( isset( $meta_data[ $key ] ) && $meta_data[ $key ] !== '' ) {
						$url = WPSEO_Utils::sanitize_url( $meta_data[ $key ] );
						if ( $url !== '' ) {
							$clean[ $key ] = $url;
						}
						unset( $url );
					}
					break;

				case 'wpseo_metakey':
				case 'wpseo_bctitle':
					if ( isset( $meta_data[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( stripslashes( $meta_data[ $key ] ) );
					}
					elseif ( isset( $old_meta[ $key ] ) ) {
						// Retain old value if field currently not in use.
						$clean[ $key ] = $old_meta[ $key ];
					}
					break;

				case 'wpseo_title':
				case 'wpseo_desc':
				default:
					if ( isset( $meta_data[ $key ] ) && is_string( $meta_data[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( stripslashes( $meta_data[ $key ] ) );
					}
					break;
			}

			$clean[ $key ] = apply_filters( 'wpseo_sanitize_tax_meta_' . $key, $clean[ $key ], ( isset( $meta_data[ $key ] ) ? $meta_data[ $key ] : null ), ( isset( $old_meta[ $key ] ) ? $old_meta[ $key ] : null ) );
		}

		// Only save the non-default values.
		return array_diff_assoc( $clean, self::$defaults_per_term );
	}


	/**
	 * Clean a given option value
	 * - Convert old option values to new
	 * - Fixes strings which were escaped (should have been sanitized - escaping is for output)
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

		/* Clean up old values and remove empty arrays */
		if ( is_array( $option_value ) && $option_value !== array() ) {

			foreach ( $option_value as $taxonomy => $terms ) {

				if ( is_array( $terms ) && $terms !== array() ) {

					foreach ( $terms as $term_id => $meta_data ) {
						if ( ! is_array( $meta_data ) || $meta_data === array() ) {
							// Remove empty term arrays.
							unset( $option_value[ $taxonomy ][ $term_id ] );
						}
						else {
							foreach ( $meta_data as $key => $value ) {

								switch ( $key ) {
									case 'noindex':
										if ( $value === 'on' ) {
											// Convert 'on' to 'noindex'.
											$option_value[ $taxonomy ][ $term_id ][ $key ] = 'noindex';
										}
										break;

									case 'canonical':
									case 'wpseo_metakey':
									case 'wpseo_bctitle':
									case 'wpseo_title':
									case 'wpseo_desc':
										// @todo [JRF => whomever] needs checking, I don't have example data [JRF].
										if ( $value !== '' ) {
											// Fix incorrectly saved (encoded) canonical urls and texts.
											$option_value[ $taxonomy ][ $term_id ][ $key ] = wp_specialchars_decode( stripslashes( $value ), ENT_QUOTES );
										}
										break;

									default:
										// @todo [JRF => whomever] needs checking, I don't have example data [JRF].
										if ( $value !== '' ) {
											// Fix incorrectly saved (escaped) text strings.
											$option_value[ $taxonomy ][ $term_id ][ $key ] = wp_specialchars_decode( $value, ENT_QUOTES );
										}
										break;
								}
							}
						}
					}
				}
				else {
					// Remove empty taxonomy arrays.
					unset( $option_value[ $taxonomy ] );
				}
			}
		}

		return $option_value;
	}


	/**
	 * Retrieve a taxonomy term's meta value(s).
	 *
	 * @static
	 *
	 * @param  mixed  $term     Term to get the meta value for
	 *                          either (string) term name, (int) term id or (object) term.
	 * @param  string $taxonomy Name of the taxonomy to which the term is attached.
	 * @param  string $meta     (optional) Meta value to get (without prefix).
	 *
	 * @return  mixed|bool    Value for the $meta if one is given, might be the default.
	 *              If no meta is given, an array of all the meta data for the term.
	 *              False if the term does not exist or the $meta provided is invalid.
	 */
	public static function get_term_meta( $term, $taxonomy, $meta = null ) {
		/* Figure out the term id */
		if ( is_int( $term ) ) {
			$term = get_term_by( 'id', $term, $taxonomy );
		}
		elseif ( is_string( $term ) ) {
			$term = get_term_by( 'slug', $term, $taxonomy );
		}

		if ( is_object( $term ) && isset( $term->term_id ) ) {
			$term_id = $term->term_id;
		}
		else {
			return false;
		}


		$tax_meta = get_option( self::$name );

		/* If we have data for the term, merge with defaults for complete array, otherwise set defaults */
		if ( isset( $tax_meta[ $taxonomy ][ $term_id ] ) ) {
			$tax_meta = array_merge( self::$defaults_per_term, $tax_meta[ $taxonomy ][ $term_id ] );
		}
		else {
			$tax_meta = self::$defaults_per_term;
		}

		/*
		Either return the complete array or a single value from it or false if the value does not exist
			   (shouldn't happen after merge with defaults, indicates typo in request)
		*/
		if ( ! isset( $meta ) ) {
			return $tax_meta;
		}
		else {
			if ( isset( $tax_meta[ 'wpseo_' . $meta ] ) ) {
				return $tax_meta[ 'wpseo_' . $meta ];
			}
			else {
				return false;
			}
		}
	}
}
