<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\User_Interface\Setup;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Google_Site_Kit_Feature_Conditional;
use Yoast\WP\SEO\Dashboard\Infrastructure\Integrations\Site_Kit;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Redirect_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Setup url Interceptor class.
 */
class Setup_Url_Interceptor implements Integration_Interface {

	/**
	 * The page url where this check lives.
	 */
	public const PAGE = 'wpseo_page_site_kit_set_up';

	/**
	 * The name of the transient.
	 */
	public const SITE_KIT_SETUP_TRANSIENT = 'wpseo_site_kit_set_up_transient';

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper $current_page_helper
	 */
	private $current_page_helper;

	/**
	 * Holds the redirect helper.
	 *
	 * @var Redirect_Helper $redirect_helper
	 */
	private $redirect_helper;

	/**
	 * The Site Kit configuration object.
	 *
	 * @var Site_Kit $site_kit_configuration
	 */
	private $site_kit_configuration;

	/**
	 * The constructor.
	 *
	 * @param Current_Page_Helper $current_page_helper    The current page helper.
	 * @param Site_Kit            $site_kit_configuration The Site Kit configuration object.
	 * @param Redirect_Helper     $redirect_helper        The redirect helper to abstract away the actual redirecting.
	 */
	public function __construct( Current_Page_Helper $current_page_helper, Site_Kit $site_kit_configuration, Redirect_Helper $redirect_helper ) {
		$this->current_page_helper = $current_page_helper;
		$this->redirect_helper     = $redirect_helper;

		$this->site_kit_configuration = $site_kit_configuration;
	}

	/**
	 * The conditions for this integration to load.
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [ Google_Site_Kit_Feature_Conditional::class, Admin_Conditional::class ];
	}

	/**
	 * Registers the interceptor code to the admin_init function.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'admin_menu', [ $this, 'add_redirect_page' ] );
		\add_action( 'admin_init', [ $this, 'intercept_site_kit_setup_url_redirect' ], 1 );
	}

	/**
	 * Adds a dummy page.
	 *
	 * We need to register this in between page.
	 *
	 * @param array<array<string>> $pages The pages.
	 *
	 * @return array<array<string>> The pages.
	 */
	public function add_redirect_page( $pages ) {
		\add_submenu_page(
			'',
			'',
			'',
			'wpseo_manage_options',
			self::PAGE
		);

		return $pages;
	}

	/**
	 * Checks if we are trying to reach a site kit setup url and sets the needed transient in between.
	 *
	 * @return void
	 */
	public function intercept_site_kit_setup_url_redirect() {
		$allowed_setup_links = [
			$this->site_kit_configuration->get_install_url(),
			$this->site_kit_configuration->get_activate_url(),
			$this->site_kit_configuration->get_setup_url(),
			$this->site_kit_configuration->get_update_url(),
		];

		// Are we on the in-between page?
		if ( $this->current_page_helper->get_current_yoast_seo_page() === self::PAGE ) {
			// Check if parameter is there and is valid.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			if ( isset( $_GET['redirect_setup_url'] ) && \in_array( \wp_unslash( $_GET['redirect_setup_url'] ), $allowed_setup_links, true ) ) {
				// We overwrite the transient for each time this redirect is hit to keep refreshing the time.
				\set_transient( self::SITE_KIT_SETUP_TRANSIENT, 1, ( \MINUTE_IN_SECONDS * 15 ) );
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Only allowed pre verified links can end up here.
				$redirect_url = \wp_unslash( $_GET['redirect_setup_url'] );
				$this->redirect_helper->do_safe_redirect( $redirect_url, 302, 'Yoast SEO' );

			}
			else {
				$this->redirect_helper->do_safe_redirect( \self_admin_url( 'admin.php?page=wpseo_dashboard' ), 302, 'Yoast SEO' );
			}
		}
	}
}
