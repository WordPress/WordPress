<?php

namespace Yoast\WP\SEO\Conditionals\Admin;

use Yoast\WP\SEO\Conditionals\Conditional;

/**
 * Checks if the post is saved by inline-save. This is the case when doing quick edit.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded -- Base class can't be written shorter without abbreviating.
 */
class Doing_Post_Quick_Edit_Save_Conditional implements Conditional {

	/**
	 * Checks if the current request is ajax and the action is inline-save.
	 *
	 * @return bool True when the quick edit action is executed.
	 */
	public function is_met() {
		if ( ! \wp_doing_ajax() ) {
			return false;
		}

		// Do the same nonce check as is done in wp_ajax_inline_save because we hook into that request.
		if ( ! \check_ajax_referer( 'inlineeditnonce', '_inline_edit', false ) ) {
			return false;
		}

		if ( ! isset( $_POST['action'] ) ) {
			return false;
		}

		$sanitized_action = \sanitize_text_field( \wp_unslash( $_POST['action'] ) );

		return ( $sanitized_action === 'inline-save' );
	}
}
