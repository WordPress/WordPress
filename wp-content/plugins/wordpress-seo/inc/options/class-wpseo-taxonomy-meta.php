<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_taxonomy_meta.
 */
class WPSEO_Taxonomy_Meta extends WPSEO_Option {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	public $option_name = 'wpseo_taxonomy_meta';

	/**
	 * Whether to include the option in the return for WPSEO_Options::get_all().
	 *
	 * @var bool
	 */
	public $include_in_all = false;

	/**
	 * Array of defaults for the option.
	 *
	 * Shouldn't be requested directly, use $this->get_defaults();
	 *
	 * {@internal Important: in contrast to most defaults, the below array format is
	 *            very bare. The real option is in the format [taxonomy_name][term_id][...]
	 *            where [...] is any of the $defaults_per_term options shown below.
	 *            This is of course taken into account in the below methods.}}
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * Option name - same as $option_name property, but now also available to static methods.
	 *
	 * @var string
	 */
	public static $name;

	/**
	 * Array of defaults for individual taxonomy meta entries.
	 *
	 * @var array
	 */
	public static $defaults_per_term = [
		'wpseo_title'                    => '',
		'wpseo_desc'                     => '',
		'wpseo_canonical'                => '',
		'wpseo_bctitle'                  => '',
		'wpseo_noindex'                  => 'default',
		'wpseo_focuskw'                  => '',
		'wpseo_linkdex'                  => '',
		'wpseo_content_score'            => '',
		'wpseo_inclusive_language_score' => '',
		'wpseo_focuskeywords'            => '[]',
		'wpseo_keywordsynonyms'          => '[]',
		'wpseo_is_cornerstone'           => '0',

		// Social fields.
		'wpseo_opengraph-title'          => '',
		'wpseo_opengraph-description'    => '',
		'wpseo_opengraph-image'          => '',
		'wpseo_opengraph-image-id'       => '',
		'wpseo_twitter-title'            => '',
		'wpseo_twitter-description'      => '',
		'wpseo_twitter-image'            => '',
		'wpseo_twitter-image-id'         => '',
	];

	/**
	 * Available index options.
	 *
	 * Used for form generation and input validation.
	 *
	 * {@internal Labels (translation) added on admin_init via WPSEO_Taxonomy::translate_meta_options().}}
	 *
	 * @var array
	 */
	public static $no_index_options = [
		'default' => '',
		'index'   => '',
		'noindex' => '',
	];

	/**
	 * Add the actions and filters for the option.
	 *
	 * @todo [JRF => testers] Check if the extra actions below would run into problems if an option
	 * is updated early on and if so, change the call to schedule these for a later action on add/update
	 * instead of running them straight away.
	 */
	protected function __construct() {
		parent::__construct();

		self::$name = $this->option_name;
	}

	/**
	 * Get the singleton instance of this class.
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
	 * Add extra default options received from a filter.
	 *
	 * @return void
	 */
	public function enrich_defaults() {
		$extra_defaults_per_term = apply_filters( 'wpseo_add_extra_taxmeta_term_defaults', [] );
		if ( is_array( $extra_defaults_per_term ) ) {
			self::$defaults_per_term = array_merge( $extra_defaults_per_term, self::$defaults_per_term );
		}
	}

