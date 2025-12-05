<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Notifications
 */

/**
 * Class Yoast_Notifications.
 */
class Yoast_Notifications {

	/**
	 * Holds the admin page's ID.
	 *
	 * @var string
	 */
	public const ADMIN_PAGE = 'wpseo_dashboard';

	/**
	 * Total notifications count.
	 *
	 * @var int
	 */
	private static $notification_count = 0;

	/**
	 * All error notifications.
	 *
	 * @var array
	 */
	private static $errors = [];

	/**
	 * Active errors.
	 *
	 * @var array
	 */
	private static $active_errors = [];

	/**
	 * Dismissed errors.
	 *
	 * @var array
	 */
	private static $dismissed_errors = [];

	/**
	 * All warning notifications.
	 *
	 * @var array
	 */
	private static $warnings = [];

	/**
	 * Active warnings.
	 *
	 * @var array
	 */
	private static $active_warnings = [];

	/**
	 * Dismissed warnings.
	 *
	 * @var array
	 */
	private static $dismissed_warnings = [];

	/**
	 * Yoast_Notifications constructor.
	 */
	public function __construct() {

		$this->add_hooks();
	}

	/**
	 * Add hooks
	 *
	 * @return void
	 */
	private function add_hooks() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['page'] ) && is_string( $_GET['page'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			if ( $page === self::ADMIN_PAGE ) {
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			}
		}

		// Needed for adminbar and Notifications page.
		add_action( 'admin_init', [ self::class, 'collect_notifications' ], 99 );

		// Add AJAX hooks.
		add_action( 'wp_ajax_yoast_dismiss_notification', [ $this, 'ajax_dismiss_notification' ] );
		add_action( 'wp_ajax_yoast_restore_notification', [ $this, 'ajax_restore_notification' ] );
	}

	/**
	 * Enqueue assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$asset_manager = new WPSEO_Admin_Asset_Manager();

		$asset_manager->enqueue_style( 'notifications' );
	}

	/**
	 * Handle ajax request to dismiss a notification.
	 *
	 * @return void
	 */
	public function ajax_dismiss_notification() {

		$notification = $this->get_notification_from_ajax_request();
		if ( $notification ) {
			$notification_center = Yoast_Notification_Center::get();
			$notification_center->maybe_dismiss_notification( $notification );

			$this->output_ajax_response( $notification->get_type() );
		}

		wp_die();
	}

	/**
	 * Handle ajax request to restore a notification.
	 *
	 * @return void
	 */
	public function ajax_restore_notification() {

		$notification = $this->get_notification_from_ajax_request();
		if ( $notification ) {
			$notification_center = Yoast_Notification_Center::get();
			$notification_center->restore_notification( $notification );

			$this->output_ajax_response( $notification->get_type() );
		}

		wp_die();
	}

	/**
	 * Create AJAX response data.
	 *
	 * @param string $type Notification type.
	 *
	 * @return void
	 */
	private function output_ajax_response( $type ) {

		$html = $this->get_view_html( $type );
		// phpcs:disable WordPress.Security.EscapeOutput -- Reason: WPSEO_Utils::format_json_encode is safe.
		echo WPSEO_Utils::format_json_encode(
			[
				'html'  => $html,
				'total' => self::get_active_notification_count(),
			]
		);
		// phpcs:enable -- Reason: WPSEO_Utils::format_json_encode is safe.
	}

	/**
	 * Get the HTML to return in the AJAX request.
	 *
	 * @param string $type Notification type.
	 *
	 * @return bool|string
	 */
	private function get_view_html( $type ) {

		switch ( $type ) {
			case 'error':
				$view = 'errors';
				break;

			case 'warning':
			default:
				$view = 'warnings';
				break;
		}

		// Re-collect notifications.
		self::collect_notifications();

		/**
		 * Stops PHPStorm from nagging about this variable being unused. The variable is used in the view.
		 *
		 * @noinspection PhpUnusedLocalVariableInspection
		 */
		$notifications_data = self::get_template_variables();

		ob_start();
		include WPSEO_PATH . 'admin/views/partial-notifications-' . $view . '.php';
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Extract the Yoast Notification from the AJAX request.
	 *
	 * This function does not handle nonce verification.
	 *
	 * @return Yoast_Notification|null A Yoast_Notification on success, null on failure.
	 */
	private function get_notification_from_ajax_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: This function does not handle nonce verification.
		if ( ! isset( $_POST['notification'] ) || ! is_string( $_POST['notification'] ) ) {
			return null;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: This function does not handle nonce verification.
		$notification_id = sanitize_text_field( wp_unslash( $_POST['notification'] ) );

		if ( empty( $notification_id ) ) {
			return null;
		}
		$notification_center = Yoast_Notification_Center::get();
		return $notification_center->get_notification_by_id( $notification_id );
	}

	/**
	 * Collect the notifications and group them together.
	 *
	 * @return void
	 */
	public static function collect_notifications() {

		$notification_center = Yoast_Notification_Center::get();

		$notifications            = $notification_center->get_sorted_notifications();
		self::$notification_count = count( $notifications );

		self::$errors           = array_filter( $notifications, [ self::class, 'filter_error_notifications' ] );
		self::$dismissed_errors = array_filter( self::$errors, [ self::class, 'filter_dismissed_notifications' ] );
		self::$active_errors    = array_diff( self::$errors, self::$dismissed_errors );

		self::$warnings           = array_filter( $notifications, [ self::class, 'filter_warning_notifications' ] );
		self::$dismissed_warnings = array_filter( self::$warnings, [ self::class, 'filter_dismissed_notifications' ] );
		self::$active_warnings    = array_diff( self::$warnings, self::$dismissed_warnings );
	}

	/**
	 * Get the variables needed in the views.
	 *
	 * @return array
	 */
	public static function get_template_variables() {

		return [
			'metrics'  => [
				'total'    => self::$notification_count,
				'active'   => self::get_active_notification_count(),
				'errors'   => count( self::$errors ),
				'warnings' => count( self::$warnings ),
			],
			'errors'   => [
				'dismissed' => self::$dismissed_errors,
				'active'    => self::$active_errors,
			],
			'warnings' => [
				'dismissed' => self::$dismissed_warnings,
				'active'    => self::$active_warnings,
			],
		];
	}

	/**
	 * Get the number of active notifications.
	 *
	 * @return int
	 */
	public static function get_active_notification_count() {

		return ( count( self::$active_errors ) + count( self::$active_warnings ) );
	}

	/**
	 * Filter out any non-errors.
	 *
	 * @param Yoast_Notification $notification Notification to test.
	 *
	 * @return bool
	 */
	private static function filter_error_notifications( Yoast_Notification $notification ) {

		return $notification->get_type() === 'error';
	}

	/**
	 * Filter out any non-warnings.
	 *
	 * @param Yoast_Notification $notification Notification to test.
	 *
	 * @return bool
	 */
	private static function filter_warning_notifications( Yoast_Notification $notification ) {

		return $notification->get_type() !== 'error';
	}

	/**
	 * Filter out any dismissed notifications.
	 *
	 * @param Yoast_Notification $notification Notification to test.
	 *
	 * @return bool
	 */
	private static function filter_dismissed_notifications( Yoast_Notification $notification ) {

		return Yoast_Notification_Center::is_notification_dismissed( $notification );
	}
}

class_alias( Yoast_Notifications::class, 'Yoast_Alerts' );
