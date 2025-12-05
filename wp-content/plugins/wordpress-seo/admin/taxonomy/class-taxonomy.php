<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Editors\Application\Site\Website_Information_Repository;
use Yoast\WP\SEO\Presenters\Admin\Alert_Presenter;

/**
 * Class that handles the edit boxes on taxonomy edit pages.
 */
class WPSEO_Taxonomy {

	/**
	 * The current active taxonomy.
	 *
	 * @var string
	 */
	private $taxonomy = '';

	/**
	 * Holds the metabox SEO analysis instance.
	 *
	 * @var WPSEO_Metabox_Analysis_SEO
	 */
	private $analysis_seo;

	/**
	 * Holds the metabox readability analysis instance.
	 *
	 * @var WPSEO_Metabox_Analysis_Readability
	 */
	private $analysis_readability;

	/**
	 * Holds the metabox inclusive language analysis instance.
	 *
	 * @var WPSEO_Metabox_Analysis_Inclusive_Language
	 */
	private $analysis_inclusive_language;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->taxonomy = $this::get_taxonomy();

		add_action( 'edit_term', [ $this, 'update_term' ], 99, 3 );
		add_action( 'init', [ $this, 'custom_category_descriptions_allow_html' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );

		if ( self::is_term_overview( $GLOBALS['pagenow'] ) ) {
			new WPSEO_Taxonomy_Columns();
		}
		$this->analysis_seo                = new WPSEO_Metabox_Analysis_SEO();
		$this->analysis_readability        = new WPSEO_Metabox_Analysis_Readability();
		$this->analysis_inclusive_language = new WPSEO_Metabox_Analysis_Inclusive_Language();
	}

