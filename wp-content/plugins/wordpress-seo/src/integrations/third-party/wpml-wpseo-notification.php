<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Third_Party\WPML_Conditional;
use Yoast\WP\SEO\Conditionals\Third_Party\WPML_WPSEO_Conditional;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast_Notification;
use Yoast_Notification_Center;

/**
 * Adds a notification to the dashboard if the WPML plugin is installed,
 * but the Yoast SEO Multilingual plugin (a glue plugin to make Yoast SEO and WPML work nicely together)
 * is not.
 */
class WPML_WPSEO_Notification implements Integration_Interface {

	/**
	 * The notification ID.
	 *
	 * @internal
	 */
	public const NOTIFICATION_ID = 'wpml-wpseo-not-installed';

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * The notification center.
	 *
	 * @var Yoast_Notification_Center
	 */
	protected $notification_center;

	/**
	 * The WPML WPSEO conditional.
	 *
	 * @var WPML_WPSEO_Conditional
	 */
	protected $wpml_wpseo_conditional;

	/**
	 * WPML WPSEO notification constructor.
	 *
	 * @param Short_Link_Helper         $short_link_helper      The short link helper.
	 * @param Yoast_Notification_Center $notification_center    The notification center.
	 * @param WPML_WPSEO_Conditional    $wpml_wpseo_conditional The WPML WPSEO conditional.
	 */
	public function __construct(
		Short_Link_Helper $short_link_helper,
		Yoast_Notification_Center $notification_center,
		WPML_WPSEO_Conditional $wpml_wpseo_conditional
	) {
		$this->short_link_helper      = $short_link_helper;
		$this->notification_center    = $notification_center;
		$this->wpml_wpseo_conditional = $wpml_wpseo_conditional;
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_notices', [ $this, 'notify_not_installed' ] );
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * This integration should only be active when WPML is installed and activated.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [ WPML_Conditional::class ];
	}

	/**
	 * Notify the user that the Yoast SEO Multilingual plugin is not installed
	 * (when the WPML plugin is installed).
	 *
	 * Remove the notification again when it is installed.
	 *
	 * @return void
	 */
	public function notify_not_installed() {
		if ( ! $this->wpml_wpseo_conditional->is_met() ) {
			$this->notification_center->add_notification( $this->get_notification() );
			return;
		}
		$this->notification_center->remove_notification_by_id( self::NOTIFICATION_ID );
	}

	/**
	 * Generates the notification to show to the user when WPML is installed,
	 * but the Yoast SEO Multilingual plugin is not.
	 *
	 * @return Yoast_Notification The notification.
	 */
	protected function get_notification() {
		return new Yoast_Notification(
			\sprintf(
				/* translators: %1$s expands to an opening anchor tag, %2$s expands to an closing anchor tag. */
				\__( 'We notice that you have installed WPML. To make sure your canonical URLs are set correctly, %1$sinstall and activate the WPML SEO add-on%2$s as well!', 'wordpress-seo' ),
				'<a href="' . \esc_url( $this->short_link_helper->get( 'https://yoa.st/wpml-yoast-seo' ) ) . '" target="_blank">',
				'</a>'
			),
			[
				'id'   => self::NOTIFICATION_ID,
				'type' => Yoast_Notification::WARNING,
			]
		);
	}
}
