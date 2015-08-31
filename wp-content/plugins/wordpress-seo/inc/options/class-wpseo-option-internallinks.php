<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_internallinks
 */
class WPSEO_Option_InternalLinks extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo_internallinks';

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 * @internal  Note: Some of the default values are added via the translate_defaults() method
	 */
	protected $defaults = array(
		'breadcrumbs-404crumb'      => '', // Text field.
		'breadcrumbs-blog-remove'   => false,
		'breadcrumbs-boldlast'      => false,
		'breadcrumbs-archiveprefix' => '', // Text field.
		'breadcrumbs-enable'        => false,
		'breadcrumbs-home'          => '', // Text field.
		'breadcrumbs-prefix'        => '', // Text field.
		'breadcrumbs-searchprefix'  => '', // Text field.
		'breadcrumbs-sep'           => '&raquo;', // Text field.

		/**
		 * Uses enrich_defaults() to add more along the lines of:
		 * - 'post_types-' . $pt->name . '-maintax'    => 0 / string
		 * - 'taxonomy-' . $tax->name . '-ptparent'    => 0 / string
		 */
	);

	/**
	 * @var  array  Array of variable option name patterns for the option
	 */
	protected $variable_array_key_patterns = array(
		'post_types-',
		'taxonomy-',
	);


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
	 * Translate strings used in the option defaults
	 *
	 * @return void
	 */
	public function translate_defaults() {
		$this->defaults['breadcrumbs-404crumb']      = __( 'Error 404: Page not found', 'wordpress-seo' );
		$this->defaults['breadcrumbs-archiveprefix'] = __( 'Archives for', 'wordpress-seo' );
		$this->defaults['breadcrumbs-home']          = __( 'Home', 'wordpress-seo' );
		$this->defaults['breadcrumbs-searchprefix']  = __( 'You searched for', 'wordpress-seo' );
	}


	/**
	 * Add dynamically created default options based on available post types and taxonomies
	 *
	 * @return  void
	 */
	public function enrich_defaults() {

		// Retrieve all the relevant post type and taxonomy arrays.
		$post_type_names       = get_post_types( array( 'public' => true ), 'names' );
		$taxonomy_names_custom = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'names' );

		if ( $post_type_names !== array() ) {
			foreach ( $post_type_names as $pt ) {
				$pto_taxonomies = get_object_taxonomies( $pt, 'names' );
				if ( $pto_taxonomies !== array() ) {
					$this->defaults[ 'post_types-' . $pt . '-maintax' ] = 0; // Select box.
				}
				unset( $pto_taxonomies );
			}
			unset( $pt );
		}

		if ( $taxonomy_names_custom !== array() ) {
			foreach ( $taxonomy_names_custom as $tax ) {
				$this->defaults[ 'taxonomy-' . $tax . '-ptparent' ] = 0; // Select box;.
			}
			unset( $tax );
		}
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

		$allowed_post_types = $this->get_allowed_post_types();

		foreach ( $clean as $key => $value ) {

			$switch_key = $this->get_switch_key( $key );

			switch ( $switch_key ) {
				/* text fields */
				case 'breadcrumbs-404crumb':
				case 'breadcrumbs-archiveprefix':
				case 'breadcrumbs-home':
				case 'breadcrumbs-prefix':
				case 'breadcrumbs-searchprefix':
				case 'breadcrumbs-sep':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = wp_kses_post( $dirty[ $key ] );
					}
					break;


				/* 'post_types-' . $pt->name . '-maintax' fields */
				case 'post_types-':
					$post_type  = str_replace( array( 'post_types-', '-maintax' ), '', $key );
					$taxonomies = get_object_taxonomies( $post_type, 'names' );

					if ( isset( $dirty[ $key ] ) ) {
						if ( $taxonomies !== array() && in_array( $dirty[ $key ], $taxonomies, true ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
						elseif ( (string) $dirty[ $key ] === '0' || (string) $dirty[ $key ] === '' ) {
							$clean[ $key ] = 0;
						}
						elseif ( sanitize_title_with_dashes( $dirty[ $key ] ) === $dirty[ $key ] ) {
							// Allow taxonomies which may not be registered yet.
							$clean[ $key ] = $dirty[ $key ];
						}
						else {
							if ( isset( $old[ $key ] ) ) {
								$clean[ $key ] = sanitize_title_with_dashes( $old[ $key ] );
							}
							if ( function_exists( 'add_settings_error' ) ) {
								/**
								 * @todo [JRF => whomever] maybe change the untranslated $pt name in the
								 * error message to the nicely translated label ?
								 */
								add_settings_error(
									$this->group_name, // Slug title of the setting.
									'_' . $key, // Suffix-id for the error message box.
									sprintf( __( 'Please select a valid taxonomy for post type "%s"', 'wordpress-seo' ), $post_type ), // The error message.
									'error' // Error type, either 'error' or 'updated'.
								);
							}
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$clean[ $key ] = sanitize_title_with_dashes( $old[ $key ] );
					}
					unset( $taxonomies, $post_type );
					break;


				/* 'taxonomy-' . $tax->name . '-ptparent' fields */
				case 'taxonomy-':
					if ( isset( $dirty[ $key ] ) ) {
						if ( $allowed_post_types !== array() && in_array( $dirty[ $key ], $allowed_post_types, true ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
						elseif ( (string) $dirty[ $key ] === '0' || (string) $dirty[ $key ] === '' ) {
							$clean[ $key ] = 0;
						}
						elseif ( sanitize_key( $dirty[ $key ] ) === $dirty[ $key ] ) {
							// Allow taxonomies which may not be registered yet.
							$clean[ $key ] = $dirty[ $key ];
						}
						else {
							if ( isset( $old[ $key ] ) ) {
								$clean[ $key ] = sanitize_key( $old[ $key ] );
							}
							if ( function_exists( 'add_settings_error' ) ) {
								/**
								 * @todo [JRF =? whomever] maybe change the untranslated $tax name in the
								 * error message to the nicely translated label ?
								 */
								$tax = str_replace( array( 'taxonomy-', '-ptparent' ), '', $key );
								add_settings_error(
									$this->group_name, // Slug title of the setting.
									'_' . $tax, // Suffix-id for the error message box.
									sprintf( __( 'Please select a valid post type for taxonomy "%s"', 'wordpress-seo' ), $tax ), // The error message.
									'error' // Error type, either 'error' or 'updated'.
								);
								unset( $tax );
							}
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$clean[ $key ] = sanitize_key( $old[ $key ] );
					}
					break;


				/*
				Boolean fields
				*/

				/*
				Covers:
				 * 		'breadcrumbs-blog-remove'
				 * 		'breadcrumbs-boldlast'
				 * 		'breadcrumbs-enable'
				 */
				default:
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;
			}
		}

		return $clean;
	}


	/**
	 * Retrieve a list of the allowed post types as breadcrumb parent for a taxonomy
	 * Helper method for validation
	 *
	 * @internal don't make static as new types may still be registered
	 *
	 * @return array
	 */
	protected function get_allowed_post_types() {
		$allowed_post_types = array();

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		if ( get_option( 'show_on_front' ) == 'page' && get_option( 'page_for_posts' ) > 0 ) {
			$allowed_post_types[] = 'post';
		}

		if ( is_array( $post_types ) && $post_types !== array() ) {
			foreach ( $post_types as $type ) {
				if ( $type->has_archive ) {
					$allowed_post_types[] = $type->name;
				}
			}
		}

		return $allowed_post_types;
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

		/* Make sure the old fall-back defaults for empty option keys are now added to the option */
		if ( isset( $current_version ) && version_compare( $current_version, '1.5.2.3', '<' ) ) {
			if ( has_action( 'init', array( 'WPSEO_Options', 'bring_back_breadcrumb_defaults' ) ) === false ) {
				add_action( 'init', array( 'WPSEO_Options', 'bring_back_breadcrumb_defaults' ), 3 );
			}
		}

		/*
		Make sure the values of the variable option key options are cleaned as they
			   may be retained and would not be cleaned/validated then
		*/
		if ( is_array( $option_value ) && $option_value !== array() ) {

			$allowed_post_types = $this->get_allowed_post_types();

			foreach ( $option_value as $key => $value ) {
				$switch_key = $this->get_switch_key( $key );

				// Similar to validation routine - any changes made there should be made here too.
				switch ( $switch_key ) {
					/* 'post_types-' . $pt->name . '-maintax' fields */
					case 'post_types-':
						$post_type  = str_replace( array( 'post_types-', '-maintax' ), '', $key );
						$taxonomies = get_object_taxonomies( $post_type, 'names' );

						if ( $taxonomies !== array() && in_array( $value, $taxonomies, true ) ) {
							$option_value[ $key ] = $value;
						}
						elseif ( (string) $value === '0' || (string) $value === '' ) {
							$option_value[ $key ] = 0;
						}
						elseif ( sanitize_title_with_dashes( $value ) === $value ) {
							// Allow taxonomies which may not be registered yet.
							$option_value[ $key ] = $value;
						}
						unset( $taxonomies, $post_type );
						break;


					/* 'taxonomy-' . $tax->name . '-ptparent' fields */
					case 'taxonomy-':
						if ( $allowed_post_types !== array() && in_array( $value, $allowed_post_types, true ) ) {
							$option_value[ $key ] = $value;
						}
						elseif ( (string) $value === '0' || (string) $value === '' ) {
							$option_value[ $key ] = 0;
						}
						elseif ( sanitize_key( $option_value[ $key ] ) === $option_value[ $key ] ) {
							// Allow post types which may not be registered yet.
							$option_value[ $key ] = $value;
						}
						break;
				}
			}
		}

		return $option_value;
	}

	/**
	 * With the changes to v1.5, the defaults for some of the textual breadcrumb settings are added
	 * dynamically, but empty strings are allowed.
	 * This caused issues for people who left the fields empty on purpose relying on the defaults.
	 * This little routine fixes that.
	 * Needs to be run on 'init' hook at prio 3 to make sure the defaults are translated.
	 */
	public function bring_back_defaults() {
		$option = get_option( $this->option_name );

		$values_to_bring_back = array(
			'breadcrumbs-404crumb',
			'breadcrumbs-archiveprefix',
			'breadcrumbs-home',
			'breadcrumbs-searchprefix',
			'breadcrumbs-sep',
		);
		foreach ( $values_to_bring_back as $key ) {
			if ( $option[ $key ] === '' && $this->defaults[ $key ] !== '' ) {
				$option[ $key ] = $this->defaults[ $key ];
			}
		}
		update_option( $this->option_name, $option );
	}
}
