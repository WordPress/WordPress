<?php
/**
 * Remote Inbox Notifications feature.
 */

namespace Automattic\WooCommerce\Internal\Admin;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\RemoteInboxNotifications\RemoteInboxNotificationsEngine;

/**
 * Remote Inbox Notifications feature logic.
 */
class RemoteInboxNotifications {
	/**
	 * Option name used to toggle this feature.
	 */
	const TOGGLE_OPTION_NAME = 'woocommerce_show_marketplace_suggestions';

	/**
	 * Class instance.
	 *
	 * @var RemoteInboxNotifications instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook into WooCommerce.
	 */
	public function __construct() {
		if ( Features::is_enabled( 'remote-inbox-notifications' ) ) {
			RemoteInboxNotificationsEngine::init();
		}
	}
}
