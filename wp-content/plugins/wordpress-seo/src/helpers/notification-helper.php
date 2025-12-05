<?php

namespace Yoast\WP\SEO\Helpers;

use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * A helper object for notifications.
 */
class Notification_Helper {

	/**
	 * Restores a notification (wrapper function).
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Yoast_Notification $notification The notification to restore.
	 *
	 * @return bool True if restored, false otherwise.
	 */
	public function restore_notification( Yoast_Notification $notification ) {
		return Yoast_Notification_Center::restore_notification( $notification );
	}

	/**
	 * Return the notifications sorted on type and priority. (wrapper function)
	 *
	 * @codeCoverageIgnore
	 *
	 * @return Yoast_Notification[] Sorted Notifications
	 */
	public function get_sorted_notifications() {
		$notification_center = Yoast_Notification_Center::get();

		return $notification_center->get_sorted_notifications();
	}

	/**
	 * Check if the user has dismissed a notification. (wrapper function)
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Yoast_Notification $notification The notification to check for dismissal.
	 * @param int|null           $user_id      User ID to check on.
	 *
	 * @return bool
	 */
	private function is_notification_dismissed( Yoast_Notification $notification, $user_id = null ) {
		return Yoast_Notification_Center::is_notification_dismissed( $notification, $user_id );
	}

	/**
	 * Parses all the notifications to an array with just id, message, nonce, type and dismissed.
	 *
	 * @return array<string, string|bool>
	 */
	public function get_alerts(): array {
		$all_notifications = $this->get_sorted_notifications();

		return \array_map(
			function ( $notification ) {
				return [
					'id'           => $notification->get_id(),
					'message'      => $notification->get_message(),
					'nonce'        => $notification->get_nonce(),
					'type'         => $notification->get_type(),
					'dismissed'    => $this->is_notification_dismissed( $notification ),
					'resolveNonce' => $notification->get_resolve_nonce(),
				];
			},
			$all_notifications
		);
	}
}
