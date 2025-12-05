<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use WpMatomo\Settings;

/**
 * Handles the display of notifications about new features that we definitely want users
 * to notice and read.
 *
 * Notification content is added and removed manually when needed from the get_current_notifications()
 * function. This function returns an array where each element describes a notification to show.
 *
 * Elements of the array can have the following keys:
 * - notification_marker_page: the MWP admin page to show a small red dot to signify to a user there's
 *   something to see.
 * - message: the HTML content of the notification.
 * - show_on: either self::SHOW_ON_ALL_PAGES or self::SHOW_ON_SINGLE_PAGE. SHOW_ON_ALL_PAGES will show
 *            the notification on every MWP admin page. SHOW_ON_SINGLE_PAGE will only show the
 *            full notification on the page specified in the notification_marker_page property.
 * - show_if: either true or false. If true, it is shown, if false it is not. If absent, defaults to true.
 *            This property should be used to determine if a notification should be used based on the
 *            current request or the current user's capabilities.
 *
 * The ID of the notification is specified as the array key.
 */
class WhatsNewNotifications {

	const NOTIFICATION_STATUSES_OPTION_NAME = 'matomo-notification-statuses';

	const STATUS_UNSEEN    = 0;
	const STATUS_SEEN      = 1;
	const STATUS_DISMISSED = 2;

	const SHOW_ON_ALL_PAGES   = 'all';
	const SHOW_ON_SINGLE_PAGE = 'single';

	const NONCE_NAME = 'matomo-whats-new-notifications';

	private $statuses = null;

