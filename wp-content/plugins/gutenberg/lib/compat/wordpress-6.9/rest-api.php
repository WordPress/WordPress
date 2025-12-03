<?php
/**
 * PHP and WordPress configuration compatibility functions for the Gutenberg
 * editor plugin changes related to REST API.
 *
 * @package gutenberg
 */

/**
 * Adds export theme link relation to the block theme responses.
 *
 * @param WP_REST_Response $response The response object.
 * @param WP_Theme         $theme    Theme object used to create response.
 * @return WP_REST_Response Modified response object.
 */
function gutenberg_rest_theme_export_link_rel( $response, $theme ) {
	if ( ! empty( $response->get_links() ) && $theme->is_block_theme() ) {
		$response->add_link(
			'https://api.w.org/export-theme',
			rest_url( 'wp-block-editor/v1/export' ),
			array(
				'targetHints' => array(
					'allow' => current_user_can( 'export' ) ? array( 'GET' ) : array(),
				),
			)
		);
	}

	return $response;
}
add_filter( 'rest_prepare_theme', 'gutenberg_rest_theme_export_link_rel', 10, 2 );

/**
 * Overrides the REST controller for the attachment post type to add support
 * for filtering by multiple media types.
 *
 * Only applies if the experimental media processing feature is not enabled,
 * as that feature includes this functionality and more.
 *
 * @param array  $args      Array of arguments for registering a post type.
 * @param string $post_type Post type key.
 * @return array Modified array of arguments.
 */
function gutenberg_override_attachments_rest_controller( $args, $post_type ) {
	if ( 'attachment' === $post_type && ! gutenberg_is_experiment_enabled( 'gutenberg-media-processing' ) ) {
		$args['rest_controller_class'] = 'Gutenberg_REST_Attachments_Controller_6_9';
	}
	return $args;
}
add_filter( 'register_post_type_args', 'gutenberg_override_attachments_rest_controller', 10, 2 );
