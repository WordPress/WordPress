<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Ajax
 */

/**
 * This class will catch the request to dismiss the target notice (set by notice_name)
 * and will store the dismiss status as an user meta in the database.
 */
class Yoast_Dismissable_Notice_Ajax {

	/**
	 * Notice type toggle value for user notices.
	 *
	 * @var string
	 */
	public const FOR_USER = 'user_meta';

	/**
	 * Notice type toggle value for network notices.
	 *
	 * @var string
	 */
	public const FOR_NETWORK = 'site_option';

	/**
	 * Notice type toggle value for site notices.
	 *
	 * @var string
	 */
	public const FOR_SITE = 'option';

	/**
	 * Name of the notice that will be dismissed.
	 *
	 * @var string
	 */
	private $notice_name;

	/**
	 * The type of the current notice.
	 *
	 * @var string
	 */
	private $notice_type;

	/**
	 * Initialize the hooks for the AJAX request.
	 *
	 * @param string $notice_name The name for the hook to catch the notice.
	 * @param string $notice_type The notice type.
	 */
	public function __construct( $notice_name, $notice_type = self::FOR_USER ) {
		$this->notice_name = $notice_name;
		$this->notice_type = $notice_type;

		add_action( 'wp_ajax_wpseo_dismiss_' . $notice_name, [ $this, 'dismiss_notice' ] );
	}

	/**
	 * Handles the dismiss notice request.
	 *
	 * @return void
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'wpseo-dismiss-' . $this->notice_name );

		$this->save_dismissed();

		wp_die( 'true' );
	}

	/**
	 * Storing the dismissed value in the database. The target location is based on the set notification type.
	 *
	 * @return void
	 */
	private function save_dismissed() {
		if ( $this->notice_type === self::FOR_SITE ) {
			update_option( 'wpseo_dismiss_' . $this->notice_name, 1 );

			return;
		}

		if ( $this->notice_type === self::FOR_NETWORK ) {
			update_site_option( 'wpseo_dismiss_' . $this->notice_name, 1 );

			return;
		}

		update_user_meta( get_current_user_id(), 'wpseo_dismiss_' . $this->notice_name, 1 );
	}
}
