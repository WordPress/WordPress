<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Performs the load on admin side.
 */
class WPSEO_Admin_Init {

	/**
	 * Holds the global `$pagenow` variable's value.
	 *
	 * @var string
	 */
	private $pagenow;

	/**
	 * Holds the asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$GLOBALS['wpseo_admin'] = new WPSEO_Admin();

		$this->pagenow = $GLOBALS['pagenow'];

		$this->asset_manager = new WPSEO_Admin_Asset_Manager();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_dismissible' ] );
		add_action( 'admin_init', [ $this, 'unsupported_php_notice' ], 15 );
		add_action( 'admin_init', [ $this, 'remove_translations_notification' ], 15 );
		add_action( 'admin_init', [ $this->asset_manager, 'register_assets' ] );
		add_action( 'admin_init', [ $this, 'show_hook_deprecation_warnings' ] );
		add_action( 'admin_init', [ 'WPSEO_Plugin_Conflict', 'hook_check_for_plugin_conflicts' ] );
		add_action( 'admin_notices', [ $this, 'permalink_settings_notice' ] );
		add_action( 'post_submitbox_misc_actions', [ $this, 'add_publish_box_section' ] );

		$this->load_meta_boxes();
		$this->load_taxonomy_class();
		$this->load_admin_page_class();
		$this->load_admin_user_class();
		$this->load_xml_sitemaps_admin();
		$this->load_plugin_suggestions();
	}

	/**
	 * Enqueue our styling for dismissible yoast notifications.
	 *
	 * @return void
	 */
	public function enqueue_dismissible() {
		$this->asset_manager->enqueue_style( 'dismissible' );
	}

	/**
	 * Removes any notification for incomplete translations.
	 *
	 * @return void
	 */
	public function remove_translations_notification() {
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification_by_id( 'i18nModuleTranslationAssistance' );
	}

	/**
	 * Creates an unsupported PHP version notification in the notification center.
	 *
	 * @return void
	 */
	public function unsupported_php_notice() {
		$notification_center = Yoast_Notification_Center::get();
		$notification_center->remove_notification_by_id( 'wpseo-dismiss-unsupported-php' );
	}

	/**
	 * Gets the latest released major WordPress version from the WordPress stable-check api.
	 *
	 * @return float|int The latest released major WordPress version. 0 when the stable-check API doesn't respond.
	 */
	private function get_latest_major_wordpress_version() {
		$core_updates = get_core_updates( [ 'dismissed' => true ] );

		if ( $core_updates === false ) {
			return 0;
		}

		$wp_version_latest = get_bloginfo( 'version' );
		foreach ( $core_updates as $update ) {
			if ( $update->response === 'upgrade' && version_compare( $update->version, $wp_version_latest, '>' ) ) {
				$wp_version_latest = $update->version;
			}
		}

		// Strip the patch version and convert to a float.
		return (float) $wp_version_latest;
	}

	/**
	 * Helper to verify if the user is currently visiting one of our admin pages.
	 *
	 * @return bool
	 */
	private function on_wpseo_admin_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( ! isset( $_GET['page'] ) || ! is_string( $_GET['page'] ) ) {
			return false;
		}

		if ( $this->pagenow !== 'admin.php' ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
		return strpos( $current_page, 'wpseo' ) === 0;
	}

	/**
	 * Whether we should load the meta box classes.
	 *
	 * @return bool true if we should load the meta box classes, false otherwise.
	 */
	private function should_load_meta_boxes() {
		/**
		 * Filter: 'wpseo_always_register_metaboxes_on_admin' - Allow developers to change whether
		 * the WPSEO metaboxes are only registered on the typical pages (lean loading) or always
		 * registered when in admin.
		 *
		 * @param bool $register_metaboxes Whether to always register the metaboxes or not. Defaults to false.
		 */
		if ( apply_filters( 'wpseo_always_register_metaboxes_on_admin', false ) ) {
			return true;
		}

		// If we are in a post editor.
		if ( WPSEO_Metabox::is_post_overview( $this->pagenow ) || WPSEO_Metabox::is_post_edit( $this->pagenow ) ) {
			return true;
		}

		// If we are doing an inline save.
		if ( check_ajax_referer( 'inlineeditnonce', '_inline_edit', false ) && isset( $_POST['action'] ) && sanitize_text_field( wp_unslash( $_POST['action'] ) ) === 'inline-save' ) {
			return true;
		}

		return false;
	}

	/**
	 * Determine whether we should load the meta box class and if so, load it.
	 *
	 * @return void
	 */
	private function load_meta_boxes() {
		if ( $this->should_load_meta_boxes() ) {
			$GLOBALS['wpseo_metabox']      = new WPSEO_Metabox();
			$GLOBALS['wpseo_meta_columns'] = new WPSEO_Meta_Columns();
		}
	}

	/**
	 * Determine if we should load our taxonomy edit class and if so, load it.
	 *
	 * @return void
	 */
	private function load_taxonomy_class() {
		if (
			WPSEO_Taxonomy::is_term_edit( $this->pagenow )
			|| WPSEO_Taxonomy::is_term_overview( $this->pagenow )
		) {
			new WPSEO_Taxonomy();
		}
	}

	/**
	 * Determine if we should load our admin pages class and if so, load it.
	 *
	 * Loads admin page class for all admin pages starting with `wpseo_`.
	 *
	 * @return void
	 */
	private function load_admin_user_class() {
		if ( in_array( $this->pagenow, [ 'user-edit.php', 'profile.php' ], true )
			&& current_user_can( 'edit_users' )
		) {
			new WPSEO_Admin_User_Profile();
		}
	}

