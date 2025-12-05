<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Notifiers
 */

/**
 * Dictates the required methods for a Notification Handler implementation.
 */
interface WPSEO_Notification_Handler {

	/**
	 * Handles the notification object.
	 *
	 * @param Yoast_Notification_Center $notification_center The notification center object.
	 *
	 * @return void
	 */
	public function handle( Yoast_Notification_Center $notification_center );
}
