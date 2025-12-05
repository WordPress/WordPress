<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Internals\Options
 */

use Yoast\WP\SEO\Config\Schema_Types;

/**
 * Option: wpseo_titles.
 */
class WPSEO_Option_Titles extends WPSEO_Option {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	public $option_name = 'wpseo_titles';

	/**
	 * Array of defaults for the option.
	 *
	 * Shouldn't be requested directly, use $this->get_defaults();
	 *
	 * {@internal Note: Some of the default values are added via the translate_defaults() method.}}
	 *
	 * @var string[]
	 */
	protected $defaults = [
		// Form fields.
		'forcerewritetitle'                => false,
		'separator'                        => 'sc-dash',
		'title-home-wpseo'                 => '%%sitename%% %%page%% %%sep%% %%sitedesc%%', // Text field.
		'title-author-wpseo'               => '', // Text field.
		'title-archive-wpseo'              => '%%date%% %%page%% %%sep%% %%sitename%%', // Text field.
		'title-search-wpseo'               => '', // Text field.
		'title-404-wpseo'                  => '', // Text field.

		'social-title-author-wpseo'        => '%%name%%', // Text field.
		'social-title-archive-wpseo'       => '%%date%%', // Text field.
		'social-description-author-wpseo'  => '', // Text area.
		'social-description-archive-wpseo' => '', // Text area.
		'social-image-url-author-wpseo'    => '', // Hidden input field.
		'social-image-url-archive-wpseo'   => '', // Hidden input field.
		'social-image-id-author-wpseo'     => 0, // Hidden input field.
		'social-image-id-archive-wpseo'    => 0, // Hidden input field.

		'metadesc-home-wpseo'              => '', // Text area.
		'metadesc-author-wpseo'            => '', // Text area.
		'metadesc-archive-wpseo'           => '', // Text area.
		'rssbefore'                        => '', // Text area.
		'rssafter'                         => '', // Text area.

		'noindex-author-wpseo'             => false,
		'noindex-author-noposts-wpseo'     => true,
		'noindex-archive-wpseo'            => true,

		'disable-author'                   => false,
		'disable-date'                     => false,
		'disable-post_format'              => false,
		'disable-attachment'               => true,

		'breadcrumbs-404crumb'             => '', // Text field.
		'breadcrumbs-display-blog-page'    => true,
		'breadcrumbs-boldlast'             => false,
		'breadcrumbs-archiveprefix'        => '', // Text field.
		'breadcrumbs-enable'               => true,
		'breadcrumbs-home'                 => '', // Text field.
		'breadcrumbs-prefix'               => '', // Text field.
		'breadcrumbs-searchprefix'         => '', // Text field.
		'breadcrumbs-sep'                  => '»', // Text field.

		'website_name'                     => '',
		'person_name'                      => '',
		'person_logo'                      => '',
		'person_logo_id'                   => 0,
		'alternate_website_name'           => '',
		'company_logo'                     => '',
		'company_logo_id'                  => 0,
		'company_logo_meta'                => false,
		'person_logo_meta'                 => false,
		'company_name'                     => '',
		'company_alternate_name'           => '',
		'company_or_person'                => 'company',
		'company_or_person_user_id'        => false,

		'stripcategorybase'                => false,

		'open_graph_frontpage_title'       => '%%sitename%%', // Text field.
		'open_graph_frontpage_desc'        => '', // Text field.
		'open_graph_frontpage_image'       => '', // Text field.
		'open_graph_frontpage_image_id'    => 0,

		'publishing_principles_id'         => 0,
		'ownership_funding_info_id'        => 0,
		'actionable_feedback_policy_id'    => 0,
		'corrections_policy_id'            => 0,
		'ethics_policy_id'                 => 0,
		'diversity_policy_id'              => 0,
		'diversity_staffing_report_id'     => 0,

		'org-description'                  => '',
		'org-email'                        => '',
		'org-phone'                        => '',
		'org-legal-name'                   => '',
		'org-founding-date'                => '',
		'org-number-employees'             => '',

		'org-vat-id'                       => '',
		'org-tax-id'                       => '',
		'org-iso'                          => '',
		'org-duns'                         => '',
		'org-leicode'                      => '',
		'org-naics'                        => '',

		/*
		 * Uses enrich_defaults to add more along the lines of:
		 * - 'title-' . $pt->name                => ''; // Text field.
		 * - 'metadesc-' . $pt->name             => ''; // Text field.
		 * - 'noindex-' . $pt->name              => false;
		 * - 'display-metabox-pt-' . $pt->name   => false;
		 *
		 * - 'title-ptarchive-' . $pt->name      => ''; // Text field.
		 * - 'metadesc-ptarchive-' . $pt->name   => ''; // Text field.
		 * - 'bctitle-ptarchive-' . $pt->name    => ''; // Text field.
		 * - 'noindex-ptarchive-' . $pt->name    => false;
		 *
		 * - 'title-tax-' . $tax->name           => '''; // Text field.
		 * - 'metadesc-tax-' . $tax->name        => ''; // Text field.
		 * - 'noindex-tax-' . $tax->name         => false;
		 * - 'display-metabox-tax-' . $tax->name => false;
		 *
		 * - 'schema-page-type-' . $pt->name     => 'WebPage';
		 * - 'schema-article-type-' . $pt->name  => 'Article';
		 */
	];

