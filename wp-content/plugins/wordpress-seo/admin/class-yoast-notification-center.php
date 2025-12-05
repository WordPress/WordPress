<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Notifications
 */

use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Handles notifications storage and display.
 */
class Yoast_Notification_Center {

	/**
	 * Option name to store notifications on.
	 *
	 * @var string
	 */
	public const STORAGE_KEY = 'yoast_notifications';

	/**
	 * The singleton instance of this object.
	 *
	 * @var Yoast_Notification_Center|null
	 */
	private static $instance = null;

	/**
	 * Holds the notifications.
	 *
	 * @var Yoast_Notification[][]
	 */
	private $notifications = [];

	/**
	 * Notifications there are newly added.
	 *
	 * @var array
	 */
	private $new = [];

	/**
	 * Notifications that were resolved this execution.
	 *
	 * @var int
	 */
	private $resolved = 0;

	/**
	 * Internal storage for transaction before notifications have been retrieved from storage.
	 *
	 * @var array
	 */
	private $queued_transactions = [];

	/**
	 * Internal flag for whether notifications have been retrieved from storage.
	 *
	 * @var bool
	 */
	private $notifications_retrieved = false;

	/**
	 * Internal flag for whether notifications need to be updated in storage.
	 *
	 * @var bool
	 */
	private $notifications_need_storage = false;

	/**
	 * Construct.
	 */
	private function __construct() {

		add_action( 'init', [ $this, 'setup_current_notifications' ], 1 );

		add_action( 'all_admin_notices', [ $this, 'display_notifications' ] );

		add_action( 'wp_ajax_yoast_get_notifications', [ $this, 'ajax_get_notifications' ] );

		add_action( 'wpseo_deactivate', [ $this, 'deactivate_hook' ] );
		add_action( 'shutdown', [ $this, 'update_storage' ] );
	}

	/**
	 * Singleton getter.
	 *
	 * @return Yoast_Notification_Center
	 */
	public static function get() {

		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Dismiss a notification.
	 *
	 * @return void
	 */
	public static function ajax_dismiss_notification() {
		$notification_center = self::get();

		if ( ! isset( $_POST['notification'] ) || ! is_string( $_POST['notification'] ) ) {
			exit( '-1' );
		}

		$notification_id = sanitize_text_field( wp_unslash( $_POST['notification'] ) );

		if ( empty( $notification_id ) ) {
			exit( '-1' );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are using the variable as a nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), $notification_id ) ) {
			exit( '-1' );
		}

		$notification = $notification_center->get_notification_by_id( $notification_id );
		if ( ( $notification instanceof Yoast_Notification ) === false ) {

			// Permit legacy.
			$options      = [
				'id'            => $notification_id,
				'dismissal_key' => $notification_id,
			];
			$notification = new Yoast_Notification( '', $options );
		}

		if ( self::maybe_dismiss_notification( $notification ) ) {
			exit( '1' );
		}

