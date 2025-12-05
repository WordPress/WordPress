<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification_Center;

/**
 * Shows a notification for users who have WordPress auto updates enabled but not Yoast SEO auto updates.
 */
class Auto_Update_Watcher implements Integration_Interface {

	use No_Conditionals;

	/**
	 * The notification ID.
	 */
	public const NOTIFICATION_ID = 'wpseo-auto-update';

	/**
	 * The Yoast notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * Auto_Update constructor.
	 *
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 */
	public function __construct( Yoast_Notification_Center $notification_center ) {
		$this->notification_center = $notification_center;
	}

	/**
	 * Initializes the integration.
	 *
	 * On admin_init, it is checked whether the notification to auto-update Yoast SEO needs to be shown or removed.
	 * This is also done when major WP core updates are being enabled or disabled,
	 * and when automatic updates for Yoast SEO are being enabled or disabled.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'remove_notification' ] );
	}

	/**
	 * Removes the notification from the notification center, if it exists.
	 *
	 * @return void
	 */
	public function remove_notification() {
		$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
	}
}
