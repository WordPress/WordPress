<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Alerts\Application\Ping_Other_Admins;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Ping_Other_Admins_Alert class.
 */
class Ping_Other_Admins_Alert implements Integration_Interface {

	public const NOTIFICATION_ID = 'wpseo-ping-other-admins';

	/**
	 * The notifications center.
	 *
	 * @var Yoast_Notification_Center
	 */
	private $notification_center;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	private $short_link_helper;

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * Ping_Other_Admins_Alert constructor.
	 *
	 * @param Yoast_Notification_Center $notification_center The notification center.
	 * @param Short_Link_Helper         $short_link_helper   The short link helper.
	 * @param Product_Helper            $product_helper      The product helper.
	 * @param Options_Helper            $options_helper      The options helper.
	 * @param User_Helper               $user_helper         The user helper.
	 */
	public function __construct(
		Yoast_Notification_Center $notification_center,
		Short_Link_Helper $short_link_helper,
		Product_Helper $product_helper,
		Options_Helper $options_helper,
		User_Helper $user_helper
	) {
		$this->notification_center = $notification_center;
		$this->short_link_helper   = $short_link_helper;
		$this->product_helper      = $product_helper;
		$this->options_helper      = $options_helper;
		$this->user_helper         = $user_helper;
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string>
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'add_notifications' ] );
	}

	/**
	 * Adds notification when user has not installed Yoast SEO themselves and has not resolved the notification yet.
	 *
	 * @return void
	 */
	public function add_notifications() {
		if ( $this->has_user_installed_yoast() ) {
			$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
			return;
		}

		if ( $this->has_notification_been_resolved() ) {
			$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
			return;
		}

		$notification = $this->get_ping_other_admins_notification();

		$this->notification_center->add_notification( $notification );
	}

	/**
	 * Returns whether user has installed Yoast SEO themselves.
	 *
	 * @return bool Whether the user has installed Yoast SEO themselves.
	 */
	private function has_user_installed_yoast(): bool {
		$first_activated_by = $this->options_helper->get( 'first_activated_by', 0 );

		if ( $first_activated_by === 0 ) {
			return true; // We cannot be sure, so we assume they did.
		}

		if ( \get_current_user_id() === $first_activated_by ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns whether the alert has been resolved before.
	 *
	 * @return bool Whether the alert has been resolved before.
	 */
	private function has_notification_been_resolved(): bool {
		return $this->user_helper->get_meta( \get_current_user_id(), self::NOTIFICATION_ID . '_resolved', true ) === '1';
	}

	/**
	 * Build the ping-other-admins notification.
	 *
	 * @return Yoast_Notification The ping-other-admins notification.
	 */
	private function get_ping_other_admins_notification(): Yoast_Notification {
		$message = $this->get_message();

		return new Yoast_Notification(
			$message,
			[
				'id'            => self::NOTIFICATION_ID,
				'type'          => Yoast_Notification::WARNING,
				'capabilities'  => [ 'wpseo_manage_options' ],
				'priority'      => 20,
				'resolve_nonce' => \wp_create_nonce( 'wpseo-resolve-alert-nonce' ),
			]
		);
	}

	/**
	 * Returns the notification as an HTML string.
	 *
	 * @return string The HTML string representation of the notification.
	 */
	private function get_message() {
		$message = \sprintf(
			/* translators: %1$s expands to "Yoast SEO". */
			\esc_html__( 'Looks like you’re new here. %1$s makes it easy to optimize your website for search engines. Want to keep your site healthy and easier to find? Sign up below to receive practical emails to get you started!', 'wordpress-seo' ),
			'Yoast SEO'
		);

		$notification_text = '<p>' . $message . '</p>';

		return $notification_text;
	}
}