	/**
	 * Used for "caching" during pageload.
	 *
	 * @var string[]|null
	 */
	protected $enriched_defaults = null;

	/**
	 * Array of variable option name patterns for the option.
	 *
	 * @var string[]
	 */
	protected $variable_array_key_patterns = [
		'title-',
		'metadesc-',
		'noindex-',
		'display-metabox-pt-',
		'bctitle-ptarchive-',
		'post_types-',
		'taxonomy-',
		'schema-page-type-',
		'schema-article-type-',
		'social-title-',
		'social-description-',
		'social-image-url-',
		'social-image-id-',
		'org-',
	];

	/**
	 * Array of sub-options which should not be overloaded with multi-site defaults.
	 *
	 * @var string[]
	 */
	public $ms_exclude = [
		'forcerewritetitle',
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
		add_action( 'update_option_' . $this->option_name, [ 'WPSEO_Utils', 'clear_cache' ] );
		add_action( 'init', [ $this, 'end_of_init' ], 999 );

		add_action( 'registered_post_type', [ $this, 'invalidate_enrich_defaults_cache' ] );
		add_action( 'unregistered_post_type', [ $this, 'invalidate_enrich_defaults_cache' ] );
		add_action( 'registered_taxonomy', [ $this, 'invalidate_enrich_defaults_cache' ] );
		add_action( 'unregistered_taxonomy', [ $this, 'invalidate_enrich_defaults_cache' ] );

		add_filter( 'admin_title', [ 'Yoast_Input_Validation', 'add_yoast_admin_document_title_errors' ] );
	}

	/**
	 * Make sure we can recognize the right action for the double cleaning.
	 *
	 * @return void
	 */
	public function end_of_init() {
		do_action( 'wpseo_double_clean_titles' );
	}

	/**
	 * Get the singleton instance of this class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the available separator options.
	 *
	 * @return string[]
	 */
	public function get_separator_options() {
		$separators = wp_list_pluck( self::get_separator_option_list(), 'option' );

		/**
		 * Allow altering the array with separator options.
		 *
		 * @param array $separator_options Array with the separator options.
		 */
		$filtered_separators = apply_filters( 'wpseo_separator_options', $separators );

		if ( is_array( $filtered_separators ) && $filtered_separators !== [] ) {
			$separators = array_merge( $separators, $filtered_separators );
		}

		return $separators;
	}

	/**
	 * Get the available separator options aria-labels.
	 *
	 * @return string[] Array with the separator options aria-labels.
	 */
	public function get_separator_options_for_display() {
		$separators     = $this->get_separator_options();
		$separator_list = self::get_separator_option_list();

		$separator_options = [];

		foreach ( $separators as $key => $label ) {
			$aria_label = ( $separator_list[ $key ]['label'] ?? '' );

			$separator_options[ $key ] = [
				'label'      => $label,
				'aria_label' => $aria_label,
			];
		}

		return $separator_options;
	}

	/**
	 * Translate strings used in the option defaults.
	 *
	 * @return void
	 */
	public function translate_defaults() {
		/* translators: 1: Author name; 2: Site name. */
		$this->defaults['title-author-wpseo'] = sprintf( __( '%1$s, Author at %2$s', 'wordpress-seo' ), '%%name%%', '%%sitename%%' ) . ' %%page%% ';
		/* translators: %s expands to the search phrase. */
		$this->defaults['title-search-wpseo'] = sprintf( __( 'You searched for %s', 'wordpress-seo' ), '%%searchphrase%%' ) . ' %%page%% %%sep%% %%sitename%%';
		$this->defaults['title-404-wpseo']    = __( 'Page not found', 'wordpress-seo' ) . ' %%sep%% %%sitename%%';
		/* translators: 1: link to post; 2: link to blog. */
		$this->defaults['rssafter'] = sprintf( __( 'The post %1$s appeared first on %2$s.', 'wordpress-seo' ), '%%POSTLINK%%', '%%BLOGLINK%%' );

		$this->defaults['breadcrumbs-404crumb']      = __( 'Error 404: Page not found', 'wordpress-seo' );
		$this->defaults['breadcrumbs-archiveprefix'] = __( 'Archives for', 'wordpress-seo' );
		$this->defaults['breadcrumbs-home']          = __( 'Home', 'wordpress-seo' );
		$this->defaults['breadcrumbs-searchprefix']  = __( 'You searched for', 'wordpress-seo' );
	}