	/**
	 * Add hooks late enough for taxonomy object to be available for checks.
	 *
	 * @return void
	 */
	public function admin_init() {

		$taxonomy = get_taxonomy( $this->taxonomy );

		if ( empty( $taxonomy ) || empty( $taxonomy->public ) || ! $this->show_metabox() ) {
			return;
		}

		// Adds custom category description editor. Needs a hook that runs before the description field.
		add_action( "{$this->taxonomy}_term_edit_form_top", [ $this, 'custom_category_description_editor' ] );

		add_action( sanitize_text_field( $this->taxonomy ) . '_edit_form', [ $this, 'term_metabox' ], 90, 1 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	/**
	 * Show the SEO inputs for term.
	 *
	 * @param stdClass|WP_Term $term Term to show the edit boxes for.
	 *
	 * @return void
	 */
	public function term_metabox( $term ) {
		if ( WPSEO_Metabox::is_internet_explorer() ) {
			$this->show_internet_explorer_notice();
			return;
		}

		$metabox = new WPSEO_Taxonomy_Metabox( $this->taxonomy, $term );
		$metabox->display();
	}

	/**
	 * Renders the content for the internet explorer metabox.
	 *
	 * @return void
	 */
	private function show_internet_explorer_notice() {
		$product_title = YoastSEO()->helpers->product->get_product_name();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: $product_title is hardcoded.
		printf( '<div id="wpseo_meta" class="postbox yoast wpseo-taxonomy-metabox-postbox"><h2><span>%1$s</span></h2>', $product_title );
		echo '<div class="inside">';

		$content = sprintf(
			/* translators: 1: Link start tag to the Firefox website, 2: Link start tag to the Chrome website, 3: Link start tag to the Edge website, 4: Link closing tag. */
			esc_html__( 'The browser you are currently using is unfortunately rather dated. Since we strive to give you the best experience possible, we no longer support this browser. Instead, please use %1$sFirefox%4$s, %2$sChrome%4$s or %3$sMicrosoft Edge%4$s.', 'wordpress-seo' ),
			'<a href="https://www.mozilla.org/firefox/new/">',
			'<a href="https://www.google.com/chrome/">',
			'<a href="https://www.microsoft.com/windows/microsoft-edge">',
			'</a>'
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output escaped above.
		echo new Alert_Presenter( $content );

		echo '</div></div>';
	}

	/**
	 * Queue assets for taxonomy screens.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		$pagenow = $GLOBALS['pagenow'];

		if ( ! ( self::is_term_edit( $pagenow ) || self::is_term_overview( $pagenow ) ) ) {
			return;
		}

		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_style( 'monorepo' );

		$tag_id = $this::get_tag_id();

		if (
			self::is_term_edit( $pagenow )
			&& $tag_id !== null
		) {
			wp_enqueue_media(); // Enqueue files needed for upload functionality.

			$asset_manager->enqueue_style( 'metabox-css' );
			if ( $this->analysis_readability->is_enabled() ) {
				$asset_manager->enqueue_style( 'scoring' );
			}
			$asset_manager->enqueue_style( 'ai-generator' );
			$asset_manager->enqueue_script( 'term-edit' );

			/**
			 * Remove the emoji script as it is incompatible with both React and any
			 * contenteditable fields.
			 */
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

			$asset_manager->localize_script( 'term-edit', 'wpseoAdminL10n', WPSEO_Utils::get_admin_l10n() );

			$script_data = [
				'analysis'              => [
					'plugins' => [
						'replaceVars' => [
							'replace_vars'             => $this->get_replace_vars(),
							'recommended_replace_vars' => $this->get_recommended_replace_vars(),
							'scope'                    => $this->determine_scope(),
						],
						'shortcodes' => [
							'wpseo_shortcode_tags'          => $this->get_valid_shortcode_tags(),
							'wpseo_filter_shortcodes_nonce' => wp_create_nonce( 'wpseo-filter-shortcodes' ),
						],
					],
					'worker'  => [
						'url'                     => YoastSEO()->helpers->asset->get_asset_url( 'yoast-seo-analysis-worker' ),
						'dependencies'            => YoastSEO()->helpers->asset->get_dependency_urls_by_handle( 'yoast-seo-analysis-worker' ),
						'keywords_assessment_url' => YoastSEO()->helpers->asset->get_asset_url( 'yoast-seo-used-keywords-assessment' ),
						'log_level'               => WPSEO_Utils::get_analysis_worker_log_level(),
					],
				],
				'metabox'               => $this->localize_term_scraper_script( $tag_id ),
				'isTerm'                => true,
				'postId'                => $tag_id,
				'postType'              => $this->get_taxonomy(),
				'usedKeywordsNonce'     => wp_create_nonce( 'wpseo-keyword-usage' ),
			];

			/**
			 * The website information repository.
			 *
			 * @var Website_Information_Repository $repo
			 */
			$repo             = YoastSEO()->classes->get( Website_Information_Repository::class );
			$term_information = $repo->get_term_site_information();
			$term_information->set_term( get_term_by( 'id', $tag_id, $this::get_taxonomy() ) );
			$script_data = array_merge_recursive( $term_information->get_legacy_site_information(), $script_data );

			$asset_manager->localize_script( 'term-edit', 'wpseoScriptData', $script_data );
		}

		if ( self::is_term_overview( $pagenow ) ) {
			$asset_manager->enqueue_script( 'edit-page' );
			$asset_manager->enqueue_style( 'edit-page' );
		}
	}

	/**
	 * Update the taxonomy meta data on save.
	 *
	 * @param int    $term_id  ID of the term to save data for.
	 * @param int    $tt_id    The taxonomy_term_id for the term.
	 * @param string $taxonomy The taxonomy the term belongs to.
	 *
	 * @return void
	 */
	public function update_term( $term_id, $tt_id, $taxonomy ) {
		// Bail if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return;
		}

		/* Create post array with only our values. */
		$new_meta_data = [];
		foreach ( WPSEO_Taxonomy_Meta::$defaults_per_term as $key => $default ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: Nonce is already checked by WordPress before executing this action.
			if ( isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: $data is getting sanitized later.
				$data                  = wp_unslash( $_POST[ $key ] );
				$new_meta_data[ $key ] = ( $key !== 'wpseo_canonical' ) ? WPSEO_Utils::sanitize_text_field( $data ) : WPSEO_Utils::sanitize_url( $data );
			}

			// If analysis is disabled remove that analysis score value from the DB.
			if ( $this->is_meta_value_disabled( $key ) ) {
				$new_meta_data[ $key ] = '';
			}
		}

		// Saving the values.
		WPSEO_Taxonomy_Meta::set_values( $term_id, $taxonomy, $new_meta_data );
	}

	/**
	 * Determines if the given meta value key is disabled.
	 *
	 * @param string $key The key of the meta value.
	 * @return bool Whether the given meta value key is disabled.
	 */
	public function is_meta_value_disabled( $key ) {
		if ( $key === 'wpseo_linkdex' && ! $this->analysis_seo->is_enabled() ) {
			return true;
		}

		if ( $key === 'wpseo_content_score' && ! $this->analysis_readability->is_enabled() ) {
			return true;
		}

		if ( $key === 'wpseo_inclusive_language_score' && ! $this->analysis_inclusive_language->is_enabled() ) {
			return true;
		}

		return false;
	}

	/**
	 * Allows post-kses-filtered HTML in term descriptions.
	 *
	 * @return void
	 */
	public function custom_category_descriptions_allow_html() {
		remove_filter( 'term_description', 'wp_kses_data' );
		remove_filter( 'pre_term_description', 'wp_filter_kses' );
		add_filter( 'term_description', 'wp_kses_post' );
		add_filter( 'pre_term_description', 'wp_filter_post_kses' );
	}

	/**
	 * Output the WordPress editor.
	 *
	 * @return void
	 */
	public function custom_category_description_editor() {
		wp_editor( '', 'description' );
	}

