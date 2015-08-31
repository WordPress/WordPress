<?php
/**
 * @package WPSEO\Internals\Options
 */

/**
 * Option: wpseo_xml
 */
class WPSEO_Option_XML extends WPSEO_Option {

	/**
	 * @var  string  option name
	 */
	public $option_name = 'wpseo_xml';

	/**
	 * @var  string  option group name for use in settings forms
	 */
	public $group_name = 'yoast_wpseo_xml_sitemap_options';

	/**
	 * @var  array  Array of defaults for the option
	 *        Shouldn't be requested directly, use $this->get_defaults();
	 */
	protected $defaults = array(
		'disable_author_sitemap' => true,
		'disable_author_noposts' => true,
		'enablexmlsitemap'       => true,
		'entries-per-page'       => 1000,
		'excluded-posts'         => '',

		/**
		 * Uses enrich_defaults to add more along the lines of:
		 * - 'user_role-' .  $role_name . '-not_in_sitemap'  => bool
		 * - 'post_types-' . $pt->name . '-not_in_sitemap'  => bool
		 * - 'taxonomies-' . $tax->name . '-not_in_sitemap'  => bool
		 */
	);

	/**
	 * @var  array  Array of variable option name patterns for the option
	 */
	protected $variable_array_key_patterns = array(
		'user_role-',
		'post_types-',
		'taxonomies-',
	);


	/**
	 * Add the actions and filters for the option
	 *
	 * @todo [JRF => testers] Check if the extra actions below would run into problems if an option
	 *       is updated early on and if so, change the call to schedule these for a later action on add/update
	 *       instead of running them straight away
	 *
	 * @return \WPSEO_Option_XML
	 */
	protected function __construct() {
		parent::__construct();
		add_action( 'update_option_' . $this->option_name, array( 'WPSEO_Utils', 'clear_rewrites' ) );
		add_action( 'update_option_' . $this->option_name, array( 'WPSEO_Utils', 'clear_sitemap_cache' ) );
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
	 * Add dynamically created default options based on available post types and taxonomies
	 *
	 * @return  void
	 */
	public function enrich_defaults() {

		$user_roles          = WPSEO_Utils::get_roles();
		$filtered_user_roles = apply_filters( 'wpseo_sitemaps_supported_user_roles', $user_roles );
		if ( is_array( $filtered_user_roles ) && $filtered_user_roles !== array() ) {
			foreach ( $filtered_user_roles as $role_name => $role_value ) {
				$this->defaults[ 'user_role-' . $role_name . '-not_in_sitemap' ] = false;

				unset( $user_role );
			}
			unset( $role_name, $role_value );
		}
		unset( $user_roles, $filtered_user_roles );

		$post_type_names     = get_post_types( array( 'public' => true ), 'names' );
		$filtered_post_types = apply_filters( 'wpseo_sitemaps_supported_post_types', $post_type_names );

		if ( is_array( $filtered_post_types ) && $filtered_post_types !== array() ) {
			foreach ( $filtered_post_types as $pt ) {
				if ( $pt !== 'attachment' ) {
					$this->defaults[ 'post_types-' . $pt . '-not_in_sitemap' ] = false;
				}
				else {
					$this->defaults[ 'post_types-' . $pt . '-not_in_sitemap' ] = true;
				}
			}
			unset( $pt );
		}
		unset( $post_type_names, $filtered_post_types );

		$taxonomy_objects    = get_taxonomies( array( 'public' => true ), 'objects' );
		$filtered_taxonomies = apply_filters( 'wpseo_sitemaps_supported_taxonomies', $taxonomy_objects );
		if ( is_array( $filtered_taxonomies ) && $filtered_taxonomies !== array() ) {
			foreach ( $filtered_taxonomies as $tax ) {
				if ( isset( $tax->labels->name ) && trim( $tax->labels->name ) != '' ) {
					$this->defaults[ 'taxonomies-' . $tax->name . '-not_in_sitemap' ] = false;
				}
			}
			unset( $tax );
		}
		unset( $taxonomy_objects, $filtered_taxonomies );

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
			$switch_key = $this->get_switch_key( $key );

			switch ( $switch_key ) {
				/* integer fields */
				case 'entries-per-page':
					/**
					 * @todo [JRF/JRF => Yoast] add some more rules (minimum 50 or something
					 * - what should be the guideline?) and adjust error message
					 */
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$int = WPSEO_Utils::validate_int( $dirty[ $key ] );
						if ( $int !== false && $int > 0 ) {
							$clean[ $key ] = $int;
						}
						else {
							if ( isset( $old[ $key ] ) && $old[ $key ] !== '' ) {
								$int = WPSEO_Utils::validate_int( $old[ $key ] );
								if ( $int !== false && $int > 0 ) {
									$clean[ $key ] = $int;
								}
							}
							if ( function_exists( 'add_settings_error' ) ) {
								add_settings_error(
									$this->group_name, // Slug title of the setting.
									'_' . $key, // Suffix-id for the error message box.
									sprintf( __( '"Max entries per sitemap page" should be a positive number, which %s is not. Please correct.', 'wordpress-seo' ), '<strong>' . esc_html( sanitize_text_field( $dirty[ $key ] ) ) . '</strong>' ), // The error message.
									'error' // Error type, either 'error' or 'updated'.
								);
							}
						}
						unset( $int );
					}
					break;

				case 'excluded-posts' :
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						if ( $filtered_array = filter_var_array( explode( ',', $dirty[ $key ] ), FILTER_VALIDATE_INT ) ) {
							$clean[ $key ] = implode( ',', array_filter( $filtered_array, 'is_integer' ) );

							unset( $filtered_array );
						}
					}

					break;

				/*
				Boolean fields
				*/

				/*
				Covers:
				 *		'disable_author_sitemap':
				 * 		'disable_author_noposts':
				 * 		'enablexmlsitemap':
				 * 		'user_role-':
				 * 		'user_role' . $role_name . '-not_in_sitemap' fields
				 * 		'post_types-':
				 * 		'post_types-' . $pt->name . '-not_in_sitemap' fields
				 * 		'taxonomies-':
				 *		'taxonomies-' . $tax->name . '-not_in_sitemap' fields
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
		/*
		Make sure the values of the variable option key options are cleaned as they
			   may be retained and would not be cleaned/validated then
		*/
		if ( is_array( $option_value ) && $option_value !== array() ) {

			foreach ( $option_value as $key => $value ) {
				$switch_key = $this->get_switch_key( $key );

				// Similar to validation routine - any changes made there should be made here too.
				switch ( $switch_key ) {
					case 'user_role-': /* 'user_role-' . $role_name. '-not_in_sitemap' fields */
					case 'post_types-': /* 'post_types-' . $pt->name . '-not_in_sitemap' fields */
					case 'taxonomies-': /* 'taxonomies-' . $tax->name . '-not_in_sitemap' fields */
						$option_value[ $key ] = WPSEO_Utils::validate_bool( $value );
						break;
				}
			}
		}

		return $option_value;
	}
}