	/**
	 * Add dynamically created default options based on available post types and taxonomies.
	 *
	 * @return  void
	 */
	public function enrich_defaults() {
		$enriched_defaults = $this->enriched_defaults;
		if ( $enriched_defaults !== null ) {
			$this->defaults += $enriched_defaults;
			return;
		}

		$enriched_defaults = [];

		/*
		 * Retrieve all the relevant post type and taxonomy arrays.
		 *
		 * WPSEO_Post_Type::get_accessible_post_types() should *not* be used here.
		 * These are the defaults and can be prepared for any public post type.
		 */
		$post_type_objects = get_post_types( [ 'public' => true ], 'objects' );

		if ( $post_type_objects ) {
			/* translators: %s expands to the name of a post type (plural). */
			$archive = sprintf( __( '%s Archive', 'wordpress-seo' ), '%%pt_plural%%' );

			foreach ( $post_type_objects as $pt ) {
				$enriched_defaults[ 'title-' . $pt->name ]                   = '%%title%% %%page%% %%sep%% %%sitename%%'; // Text field.
				$enriched_defaults[ 'metadesc-' . $pt->name ]                = ''; // Text area.
				$enriched_defaults[ 'noindex-' . $pt->name ]                 = false;
				$enriched_defaults[ 'display-metabox-pt-' . $pt->name ]      = true;
				$enriched_defaults[ 'post_types-' . $pt->name . '-maintax' ] = 0; // Select box.
				$enriched_defaults[ 'schema-page-type-' . $pt->name ]        = 'WebPage';
				$enriched_defaults[ 'schema-article-type-' . $pt->name ]     = ( $pt->name === 'post' ) ? 'Article' : 'None';

				if ( $pt->name !== 'attachment' ) {
					$enriched_defaults[ 'social-title-' . $pt->name ]       = '%%title%%'; // Text field.
					$enriched_defaults[ 'social-description-' . $pt->name ] = ''; // Text area.
					$enriched_defaults[ 'social-image-url-' . $pt->name ]   = ''; // Hidden input field.
					$enriched_defaults[ 'social-image-id-' . $pt->name ]    = 0; // Hidden input field.
				}

				// Custom post types that have archives.
				if ( ! $pt->_builtin && WPSEO_Post_Type::has_archive( $pt ) ) {
					$enriched_defaults[ 'title-ptarchive-' . $pt->name ]              = $archive . ' %%page%% %%sep%% %%sitename%%'; // Text field.
					$enriched_defaults[ 'metadesc-ptarchive-' . $pt->name ]           = ''; // Text area.
					$enriched_defaults[ 'bctitle-ptarchive-' . $pt->name ]            = ''; // Text field.
					$enriched_defaults[ 'noindex-ptarchive-' . $pt->name ]            = false;
					$enriched_defaults[ 'social-title-ptarchive-' . $pt->name ]       = $archive; // Text field.
					$enriched_defaults[ 'social-description-ptarchive-' . $pt->name ] = ''; // Text area.
					$enriched_defaults[ 'social-image-url-ptarchive-' . $pt->name ]   = ''; // Hidden input field.
					$enriched_defaults[ 'social-image-id-ptarchive-' . $pt->name ]    = 0; // Hidden input field.
				}
			}
		}

		$taxonomy_objects = get_taxonomies( [ 'public' => true ], 'object' );

		if ( $taxonomy_objects ) {
			/* translators: %s expands to the variable used for term title. */
			$archives = sprintf( __( '%s Archives', 'wordpress-seo' ), '%%term_title%%' );

			foreach ( $taxonomy_objects as $tax ) {
				$enriched_defaults[ 'title-tax-' . $tax->name ]           = $archives . ' %%page%% %%sep%% %%sitename%%'; // Text field.
				$enriched_defaults[ 'metadesc-tax-' . $tax->name ]        = ''; // Text area.
				$enriched_defaults[ 'display-metabox-tax-' . $tax->name ] = true;

				$enriched_defaults[ 'noindex-tax-' . $tax->name ] = ( $tax->name === 'post_format' );

				$enriched_defaults[ 'social-title-tax-' . $tax->name ]       = $archives; // Text field.
				$enriched_defaults[ 'social-description-tax-' . $tax->name ] = ''; // Text area.
				$enriched_defaults[ 'social-image-url-tax-' . $tax->name ]   = ''; // Hidden input field.
				$enriched_defaults[ 'social-image-id-tax-' . $tax->name ]    = 0; // Hidden input field.

				$enriched_defaults[ 'taxonomy-' . $tax->name . '-ptparent' ] = 0; // Select box;.
			}
		}

		$this->enriched_defaults = $enriched_defaults;
		$this->defaults         += $enriched_defaults;
	}

