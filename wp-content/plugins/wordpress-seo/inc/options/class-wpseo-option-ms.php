<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals\Options
 */

/**
 * Site option for Multisite installs only
 *
 * Overloads a number of methods of the abstract class to ensure the use of the correct site_option
 * WP functions.
 */
class WPSEO_Option_MS extends WPSEO_Option {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	public $option_name = 'wpseo_ms';

	/**
	 * Option group name for use in settings forms.
	 *
	 * @var string
	 */
	public $group_name = 'yoast_wpseo_multisite_options';

	/**
	 * Whether to include the option in the return for WPSEO_Options::get_all().
	 *
	 * @var bool
	 */
	public $include_in_all = false;

	/**
	 * Whether this option is only for when the install is multisite.
	 *
	 * @var bool
	 */
	public $multisite_only = true;

	/**
	 * Array of defaults for the option.
	 *
	 * Shouldn't be requested directly, use $this->get_defaults();
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * Available options for the 'access' setting. Used for input validation.
	 *
	 * {@internal Important: Make sure the options added to the array here are in line
	 *            with the keys for the options set for the select box in the
	 *            admin/pages/network.php file.}}
	 *
	 * @var array
	 */
	public static $allowed_access_options = [
		'admin',
		'superadmin',
	];

	/**
	 * Get the singleton instance of this class.
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
	 * Only run parent constructor in multisite context.
	 */
	public function __construct() {
		$allow_prefix   = self::ALLOW_KEY_PREFIX;
		$this->defaults = [
			'access'                                            => 'admin',
			'defaultblog'                                       => '', // Numeric blog ID or empty.
			"{$allow_prefix}disableadvanced_meta"               => true,
			"{$allow_prefix}ryte_indexability"                  => false,
			"{$allow_prefix}content_analysis_active"            => true,
			"{$allow_prefix}keyword_analysis_active"            => true,
			"{$allow_prefix}inclusive_language_analysis_active" => true,
			"{$allow_prefix}enable_admin_bar_menu"              => true,
			"{$allow_prefix}enable_cornerstone_content"         => true,
			"{$allow_prefix}enable_xml_sitemap"                 => true,
			"{$allow_prefix}enable_text_link_counter"           => true,
			"{$allow_prefix}enable_headless_rest_endpoints"     => true,
			"{$allow_prefix}enable_metabox_insights"            => true,
			"{$allow_prefix}enable_link_suggestions"            => true,
			"{$allow_prefix}tracking"                           => true,
			"{$allow_prefix}enable_enhanced_slack_sharing"      => true,
			"{$allow_prefix}semrush_integration_active"         => true,
			"{$allow_prefix}wincher_integration_active"         => false,
			"{$allow_prefix}remove_feed_global"                 => true,
			"{$allow_prefix}remove_feed_global_comments"        => true,
			"{$allow_prefix}remove_feed_post_comments"          => true,
			"{$allow_prefix}enable_index_now"                   => true,
			"{$allow_prefix}enable_ai_generator"                => true,
			"{$allow_prefix}remove_feed_authors"                => true,
			"{$allow_prefix}remove_feed_categories"             => true,
			"{$allow_prefix}remove_feed_tags"                   => true,
			"{$allow_prefix}remove_feed_custom_taxonomies"      => true,
			"{$allow_prefix}remove_feed_post_types"             => true,
			"{$allow_prefix}remove_feed_search"                 => true,
			"{$allow_prefix}remove_atom_rdf_feeds"              => true,
			"{$allow_prefix}remove_shortlinks"                  => true,
			"{$allow_prefix}remove_rest_api_links"              => true,
			"{$allow_prefix}remove_rsd_wlw_links"               => true,
			"{$allow_prefix}remove_oembed_links"                => true,
			"{$allow_prefix}remove_generator"                   => true,
			"{$allow_prefix}remove_emoji_scripts"               => true,
			"{$allow_prefix}remove_powered_by_header"           => true,
			"{$allow_prefix}remove_pingback_header"             => true,
			"{$allow_prefix}clean_campaign_tracking_urls"       => true,
			"{$allow_prefix}clean_permalinks"                   => true,
			"{$allow_prefix}search_cleanup"                     => true,
			"{$allow_prefix}search_cleanup_emoji"               => true,
			"{$allow_prefix}search_cleanup_patterns"            => true,
			"{$allow_prefix}redirect_search_pretty_urls"        => true,
			"{$allow_prefix}algolia_integration_active"         => true,
		];

		if ( is_multisite() ) {
			parent::__construct();

			add_filter( 'admin_title', [ 'Yoast_Input_Validation', 'add_yoast_admin_document_title_errors' ] );
		}
	}

