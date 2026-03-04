<?php
/**
 * Bootstraps collaborative editing.
 *
 * @package WordPress
 * @since 7.0.0
 */

/**
 * Injects the real-time collaboration setting into a global variable.
 *
 * @since 7.0.0
 *
 * @access private
 */
function wp_collaboration_inject_setting() {
	if ( ! boolval( get_option( 'wp_disable_real_time_collaboration' ) ) ) {
		wp_add_inline_script(
			'wp-core-data',
			'window._wpCollaborationEnabled = true;',
			'after'
		);
	}
}