	/**
	 * Invalidates enrich_defaults() cache.
	 *
	 * Called from actions:
	 * - (un)registered_post_type
	 * - (un)registered_taxonomy
	 *
	 * @return void
	 */
	public function invalidate_enrich_defaults_cache() {
		$this->enriched_defaults = null;
	}

	/**
	 * Validate the option.
	 *
	 * @param string[] $dirty New value for the option.
	 * @param string[] $clean Clean value for the option, normally the defaults.
	 * @param string[] $old   Old value of the option.
	 *
	 * @return string[] Validated clean value for the option to be saved to the database.
	 */
	protected function validate_option( $dirty, $clean, $old ) {
		$allowed_post_types = $this->get_allowed_post_types();

		foreach ( $clean as $key => $value ) {
			$switch_key = $this->get_switch_key( $key );

			switch ( $switch_key ) {
				// Only ever set programmatically, so no reason for intense validation.
				case 'company_logo_meta':
				case 'person_logo_meta':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = $dirty[ $key ];
					}
					break;

				/* Breadcrumbs text fields. */
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

				/*
				 * Text fields.
				 */

				/*
				 * Covers:
				 *  'title-home-wpseo', 'title-author-wpseo', 'title-archive-wpseo', // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This isn't commented out code.
				 *  'title-search-wpseo', 'title-404-wpseo'
				 *  'title-' . $pt->name
				 *  'title-ptarchive-' . $pt->name
				 *  'title-tax-' . $tax->name
				 *  'social-title-' . $pt->name
				 *  'social-title-ptarchive-' . $pt->name
				 *  'social-title-tax-' . $tax->name
				 *  'social-title-author-wpseo', 'social-title-archive-wpseo'
				 *  'open_graph_frontpage_title'
				 */
				case 'org-':
				case 'website_name':
				case 'alternate_website_name':
				case 'title-':
				case 'social-title-':
				case 'open_graph_frontpage_title':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $dirty[ $key ] );
					}
					break;

