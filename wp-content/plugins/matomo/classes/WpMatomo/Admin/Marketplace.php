<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo\Settings;
use WpMatomo\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Marketplace {

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function show() {
		$settings   = $this->settings;
		$valid_tabs = [];
		$active_tab = '';

		if ( ! is_plugin_active( MATOMO_MARKETPLACE_PLUGIN_NAME ) ) {
			$valid_tabs = [ 'marketplace' ];
			$active_tab = 'marketplace';

			if ( $this->can_user_manage() ) {
				if ( current_user_can( 'install_plugins' ) ) {
					$valid_tabs[] = 'install';
				}
				$valid_tabs[] = 'subscriptions';
			}

			if ( isset( $_GET['tab'] )
				&& in_array( $_GET['tab'], $valid_tabs, true )
			) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$active_tab = wp_unslash( $_GET['tab'] );
			}

			if ( 'install' === $active_tab || 'subscriptions' === $active_tab ) {
				$marketplace_setup_wizard = new MarketplaceSetupWizard();
			}
		}

		include dirname( __FILE__ ) . '/views/marketplace.php';
	}

	private function can_user_manage() {
		// only someone who can activate plugins is allowed to manage subscriptions
		if ( $this->is_multisite() ) {
			return is_super_admin();
		}

		return current_user_can( Capabilities::KEY_SUPERUSER );
	}

	private function is_multisite() {
		return function_exists( 'is_multisite' ) && is_multisite();
	}
}