	/**
	 * Determine if we should load our admin pages class and if so, load it.
	 *
	 * Loads admin page class for all admin pages starting with `wpseo_`.
	 *
	 * @return void
	 */
	private function load_admin_page_class() {

		if ( $this->on_wpseo_admin_page() ) {
			// For backwards compatabilty, this still needs a global, for now...
			$GLOBALS['wpseo_admin_pages'] = new WPSEO_Admin_Pages();

			$page = null;

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			}

			// Only renders Yoast SEO Premium upsells when the page is a Yoast SEO page.
			if ( $page !== null && WPSEO_Utils::is_yoast_seo_free_page( $page ) ) {
				$this->register_premium_upsell_admin_block();
			}
		}
	}

	/**
	 * Loads the plugin suggestions.
	 *
	 * @return void
	 */
	private function load_plugin_suggestions() {
		$suggestions = new WPSEO_Suggested_Plugins( new WPSEO_Plugin_Availability(), Yoast_Notification_Center::get() );
		$suggestions->register_hooks();
	}

	/**
	 * Registers the Premium Upsell Admin Block.
	 *
	 * @return void
	 */
	private function register_premium_upsell_admin_block() {
		if ( ! YoastSEO()->helpers->product->is_premium() ) {
			$upsell_block = new WPSEO_Premium_Upsell_Admin_Block( 'wpseo_admin_promo_footer' );
			$upsell_block->register_hooks();
		}
	}

	/**
	 * See if we should start our XML Sitemaps Admin class.
	 *
	 * @return void
	 */
	private function load_xml_sitemaps_admin() {
		if ( WPSEO_Options::get( 'enable_xml_sitemap', false, [ 'wpseo' ] ) ) {
			new WPSEO_Sitemaps_Admin();
		}
	}

	/**
	 * Shows deprecation warnings to the user if a plugin has registered a filter we have deprecated.
	 *
	 * @return void
	 */
	public function show_hook_deprecation_warnings() {
		global $wp_filter;

		if ( wp_doing_ajax() ) {
			return;
		}

		// WordPress hooks that have been deprecated since a Yoast SEO version.
		$deprecated_filters = [
			'wpseo_genesis_force_adjacent_rel_home' => [
				'version'     => '9.4',
				'alternative' => null,
			],
			'wpseo_opengraph' => [
				'version'     => '14.0',
				'alternative' => null,
			],
			'wpseo_twitter' => [
				'version'     => '14.0',
				'alternative' => null,
			],
			'wpseo_twitter_taxonomy_image' => [
				'version'     => '14.0',
				'alternative' => null,
			],
			'wpseo_twitter_metatag_key' => [
				'version'     => '14.0',
				'alternative' => null,
			],
			'wp_seo_get_bc_ancestors' => [
				'version'     => '14.0',
				'alternative' => 'wpseo_breadcrumb_links',
			],
			'validate_facebook_app_id_api_response_code' => [
				'version'     => '15.5',
				'alternative' => null,
			],
			'validate_facebook_app_id_api_response_body' => [
				'version'     => '15.5',
				'alternative' => null,
			],
		];

		// Determine which filters have been registered.
		$deprecated_notices = array_intersect(
			array_keys( $deprecated_filters ),
			array_keys( $wp_filter )
		);

		// Show notice for each deprecated filter or action that has been registered.
		foreach ( $deprecated_notices as $deprecated_filter ) {
			$deprecation_info = $deprecated_filters[ $deprecated_filter ];
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Only uses the hardcoded values from above.
			_deprecated_hook(
				$deprecated_filter,
				'WPSEO ' . $deprecation_info['version'],
				$deprecation_info['alternative']
			);
			// phpcs:enable
		}
	}

	/**
	 * Check if the permalink uses %postname%.
	 *
	 * @return bool
	 */
	private function has_postname_in_permalink() {
		return ( strpos( get_option( 'permalink_structure' ), '%postname%' ) !== false );
	}

	/**
	 * Shows a notice on the permalink settings page.
	 *
	 * @return void
	 */
	public function permalink_settings_notice() {
		global $pagenow;

		if ( $pagenow === 'options-permalink.php' ) {
			printf(
				'<div class="notice notice-warning"><p><strong>%1$s</strong><br>%2$s<br><a href="%3$s" target="_blank">%4$s</a></p></div>',
				esc_html__( 'WARNING:', 'wordpress-seo' ),
				sprintf(
					/* translators: %1$s and %2$s expand to <em> items to emphasize the word in the middle. */
					esc_html__( 'Changing your permalinks settings can seriously impact your search engine visibility. It should almost %1$s never %2$s be done on a live website.', 'wordpress-seo' ),
					'<em>',
					'</em>'
				),
				esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/why-permalinks/' ) ),
				// The link's content.
				esc_html__( 'Learn about why permalinks are important for SEO.', 'wordpress-seo' )
			);
		}
	}

	/**
	 * Adds a custom Yoast section within the Classic Editor publish box.
	 *
	 * @param WP_Post $post The current post object.
	 *
	 * @return void
	 */
	public function add_publish_box_section( $post ) {
		if ( in_array( $this->pagenow, [ 'post.php', 'post-new.php' ], true ) ) {
			?>
			<div id="yoast-seo-publishbox-section"></div>
			<?php
			/**
			 * Fires after the post time/date setting in the Publish meta box.
			 *
			 * @param WP_Post $post The current post object.
			 */
			do_action( 'wpseo_publishbox_misc_actions', $post );
		}
	}
}
