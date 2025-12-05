<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Integrations\Settings_Integration;

/**
 * Class that holds most of the admin functionality for Yoast SEO.
 */
class WPSEO_Admin {

	/**
	 * The page identifier used in WordPress to register the admin page.
	 *
	 * !DO NOT CHANGE THIS!
	 *
	 * @var string
	 */
	public const PAGE_IDENTIFIER = 'wpseo_dashboard';

	/**
	 * Array of classes that add admin functionality.
	 *
	 * @var array
	 */
	protected $admin_features;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$integrations = [];

		global $pagenow;

		$wpseo_menu = new WPSEO_Menu();
		$wpseo_menu->register_hooks();

		if ( is_multisite() ) {
			WPSEO_Options::maybe_set_multisite_defaults( false );
		}

		add_action( 'created_category', [ $this, 'schedule_rewrite_flush' ] );
		add_action( 'edited_category', [ $this, 'schedule_rewrite_flush' ] );
		add_action( 'delete_category', [ $this, 'schedule_rewrite_flush' ] );

		add_filter( 'wpseo_accessible_post_types', [ 'WPSEO_Post_Type', 'filter_attachment_post_type' ] );

		add_filter( 'plugin_action_links_' . WPSEO_BASENAME, [ $this, 'add_action_link' ], 10, 2 );
		add_filter( 'network_admin_plugin_action_links_' . WPSEO_BASENAME, [ $this, 'add_action_link' ], 10, 2 );

		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_global_style' ] );

		add_action( 'after_switch_theme', [ $this, 'switch_theme' ] );
		add_action( 'switch_theme', [ $this, 'switch_theme' ] );

		add_filter( 'set-screen-option', [ $this, 'save_bulk_edit_options' ], 10, 3 );

		add_action( 'admin_init', [ 'WPSEO_Plugin_Conflict', 'hook_check_for_plugin_conflicts' ], 10, 1 );

		add_action( 'admin_init', [ $this, 'map_manage_options_cap' ] );

		WPSEO_Sitemaps_Cache::register_clear_on_option_update( 'wpseo' );
		WPSEO_Sitemaps_Cache::register_clear_on_option_update( 'home' );

		$this->initialize_cornerstone_content();

		if ( WPSEO_Utils::is_plugin_network_active() ) {
			$integrations[] = new Yoast_Network_Admin();
		}

		$this->admin_features = [
			'dashboard_widget'         => new Yoast_Dashboard_Widget(),
			'wincher_dashboard_widget' => new Wincher_Dashboard_Widget(),
		];

		if ( WPSEO_Metabox::is_post_overview( $pagenow ) || WPSEO_Metabox::is_post_edit( $pagenow ) ) {
			$this->admin_features['primary_category'] = new WPSEO_Primary_Term_Admin();
		}

		$integrations[] = new WPSEO_Yoast_Columns();
		$integrations[] = new WPSEO_Statistic_Integration();
		$integrations[] = new WPSEO_Capability_Manager_Integration( WPSEO_Capability_Manager_Factory::get() );
		$integrations[] = new WPSEO_Admin_Gutenberg_Compatibility_Notification();
		$integrations[] = new WPSEO_Expose_Shortlinks();
		$integrations[] = new WPSEO_MyYoast_Proxy();
		$integrations[] = new WPSEO_Schema_Person_Upgrade_Notification();
		$integrations[] = new WPSEO_Tracking( 'https://tracking.yoast.com/stats', ( WEEK_IN_SECONDS * 2 ) );
		$integrations[] = new WPSEO_Admin_Settings_Changed_Listener();

		$integrations = array_merge(
			$integrations,
			$this->get_admin_features(),
			$this->initialize_cornerstone_content()
		);