		exit( '-1' );
	}

	/**
	 * Check if the user has dismissed a notification.
	 *
	 * @param Yoast_Notification $notification The notification to check for dismissal.
	 * @param int|null           $user_id      User ID to check on.
	 *
	 * @return bool
	 */
	public static function is_notification_dismissed( Yoast_Notification $notification, $user_id = null ) {

		$user_id       = self::get_user_id( $user_id );
		$dismissal_key = $notification->get_dismissal_key();

		// This checks both the site-specific user option and the meta value.
		$current_value = get_user_option( $dismissal_key, $user_id );

		// Migrate old user meta to user option on-the-fly.
		if ( ! empty( $current_value )
			&& metadata_exists( 'user', $user_id, $dismissal_key )
			&& update_user_option( $user_id, $dismissal_key, $current_value ) ) {
			delete_user_meta( $user_id, $dismissal_key );
		}

		return ! empty( $current_value );
	}

	/**
	 * Checks if the notification is being dismissed.
	 *
	 * @param Yoast_Notification $notification Notification to check dismissal of.
	 * @param string             $meta_value   Value to set the meta value to if dismissed.
	 *
	 * @return bool True if dismissed.
	 */
	public static function maybe_dismiss_notification( Yoast_Notification $notification, $meta_value = 'seen' ) {

		// Only persistent notifications are dismissible.
		if ( ! $notification->is_persistent() ) {
			return false;
		}

		// If notification is already dismissed, we're done.
		if ( self::is_notification_dismissed( $notification ) ) {
			return true;
		}

		$dismissal_key   = $notification->get_dismissal_key();
		$notification_id = $notification->get_id();

		$is_dismissing = ( $dismissal_key === self::get_user_input( 'notification' ) );
		if ( ! $is_dismissing ) {
			$is_dismissing = ( $notification_id === self::get_user_input( 'notification' ) );
		}

		// Fallback to ?dismissal_key=1&nonce=bla when JavaScript fails.
		if ( ! $is_dismissing ) {
			$is_dismissing = ( self::get_user_input( $dismissal_key ) === '1' );
		}

		if ( ! $is_dismissing ) {
			return false;
		}

		$user_nonce = self::get_user_input( 'nonce' );
		if ( wp_verify_nonce( $user_nonce, $notification_id ) === false ) {
			return false;
		}

		return self::dismiss_notification( $notification, $meta_value );
	}

	/**
	 * Dismisses a notification.
	 *
	 * @param Yoast_Notification $notification Notification to dismiss.
	 * @param string             $meta_value   Value to save in the dismissal.
	 *
	 * @return bool True if dismissed, false otherwise.
	 */
	public static function dismiss_notification( Yoast_Notification $notification, $meta_value = 'seen' ) {
		// Dismiss notification.
		return update_user_option( get_current_user_id(), $notification->get_dismissal_key(), $meta_value ) !== false;
	}

	/**
	 * Restores a notification.
	 *
	 * @param Yoast_Notification $notification Notification to restore.
	 *
	 * @return bool True if restored, false otherwise.
	 */
	public static function restore_notification( Yoast_Notification $notification ) {

		$user_id       = get_current_user_id();
		$dismissal_key = $notification->get_dismissal_key();

		// Restore notification.
		$restored = delete_user_option( $user_id, $dismissal_key );

		// Delete unprefixed user meta too for backward-compatibility.
		if ( metadata_exists( 'user', $user_id, $dismissal_key ) ) {
			$restored = delete_user_meta( $user_id, $dismissal_key ) && $restored;
		}

		return $restored;
	}

	/**
	 * Clear dismissal information for the specified Notification.
	 *
	 * When a cause is resolved, the next time it is present we want to show
	 * the message again.
	 *
	 * @param string|Yoast_Notification $notification Notification to clear the dismissal of.
	 *
	 * @return bool
	 */
	public function clear_dismissal( $notification ) {

		global $wpdb;

		if ( $notification instanceof Yoast_Notification ) {
			$dismissal_key = $notification->get_dismissal_key();
		}

		if ( is_string( $notification ) ) {
			$dismissal_key = $notification;
		}

		if ( empty( $dismissal_key ) ) {
			return false;
		}

		// Remove notification dismissal for all users.
		$deleted = delete_metadata( 'user', 0, $wpdb->get_blog_prefix() . $dismissal_key, '', true );

		// Delete unprefixed user meta too for backward-compatibility.
		$deleted = delete_metadata( 'user', 0, $dismissal_key, '', true ) || $deleted;

		return $deleted;
	}

	/**
	 * Retrieves notifications from the storage and merges in previous notification changes.
	 *
	 * The current user in WordPress is not loaded shortly before the 'init' hook, but the plugin
	 * sometimes needs to add or remove notifications before that. In such cases, the transactions
	 * are not actually executed, but added to a queue. That queue is then handled in this method,
	 * after notifications for the current user have been set up.
	 *
	 * @return void
	 */
	public function setup_current_notifications() {
		$this->retrieve_notifications_from_storage( get_current_user_id() );

		foreach ( $this->queued_transactions as $transaction ) {
			list( $callback, $args ) = $transaction;

			call_user_func_array( $callback, $args );
		}

		$this->queued_transactions = [];
	}

	/**
	 * Add notification to the cookie.
	 *
	 * @param Yoast_Notification $notification Notification object instance.
	 *
	 * @return void
	 */
	public function add_notification( Yoast_Notification $notification ) {

		$callback = [ $this, __FUNCTION__ ];
		$args     = func_get_args();
		if ( $this->queue_transaction( $callback, $args ) ) {
			return;
		}

		// Don't add if the user can't see it.
		if ( ! $notification->display_for_current_user() ) {
			return;
		}

		$notification_id = $notification->get_id();
		$user_id         = $notification->get_user_id();

		// Empty notifications are always added.
		if ( $notification_id !== '' ) {

			// If notification ID exists in notifications, don't add again.
			$present_notification = $this->get_notification_by_id( $notification_id, $user_id );
			if ( $present_notification !== null ) {
				$this->remove_notification( $present_notification, false );
			}

			if ( $present_notification === null ) {
				$this->new[] = $notification_id;
			}
		}

		// Add to list.
		$this->notifications[ $user_id ][] = $notification;

		$this->notifications_need_storage = true;
	}

	/**
	 * Get the notification by ID and user ID.
	 *
	 * @param string   $notification_id The ID of the notification to search for.
	 * @param int|null $user_id         The ID of the user.
	 *
	 * @return Yoast_Notification|null
	 */
	public function get_notification_by_id( $notification_id, $user_id = null ) {
		$user_id = self::get_user_id( $user_id );

		$notifications = $this->get_notifications_for_user( $user_id );

		foreach ( $notifications as $notification ) {
			if ( $notification_id === $notification->get_id() ) {
				return $notification;
			}
		}

		return null;
	}

	/**
	 * Display the notifications.
	 *
	 * @param bool $echo_as_json True when notifications should be printed directly.
	 *
	 * @return void
	 */
	public function display_notifications( $echo_as_json = false ) {

		// Never display notifications for network admin.
		if ( is_network_admin() ) {
			return;
		}

		$sorted_notifications = $this->get_sorted_notifications();
		$notifications        = array_filter( $sorted_notifications, [ $this, 'is_notification_persistent' ] );

		if ( empty( $notifications ) ) {
			return;
		}

		array_walk( $notifications, [ $this, 'remove_notification' ] );

		$notifications = array_unique( $notifications );
		if ( $echo_as_json ) {
			$notification_json = [];

			foreach ( $notifications as $notification ) {
				$notification_json[] = $notification->render();
			}

			// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: WPSEO_Utils::format_json_encode is safe.
			echo WPSEO_Utils::format_json_encode( $notification_json );

			return;
		}

		foreach ( $notifications as $notification ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Temporarily disabled, see: https://github.com/Yoast/wordpress-seo-premium/issues/2510 and https://github.com/Yoast/wordpress-seo-premium/issues/2511.
			echo $notification;
		}
	}

	/**
	 * Remove notification after it has been displayed.
	 *
	 * @param Yoast_Notification $notification Notification to remove.
	 * @param bool               $resolve      Resolve as fixed.
	 *
	 * @return void
	 */
	public function remove_notification( Yoast_Notification $notification, $resolve = true ) {

		$callback = [ $this, __FUNCTION__ ];
		$args     = func_get_args();
		if ( $this->queue_transaction( $callback, $args ) ) {
			return;
		}

		$index = false;

		// ID of the user to show the notification for, defaults to current user id.
		$user_id       = $notification->get_user_id();
		$notifications = $this->get_notifications_for_user( $user_id );

		// Match persistent Notifications by ID, non persistent by item in the array.
		if ( $notification->is_persistent() ) {
			foreach ( $notifications as $current_index => $present_notification ) {
				if ( $present_notification->get_id() === $notification->get_id() ) {
					$index = $current_index;
					break;
				}
			}
		}
		else {
			$index = array_search( $notification, $notifications, true );
		}

		if ( $index === false ) {
			return;
		}

		if ( $notification->is_persistent() && $resolve ) {
			++$this->resolved;
			$this->clear_dismissal( $notification );
		}

		unset( $notifications[ $index ] );
		$this->notifications[ $user_id ] = array_values( $notifications );

		$this->notifications_need_storage = true;
	}

	/**
	 * Removes a notification by its ID.
	 *
	 * @param string $notification_id The notification id.
	 * @param bool   $resolve         Resolve as fixed.
	 *
	 * @return void
	 */
	public function remove_notification_by_id( $notification_id, $resolve = true ) {
		$notification = $this->get_notification_by_id( $notification_id );

		if ( $notification === null ) {
			return;
		}

		$this->remove_notification( $notification, $resolve );
		$this->notifications_need_storage = true;
	}

	/**
	 * Get the notification count.
	 *
	 * @param bool $dismissed Count dismissed notifications.
	 *
	 * @return int Number of notifications
	 */
	public function get_notification_count( $dismissed = false ) {

		$notifications = $this->get_notifications_for_user( get_current_user_id() );
		$notifications = array_filter( $notifications, [ $this, 'filter_persistent_notifications' ] );

		if ( ! $dismissed ) {
			$notifications = array_filter( $notifications, [ $this, 'filter_dismissed_notifications' ] );
		}

		return count( $notifications );
	}

	/**
	 * Get the number of notifications resolved this execution.
	 *
	 * These notifications have been resolved and should be counted when active again.
	 *
	 * @return int
	 */
	public function get_resolved_notification_count() {

		return $this->resolved;
	}

	/**
	 * Return the notifications sorted on type and priority.
	 *
	 * @return Yoast_Notification[] Sorted Notifications
	 */
	public function get_sorted_notifications() {
		$notifications = $this->get_notifications_for_user( get_current_user_id() );
		if ( empty( $notifications ) ) {
			return [];
		}

		// Sort by severity, error first.
		usort( $notifications, [ $this, 'sort_notifications' ] );

		return $notifications;
	}

	/**
	 * AJAX display notifications.
	 *
	 * @return void
	 */
	public function ajax_get_notifications() {
		$echo = false;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form data.
		if ( isset( $_POST['version'] ) && is_string( $_POST['version'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only comparing the variable in a condition.
			$echo = wp_unslash( $_POST['version'] ) === '2';
		}

		// Display the notices.
		$this->display_notifications( $echo );

		// AJAX die.
		exit;
	}

	/**
	 * Remove storage when the plugin is deactivated.
	 *
	 * @return void
	 */
	public function deactivate_hook() {

		$this->clear_notifications();
	}

	/**
	 * Returns the given user ID if it exists.
	 * Otherwise, this function returns the ID of the current user.
	 *
	 * @param int $user_id The user ID to check.
	 *
	 * @return int The user ID to use.
	 */
	private static function get_user_id( $user_id ) {
		if ( $user_id ) {
			return $user_id;
		}
		return get_current_user_id();
	}

	/**
	 * Splits the notifications on user ID.
	 *
	 * In other terms, it returns an associative array,
	 * mapping user ID to a list of notifications for this user.
	 *
	 * @param Yoast_Notification[] $notifications The notifications to split.
	 *
	 * @return array The notifications, split on user ID.
	 */
	private function split_on_user_id( $notifications ) {
		$split_notifications = [];
		foreach ( $notifications as $notification ) {
			$split_notifications[ $notification->get_user_id() ][] = $notification;
		}
		return $split_notifications;
	}

	/**
	 * Save persistent notifications to storage.
	 *
	 * We need to be able to retrieve these so they can be dismissed at any time during the execution.
	 *
	 * @since 3.2
	 *
	 * @return void
	 */
	public function update_storage() {
		/**
		 * Plugins might exit on the plugins_loaded hook.
		 * This prevents the pluggable.php file from loading, as it's loaded after the plugins_loaded hook.
		 * As we need functions defined in pluggable.php, make sure it's loaded.
		 */
		require_once ABSPATH . WPINC . '/pluggable.php';

		$notifications = $this->notifications;

		/**
		 * One array of Yoast_Notifications, merged from multiple arrays.
		 *
		 * @var Yoast_Notification[] $merged_notifications
		 */
		$merged_notifications = [];
		if ( ! empty( $notifications ) ) {
			$merged_notifications = array_merge( ...$notifications );
		}

		/**
		 * Filter: 'yoast_notifications_before_storage' - Allows developer to filter notifications before saving them.
		 *
		 * @param Yoast_Notification[] $notifications
		 */
		$filtered_merged_notifications = apply_filters( 'yoast_notifications_before_storage', $merged_notifications );

		// The notifications were filtered and therefore need to be stored.
		if ( $merged_notifications !== $filtered_merged_notifications ) {
			$merged_notifications             = $filtered_merged_notifications;
			$this->notifications_need_storage = true;
		}

		$notifications = $this->split_on_user_id( $merged_notifications );

		// No notifications to store, clear storage if it was previously present.
		if ( empty( $notifications ) ) {
			$this->remove_storage();

			return;
		}

		// Only store notifications if changes are made.
		if ( $this->notifications_need_storage ) {
			array_walk( $notifications, [ $this, 'store_notifications_for_user' ] );
		}
	}

	/**
	 * Stores the notifications to its respective user's storage.
	 *
	 * @param Yoast_Notification[] $notifications The notifications to store.
	 * @param int                  $user_id       The ID of the user for which to store the notifications.
	 *
	 * @return void
	 */
	private function store_notifications_for_user( $notifications, $user_id ) {
		$notifications_as_arrays = array_map( [ $this, 'notification_to_array' ], $notifications );
		update_user_option( $user_id, self::STORAGE_KEY, $notifications_as_arrays );
	}

	/**
	 * Provide a way to verify present notifications.
	 *
	 * @return Yoast_Notification[] Registered notifications.
	 */
	public function get_notifications() {
		if ( ! $this->notifications ) {
			return [];
		}
		return array_merge( ...$this->notifications );
	}

	/**
	 * Returns the notifications for the given user.
	 *
	 * @param int $user_id The id of the user to check.
	 *
	 * @return Yoast_Notification[] The notifications for the user with the given ID.
	 */
	public function get_notifications_for_user( $user_id ) {
		if ( array_key_exists( $user_id, $this->notifications ) ) {
			return $this->notifications[ $user_id ];
		}
		return [];
	}

	/**
	 * Get newly added notifications.
	 *
	 * @return array
	 */
	public function get_new_notifications() {

		return array_map( [ $this, 'get_notification_by_id' ], $this->new );
	}

	/**
	 * Get information from the User input.
	 *
	 * Note that this function does not handle nonce verification.
	 *
	 * @param string $key Key to retrieve.
	 *
	 * @return string non-sanitized value of key if set, an empty string otherwise.
	 */
	private static function get_user_input( $key ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information and only using this variable in a comparison.
		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) && is_string( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: This function does not sanitize variables.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing -- Reason: This function does not verify a nonce.
		if ( $request_method === 'POST' ) {
			if ( isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ) {
				return wp_unslash( $_POST[ $key ] );
			}
		}
		elseif ( isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) ) {
			return wp_unslash( $_GET[ $key ] );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return '';
	}

	/**
	 * Retrieve the notifications from storage and fill the relevant property.
	 *
	 * @param int $user_id The ID of the user to retrieve notifications for.
	 *
	 * @return void
	 */
	private function retrieve_notifications_from_storage( $user_id ) {
		if ( $this->notifications_retrieved ) {
			return;
		}

		$this->notifications_retrieved = true;

		$stored_notifications = get_user_option( self::STORAGE_KEY, $user_id );

		// Check if notifications are stored.
		if ( empty( $stored_notifications ) ) {
			return;
		}

		if ( is_array( $stored_notifications ) ) {
			$notifications = array_map( [ $this, 'array_to_notification' ], $stored_notifications );

			// Apply array_values to ensure we get a 0-indexed array.
			$notifications = array_values( array_filter( $notifications, [ $this, 'filter_notification_current_user' ] ) );

			$this->notifications[ $user_id ] = $notifications;
		}
	}

	/**
	 * Sort on type then priority.
	 *
	 * @param Yoast_Notification $a Compare with B.
	 * @param Yoast_Notification $b Compare with A.
	 *
	 * @return int 1, 0 or -1 for sorting offset.
	 */
	private function sort_notifications( Yoast_Notification $a, Yoast_Notification $b ) {

		$a_type = $a->get_type();
		$b_type = $b->get_type();

		if ( $a_type === $b_type ) {
			return WPSEO_Utils::calc( $b->get_priority(), 'compare', $a->get_priority() );
		}

		if ( $a_type === 'error' ) {
			return -1;
		}

		if ( $b_type === 'error' ) {
			return 1;
		}

		return 0;
	}

	/**
	 * Clear local stored notifications.
	 *
	 * @return void
	 */
	private function clear_notifications() {

		$this->notifications           = [];
		$this->notifications_retrieved = false;
	}

	/**
	 * Filter out non-persistent notifications.
	 *
	 * @since 3.2
	 *
	 * @param Yoast_Notification $notification Notification to test for persistent.
	 *
	 * @return bool
	 */
	private function filter_persistent_notifications( Yoast_Notification $notification ) {

		return $notification->is_persistent();
	}

	/**
	 * Filter out dismissed notifications.
	 *
	 * @param Yoast_Notification $notification Notification to check.
	 *
	 * @return bool
	 */
	private function filter_dismissed_notifications( Yoast_Notification $notification ) {

		return ! self::maybe_dismiss_notification( $notification );
	}

	/**
	 * Convert Notification to array representation.
	 *
	 * @since 3.2
	 *
	 * @param Yoast_Notification $notification Notification to convert.
	 *
	 * @return array
	 */
	private function notification_to_array( Yoast_Notification $notification ) {

		$notification_data = $notification->to_array();

		if ( isset( $notification_data['nonce'] ) ) {
			unset( $notification_data['nonce'] );
		}

		return $notification_data;
	}

	/**
	 * Convert stored array to Notification.
	 *
	 * @param array $notification_data Array to convert to Notification.
	 *
	 * @return Yoast_Notification
	 */
	private function array_to_notification( $notification_data ) {

		if ( isset( $notification_data['options']['nonce'] ) ) {
			unset( $notification_data['options']['nonce'] );
		}

		if ( isset( $notification_data['message'] )
			&& is_subclass_of( $notification_data['message'], Abstract_Presenter::class, false )
		) {
			$notification_data['message'] = $notification_data['message']->present();
		}

		if ( isset( $notification_data['options']['user'] ) ) {
			$notification_data['options']['user_id'] = $notification_data['options']['user']->ID;
			unset( $notification_data['options']['user'] );

			$this->notifications_need_storage = true;
		}

		return new Yoast_Notification(
			$notification_data['message'],
			$notification_data['options']
		);
	}

	/**
	 * Filter notifications that should not be displayed for the current user.
	 *
	 * @param Yoast_Notification $notification Notification to test.
	 *
	 * @return bool
	 */
	private function filter_notification_current_user( Yoast_Notification $notification ) {
		return $notification->display_for_current_user();
	}

	/**
	 * Checks if given notification is persistent.
	 *
	 * @param Yoast_Notification $notification The notification to check.
	 *
	 * @return bool True when notification is not persistent.
	 */
	private function is_notification_persistent( Yoast_Notification $notification ) {
		return ! $notification->is_persistent();
	}

	/**
	 * Queues a notification transaction for later execution if notifications are not yet set up.
	 *
	 * @param callable $callback Callback that performs the transaction.
	 * @param array    $args     Arguments to pass to the callback.
	 *
	 * @return bool True if transaction was queued, false if it can be performed immediately.
	 */
	private function queue_transaction( $callback, $args ) {
		if ( $this->notifications_retrieved ) {
			return false;
		}

		$this->add_transaction_to_queue( $callback, $args );

		return true;
	}

	/**
	 * Adds a notification transaction to the queue for later execution.
	 *
	 * @param callable $callback Callback that performs the transaction.
	 * @param array    $args     Arguments to pass to the callback.
	 *
	 * @return void
	 */
	private function add_transaction_to_queue( $callback, $args ) {
		$this->queued_transactions[] = [ $callback, $args ];
	}

	/**
	 * Removes all notifications from storage.
	 *
	 * @return bool True when notifications got removed.
	 */
	protected function remove_storage() {
		if ( ! $this->has_stored_notifications() ) {
			return false;
		}

		delete_user_option( get_current_user_id(), self::STORAGE_KEY );
		return true;
	}

	/**
	 * Checks if there are stored notifications.
	 *
	 * @return bool True when there are stored notifications.
	 */
	protected function has_stored_notifications() {
		$stored_notifications = $this->get_stored_notifications();

		return ! empty( $stored_notifications );
	}

	/**
	 * Retrieves the stored notifications.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array|false Array with notifications or false when not set.
	 */
	protected function get_stored_notifications() {
		return get_user_option( self::STORAGE_KEY, get_current_user_id() );
	}
}