	/**
	 * @var Settings
	 */
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function is_active() {
		if ( ! is_admin() ) {
			return false;
		}

		$notifications_to_show = $this->get_notifications_to_show();
		return ! empty( $notifications_to_show );
	}

	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'on_admin_enqueue_scripts' ] );

		$current_page = Admin::get_current_page();
		if (
			Admin::is_matomo_admin()
			&& Menu::SLUG_GET_STARTED !== $current_page
		) {
			add_action( 'admin_notices', [ $this, 'on_admin_notices' ] );
		}
	}

	public function register_ajax() {
		add_action( 'wp_ajax_mtm_dismiss_whats_new', [ $this, 'on_dismiss_notification' ] );
	}

	public function on_admin_notices() {
		$matomo_notifications = $this->get_notifications_to_show();

		require __DIR__ . '/views/whats-new-notifications.php';
	}

	public function on_admin_enqueue_scripts() {
		$this->mark_current_page_as_seen();

		wp_localize_script(
			'matomo-admin-js',
			'mtmWhatsNewNotificationAjax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::NONCE_NAME ),
			]
		);

		$unseen_notifications = $this->get_unseen_notification_pages();
		$unseen_notifications = array_values( $unseen_notifications );
		wp_localize_script(
			'matomo-admin-js',
			'mtmUnseenWhatsNewNotifications',
			$unseen_notifications
		);
	}

	public function on_dismiss_notification() {
		check_ajax_referer( self::NONCE_NAME );

		if ( empty( $_POST['matomo_notification'] ) ) {
			wp_send_json( false );
			return;
		}

		$notifications   = $this->get_current_notifications();
		$notification_id = sanitize_text_field( wp_unslash( $_POST['matomo_notification'] ) );
		if ( ! isset( $notifications[ $notification_id ] ) ) {
			wp_send_json( false );
			return;
		}

		$statuses                     = $this->get_notification_statuses();
		$statuses[ $notification_id ] = self::STATUS_DISMISSED;
		$this->save_notification_statuses( $statuses );

		wp_send_json( true );
	}

	private function get_notification_statuses() {
		if ( null !== $this->statuses ) {
			return $this->statuses;
		}

		$option_name = $this->get_notification_status_option_name();
		if ( $this->settings->is_network_enabled() ) {
			$this->statuses = get_site_option( $option_name );
		} else {
			$this->statuses = get_option( $option_name );
		}

		if ( ! is_array( $this->statuses ) ) {
			$this->statuses = [];
		}

		return $this->statuses;
	}

	private function save_notification_statuses( $statuses ) {
		if ( ! is_array( $statuses ) ) {
			$statuses = [];
		}

		$option_name = $this->get_notification_status_option_name();
		if ( $this->settings->is_network_enabled() ) {
			update_site_option( $option_name, $statuses );
		} else {
			update_option( $option_name, $statuses );
		}

		$this->statuses = $statuses;
	}

	private function mark_current_page_as_seen() {
		$current_page = Admin::get_current_page();

		$notifications = $this->get_current_notifications();

		$notifications_to_mark = [];
		foreach ( $notifications as $notification_id => $notification ) {
			if ( $notification['notification_marker_page'] === $current_page ) {
				$notifications_to_mark[ $notification_id ] = self::STATUS_SEEN;
			}
		}

		if ( empty( $notifications_to_mark ) ) {
			return;
		}

		$statuses = $this->get_notification_statuses();
		$statuses = array_merge( $statuses, $notifications_to_mark );
		$this->save_notification_statuses( $statuses );
	}

	private function get_unseen_notification_pages() {
		$matomo_notifications = $this->get_current_notifications();
		$matomo_statuses      = $this->get_notification_statuses();

		$matomo_unseen_notifications = [];
		foreach ( $matomo_notifications as $id => $notification ) {
			if ( ! isset( $matomo_statuses[ $id ] )
				|| self::STATUS_UNSEEN === $matomo_statuses[ $id ]
			) {
				$matomo_unseen_notifications[ $id ] = $notification['notification_marker_page'];
			}
		}
		return $matomo_unseen_notifications;
	}

	private function get_notifications_to_show() {
		$current_page = Admin::get_current_page();

		$matomo_notifications = $this->get_current_notifications();
		$matomo_notifications = array_filter(
			$matomo_notifications,
			function ( $notification ) use ( $current_page ) {
				if (
					isset( $notification['show_if'] )
					&& false === $notification['show_if']
				) {
					return false;
				}

				// do not show notification if configured to show only on one page, and the current page
				// isn't the page to display
				if ( self::SHOW_ON_SINGLE_PAGE === $notification['show_on']
					&& $notification['notification_marker_page'] !== $current_page
				) {
					return false;
				}

				return true;
			}
		);

		if ( empty( $matomo_notifications ) ) { // return early to avoid getting the option below
			return [];
		}

		$matomo_statuses = $this->get_notification_statuses();

		$notifications = [];
		foreach ( $matomo_notifications as $id => $notification ) {
			// do not show the notification if it's been dismissed
			if ( isset( $matomo_statuses[ $id ] )
				&& self::STATUS_DISMISSED === $matomo_statuses[ $id ]
			) {
				continue;
			}

			$notifications[ $id ] = $notification;
		}
		return $notifications;
	}

	/**
	 * Example:
	 *
	 * ```
	 * 'crash-analytics-promo' => [
	 *     'notification_marker_page' => 'matomo-marketplace',
	 *     'message'                  => $this->get_crash_analytics_promo_message(),
	 *     'show_on'                  => self::SHOW_ON_ALL_PAGES,
	 *     'show_if'                  => current_user_can( 'install_plugins' ),
	 * ],
	 * ```
	 *
	 * @return array[]
	 */
	protected function get_current_notifications() {
		return [
			// crash analytics
			'crash-analytics-promo' => [
				'notification_marker_page' => 'matomo-marketplace',
				'message'                  => $this->get_crash_analytics_promo_message(),
				'show_on'                  => self::SHOW_ON_ALL_PAGES,
				'show_if'                  => current_user_can( 'install_plugins' ),
			],
		];
	}

	private function get_crash_analytics_promo_message() {
		$matomo_version = $this->settings->get_matomo_major_version();
		$screenshot_url = plugins_url( 'assets/img/crash_analytics_screenshot.png', MATOMO_ANALYTICS_FILE );
		$plugin_url     = 'https://plugins.matomo.org/CrashAnalytics?wp=1&pk_campaign=WP&pk_source=Plugin&matomoversion=' . $matomo_version;

		ob_start();
		?>
<div style="display: flex; flex-direction: row; align-items: stretch; justify-content: space-evenly;">
	<div style="flex: 1;display:flex;flex-direction: column;justify-content: space-between;">
		<div style="margin-right: 8px;">
			<h6><?php esc_html_e( 'New Premium Plugin', 'matomo' ); ?>!</h6>
			<h3><?php esc_html_e( 'Crash Analytics', 'matomo' ); ?></h3>
			<p><em><?php esc_html_e( 'Uncover Errors and Elevate Your Site’s Performance', 'matomo' ); ?></em></p>
			<p>
			<?php esc_html_e( 'Broken carts, glitchy checkouts, unresponsive contact forms – they\'re not just annoyances; they\'re revenue pitfalls waiting to happen.', 'matomo' ); ?>
			</p>
			<p>
			<?php esc_html_e( 'With Crash Analytics, you can improve user experience, boost conversion rates and grow revenue with 100% website reliability.', 'matomo' ); ?>
			</p>
		</div>
		<div>
			<p>
				<a href="<?php echo esc_attr( $plugin_url ); ?>" rel="noreferrer noopener" target="_blank">
					<button class="button-primary"><?php esc_html_e( 'Learn more', 'matomo' ); ?></button>
				</a>
			</p>
		</div>
	</div>
	<div style="flex: 1; position: relative; height: 240px;">
		<div style="position: absolute; background-image: url(<?php echo esc_attr( $screenshot_url ); ?>); top: 10px; bottom: 10px; left: 0; right: 0; background-size: 100% auto; background-position: top -140px left;"></div>
	</div>
</div>
		<?php

		$text = ob_get_clean();
		return $text;
	}

	private function get_notification_status_option_name() {
		return self::NOTIFICATION_STATUSES_OPTION_NAME . '-' . get_current_user_id();
	}
}
