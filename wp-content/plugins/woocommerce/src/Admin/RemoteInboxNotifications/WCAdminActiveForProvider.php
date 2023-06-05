<?php
/**
 * WCAdmin active for provider.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

use Automattic\WooCommerce\Admin\WCAdminHelper;

defined( 'ABSPATH' ) || exit;

/**
 * WCAdminActiveForProvider class
 */
class WCAdminActiveForProvider {
	/**
	 * Get the number of seconds that the store has been active.
	 *
	 * @return number Number of seconds.
	 */
	public function get_wcadmin_active_for_in_seconds() {
		return WCAdminHelper::get_wcadmin_active_for_in_seconds();
	}
}
