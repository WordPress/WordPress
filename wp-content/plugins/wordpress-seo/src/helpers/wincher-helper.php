<?php

namespace Yoast\WP\SEO\Helpers;

use WPSEO_Shortlinker;
use Yoast\WP\SEO\Conditionals\Non_Multisite_Conditional;
use Yoast\WP\SEO\Config\Wincher_Client;
use Yoast\WP\SEO\Exceptions\OAuth\Authentication_Failed_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Property_Exception;
use Yoast\WP\SEO\Exceptions\OAuth\Tokens\Empty_Token_Exception;

/**
 * A helper object for Wincher matters.
 */
class Wincher_Helper {

	/**
	 * Holds the Options Page helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * Options_Helper constructor.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Checks if the integration should be active for the current user.
	 *
	 * @return bool Whether the integration is active.
	 */
	public function is_active() {
		$conditional = new Non_Multisite_Conditional();

		if ( ! $conditional->is_met() ) {
			return false;
		}

		if ( ! \current_user_can( 'publish_posts' ) && ! \current_user_can( 'publish_pages' ) ) {
			return false;
		}

		return (bool) $this->options->get( 'wincher_integration_active', true );
	}

	/**
	 * Checks if the user is logged in to Wincher.
	 *
	 * @return bool The Wincher login status.
	 */
	public function login_status() {
		try {
			$wincher = \YoastSEO()->classes->get( Wincher_Client::class );
		} catch ( Empty_Property_Exception $e ) {
			// Return false if token is malformed (empty property).
			return false;
		}

		// Get token (and refresh it if it's expired).
		try {
			$wincher->get_tokens();
		} catch ( Authentication_Failed_Exception $e ) {
			return false;
		} catch ( Empty_Token_Exception $e ) {
			return false;
		}

		return $wincher->has_valid_tokens();
	}

	/**
	 * Returns the Wincher links that can be used to localize the global admin
	 * script. Mainly exists to avoid duplicating these links in multiple places
	 * around the code base.
	 *
	 * @return string[]
	 */
	public function get_admin_global_links() {
		return [
			'links.wincher.login'   => 'https://app.wincher.com/login?utm_medium=plugin&utm_source=yoast&referer=yoast&partner=yoast',
			'links.wincher.about'   => WPSEO_Shortlinker::get( 'https://yoa.st/dashboard-about-wincher' ),
			'links.wincher.pricing' => WPSEO_Shortlinker::get( 'https://yoa.st/wincher-popup-pricing' ),
			'links.wincher.website' => WPSEO_Shortlinker::get( 'https://yoa.st/wincher-popup' ),
			'links.wincher.upgrade' => WPSEO_Shortlinker::get( 'https://yoa.st/wincher-upgrade' ),
		];
	}
}
