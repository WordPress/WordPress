<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

use Yoast\WP\SEO\Actions\Alert_Dismissal_Action;
use Yoast\WP\SEO\General\User_Interface\General_Page_Integration;
use Yoast\WP\SEO\Integrations\Academy_Integration;
use Yoast\WP\SEO\Integrations\Admin\Redirects_Page_Integration;
use Yoast\WP\SEO\Integrations\Settings_Integration;
use Yoast\WP\SEO\Integrations\Support_Integration;
use Yoast\WP\SEO\Plans\User_Interface\Plans_Page_Integration;
use Yoast\WP\SEO\Promotions\Application\Promotion_Manager;

/**
 * Class WPSEO_Admin_Pages.
 *
 * Class with functionality for the Yoast SEO admin pages.
 */
class WPSEO_Admin_Pages {

	/**
	 * The option in use for the current admin page.
	 *
	 * @var string
	 */
	public $currentoption = 'wpseo';

	/**
	 * Holds the asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	private $asset_manager;

	/**
	 * Class constructor, which basically only hooks the init function on the init hook.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ], 20 );

		$this->asset_manager = new WPSEO_Admin_Asset_Manager();
	}

	/**
	 * Make sure the needed scripts are loaded for admin pages.
	 *
	 * @return void
	 */
	public function init() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		// Don't load the scripts for the following pages.
		$page_exceptions    = in_array(
			$page,
			[
				Settings_Integration::PAGE,
				Academy_Integration::PAGE,
				Support_Integration::PAGE,
				Plans_Page_Integration::PAGE,
				Redirects_Page_Integration::PAGE,
			],
			true
		);
		$new_dashboard_page = ( $page === General_Page_Integration::PAGE && ! is_network_admin() );
		if ( $page_exceptions || $new_dashboard_page ) {
			// Bail, this is managed in the applicable integration.
			return;
		}
		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'config_page_styles' ] );
	}

	/**
	 * Loads the required styles for the config page.
	 *
	 * @return void
	 */
	public function config_page_styles() {
		wp_enqueue_style( 'dashboard' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'global' );
		wp_enqueue_style( 'wp-admin' );
		$this->asset_manager->enqueue_style( 'admin-css' );
		$this->asset_manager->enqueue_style( 'monorepo' );
	}

	/**
	 * Loads the required scripts for the config page.
	 *
	 * @return void
	 */
	public function config_page_scripts() {
		$this->asset_manager->enqueue_script( 'settings' );
		wp_enqueue_script( 'dashboard' );
		wp_enqueue_script( 'thickbox' );

		$alert_dismissal_action = YoastSEO()->classes->get( Alert_Dismissal_Action::class );
		$dismissed_alerts       = $alert_dismissal_action->all_dismissed();

		$script_data = [
			'dismissedAlerts'                => $dismissed_alerts,
			'isRtl'                          => is_rtl(),
			'isPremium'                      => YoastSEO()->helpers->product->is_premium(),
			'currentPromotions'              => YoastSEO()->classes->get( Promotion_Manager::class )
				->get_current_promotions(),
			'webinarIntroFirstTimeConfigUrl' => $this->get_webinar_shortlink(),
			'linkParams'                     => WPSEO_Shortlinker::get_query_params(),
			'pluginUrl'                      => plugins_url( '', WPSEO_FILE ),
		];

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( in_array( $page, [ WPSEO_Admin::PAGE_IDENTIFIER, 'wpseo_workouts' ], true ) ) {
			wp_enqueue_media();

			$script_data['userEditUrl'] = add_query_arg( 'user_id', '{user_id}', admin_url( 'user-edit.php' ) );
		}

		if ( $page === 'wpseo_tools' ) {
			$this->enqueue_tools_scripts();
		}

		$this->asset_manager->localize_script( 'settings', 'wpseoScriptData', $script_data );
	}

	/**
	 * Enqueues and handles all the tool dependencies.
	 *
	 * @return void
	 */
	private function enqueue_tools_scripts() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$tool = isset( $_GET['tool'] ) && is_string( $_GET['tool'] ) ? sanitize_text_field( wp_unslash( $_GET['tool'] ) ) : '';

		if ( empty( $tool ) ) {
			$this->asset_manager->enqueue_script( 'yoast-seo' );
		}

		if ( $tool === 'bulk-editor' ) {
			$this->asset_manager->enqueue_script( 'bulk-editor' );
		}
	}

	/**
	 * Returns the appropriate shortlink for the Webinar.
	 *
	 * @return string The shortlink for the Webinar.
	 */
	private function get_webinar_shortlink() {
		if ( YoastSEO()->helpers->product->is_premium() ) {
			return WPSEO_Shortlinker::get( 'https://yoa.st/webinar-intro-first-time-config-premium' );
		}

		return WPSEO_Shortlinker::get( 'https://yoa.st/webinar-intro-first-time-config' );
	}
}
