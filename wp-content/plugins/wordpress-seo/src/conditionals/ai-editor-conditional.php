<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Conditionals\Admin\Post_Conditional;
use Yoast\WP\SEO\Helpers\Current_Page_Helper;
use Yoast\WP\SEO\Helpers\Product_Helper;

/**
 * Conditional that is met when the AI editor integration should be active.
 */
class AI_Editor_Conditional implements Conditional {

	/**
	 * Holds the Post_Conditional.
	 *
	 * @var Post_Conditional
	 */
	private $post_conditional;

	/**
	 * Holds the Current_Page_Helper.
	 *
	 * @var Current_Page_Helper
	 */
	private $current_page_helper;

	/**
	 * Holds the Product_Helper.
	 *
	 * @var Product_Helper
	 */
	private $product_helper;

	/**
	 * Constructs Ai_Editor_Conditional.
	 *
	 * @param Post_Conditional    $post_conditional    The Post_Conditional.
	 * @param Current_Page_Helper $current_page_helper The Current_Page_Helper.
	 * @param Product_Helper      $product_helper      The Product_Helper.
	 */
	public function __construct( Post_Conditional $post_conditional, Current_Page_Helper $current_page_helper, Product_Helper $product_helper ) {
		$this->post_conditional    = $post_conditional;
		$this->current_page_helper = $current_page_helper;
		$this->product_helper      = $product_helper;
	}

	/**
	 * Returns `true` when the AI editor integration should be active.
	 *
	 * @return bool `true` when the AI editor integration should be active.
	 */
	public function is_met() {
		if ( $this->is_attachment() ) {
			return false;
		}

		if ( $this->is_ai_generator_premium() ) {
			return false;
		}

		return $this->post_conditional->is_met() || $this->is_term() || $this->is_elementor_editor();
	}

	/**
	 * Returns `true` when the page is a term page.
	 *
	 * @return bool `true` when the page is a term page.
	 */
	private function is_term() {
		return $this->current_page_helper->get_current_admin_page() === 'term.php';
	}

	/**
	 * Returns `true` when the page is the Elementor editor.
	 *
	 * @return bool `true` when the page is the Elementor editor.
	 */
	private function is_elementor_editor() {
		if ( $this->current_page_helper->get_current_admin_page() !== 'post.php' ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( isset( $_GET['action'] ) && \is_string( $_GET['action'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, We are only strictly comparing.
			if ( \wp_unslash( $_GET['action'] ) === 'elementor' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Is an attchment post type.
	 *
	 * @return bool
	 */
	public function is_attachment() {
		return $this->current_page_helper->get_current_post_type() === 'attachment';
	}

	/**
	 * Is premium version containes AI generator. We exclude product post type because it is not supported in premium version before 25.6.
	 *
	 * @return bool
	 */
	public function is_ai_generator_premium() {
		if ( ! $this->product_helper->is_premium() ) {
			return false;
		}
		$premium_version = $this->product_helper->get_premium_version();
		return \version_compare( $premium_version, '25.6-RC0', '<' ) && $this->current_page_helper->get_current_post_type() !== 'product';
	}
}
