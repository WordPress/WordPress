<?php

namespace Yoast\WP\SEO\Conditionals\Third_Party;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Conditional that is only met when on an Elementor edit page or when the current
 * request is an ajax request for saving our post meta data.
 */
class Elementor_Edit_Conditional implements Conditional {

	/**
	 * Returns whether this conditional is met.
	 *
	 * @return bool Whether the conditional is met.
	 */
	public function is_met() {
		global $pagenow;

		// Editing a post/page in Elementor.
		if ( $pagenow === 'post.php' && $this->is_elementor_get_action() ) {
			return true;
		}

		// Request for us saving a post/page in Elementor (submits our form via AJAX).
		return \wp_doing_ajax() && $this->is_yoast_save_post_action();
	}

	/**
	 * Checks if the current request' GET action is 'elementor'.
	 *
	 * @return bool True when the GET action is 'elementor'.
	 */
	private function is_elementor_get_action(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( ! isset( $_GET['action'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
		if ( ! \is_string( $_GET['action'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, we are only strictly comparing.
		return \wp_unslash( $_GET['action'] ) === 'elementor';
	}

	/**
	 * Checks if the current request' POST action is 'wpseo_elementor_save'.
	 *
	 * @return bool True when the POST action is 'wpseo_elementor_save'.
	 */
	private function is_yoast_save_post_action(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
		if ( ! isset( $_POST['action'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Reason: We are not processing form information.
		if ( ! \is_string( $_POST['action'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form information, we are only strictly comparing.
		return \wp_unslash( $_POST['action'] ) === 'wpseo_elementor_save';
	}
}