	/**
	 * Pass variables to js for use with the term-scraper.
	 *
	 * @param int $term_id The ID of the term to localize the script for.
	 *
	 * @return array
	 */
	public function localize_term_scraper_script( $term_id ) {
		$term     = get_term_by( 'id', $term_id, $this::get_taxonomy() );
		$taxonomy = get_taxonomy( $term->taxonomy );

		$term_formatter = new WPSEO_Metabox_Formatter(
			new WPSEO_Term_Metabox_Formatter( $taxonomy, $term )
		);

		return $term_formatter->get_values();
	}

	/**
	 * Pass some variables to js for replacing variables.
	 *
	 * @return array
	 */
	public function localize_replace_vars_script() {
		return [
			'replace_vars'             => $this->get_replace_vars(),
			'recommended_replace_vars' => $this->get_recommended_replace_vars(),
			'scope'                    => $this->determine_scope(),
		];
	}

	/**
	 * Determines the scope based on the current taxonomy.
	 * This can be used by the replacevar plugin to determine if a replacement needs to be executed.
	 *
	 * @return string String decribing the current scope.
	 */
	private function determine_scope() {
		$taxonomy = $this::get_taxonomy();

		if ( $taxonomy === 'category' ) {
			return 'category';
		}

		if ( $taxonomy === 'post_tag' ) {
			return 'tag';
		}

		return 'term';
	}

	/**
	 * Determines if a given page is the term overview page.
	 *
	 * @param string $page The string to check for the term overview page.
	 *
	 * @return bool
	 */
	public static function is_term_overview( $page ) {
		return $page === 'edit-tags.php';
	}

	/**
	 * Determines if a given page is the term edit page.
	 *
	 * @param string $page The string to check for the term edit page.
	 *
	 * @return bool
	 */
	public static function is_term_edit( $page ) {
		return $page === 'term.php';
	}

	/**
	 * Function to get the labels for the current taxonomy.
	 *
	 * @return object|null Labels for the current taxonomy or null if the taxonomy is not set.
	 */
	public static function get_labels() {
		$term = self::get_taxonomy();
		if ( $term !== '' ) {
			$taxonomy = get_taxonomy( $term );
			return $taxonomy->labels;
		}
		return null;
	}

	/**
	 * Retrieves a template.
	 * Check if metabox for current taxonomy should be displayed.
	 *
	 * @return bool
	 */
	private function show_metabox() {
		$option_key = 'display-metabox-tax-' . $this->taxonomy;

		return WPSEO_Options::get( $option_key );
	}

	/**
	 * Getting the taxonomy from the URL.
	 *
	 * @return string
	 */
	private static function get_taxonomy() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['taxonomy'] ) && is_string( $_GET['taxonomy'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			return sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) );
		}
		return '';
	}

	/**
	 * Get the current tag ID from the GET parameters.
	 *
	 * @return int|null the tag ID if it exists, null otherwise.
	 */
	private static function get_tag_id() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['tag_ID'] ) && is_string( $_GET['tag_ID'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are casting to an integer.
			$tag_id = (int) wp_unslash( $_GET['tag_ID'] );
			if ( $tag_id > 0 ) {
				return $tag_id;
			}
		}
		return null;
	}

	/**
	 * Prepares the replace vars for localization.
	 *
	 * @return array<string, string> The replacement variables.
	 */
	private function get_replace_vars() {
		$term_id = $this::get_tag_id();
		$term    = get_term_by( 'id', $term_id, $this::get_taxonomy() );

		$cached_replacement_vars = [];

		$vars_to_cache = [
			'date',
			'id',
			'sitename',
			'sitedesc',
			'sep',
			'page',
			'term_title',
			'term_description',
			'term_hierarchy',
			'category_description',
			'tag_description',
			'searchphrase',
			'currentyear',
		];

		foreach ( $vars_to_cache as $var ) {
			$cached_replacement_vars[ $var ] = wpseo_replace_vars( '%%' . $var . '%%', $term );
		}

		return $cached_replacement_vars;
	}

	/**
	 * Prepares the recommended replace vars for localization.
	 *
	 * @return array<string> The recommended replacement variables.
	 */
	private function get_recommended_replace_vars() {
		$recommended_replace_vars = new WPSEO_Admin_Recommended_Replace_Vars();
		$taxonomy                 = $this::get_taxonomy();

		if ( $taxonomy === '' ) {
			return [];
		}

		// What is recommended depends on the current context.
		$page_type = $recommended_replace_vars->determine_for_term( $taxonomy );

		return $recommended_replace_vars->get_recommended_replacevars_for( $page_type );
	}

	/**
	 * Returns an array with shortcode tags for all registered shortcodes.
	 *
	 * @return array<string> Array with shortcode tags.
	 */
	private function get_valid_shortcode_tags() {
		$shortcode_tags = [];

		foreach ( $GLOBALS['shortcode_tags'] as $tag => $description ) {
			$shortcode_tags[] = $tag;
		}

		return $shortcode_tags;
	}
}
