<?php
namespace Elementor\Modules\Notifications;

class Options {

	public static function has_unread_notifications(): bool {
		$current_user = wp_get_current_user();

		if ( ! $current_user ) {
			return false;
		}

		$unread_notifications = get_transient( "elementor_unread_notifications_{$current_user->ID}" );

		if ( false === $unread_notifications ) {
			$notifications = API::get_notifications_by_conditions();
			$notifications_ids = wp_list_pluck( $notifications, 'id' );

			$unread_notifications = array_diff( $notifications_ids, static::get_notifications_dismissed() );

			set_transient( "elementor_unread_notifications_{$current_user->ID}", $unread_notifications, HOUR_IN_SECONDS );
		}

		return ! empty( $unread_notifications );
	}

	public static function get_notifications_dismissed() {
		$current_user = wp_get_current_user();

		if ( ! $current_user ) {
			return [];
		}

		$notifications_dismissed = get_user_meta( $current_user->ID, '_e_notifications_dismissed', true );

		if ( ! is_array( $notifications_dismissed ) ) {
			$notifications_dismissed = [];
		}

		return $notifications_dismissed;
	}

	public static function mark_notification_read( $notifications ): bool {
		$current_user = wp_get_current_user();

		if ( ! $current_user ) {
			return false;
		}

		$notifications_dismissed = static::get_notifications_dismissed();

		foreach ( $notifications as $notification ) {
			if ( ! in_array( $notification['id'], $notifications_dismissed, true ) ) {
				$notifications_dismissed[] = $notification['id'];
			}
		}

		$notifications_dismissed = array_unique( $notifications_dismissed );

		update_user_meta( $current_user->ID, '_e_notifications_dismissed', $notifications_dismissed );

		delete_transient( "elementor_unread_notifications_{$current_user->ID}" );

		return true;
	}
}
