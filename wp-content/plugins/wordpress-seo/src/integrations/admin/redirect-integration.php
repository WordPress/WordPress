<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Redirect_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Class Redirect_Integration.
 */
class Redirect_Integration implements Integration_Interface {

	/**
	 * The redirect helper.
	 *
	 * @var Redirect_Helper
	 */
	private $redirect;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $short_link_helper;

	/**
	 * Sets the helpers.
	 *
	 * @param Redirect_Helper   $redirect          The redirect helper.
	 * @param Short_Link_Helper $short_link_helper The short link helper.
	 */
	public function __construct(
		Redirect_Helper $redirect,
		Short_Link_Helper $short_link_helper
	) {
		$this->redirect          = $redirect;
		$this->short_link_helper = $short_link_helper;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wp_loaded', [ $this, 'settings_redirect' ] );
	}

	/**
	 * Catch all method to redirect certain pages related to redirects.
	 *
	 * @return void
	 */
	public function settings_redirect() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		$current_page = \sanitize_text_field( \wp_unslash( $_GET['page'] ) );

		switch ( $current_page ) {
			case 'wpseo_titles': // Redirect to new settings URLs. We're adding this, so that not-updated add-ons don't point to non-existent pages.
				$this->redirect->do_safe_redirect( \admin_url( 'admin.php?page=wpseo_page_settings#/site-representation' ), 301 );
				return;
			case 'wpseo_redirects_tools': // Redirect to Yoast redirection page, from the respective WP tools page.
				$this->redirect->do_safe_redirect( \admin_url( 'admin.php?page=wpseo_redirects&from_tools=1' ), 302 );
				return;
			case 'wpseo_brand_insights':
				$this->redirect->do_unsafe_redirect( $this->short_link_helper->get( 'https://yoa.st/brand-insights-wp-admin' ), 302 );
				return;
			case 'wpseo_brand_insights_premium':
				$this->redirect->do_unsafe_redirect( $this->short_link_helper->get( 'https://yoa.st/brand-insights-wp-admin-premium' ), 302 );
				return;
			default:
				return;
		}
	}

	/**
	 * Old method kept for backward compatibility.
	 *
	 * @deprecated 26.2
	 * @codeCoverageIgnore Because of deprecation.
	 * @return void
	 */
	public function old_settings_redirect() {
		\_deprecated_function( __METHOD__, 'Yoast SEO 26.2', 'Use settings_redirect() instead.' );
		$this->settings_redirect();
	}
}
