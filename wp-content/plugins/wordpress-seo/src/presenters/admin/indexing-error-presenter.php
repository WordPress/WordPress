<?php

namespace Yoast\WP\SEO\Presenters\Admin;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Helpers\Product_Helper;
use Yoast\WP\SEO\Helpers\Short_Link_Helper;
use Yoast\WP\SEO\Presenters\Abstract_Presenter;

/**
 * An error that should be shown when indexation has failed.
 */
class Indexing_Error_Presenter extends Abstract_Presenter {

	/**
	 * The short link helper.
	 *
	 * @var Short_Link_Helper
	 */
	protected $short_link_helper;

	/**
	 * The product helper
	 *
	 * @var Product_Helper
	 */
	protected $product_helper;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * Indexing_Error_Presenter constructor.
	 *
	 * @param Short_Link_Helper   $short_link_helper Represents the short link helper.
	 * @param Product_Helper      $product_helper    The product helper.
	 * @param WPSEO_Addon_Manager $addon_manager     The addon manager.
	 */
	public function __construct(
		Short_Link_Helper $short_link_helper,
		Product_Helper $product_helper,
		WPSEO_Addon_Manager $addon_manager
	) {
		$this->short_link_helper = $short_link_helper;
		$this->product_helper    = $product_helper;
		$this->addon_manager     = $addon_manager;
	}

	/**
	 * Generates the first paragraph of the error message to show when indexing failed.
	 *
	 * The contents of the paragraph varies based on whether WordPress SEO Premium has a valid, activated subscription or not.
	 *
	 * @param bool $is_premium                     Whether WordPress SEO Premium is currently active.
	 * @param bool $has_valid_premium_subscription Whether WordPress SEO Premium currently has a valid subscription.
	 *
	 * @return string
	 */
	protected function generate_first_paragraph( $is_premium, $has_valid_premium_subscription ) {
		$message = \__(
			'Oops, something has gone wrong and we couldn\'t complete the optimization of your SEO data. Please click the button again to re-start the process. ',
			'wordpress-seo'
		);

		if ( $is_premium ) {
			if ( $has_valid_premium_subscription ) {
				$message .= \__( 'If the problem persists, please contact support.', 'wordpress-seo' );
			}
			else {
				$message = \sprintf(
					/* translators: %1$s expands to an opening anchor tag for a link leading to the Premium installation page, %2$s expands to a closing anchor tag. */
					\__(
						'Oops, something has gone wrong and we couldn\'t complete the optimization of your SEO data. Please make sure to activate your subscription in MyYoast by completing %1$sthese steps%2$s.',
						'wordpress-seo'
					),
					'<a href="' . \esc_url( $this->short_link_helper->get( 'https://yoa.st/3wv' ) ) . '">',
					'</a>'
				);
			}
		}

		return $message;
	}

	/**
	 * Generates the second paragraph of the error message to show when indexing failed.
	 *
	 * The error message varies based on whether WordPress SEO Premium has a valid, activated subscription or not.
	 *
	 * @param bool $is_premium                     Whether WordPress SEO Premium is currently active.
	 * @param bool $has_valid_premium_subscription Whether WordPress SEO Premium currently has a valid subscription.
	 *
	 * @return string The second paragraph of the error message.
	 */
	protected function generate_second_paragraph( $is_premium, $has_valid_premium_subscription ) {
		return \sprintf(
			/* translators: %1$s expands to an opening anchor tag for a link leading to the Premium installation page, %2$s expands to a closing anchor tag. */
			\__(
				'Below are the technical details for the error. See %1$sthis page%2$s for a more detailed explanation.',
				'wordpress-seo'
			),
			'<a href="' . \esc_url( $this->short_link_helper->get( 'https://yoa.st/4f3' ) ) . '">',
			'</a>'
		);
	}

	/**
	 * Presents the error message to show if SEO optimization failed.
	 *
	 * The error message varies based on whether WordPress SEO Premium has a valid, activated subscription or not.
	 *
	 * @return string The error message to show.
	 */
	public function present() {
		$is_premium                     = $this->product_helper->is_premium();
		$has_valid_premium_subscription = $this->addon_manager->has_valid_subscription( WPSEO_Addon_Manager::PREMIUM_SLUG );

		$output  = '<p>' . $this->generate_first_paragraph( $is_premium, $has_valid_premium_subscription ) . '</p>';
		$output .= '<p>' . $this->generate_second_paragraph( $is_premium, $has_valid_premium_subscription ) . '</p>';

		return $output;
	}
}
