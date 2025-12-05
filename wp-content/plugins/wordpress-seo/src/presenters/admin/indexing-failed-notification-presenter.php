<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Indexing_Failed_Notification_Presenter.
 *
 * @package Yoast\WP\SEO\Presenters\Notifications
 */
class Indexing_Failed_Notification_Presenter extends Abstract_Presenter {

	/**
	 * The product helper.
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $class_addon_manager;

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * Indexing_Failed_Notification_Presenter constructor.
	 *
	 * @param Product_Helper      $product_helper      The product helper.
	 * @param Short_Link_Helper   $short_link_helper   The addon manager.
	 * @param WPSEO_Addon_Manager $class_addon_manager The addon manager.
	 */
	public function __construct( $product_helper, $short_link_helper, $class_addon_manager ) {
		$this->class_addon_manager = $class_addon_manager;
		$this->short_link_helper   = $short_link_helper;
		$this->product_helper      = $product_helper;
	}

	/**
	 * Returns the notification as an HTML string.
	 *
	 * @return string The notification in an HTML string representation.
	 */
	public function present() {
		$notification_text = \sprintf(
			/* Translators: %1$s expands to an opening anchor tag for a link leading to the Yoast SEO tools page, %2$s expands to a closing anchor tag. */
			\esc_html__(
				'Something has gone wrong and we couldn\'t complete the optimization of your SEO data. Please %1$sre-start the process%2$s.',
				'wordpress-seo'
			),
			'<a href="' . \get_admin_url( null, 'admin.php?page=wpseo_tools' ) . '">',
			'</a>'
		);

		if ( $this->product_helper->is_premium() ) {
			if ( $this->has_valid_premium_subscription() ) {
				// Add a support message for premium customers.
				$notification_text .= ' ';
				$notification_text .= \esc_html__( 'If the problem persists, please contact support.', 'wordpress-seo' );
			}
			else {
				// Premium plugin with inactive addon; overwrite the entire error message.
				$notification_text = \sprintf(
					/* Translators: %1$s expands to an opening anchor tag for a link leading to the Premium installation page, %2$s expands to a closing anchor tag. */
					\esc_html__(
						'Oops, something has gone wrong and we couldn\'t complete the optimization of your SEO data. Please make sure to activate your subscription in MyYoast by completing %1$sthese steps%2$s.',
						'wordpress-seo'
					),
					'<a href="' . \esc_url( $this->short_link_helper->get( 'https://yoa.st/3wv' ) ) . '">',
					'</a>'
				);
			}
		}

		return '<p>' . $notification_text . '</p>';
	}

	/**
	 * Determines if the site has a valid Premium subscription.
	 *
	 * @return bool
	 */
	protected function has_valid_premium_subscription() {
		return $this->class_addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG );
	}
}