				case 'company_or_person':
					if ( isset( $dirty[ $key ] ) ) {
						if ( in_array( $dirty[ $key ], [ 'company', 'person' ], true ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
						else {
							$defaults      = $this->get_defaults();
							$clean[ $key ] = $defaults['company_or_person'];
						}
					}
					break;

				/*
				 * Covers:
				 *  'company_logo', 'person_logo' // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This isn't commented out code.
				 */
				case 'company_logo':
				case 'person_logo':
				case 'open_graph_frontpage_image':
					// When a logo changes, we need to ditch the caches we have for it.
					unset( $clean[ $switch_key . '_id' ] );
					unset( $clean[ $switch_key . '_meta' ] );
					$this->validate_url( $key, $dirty, $old, $clean );
					break;

				/*
				 * Covers:
				 *  'social-image-url-' . $pt->name
				 *  'social-image-url-ptarchive-' . $pt->name
				 *  'social-image-url-tax-' . $tax->name
				 *  'social-image-url-author-wpseo', 'social-image-url-archive-wpseo'
				 */
				case 'social-image-url-':
					$this->validate_url( $key, $dirty, $old, $clean );
					break;

				/*
				 * Covers:
				 *  'metadesc-home-wpseo', 'metadesc-author-wpseo', 'metadesc-archive-wpseo'
				 *  'metadesc-' . $pt->name
				 *  'metadesc-ptarchive-' . $pt->name
				 *  'metadesc-tax-' . $tax->name
				 *  and also:
				 *  'bctitle-ptarchive-' . $pt->name
				 *  'social-description-' . $pt->name
				 *  'social-description-ptarchive-' . $pt->name
				 *  'social-description-tax-' . $tax->name
				 *  'social-description-author-wpseo', 'social-description-archive-wpseo'
				 *  'open_graph_frontpage_desc'
				 */
				case 'metadesc-':
				case 'bctitle-ptarchive-':
				case 'company_name':
				case 'company_alternate_name':
				case 'person_name':
				case 'social-description-':
				case 'open_graph_frontpage_desc':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {
						$clean[ $key ] = WPSEO_Utils::sanitize_text_field( $dirty[ $key ] );
					}
					break;

				/*
				 * Covers: 'rssbefore', 'rssafter' // phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- This isn't commented out code.
				 */
				case 'rssbefore':
				case 'rssafter':
					if ( isset( $dirty[ $key ] ) ) {
						$clean[ $key ] = wp_kses_post( $dirty[ $key ] );
					}
					break;

				/* 'post_types-' . $pt->name . '-maintax' fields. */
				case 'post_types-':
					$post_type  = str_replace( [ 'post_types-', '-maintax' ], '', $key );
					$taxonomies = get_object_taxonomies( $post_type, 'names' );

					if ( isset( $dirty[ $key ] ) ) {
						if ( $taxonomies !== [] && in_array( $dirty[ $key ], $taxonomies, true ) ) {
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

							/*
							 * @todo [JRF => whomever] Maybe change the untranslated $pt name in the
							 * error message to the nicely translated label ?
							 */
							add_settings_error(
								$this->group_name, // Slug title of the setting.
								$key, // Suffix-id for the error message box.
								/* translators: %s expands to a post type. */
								sprintf( __( 'Please select a valid taxonomy for post type "%s"', 'wordpress-seo' ), $post_type ), // The error message.
								'error' // Message type.
							);
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$clean[ $key ] = sanitize_title_with_dashes( $old[ $key ] );
					}
					unset( $taxonomies, $post_type );
					break;

				/* 'taxonomy-' . $tax->name . '-ptparent' fields. */
				case 'taxonomy-':
					if ( isset( $dirty[ $key ] ) ) {
						if ( $allowed_post_types !== [] && in_array( $dirty[ $key ], $allowed_post_types, true ) ) {
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

							/*
							 * @todo [JRF =? whomever] Maybe change the untranslated $tax name in the
							 * error message to the nicely translated label ?
							 */
							$tax = str_replace( [ 'taxonomy-', '-ptparent' ], '', $key );
							add_settings_error(
								$this->group_name, // Slug title of the setting.
								'_' . $tax, // Suffix-ID for the error message box.
								/* translators: %s expands to a taxonomy slug. */
								sprintf( __( 'Please select a valid post type for taxonomy "%s"', 'wordpress-seo' ), $tax ), // The error message.
								'error' // Message type.
							);
							unset( $tax );
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$clean[ $key ] = sanitize_key( $old[ $key ] );
					}
					break;

				/*
				 * Covers:
				 *  'company_or_person_user_id'
				 *  'company_logo_id', 'person_logo_id', 'open_graph_frontpage_image_id'
				 *  'social-image-id-' . $pt->name
				 *  'social-image-id-ptarchive-' . $pt->name
				 *  'social-image-id-tax-' . $tax->name
				 *  'social-image-id-author-wpseo', 'social-image-id-archive-wpseo'
				 */
				case 'company_or_person_user_id':
				case 'company_logo_id':
				case 'person_logo_id':
				case 'social-image-id-':
				case 'open_graph_frontpage_image_id':
				case 'publishing_principles_id':
				case 'ownership_funding_info_id':
				case 'actionable_feedback_policy_id':
				case 'corrections_policy_id':
				case 'ethics_policy_id':
				case 'diversity_policy_id':
				case 'diversity_staffing_report_id':
					if ( isset( $dirty[ $key ] ) ) {
						$int = WPSEO_Utils::validate_int( $dirty[ $key ] );
						if ( $int !== false && $int >= 0 ) {
							$clean[ $key ] = $int;
						}
					}
					elseif ( isset( $old[ $key ] ) ) {
						$int = WPSEO_Utils::validate_int( $old[ $key ] );
						if ( $int !== false && $int >= 0 ) {
							$clean[ $key ] = $int;
						}
					}
					break;
				/* Separator field - Radio. */
				case 'separator':
					if ( isset( $dirty[ $key ] ) && $dirty[ $key ] !== '' ) {

						// Get separator fields.
						$separator_fields = $this->get_separator_options();

						// Check if the given separator exists.
						if ( isset( $separator_fields[ $dirty[ $key ] ] ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
					}
					break;

				case 'schema-page-type-':
					if ( isset( $dirty[ $key ] ) && is_string( $dirty[ $key ] ) ) {
						if ( array_key_exists( $dirty[ $key ], Schema_Types::PAGE_TYPES ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
						else {
							$defaults      = $this->get_defaults();
							$post_type     = str_replace( $switch_key, '', $key );
							$clean[ $key ] = $defaults[ $switch_key . $post_type ];
						}
					}
					break;
				case 'schema-article-type-':
					if ( isset( $dirty[ $key ] ) && is_string( $dirty[ $key ] ) ) {
						/**
						 * Filter: 'wpseo_schema_article_types' - Allow developers to filter the available article types.
						 *
						 * Make sure when you filter this to also filter `wpseo_schema_article_types_labels`.
						 *
						 * @param array $schema_article_types The available schema article types.
						 */
						if ( array_key_exists( $dirty[ $key ], apply_filters( 'wpseo_schema_article_types', Schema_Types::ARTICLE_TYPES ) ) ) {
							$clean[ $key ] = $dirty[ $key ];
						}
						else {
							$defaults      = $this->get_defaults();
							$post_type     = str_replace( $switch_key, '', $key );
							$clean[ $key ] = $defaults[ $switch_key . $post_type ];
						}
					}
					break;

				/*
				 * Boolean fields.
				 */

				/*
				 * Covers:
				 *  'noindex-author-wpseo', 'noindex-author-noposts-wpseo', 'noindex-archive-wpseo'
				 *  'noindex-' . $pt->name
				 *  'noindex-ptarchive-' . $pt->name
				 *  'noindex-tax-' . $tax->name
				 *  'forcerewritetitle':
				 *  'noodp':
				 *  'noydir':
				 *  'disable-author':
				 *  'disable-date':
				 *  'disable-post_format';
				 *  'noindex-'
				 *  'display-metabox-pt-'
				 *  'display-metabox-pt-'. $pt->name
				 *  'display-metabox-tax-'
				 *  'display-metabox-tax-' . $tax->name
				 *  'breadcrumbs-display-blog-page'
				 *  'breadcrumbs-boldlast'
				 *  'breadcrumbs-enable'
				 *  'stripcategorybase'
				 */
				default:
					$clean[ $key ] = ( isset( $dirty[ $key ] ) ? WPSEO_Utils::validate_bool( $dirty[ $key ] ) : false );
					break;
			}
		}

		return $clean;
	}

	/**
	 * Retrieve a list of the allowed post types as breadcrumb parent for a taxonomy.
	 * Helper method for validation.
	 *
	 * {@internal Don't make static as new types may still be registered.}}
	 *
	 * @return string[]
	 */
	protected function get_allowed_post_types() {
		$allowed_post_types = [];

		/*
		 * WPSEO_Post_Type::get_accessible_post_types() should *not* be used here.
		 */
		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		if ( get_option( 'show_on_front' ) === 'page' && get_option( 'page_for_posts' ) > 0 ) {
			$allowed_post_types[] = 'post';
		}

		if ( is_array( $post_types ) && $post_types !== [] ) {
			foreach ( $post_types as $type ) {
				if ( WPSEO_Post_Type::has_archive( $type ) ) {
					$allowed_post_types[] = $type->name;
				}
			}
		}

		return $allowed_post_types;
	}

	/**
	 * Clean a given option value.
	 *
	 * @param string[]      $option_value          Old (not merged with defaults or filtered) option value to clean according to the rules for this option.
	 * @param string[]|null $current_version       Optional. Version from which to upgrade, if not set, version specific upgrades will be disregarded.
	 * @param string[]|null $all_old_option_values Optional. Only used when importing old options to have access to the real old values, in contrast to the saved ones.
	 *
	 * @return string[] Cleaned option.
	 */
	protected function clean_option( $option_value, $current_version = null, $all_old_option_values = null ) {
		static $original = null;

		// Double-run this function to ensure renaming of the taxonomy options will work.
		if ( ! isset( $original )
			&& has_action( 'wpseo_double_clean_titles', [ $this, 'clean' ] ) === false
		) {
			add_action( 'wpseo_double_clean_titles', [ $this, 'clean' ] );
			$original = $option_value;
		}

		/*
		 * Move options from very old option to this one.
		 *
		 * {@internal Don't rename to the 'current' names straight away as that would prevent
		 *            the rename/unset combi below from working.}}
		 *
		 * @todo [JRF] Maybe figure out a smarter way to deal with this.
		 */
		$old_option = null;
		if ( isset( $all_old_option_values ) ) {
			// Ok, we have an import.
			if ( isset( $all_old_option_values['wpseo_indexation'] ) && is_array( $all_old_option_values['wpseo_indexation'] ) && $all_old_option_values['wpseo_indexation'] !== [] ) {
				$old_option = $all_old_option_values['wpseo_indexation'];
			}
		}
		else {
			$old_option = get_option( 'wpseo_indexation' );
		}
		if ( is_array( $old_option ) && $old_option !== [] ) {
			$move = [
				'noindexauthor'     => 'noindex-author',
				'disableauthor'     => 'disable-author',
				'noindexdate'       => 'noindex-archive',
				'noindexcat'        => 'noindex-category',
				'noindextag'        => 'noindex-post_tag',
				'noindexpostformat' => 'noindex-post_format',
			];
			foreach ( $move as $old => $new ) {
				if ( isset( $old_option[ $old ] ) && ! isset( $option_value[ $new ] ) ) {
					$option_value[ $new ] = $old_option[ $old ];
				}
			}
			unset( $move, $old, $new );
		}
		unset( $old_option );

		// Fix wrongness created by buggy version 1.2.2.
		if ( isset( $option_value['title-home'] ) && $option_value['title-home'] === '%%sitename%% - %%sitedesc%% - 12345' ) {
			$option_value['title-home-wpseo'] = '%%sitename%% - %%sitedesc%%';
		}

		/*
		 * Renaming these options to avoid ever overwritting these if a (bloody stupid) user /
		 * programmer would use any of the following as a custom post type or custom taxonomy:
		 * 'home', 'author', 'archive', 'search', '404', 'subpages'.
		 *
		 * Similarly, renaming the tax options to avoid a custom post type and a taxonomy
		 * with the same name occupying the same option.
		 */
		$rename = [
			'title-home'       => 'title-home-wpseo',
			'title-author'     => 'title-author-wpseo',
			'title-archive'    => 'title-archive-wpseo',
			'title-search'     => 'title-search-wpseo',
			'title-404'        => 'title-404-wpseo',
			'metadesc-home'    => 'metadesc-home-wpseo',
			'metadesc-author'  => 'metadesc-author-wpseo',
			'metadesc-archive' => 'metadesc-archive-wpseo',
			'noindex-author'   => 'noindex-author-wpseo',
			'noindex-archive'  => 'noindex-archive-wpseo',
		];
		foreach ( $rename as $old => $new ) {
			if ( isset( $option_value[ $old ] ) && ! isset( $option_value[ $new ] ) ) {
				$option_value[ $new ] = $option_value[ $old ];
				unset( $option_value[ $old ] );
			}
		}
		unset( $rename, $old, $new );

		/*
		 * {@internal This clean-up action can only be done effectively once the taxonomies
		 *            and post_types have been registered, i.e. at the end of the init action.}}
		 */
		if ( ( isset( $original ) && current_filter() === 'wpseo_double_clean_titles' ) || did_action( 'wpseo_double_clean_titles' ) > 0 ) {
			$rename = [
				'title-'           => 'title-tax-',
				'metadesc-'        => 'metadesc-tax-',
				'noindex-'         => 'noindex-tax-',
				'tax-hideeditbox-' => 'hideeditbox-tax-',

			];

			$taxonomy_names  = get_taxonomies( [ 'public' => true ], 'names' );
			$post_type_names = get_post_types( [ 'public' => true ], 'names' );
			$defaults        = $this->get_defaults();
			if ( $taxonomy_names !== [] ) {
				foreach ( $taxonomy_names as $tax ) {
					foreach ( $rename as $old_prefix => $new_prefix ) {
						if (
							( isset( $original[ $old_prefix . $tax ] ) && ! isset( $original[ $new_prefix . $tax ] ) )
							&& ( ! isset( $option_value[ $new_prefix . $tax ] )
								|| ( isset( $option_value[ $new_prefix . $tax ] )
									&& $option_value[ $new_prefix . $tax ] === $defaults[ $new_prefix . $tax ] ) )
						) {
							$option_value[ $new_prefix . $tax ] = $original[ $old_prefix . $tax ];

							/*
							 * Check if there is a cpt with the same name as the tax,
							 * if so, we should make sure that the old setting hasn't been removed.
							 */
							if ( ! isset( $post_type_names[ $tax ] ) && isset( $option_value[ $old_prefix . $tax ] ) ) {
								unset( $option_value[ $old_prefix . $tax ] );
							}
							elseif ( isset( $post_type_names[ $tax ] ) && ! isset( $option_value[ $old_prefix . $tax ] ) ) {
								$option_value[ $old_prefix . $tax ] = $original[ $old_prefix . $tax ];
							}

							if ( $old_prefix === 'tax-hideeditbox-' ) {
								unset( $option_value[ $old_prefix . $tax ] );
							}
						}
					}
				}
			}
			unset( $rename, $taxonomy_names, $post_type_names, $defaults, $tax, $old_prefix, $new_prefix );
		}

		return $option_value;
	}

	/**
	 * Make sure that any set option values relating to post_types and/or taxonomies are retained,
	 * even when that post_type or taxonomy may not yet have been registered.
	 *
	 * {@internal Overrule the abstract class version of this to make sure one extra renamed
	 *            variable key does not get removed. IMPORTANT: keep this method in line with
	 *            the parent on which it is based!}}
	 *
	 * @param string[] $dirty Original option as retrieved from the database.
	 * @param string[] $clean Filtered option where any options which shouldn't be in our option
	 *                     have already been removed and any options which weren't set
	 *                     have been set to their defaults.
	 *
	 * @return string[]
	 */
	protected function retain_variable_keys( $dirty, $clean ) {
		if ( ( is_array( $this->variable_array_key_patterns ) && $this->variable_array_key_patterns !== [] ) && ( is_array( $dirty ) && $dirty !== [] ) ) {

			// Add the extra pattern.
			$patterns   = $this->variable_array_key_patterns;
			$patterns[] = 'tax-hideeditbox-';

			/**
			 * Allow altering the array with variable array key patterns.
			 *
			 * @param array $patterns Array with the variable array key patterns.
			 */
			$patterns = apply_filters( 'wpseo_option_titles_variable_array_key_patterns', $patterns );

			foreach ( $dirty as $key => $value ) {

				// Do nothing if already in filtered option array.
				if ( isset( $clean[ $key ] ) ) {
					continue;
				}

				foreach ( $patterns as $pattern ) {
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
	 * Retrieves a list of separator options.
	 *
	 * @return string[] An array of the separator options.
	 */
	protected static function get_separator_option_list() {
		$separators = [
			'sc-dash'   => [
				'option' => '-',
				'label'  => __( 'Dash', 'wordpress-seo' ),
			],
			'sc-ndash'  => [
				'option' => '&#8211;',
				'label'  => __( 'En dash', 'wordpress-seo' ),
			],
			'sc-mdash'  => [
				'option' => '&#8212;',
				'label'  => __( 'Em dash', 'wordpress-seo' ),
			],
			'sc-colon'  => [
				'option' => ':',
				'label'  => __( 'Colon', 'wordpress-seo' ),
			],
			'sc-middot' => [
				'option' => '&#183;',
				'label'  => __( 'Middle dot', 'wordpress-seo' ),
			],
			'sc-bull'   => [
				'option' => '&#8226;',
				'label'  => __( 'Bullet', 'wordpress-seo' ),
			],
			'sc-star'   => [
				'option' => '*',
				'label'  => __( 'Asterisk', 'wordpress-seo' ),
			],
			'sc-smstar' => [
				'option' => '&#8902;',
				'label'  => __( 'Low asterisk', 'wordpress-seo' ),
			],
			'sc-pipe'   => [
				'option' => '|',
				'label'  => __( 'Vertical bar', 'wordpress-seo' ),
			],
			'sc-tilde'  => [
				'option' => '~',
				'label'  => __( 'Small tilde', 'wordpress-seo' ),
			],
			'sc-laquo'  => [
				'option' => '&#171;',
				'label'  => __( 'Left angle quotation mark', 'wordpress-seo' ),
			],
			'sc-raquo'  => [
				'option' => '&#187;',
				'label'  => __( 'Right angle quotation mark', 'wordpress-seo' ),
			],
			'sc-lt'     => [
				'option' => '&#062;',
				'label'  => __( 'Less than sign', 'wordpress-seo' ),
			],
			'sc-gt'     => [
				'option' => '&#060;',
				'label'  => __( 'Greater than sign', 'wordpress-seo' ),
			],
		];

		/**
		 * Allows altering the separator options array.
		 *
		 * @param array $separators Array with the separator options.
		 */
		$separator_list = apply_filters( 'wpseo_separator_option_list', $separators );

		if ( ! is_array( $separator_list ) ) {
			return $separators;
		}

		return $separator_list;
	}
}
