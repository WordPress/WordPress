<?php
/**
 * Server-side rendering of the `core/form-submission-notification` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/form-submission-notification` block on server.
 *
 * @param array  $attributes The block attributes.
 * @param string $content The saved content.
 *
 * @return string The content of the block being rendered.
 */
function gutenberg_render_block_core_form_submission_notification( $attributes, $content ) {
	$show = isset( $_GET['wp-form-result'] ) && sanitize_text_field( wp_unslash( $_GET['wp-form-result'] ) ) === $attributes['type'];
	/**
	 * Filters whether to show the form submission notification block.
	 *
	 * @param bool   $show       Whether to show the form submission notification block.
	 * @param array  $attributes The block attributes.
	 * @param string $content    The saved content.
	 *
	 * @return bool Whether to show the form submission notification block.
	 */
	$show = apply_filters( 'show_form_submission_notification_block', $show, $attributes, $content );
	if ( ! $show ) {
		return '';
	}
	return $content;
}

/**
 * Registers the `core/form-submission-notification` block on server.
 */
function gutenberg_register_block_core_form_submission_notification() {
	if ( ! gutenberg_is_experiment_enabled( 'gutenberg-form-blocks' ) ) {
		return;
	}
	register_block_type_from_metadata(
		__DIR__ . '/form-submission-notification',
		array(
			'render_callback' => 'gutenberg_render_block_core_form_submission_notification',
		)
	);
}
add_action( 'init', 'gutenberg_register_block_core_form_submission_notification', 20 );
