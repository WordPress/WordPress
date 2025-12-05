<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Notifiers
 */

/**
 * Abstract class representing a dismissible notification.
 */
abstract class WPSEO_Dismissible_Notification implements WPSEO_Listener, WPSEO_Notification_Handler {

	/**
	 * The identifier for the notification.
	 *
	 * @var string
	 */
	protected $notification_identifier = '';

	/**
	 * Retrieves instance of a notification.
	 *
	 * @return Yoast_Notification The notification.
	 */
	abstract protected function get_notification();

	/**
	 * Listens to an argument in the request URL and triggers an action.
	 *
	 * @return void
	 */
	public function listen() {
		if ( $this->get_listener_value() !== $this->notification_identifier ) {
			return;
		}

		$this->dismiss();
	}

	/**
	 * Adds the notification if applicable, otherwise removes it.
	 *
	 * @param Yoast_Notification_Center $notification_center The notification center object.
	 *
	 * @return void
	 */
	public function handle( Yoast_Notification_Center $notification_center ) {
		if ( $this->is_applicable() ) {
			$notification = $this->get_notification();
			$notification_center->add_notification( $notification );

			return;
		}

		$notification_center->remove_notification_by_id( 'wpseo-' . $this->notification_identifier );
	}

	/**
	 * Listens to an argument in the request URL and triggers an action.
	 *
	 * @return void
	 */
	protected function dismiss() {
		$this->set_dismissal_state();
		$this->redirect_to_dashboard();
	}

	/**
	 * Checks if a notice is applicable.
	 *
	 * @return bool Whether a notice should be shown or not.
	 */
	protected function is_applicable() {
		return $this->is_notice_dismissed() === false;
	}

	/**
	 * Checks whether the notification has been dismissed.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return bool True when notification is dismissed.
	 */
	protected function is_notice_dismissed() {
		return get_user_meta( get_current_user_id(), 'wpseo-remove-' . $this->notification_identifier, true ) === '1';
	}

	/**
	 * Retrieves the value where listener is listening for.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string|null The listener value or null if not set.
	 */
	protected function get_listener_value() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: Normally we would need to check for a nonce here but this class is not used anymore.
		if ( isset( $_GET['yoast_dismiss'] ) && is_string( $_GET['yoast_dismiss'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: Normally we would need to check for a nonce here but this class is not used anymore.
			return sanitize_text_field( wp_unslash( $_GET['yoast_dismiss'] ) );
		}
		return null;
	}

	/**
	 * Dismisses the notification.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	protected function set_dismissal_state() {
		update_user_meta( get_current_user_id(), 'wpseo-remove-' . $this->notification_identifier, true );
	}

	/**
	 * Redirects the user back to the dashboard.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	protected function redirect_to_dashboard() {
		wp_safe_redirect( admin_url( 'admin.php?page=wpseo_dashboard' ) );
		exit;
	}
}
