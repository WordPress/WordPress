<?php
/**
 * @package WPSEO\Admin
 */

/**
 * Handles notifications storage and display.
 */
class Yoast_Notification_Center {

	const TRANSIENT_KEY = 'yoast_notifications';

	/**
	 * The singleton instance of this object
	 *
	 * @var \Yoast_Notification_Center
	 */
	private static $instance = null;

	/**
	 * @var array
	 */
	private $notifications = array();

	/**
	 * Construct
	 */
	private function __construct() {

		// Load the notifications from cookie.
		$this->notifications = $this->get_notifications_from_transient();

		// Clear the cookie.
		if ( count( $this->notifications ) > 0 ) {
			$this->remove_transient();
		}

		add_action( 'all_admin_notices', array( $this, 'display_notifications' ) );
		add_action( 'shutdown', array( $this, 'set_transient' ) );
		add_action( 'wp_ajax_yoast_get_notifications', array( $this, 'ajax_get_notifications' ) );
		add_action( 'wpseo_deactivate', array( $this, 'deactivate_hook' ) );
	}

	/**
	 * Singleton getter
	 *
	 * @return Yoast_Notification_Center
	 */
	public static function get() {

		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the notifications from cookie
	 *
	 * @return array
	 */
	private function get_notifications_from_transient() {

		// The notifications array.
		$notifications = array();

		$transient_notifications = get_transient( self::TRANSIENT_KEY );

		// Check if cookie is set.
		if ( false !== $transient_notifications ) {

			// Get json notifications from cookie.
			$json_notifications = json_decode( $transient_notifications, true );

			// Create Yoast_Notification objects.
			if ( count( $json_notifications ) > 0 ) {
				foreach ( $json_notifications as $json_notification ) {
					$notifications[] = new Yoast_Notification( $json_notification['message'], $json_notification['options'] );
				}
			}
		}

		return $notifications;
	}

	/**
	 * Clear the cookie
	 */
	private function remove_transient() {
		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Clear local stored notifications
	 */
	private function clear_notifications() {
		$this->notifications = array();
	}

	/**
	 * Remove transient when the plugin is deactivated
	 */
	public function deactivate_hook() {
		$this->clear_notifications();
	}

	/**
	 * Write the notifications to a cookie (hooked on shutdown)
	 */
	public function set_transient() {

		// Count local stored notifications.
		if ( count( $this->notifications ) > 0 ) {

			// Create array with all notifications.
			$arr_notifications = array();

			// Add each notification as array to $arr_notifications.
			foreach ( $this->notifications as $notification ) {
				$arr_notifications[] = $notification->to_array();
			}

			// Set the cookie with notifications.
			set_transient( self::TRANSIENT_KEY, json_encode( $arr_notifications ), ( MINUTE_IN_SECONDS * 10 ) );
		}
	}

	/**
	 * Add notification to the cookie
	 *
	 * @param Yoast_Notification $notification
	 */
	public function add_notification( Yoast_Notification $notification ) {
		$this->notifications[] = $notification;
	}

	/**
	 * Display the notifications
	 */
	public function display_notifications() {
		$this->notifications = array_unique( $this->notifications );

		// Display notifications.
		if ( count( $this->notifications ) > 0 ) {
			foreach ( $this->notifications as $notification ) {
				echo $notification;
			}
		}

		// Clear the local stored notifications.
		$this->clear_notifications();
	}

	/**
	 * AJAX display notifications
	 */
	public function ajax_get_notifications() {

		// Display the notices.
		$this->display_notifications();

		// AJAX die.
		exit;
	}

}
