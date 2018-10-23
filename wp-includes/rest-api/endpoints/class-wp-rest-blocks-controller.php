<?php
/**
 * Reusable blocks REST API: WP_REST_Blocks_Controller class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 5.0.0
 */

/**
 * Controller which provides a REST endpoint for the editor to read, create,
 * edit and delete reusable blocks. Blocks are stored as posts with the wp_block
 * post type.
 *
 * @since 5.0.0
 *
 * @see WP_REST_Posts_Controller
 * @see WP_REST_Controller
 */
class WP_REST_Blocks_Controller extends WP_REST_Posts_Controller {

	/**
	 * Checks if a block can be read.
	 *
	 * @since 5.0.0
	 *
	 * @param object $post Post object that backs the block.
	 * @return bool Whether the block can be read.
	 */
	public function check_read_permission( $post ) {
		// Ensure that the user is logged in and has the read_blocks capability.
		$post_type = get_post_type_object( $post->post_type );
		if ( ! current_user_can( $post_type->cap->read_post, $post->ID ) ) {
			return false;
		}

		return parent::check_read_permission( $post );
	}
}
