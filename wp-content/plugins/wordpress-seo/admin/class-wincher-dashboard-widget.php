<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Wincher dashboard widget.
 */
class Wincher_Dashboard_Widget implements WPSEO_WordPress_Integration {

	/**
	 * Holds an instance of the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Wincher_Dashboard_Widget constructor.
	 */
	public function __construct() {
		$this->asset_manager = new WPSEO_Admin_Asset_Manager();
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_wincher_dashboard_assets' ] );
		add_action( 'admin_init', [ $this, 'queue_wincher_dashboard_widget' ] );
	}

	/**
	 * Adds the Wincher dashboard widget if it should be shown.
	 *
	 * @return void
	 */
	public function queue_wincher_dashboard_widget() {
		if ( $this->show_widget() ) {
			add_action( 'wp_dashboard_setup', [ $this, 'add_wincher_dashboard_widget' ] );
		}
	}

	/**
	 * Adds the Wincher dashboard widget to WordPress.
	 *
	 * @return void
	 */
	public function add_wincher_dashboard_widget() {
		add_filter( 'postbox_classes_dashboard_wpseo-wincher-dashboard-overview', [ $this, 'wpseo_wincher_dashboard_overview_class' ] );
		wp_add_dashboard_widget(
			'wpseo-wincher-dashboard-overview',
			/* translators: %1$s expands to Yoast SEO, %2$s to Wincher */
			sprintf( __( '%1$s / %2$s: Top Keyphrases', 'wordpress-seo' ), 'Yoast SEO', 'Wincher' ),
			[ $this, 'display_wincher_dashboard_widget' ]
		);
	}

	/**
	 * Adds CSS classes to the dashboard widget.
	 *
	 * @param array $classes An array of postbox CSS classes.
	 *
	 * @return array
	 */
	public function wpseo_wincher_dashboard_overview_class( $classes ) {
		$classes[] = 'yoast wpseo-wincherdashboard-overview';
		return $classes;
	}

	/**
	 * Displays the Wincher dashboard widget.
	 *
	 * @return void
	 */
	public function display_wincher_dashboard_widget() {
		echo '<div id="yoast-seo-wincher-dashboard-widget"></div>';
	}

	/**
	 * Enqueues assets for the dashboard if the current page is the dashboard.
	 *
	 * @return void
	 */
	public function enqueue_wincher_dashboard_assets() {
		if ( ! $this->is_dashboard_screen() ) {
			return;
		}

		$this->asset_manager->localize_script( 'wincher-dashboard-widget', 'wpseoWincherDashboardWidgetL10n', $this->localize_wincher_dashboard_script() );
		$this->asset_manager->enqueue_script( 'wincher-dashboard-widget' );
		$this->asset_manager->enqueue_style( 'wp-dashboard' );
		$this->asset_manager->enqueue_style( 'monorepo' );
	}

	/**
	 * Translates strings used in the Wincher dashboard widget.
	 *
	 * @return array The translated strings.
	 */
	public function localize_wincher_dashboard_script() {

		return [
			'wincher_is_logged_in' => YoastSEO()->helpers->wincher->login_status(),
			'wincher_website_id'   => WPSEO_Options::get( 'wincher_website_id', '' ),
		];
	}

	/**
	 * Checks if the current screen is the dashboard screen.
	 *
	 * @return bool Whether or not this is the dashboard screen.
	 */
	private function is_dashboard_screen() {
		$current_screen = get_current_screen();

		return ( $current_screen instanceof WP_Screen && $current_screen->id === 'dashboard' );
	}

	/**
	 * Returns true when the Wincher dashboard widget should be shown.
	 *
	 * @return bool
	 */
	private function show_widget() {
		$analysis_seo      = new WPSEO_Metabox_Analysis_SEO();
		$user_can_edit     = $analysis_seo->is_enabled() && current_user_can( 'edit_posts' );
		$is_wincher_active = YoastSEO()->helpers->wincher->is_active();

		return $user_can_edit && $is_wincher_active;
	}
}
