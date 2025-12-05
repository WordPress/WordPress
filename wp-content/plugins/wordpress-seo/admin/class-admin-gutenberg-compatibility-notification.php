<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Handles the Gutenberg Compatibility notification showing and hiding.
 */
class WPSEO_Admin_Gutenberg_Compatibility_Notification implements WPSEO_WordPress_Integration {

	/**
	 * Notification ID to use.
	 *
	 * @var string
	 */
	private $notification_id = 'wpseo-outdated-gutenberg-plugin';

	/**
	 * Instance of gutenberg compatibility checker.
	 *
	 * @var WPSEO_Gutenberg_Compatibility
	 */
	protected $compatibility_checker;

	/**
	 * Instance of Yoast Notification Center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * WPSEO_Admin_Gutenberg_Compatibility_Notification constructor.
	 */
	public function __construct() {
		$this->compatibility_checker = new WPSEO_Gutenberg_Compatibility();
		$this->notification_center   = Yoast_Notification_Center::get();
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'manage_notification' ] );
	}

	/**
	 * Manages if the notification should be shown or removed.
	 *
	 * @return void
	 */
	public function manage_notification() {
		/**
		 * Filter: 'yoast_display_gutenberg_compat_notification' - Allows developer to disable the Gutenberg compatibility
		 * notification.
		 *
		 * @param bool $display_notification
		 */
		$display_notification = apply_filters( 'yoast_display_gutenberg_compat_notification', true );

		if (
			! $this->compatibility_checker->is_installed()
			|| $this->compatibility_checker->is_fully_compatible()
			|| ! $display_notification
		) {
			$this->notification_center->remove_notification_by_id( $this->notification_id );

			return;
		}

		$this->add_notification();
	}

	/**
	 * Adds the notification to the notificaton center.
	 *
	 * @return void
	 */
	protected function add_notification() {
		$level = $this->compatibility_checker->is_below_minimum() ? Yoast_Notification::ERROR : Yoast_Notification::WARNING;

		$message = sprintf(
			/* translators: %1$s expands to Yoast SEO, %2$s expands to the installed version, %3$s expands to Gutenberg */
			__( '%1$s detected you are using version %2$s of %3$s, please update to the latest version to prevent compatibility issues.', 'wordpress-seo' ),
			'Yoast SEO',
			$this->compatibility_checker->get_installed_version(),
			'Gutenberg'
		);

		$notification = new Yoast_Notification(
			$message,
			[
				'id'       => $this->notification_id,
				'type'     => $level,
				'priority' => 1,
			]
		);

		$this->notification_center->add_notification( $notification );
	}
}