	/**
	 * Add filters to make sure that the option default is returned if the option is not set
	 *
	 * @return void
	 */
	public function add_default_filters() {
		// Don't change, needs to check for false as could return prio 0 which would evaluate to false.
		if ( has_filter( 'default_site_option_' . $this->option_name, [ $this, 'get_defaults' ] ) === false ) {
			add_filter( 'default_site_option_' . $this->option_name, [ $this, 'get_defaults' ] );
		}
	}

	/**
	 * Remove the default filters.
	 * Called from the validate() method to prevent failure to add new options.
	 *
	 * @return void
	 */
	public function remove_default_filters() {
		remove_filter( 'default_site_option_' . $this->option_name, [ $this, 'get_defaults' ] );
	}

	/**
	 * Add filters to make sure that the option is merged with its defaults before being returned.
	 *
	 * @return void
	 */
	public function add_option_filters() {
		// Don't change, needs to check for false as could return prio 0 which would evaluate to false.
		if ( has_filter( 'site_option_' . $this->option_name, [ $this, 'get_option' ] ) === false ) {
			add_filter( 'site_option_' . $this->option_name, [ $this, 'get_option' ] );
		}
	}

	/**
	 * Remove the option filters.
	 * Called from the clean_up methods to make sure we retrieve the original old option.
	 *
	 * @return void
	 */
	public function remove_option_filters() {
		remove_filter( 'site_option_' . $this->option_name, [ $this, 'get_option' ] );
	}

	/* *********** METHODS influencing add_uption(), update_option() and saving from admin pages *********** */

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

		foreach ( $clean as $key => $value ) {
			switch ( $key ) {
				case 'access':
					if ( isset( $dirty[ $key ] ) && in_array( $dirty[ $key ], self::$allowed_access_options, true ) ) {
						$clean[ $key ] = $dirty[ $key ];
					}
					elseif ( function_exists( 'add_settings_error' ) ) {
						add_settings_error(
							$this->group_name, // Slug title of the setting.
							$key, // Suffix-ID for the error message box.
							/* translators: %1$s expands to the option name and %2$sexpands to Yoast SEO */
							sprintf( __( '%1$s is not a valid choice for who should be allowed access to the %2$s settings. Value reset to the default.', 'wordpress-seo' ), esc_html( sanitize_text_field( $dirty[ $key ] ) ), 'Yoast SEO' ), // The error message.
							'error' // Message type.
						);
					}
					break;

				case 'defaultblog':
					if ( isset( $dirty[ $key ] ) && ( $dirty[ $key ] !== '' && $dirty[ $key ] !== '-' ) ) {
						$int = WPSEO_Utils::validate_int( $dirty[ $key ] );
						if ( $int !== false && $int > 0 ) {
							// Check if a valid blog number has been received.
							$exists = get_blog_details( $int, false );
							if ( $exists && $exists->deleted === '0' ) {
								$clean[ $key ] = $int;
							}
							elseif ( function_exists( 'add_settings_error' ) ) {
								add_settings_error(
									$this->group_name, // Slug title of the setting.
									$key, // Suffix-ID for the error message box.
									esc_html__( 'The default blog setting must be the numeric blog id of the blog you want to use as default.', 'wordpress-seo' )
										. '<br>'
										. sprintf(
											/* translators: %s is the ID number of a blog. */
											esc_html__( 'This must be an existing blog. Blog %s does not exist or has been marked as deleted.', 'wordpress-seo' ),
											'<strong>' . esc_html( sanitize_text_field( $dirty[ $key ] ) ) . '</strong>'
										), // The error message.
									'error' // Message type.
								);
							}
							unset( $exists );
						}
						elseif ( function_exists( 'add_settings_error' ) ) {
							add_settings_error(
								$this->group_name, // Slug title of the setting.
								$key, // Suffix-ID for the error message box.
								esc_html__( 'The default blog setting must be the numeric blog id of the blog you want to use as default.', 'wordpress-seo' ) . '<br>' . esc_html__( 'No numeric value was received.', 'wordpress-seo' ), // The error message.
								'error' // Message type.
							);
						}
						unset( $int );
					}
					break;

				default:
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;
			}
		}

		return $clean;
	}
}
