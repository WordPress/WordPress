<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * Class Woocommerce_Beta_Editor_Presenter.
 */
class Woocommerce_Beta_Editor_Presenter extends Abstract_Presenter {

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * Woocommerce_Beta_Editor_Presenter constructor.
	 *
	 * @param Short_Link_Helper $short_link_helper The short link helper.
	 */
	public function __construct( Short_Link_Helper $short_link_helper ) {
		$this->short_link_helper = $short_link_helper;
	}

	/**
	 * Returns the notification as an HTML string.
	 *
	 * @return string The notification in an HTML string representation.
	 */
	public function present() {
		$notification_text  = '<p>';
		$notification_text .= $this->get_message();
		$notification_text .= '</p>';

		return $notification_text;
	}

	/**
	 * Returns the message to show.
	 *
	 * @return string The message.
	 */
	protected function get_message() {
		return \sprintf(
			'<strong>%1$s</strong> %2$s',
			\esc_html__( 'Compatibility issue: Yoast SEO is incompatible with the beta WooCommerce product editor.', 'wordpress-seo' ),
			\sprintf(
				/* translators: 1: Yoast SEO, 2: Link start tag to the Learn more link, 3: Link closing tag. */
				\esc_html__( 'The %1$s interface is currently unavailable in the beta WooCommerce product editor. To resolve any issues, please disable the beta editor. %2$sLearn how to disable the beta WooCommerce product editor.%3$s', 'wordpress-seo' ),
				'Yoast SEO',
				'<a href="' . \esc_url( $this->short_link_helper->get( 'https://yoa.st/learn-how-disable-beta-woocommerce-product-editor' ) ) . '" target="_blank">',
				'</a>'
			)
		);
	}
}