	/**
	 * Validate the option.
	 *
	 * @param array $dirty New value for the option.
	 * @param array $clean Clean value for the option, normally the defaults.
	 * @param array $old   Old value of the option.
	 *
	 * @return array Validated clean value for the option to be saved to the database.
	 */
	protected function validate_option( $dirty, $clean, $old ) {
		/*
		 * Prevent complete validation (which can be expensive when there are lots of terms)
		 * if only one item has changed and has already been validated.
		 */
		if ( isset( $dirty['wpseo_already_validated'] ) && $dirty['wpseo_already_validated'] === true ) {
			unset( $dirty['wpseo_already_validated'] );

			return $dirty;
		}

		foreach ( $dirty as $taxonomy => $terms ) {
			/* Don't validate taxonomy - may not be registered yet and we don't want to remove valid ones. */
			if ( is_array( $terms ) && $terms !== [] ) {
				foreach ( $terms as $term_id => $meta_data ) {
					/* Only validate term if the taxonomy exists. */
					if ( taxonomy_exists( $taxonomy ) && get_term_by( 'id', $term_id, $taxonomy ) === false ) {
						/* Is this term id a special case ? */
						if ( has_filter( 'wpseo_tax_meta_special_term_id_validation_' . $term_id ) !== false ) {
							$clean[ $taxonomy ][ $term_id ] = apply_filters( 'wpseo_tax_meta_special_term_id_validation_' . $term_id, $meta_data, $taxonomy, $term_id );
						}
						continue;
					}

					if ( is_array( $meta_data ) && $meta_data !== [] ) {
						/* Validate meta data. */
						$old_meta  = self::get_term_meta( $term_id, $taxonomy );
						$meta_data = self::validate_term_meta_data( $meta_data, $old_meta );
						if ( $meta_data !== [] ) {
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
	 * Validate the meta data for one individual term and removes default values (no need to save those).
	 *
	 * @param array $meta_data New values.
	 * @param array $old_meta  The original values.
	 *
	 * @return array Validated and filtered value.
	 */
	public static function validate_term_meta_data( $meta_data, $old_meta ) {

		$clean     = self::$defaults_per_term;
		$meta_data = array_map( [ 'WPSEO_Utils', 'trim_recursive' ], $meta_data );

		if ( ! is_array( $meta_data ) || $meta_data === [] ) {
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

				case 'wpseo_canonical':
					if ( isset( $meta_data[ $key ] ) && $meta_data[ $key ] !== '' ) {
						$url = WPSEO_Utils::sanitize_url( $meta_data[ $key ] );
						if ( $url !== '' ) {
							$clean[ $key ] = $url;
						}
						unset( $url );
					}
					break;

				case 'wpseo_bctitle':
					if ( isset( $meta_data[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $meta_data[ $key ] );
					}
					elseif ( isset( $old_meta[ $key ] ) ) {
						// Retain old value if field currently not in use.
						$clean[ $key ] = $old_meta[ $key ];
					}
					break;

				case 'wpseo_keywordsynonyms':
					if ( isset( $meta_data[ $key ] ) && is_string( $meta_data[ $key ] ) ) {
						// The data is stringified JSON. Use `json_decode` and `json_encode` around the sanitation.
						$input         = json_decode( $meta_data[ $key ], true );
						$sanitized     = array_map( [ 'WPSEO_Utils', 'sanitize_text_field' ], $input );
						$clean[ $key ] = WPSEO_Utils::format_json_encode( $sanitized );
					}
					elseif ( isset( $old_meta[ $key ] ) ) {
						// Retain old value if field currently not in use.
						$clean[ $key ] = $old_meta[ $key ];
					}
					break;

				case 'wpseo_focuskeywords':
					if ( isset( $meta_data[ $key ] ) && is_string( $meta_data[ $key ] ) ) {
						// The data is stringified JSON. Use `json_decode` and `json_encode` around the sanitation.
						$input = json_decode( $meta_data[ $key ], true );

						// This data has two known keys: `keyword` and `score`.
						$sanitized = [];
						foreach ( $input as $entry ) {
							$sanitized[] = [
								'keyword' => WPSEO_Utils::sanitize_text_field( $entry['keyword'] ),
								'score'   => WPSEO_Utils::sanitize_text_field( $entry['score'] ),
							];
						}

						$clean[ $key ] = WPSEO_Utils::format_json_encode( $sanitized );
					}
					elseif ( isset( $old_meta[ $key ] ) ) {
						// Retain old value if field currently not in use.
						$clean[ $key ] = $old_meta[ $key ];
					}
					break;

				case 'wpseo_focuskw':
				case 'wpseo_title':
				case 'wpseo_desc':
				case 'wpseo_linkdex':
				default:
					if ( isset( $meta_data[ $key ] ) && is_string( $meta_data[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $meta_data[ $key ] );
					}

					if ( $key === 'wpseo_focuskw' ) {
						$search = [
							'&lt;',
							'&gt;',
							'&#96',
							'<',
							'>',
							'`',
						];

						$clean[ $key ] = str_replace( $search, '', $clean[ $key ] );
					}
					break;
			}

			$clean[ $key ] = apply_filters( 'wpseo_sanitize_tax_meta_' . $key, $clean[ $key ], ( $meta_data[ $key ] ?? null ), ( $old_meta[ $key ] ?? null ) );
		}

		// Only save the non-default values.
		return array_diff_assoc( $clean, self::$defaults_per_term );
	}

	/**
	 * Clean a given option value.
	 * - Convert old option values to new
	 * - Fixes strings which were escaped (should have been sanitized - escaping is for output)
	 *
	 * @param array       $option_value          Old (not merged with defaults or filtered) option value to
	 *                                           clean according to the rules for this option.
	 * @param string|null $current_version       Optional. Version from which to upgrade, if not set,
	 *                                           version specific upgrades will be disregarded.
	 * @param array|null  $all_old_option_values Optional. Only used when importing old options to have
	 *                                           access to the real old values, in contrast to the saved ones.
	 *
	 * @return array Cleaned option.
	 */
	protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {

		/* Clean up old values and remove empty arrays. */
		if ( is_array( $option_value ) && $option_value !== [] ) {

			foreach ( $option_value as $taxonomy => $terms ) {

				if ( is_array( $terms ) && $terms !== [] ) {

					foreach ( $terms as $term_id => $meta_data ) {
						if ( ! is_array( $meta_data ) || $meta_data === [] ) {
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
									case 'wpseo_bctitle':
									case 'wpseo_title':
									case 'wpseo_desc':
									case 'wpseo_linkdex':
										// @todo [JRF => whomever] Needs checking, I don't have example data [JRF].
										if ( $value !== '' ) {
											// Fix incorrectly saved (encoded) canonical urls and texts.
											$option_value[ $taxonomy ][ $term_id ][ $key ] = wp_specialchars_decode( stripslashes( $value ), ENT_QUOTES );
										}
										break;

									default:
										// @todo [JRF => whomever] Needs checking, I don't have example data [JRF].
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
	 * @param mixed       $term     Term to get the meta value for
	 *                              either (string) term name, (int) term id or (object) term.
	 * @param string      $taxonomy Name of the taxonomy to which the term is attached.
	 * @param string|null $meta     Optional. Meta value to get (without prefix).
	 *
	 * @return mixed Value for the $meta if one is given, might be the default.
	 *               If no meta is given, an array of all the meta data for the term.
	 *               False if the term does not exist or the $meta provided is invalid.
	 */
	public static function get_term_meta( $term, $taxonomy, $meta = null ) {
		/* Figure out the term id. */
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

		$tax_meta = self::get_term_tax_meta( $term_id, $taxonomy );

		/*
		 * Either return the complete array or a single value from it or false if the value does not exist
		 * (shouldn't happen after merge with defaults, indicates typo in request).
		 */
		if ( ! isset( $meta ) ) {
			return $tax_meta;
		}

		if ( isset( $tax_meta[ 'wpseo_' . $meta ] ) ) {
			return $tax_meta[ 'wpseo_' . $meta ];
		}

		return false;
	}

	/**
	 * Get the current queried object and return the meta value.
	 *
	 * @param string $meta The meta field that is needed.
	 *
	 * @return mixed
	 */
	public static function get_meta_without_term( $meta ) {
		$term = $GLOBALS['wp_query']->get_queried_object();
		if ( ! $term || empty( $term->taxonomy ) ) {
			return false;
		}

		return self::get_term_meta( $term, $term->taxonomy, $meta );
	}

	/**
	 * Saving the values for the given term_id.
	 *
	 * @param int    $term_id     ID of the term to save data for.
	 * @param string $taxonomy    The taxonomy the term belongs to.
	 * @param array  $meta_values The values that will be saved.
	 *
	 * @return void
	 */
	public static function set_values( $term_id, $taxonomy, array $meta_values ) {
		/* Validate the post values */
		$old   = self::get_term_meta( $term_id, $taxonomy );
		$clean = self::validate_term_meta_data( $meta_values, $old );

		self::save_clean_values( $term_id, $taxonomy, $clean );
	}

	/**
	 * Setting a single value to the term meta.
	 *
	 * @param int    $term_id    ID of the term to save data for.
	 * @param string $taxonomy   The taxonomy the term belongs to.
	 * @param string $meta_key   The target meta key to store the value in.
	 * @param string $meta_value The value of the target meta key.
	 *
	 * @return void
	 */
	public static function set_value( $term_id, $taxonomy, $meta_key, $meta_value ) {

		if ( substr( strtolower( $meta_key ), 0, 6 ) !== 'wpseo_' ) {
			$meta_key = 'wpseo_' . $meta_key;
		}

		self::set_values( $term_id, $taxonomy, [ $meta_key => $meta_value ] );
	}

	/**
	 * Find the keyword usages in the metas for the taxonomies/terms.
	 *
	 * @param string $keyword          The keyword to look for.
	 * @param string $current_term_id  The current term id.
	 * @param string $current_taxonomy The current taxonomy name.
	 *
	 * @return array
	 */
	public static function get_keyword_usage( $keyword, $current_term_id, $current_taxonomy ) {
		$tax_meta = self::get_tax_meta();

		$found = [];
		// @todo Check for terms of all taxonomies, not only the current taxonomy.
		foreach ( $tax_meta as $taxonomy_name => $terms ) {
			foreach ( $terms as $term_id => $meta_values ) {
				$is_current = ( $current_taxonomy === $taxonomy_name && (string) $current_term_id === (string) $term_id );
				if ( ! $is_current && ! empty( $meta_values['wpseo_focuskw'] ) && $meta_values['wpseo_focuskw'] === $keyword ) {
					$found[] = $term_id;
				}
			}
		}

		return [ $keyword => $found ];
	}

	/**
	 * Saving the values for the given term_id.
	 *
	 * @param int    $term_id  ID of the term to save data for.
	 * @param string $taxonomy The taxonomy the term belongs to.
	 * @param array  $clean    Array with clean values.
	 *
	 * @return void
	 */
	private static function save_clean_values( $term_id, $taxonomy, array $clean ) {
		$tax_meta = self::get_tax_meta();

		/* Add/remove the result to/from the original option value. */
		if ( $clean !== [] ) {
			$tax_meta[ $taxonomy ][ $term_id ] = $clean;
		}
		else {
			unset( $tax_meta[ $taxonomy ][ $term_id ] );
			if ( isset( $tax_meta[ $taxonomy ] ) && $tax_meta[ $taxonomy ] === [] ) {
				unset( $tax_meta[ $taxonomy ] );
			}
		}

		// Prevent complete array validation.
		$tax_meta['wpseo_already_validated'] = true;

		self::save_tax_meta( $tax_meta );
	}

	/**
	 * Getting the meta from the options.
	 *
	 * @return array|void
	 */
	private static function get_tax_meta() {
		return get_option( self::$name );
	}

	/**
	 * Saving the tax meta values to the database.
	 *
	 * @param array $tax_meta Array with the meta values for taxonomy.
	 *
	 * @return void
	 */
	private static function save_tax_meta( $tax_meta ) {
		update_option( self::$name, $tax_meta );
	}

	/**
	 * Getting the taxonomy meta for the given term_id and taxonomy.
	 *
	 * @param int    $term_id  The id of the term.
	 * @param string $taxonomy Name of the taxonomy to which the term is attached.
	 *
	 * @return array
	 */
	private static function get_term_tax_meta( $term_id, $taxonomy ) {
		$tax_meta = self::get_tax_meta();

		/* If we have data for the term, merge with defaults for complete array, otherwise set defaults. */
		if ( isset( $tax_meta[ $taxonomy ][ $term_id ] ) ) {
			return array_merge( self::$defaults_per_term, $tax_meta[ $taxonomy ][ $term_id ] );
		}

		return self::$defaults_per_term;
	}
}
