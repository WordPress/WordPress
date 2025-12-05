<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\User_Interface\Configuration;

use Google\Site_Kit\Core\Permissions\Permissions;
use Yoast\WP\SEO\Conditionals\Google_Site_Kit_Feature_Conditional;
use Yoast\WP\SEO\Conditionals\Third_Party\Site_Kit_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Enables the needed Site Kit capabilities for the SEO manager role.
 */
class Site_Kit_Capabilities_Integration implements Integration_Interface {

	/**
	 * Registers needed filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'user_has_cap', [ $this, 'enable_site_kit_capabilities' ], 10, 2 );
	}

	/**
	 * The needed conditionals.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		// This cannot have the Admin Conditional since it also needs to run in Rest requests.
		return [ Google_Site_Kit_Feature_Conditional::class, Site_Kit_Conditional::class ];
	}

	/**
	 * Checks if the Site Kit capabilities need to be enabled for a manager.
	 *
	 * @param array<string> $all_caps     All the current capabilities of the current user.
	 * @param array<string> $cap_to_check The capability to check against.
	 *
	 * @return array<string>
	 */
	public function enable_site_kit_capabilities( $all_caps, $cap_to_check ) {
		if ( ! isset( $cap_to_check[0] ) || ! \class_exists( Permissions::class ) ) {
			return $all_caps;
		}
		$user          = \wp_get_current_user();
		$caps_to_check = [
			Permissions::SETUP,
			Permissions::VIEW_DASHBOARD,
		];
		if ( \in_array( $cap_to_check[0], $caps_to_check, true ) && \in_array( 'wpseo_manager', $user->roles, true ) ) {
			$all_caps[ $cap_to_check[0] ] = true;
		}

		return $all_caps;
	}
}