		foreach ( $integrations as $integration ) {
			$integration->register_hooks();
		}
	}

	/**
	 * Schedules a rewrite flush to happen at shutdown.
	 *
	 * @return void
	 */
	public function schedule_rewrite_flush() {
		if ( WPSEO_Options::get( 'stripcategorybase' ) !== true ) {
			return;
		}

		// Bail if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return;
		}

		add_action( 'shutdown', 'flush_rewrite_rules' );
	}

	/**
	 * Returns all the classes for the admin features.
	 *
	 * @return array
	 */
	public function get_admin_features() {
		return $this->admin_features;
	}

	/**
	 * Register assets needed on admin pages.
	 *
	 * @deprecated 25.5
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		_deprecated_function( __METHOD__, 'Yoast SEO 25.5' );
	}

	/**
	 * Returns the manage_options capability.
	 *
	 * @return string The capability to use.
	 */
	public function get_manage_options_cap() {
		/**
		 * Filter: 'wpseo_manage_options_capability' - Allow changing the capability users need to view the settings pages.
		 *
		 * @param string $capability The capability.
		 */
		return apply_filters( 'wpseo_manage_options_capability', 'wpseo_manage_options' );
	}

	/**
	 * Maps the manage_options cap on saving an options page to wpseo_manage_options.
	 *
	 * @return void
	 */
	public function map_manage_options_cap() {
		// phpcs:ignore WordPress.Security -- The variable is only used in strpos and thus safe to not unslash or sanitize.
		$option_page = ! empty( $_POST['option_page'] ) ? $_POST['option_page'] : '';

		if ( strpos( $option_page, 'yoast_wpseo' ) === 0 || strpos( $option_page, Settings_Integration::PAGE ) === 0 ) {
			add_filter( 'option_page_capability_' . $option_page, [ $this, 'get_manage_options_cap' ] );
		}
	}

	/**
	 * Adds the ability to choose how many posts are displayed per page
	 * on the bulk edit pages.
	 *
	 * @return void
	 */
	public function bulk_edit_options() {
		$option = 'per_page';
		$args   = [
			'label'   => __( 'Posts', 'wordpress-seo' ),
			'default' => 10,
			'option'  => 'wpseo_posts_per_page',
		];
		add_screen_option( $option, $args );
	}

	/**
	 * Saves the posts per page limit for bulk edit pages.
	 *
	 * @param int    $status Status value to pass through.
	 * @param string $option Option name.
	 * @param int    $value  Count value to check.
	 *
	 * @return int
	 */
	public function save_bulk_edit_options( $status, $option, $value ) {
		if ( $option && ( $value > 0 && $value < 1000 ) === 'wpseo_posts_per_page' ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Adds links to Premium Support and FAQ under the plugin in the plugin overview page.
	 *
	 * @param array  $links Array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  The filename for the current plugin, which the filter loops through.
	 *
	 * @return array
	 */
	public function add_action_link( $links, $file ) {
		$first_time_configuration_notice_helper = YoastSEO()->helpers->first_time_configuration_notice;

		if ( $file === WPSEO_BASENAME && WPSEO_Capability_Utils::current_user_can( 'wpseo_manage_options' ) ) {
			if ( is_network_admin() ) {
				$settings_url = network_admin_url( 'admin.php?page=' . self::PAGE_IDENTIFIER );
			}
			else {
				$settings_url = admin_url( 'admin.php?page=' . self::PAGE_IDENTIFIER );
			}
			$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'wordpress-seo' ) . '</a>';
			array_unshift( $links, $settings_link );
		}

		// Add link to docs.
		$faq_link = '<a href="' . esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/1yc' ) ) . '" target="_blank">' . __( 'FAQ', 'wordpress-seo' ) . '</a>';
		array_unshift( $links, $faq_link );

		if ( $first_time_configuration_notice_helper->first_time_configuration_not_finished() && ! is_network_admin() ) {
			$configuration_title = ( ! $first_time_configuration_notice_helper->should_show_alternate_message() ) ? 'first-time configuration' : 'SEO configuration';
			/* translators: CTA to finish the first time configuration. %s: Either first-time SEO configuration or SEO configuration. */
			$message  = sprintf( __( 'Finish your %s', 'wordpress-seo' ), $configuration_title );
			$ftc_page = 'admin.php?page=wpseo_dashboard#/first-time-configuration';
			$ftc_link = '<a href="' . esc_url( admin_url( $ftc_page ) ) . '" target="_blank">' . $message . '</a>';
			array_unshift( $links, $ftc_link );
		}

		$addon_manager = new WPSEO_Addon_Manager();
		if ( YoastSEO()->helpers->product->is_premium() ) {

			// Remove Free 'deactivate' link if Premium is active as well. We don't want users to deactivate Free when Premium is active.
			unset( $links['deactivate'] );
			$no_deactivation_explanation = '<span style="color: #32373c">' . sprintf(
				/* translators: %s expands to Yoast SEO Premium. */
				__( 'Required by %s', 'wordpress-seo' ),
				'Yoast SEO Premium'
			) . '</span>';

			array_unshift( $links, $no_deactivation_explanation );

			if ( $addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG ) ) {
				return $links;
			}

			// Add link to where premium can be activated.
			$activation_link = '<a style="font-weight: bold;" href="' . esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/activate-my-yoast' ) ) . '" target="_blank">' . __( 'Activate your subscription', 'wordpress-seo' ) . '</a>';
			array_unshift( $links, $activation_link );

			return $links;
		}

		// Add link to premium landing page.
		$premium_link = '<a style="font-weight: bold;" href="' . esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/1yb' ) ) . '" target="_blank" data-action="load-nfd-ctb" data-ctb-id="f6a84663-465f-4cb5-8ba5-f7a6d72224b2">' . __( 'Get Premium', 'wordpress-seo' ) . '</a>';
		array_unshift( $links, $premium_link );

		return $links;
	}

	/**
	 * Enqueues the (tiny) global JS needed for the plugin.
	 *
	 * @return void
	 */
	public function config_page_scripts() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_script( 'admin-global' );
		$asset_manager->localize_script( 'admin-global', 'wpseoAdminGlobalL10n', $this->localize_admin_global_script() );
	}

	/**
	 * Enqueues the (tiny) global stylesheet needed for the plugin.
	 *
	 * @return void
	 */
	public function enqueue_global_style() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();
		$asset_manager->enqueue_style( 'admin-global' );
	}

	/**
	 * Filter the $contactmethods array and add a set of social profiles.
	 *
	 * These are used with the Facebook author, rel="author" and Twitter cards implementation.
	 *
	 * @deprecated 22.6
	 * @codeCoverageIgnore
	 *
	 * @param array<string, string> $contactmethods Currently set contactmethods.
	 *
	 * @return array<string, string> Contactmethods with added contactmethods.
	 */
	public function update_contactmethods( $contactmethods ) {
		_deprecated_function( __METHOD__, 'Yoast SEO 22.6' );

		$contactmethods['facebook']   = __( 'Facebook profile URL', 'wordpress-seo' );
		$contactmethods['instagram']  = __( 'Instagram profile URL', 'wordpress-seo' );
		$contactmethods['linkedin']   = __( 'LinkedIn profile URL', 'wordpress-seo' );
		$contactmethods['myspace']    = __( 'MySpace profile URL', 'wordpress-seo' );
		$contactmethods['pinterest']  = __( 'Pinterest profile URL', 'wordpress-seo' );
		$contactmethods['soundcloud'] = __( 'SoundCloud profile URL', 'wordpress-seo' );
		$contactmethods['tumblr']     = __( 'Tumblr profile URL', 'wordpress-seo' );
		$contactmethods['twitter']    = __( 'X username (without @)', 'wordpress-seo' );
		$contactmethods['youtube']    = __( 'YouTube profile URL', 'wordpress-seo' );
		$contactmethods['wikipedia']  = __( 'Wikipedia page about you', 'wordpress-seo' ) . '<br/><small>' . __( '(if one exists)', 'wordpress-seo' ) . '</small>';

		return $contactmethods;
	}

	/**
	 * Log the updated timestamp for user profiles when theme is changed.
	 *
	 * @return void
	 */
	public function switch_theme() {

		$users = get_users( [ 'capability' => [ 'edit_posts' ] ] );

		if ( is_array( $users ) && $users !== [] ) {
			foreach ( $users as $user ) {
				update_user_meta( $user->ID, '_yoast_wpseo_profile_updated', time() );
			}
		}
	}

	/**
	 * Localization for the dismiss urls.
	 *
	 * @return array
	 */
	private function localize_admin_global_script() {
		return array_merge(
			[
				'isRtl'                   => is_rtl(),
				'variable_warning'        => sprintf(
				/* translators: %1$s: '%%term_title%%' variable used in titles and meta's template that's not compatible with the given template, %2$s: expands to 'HelpScout beacon' */
					__( 'Warning: the variable %1$s cannot be used in this template. See the %2$s for more info.', 'wordpress-seo' ),
					'<code>%s</code>',
					'HelpScout beacon'
				),
				/* translators: %s: expends to Yoast SEO */
				'help_video_iframe_title' => sprintf( __( '%s video tutorial', 'wordpress-seo' ), 'Yoast SEO' ),
				'scrollable_table_hint'   => __( 'Scroll to see the table content.', 'wordpress-seo' ),
				'wincher_is_logged_in'    => WPSEO_Options::get( 'wincher_integration_active', true ) ? YoastSEO()->helpers->wincher->login_status() : false,
			],
			YoastSEO()->helpers->wincher->get_admin_global_links()
		);
	}

	/**
	 * Whether we are on the admin dashboard page.
	 *
	 * @return bool
	 */
	protected function on_dashboard_page() {
		return $GLOBALS['pagenow'] === 'index.php';
	}

	/**
	 * Loads the cornerstone filter.
	 *
	 * @return WPSEO_WordPress_Integration[] The integrations to initialize.
	 */
	protected function initialize_cornerstone_content() {
		if ( ! WPSEO_Options::get( 'enable_cornerstone_content' ) ) {
			return [];
		}

		return [
			'cornerstone_filter' => new WPSEO_Cornerstone_Filter(),
		];
	}
}
