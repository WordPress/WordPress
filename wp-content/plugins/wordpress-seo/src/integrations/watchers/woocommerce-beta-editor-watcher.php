<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Not_Admin_Ajax_Conditional;
use Yoast\WP\SEO\Conditionals\WooCommerce_Conditional;
use Yoast\WP\SEO\Helpers\Notification_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Woocommerce_Beta_Editor_Presenter;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Shows a notification for users who have Woocommerce product beta editor enabled.
 */
class Woocommerce_Beta_Editor_Watcher implements Integration_Interface {

	/**
	 * The notification ID.
	 */
	public const NOTIFICATION_ID = 'wpseo-woocommerce-beta-editor-warning';

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * The Yoast notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * The notification helper.
	 *
	 * @var Notification_Helper
	 */
	protected $notification_helper;

	/**
	 * The WooCommerce conditional.
	 *
	 * @var WooCommerce_Conditional
	 */
	protected $woocommerce_conditional;

	/**
	 * The Woocommerce beta editor presenter.
	 *
	 * @var Woocommerce_Beta_Editor_Presenter
	 */
	protected $presenter;

	/**
	 * Woocommerce_Beta_Editor_Watcher constructor.
	 *
	 * @param Yoast_Notification_Center $notification_center     The notification center.
	 * @param Notification_Helper       $notification_helper     The notification helper.
	 * @param Short_Link_Helper         $short_link_helper       The short link helper.
	 * @param WooCommerce_Conditional   $woocommerce_conditional The WooCommerce conditional.
	 */
	public function __construct(
		Yoast_Notification_Center $notification_center,
		Notification_Helper $notification_helper,
		Short_Link_Helper $short_link_helper,
		WooCommerce_Conditional $woocommerce_conditional
	) {
		$this->notification_center     = $notification_center;
		$this->notification_helper     = $notification_helper;
		$this->short_link_helper       = $short_link_helper;
		$this->woocommerce_conditional = $woocommerce_conditional;
		$this->presenter               = new Woocommerce_Beta_Editor_Presenter( $this->short_link_helper );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class, Not_Admin_Ajax_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * On admin_init, it is checked whether the notification about Woocommerce product beta editor enabled should be shown.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_init', [ $this, 'manage_woocommerce_beta_editor_notification' ] );
	}

	/**
	 * Manage the Woocommerce product beta editor notification.
	 *
	 * Shows the notification if needed and deletes it if needed.
	 *
	 * @return void
	 */
	public function manage_woocommerce_beta_editor_notification() {
		if ( \get_option( 'woocommerce_feature_product_block_editor_enabled' ) === 'yes' && $this->woocommerce_conditional->is_met() ) {
			$this->maybe_add_woocommerce_beta_editor_notification();
		}
		else {
			$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
		}
	}

	/**
	 * Add the Woocommerce product beta editor enabled notification if it does not exist yet.
	 *
	 * @return void
	 */
	public function maybe_add_woocommerce_beta_editor_notification() {
		if ( ! $this->notification_center->get_notification_by_id( self::NOTIFICATION_ID ) ) {
			$notification = $this->notification();
			$this->notification_helper->restore_notification( $notification );
			$this->notification_center->add_notification( $notification );
		}
	}

	/**
	 * Returns an instance of the notification.
	 *
	 * @return Yoast_Notification The notification to show.
	 */
	protected function notification() {
		return new Yoast_Notification(
			$this->presenter->present(),
			[
				'type'         => Yoast_Notification::ERROR,
				'id'           => self::NOTIFICATION_ID,
				'capabilities' => 'wpseo_manage_options',
				'priority'     => 1,
			]
		);
	}
}
