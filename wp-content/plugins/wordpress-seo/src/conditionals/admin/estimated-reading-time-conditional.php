<?php

namespace Yoast\WP\SEO\Conditionals\Admin;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only when we want the Estimated Reading Time.
 */
class Estimated_Reading_Time_Conditional implements Conditional {

	/**
	 * The Post Conditional.
	 *
	 * @var Post_Conditional
	 */
	protected $post_conditional;

	/**
	 * Constructs the Estimated Reading Time Conditional.
	 *
	 * @param Post_Conditional $post_conditional The post conditional.
	 */
	public function __construct( Post_Conditional $post_conditional ) {
		$this->post_conditional = $post_conditional;
	}

	/**
	 * Returns whether this conditional is met.
	 *
	 * @return bool Whether the conditional is met.
	 */
	public function is_met() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing -- Reason: Nonce verification should not be done in a conditional but rather in the classes using the conditional.
		// Check if we are in our Elementor ajax request (for saving).
		if ( \wp_doing_ajax() && isset( $_POST['action'] ) && \is_string( $_POST['action'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only strictly comparing the variable.
			$post_action = \wp_unslash( $_POST['action'] );
			if ( $post_action === 'wpseo_elementor_save' ) {
				return true;
			}
		}

		if ( ! $this->post_conditional->is_met() ) {
			return false;
		}

		// We don't support Estimated Reading Time on the attachment post type.
		if ( isset( $_GET['post'] ) && \is_string( $_GET['post'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are casting to an integer.
			$post_id = (int) \wp_unslash( $_GET['post'] );
			if ( $post_id !== 0 && \get_post_type( $post_id ) === 'attachment' ) {
				return false;
			}
		}

		return true;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing
	}
}
