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
	 * @param WP_Post $post Post object that backs the block.
	 * @return bool Whether the block can be read.
	 */
	public function check_read_permission( $post ) {
		// By default the read_post capability is mapped to edit_posts.
		if ( ! current_user_can( 'read_post', $post->ID ) ) {
			return false;
		}

		return parent::check_read_permission( $post );
	}

	/**
	 * Filters a response based on the context defined in the schema.
	 *
	 * @since 5.0.0
	 * @since 6.3.0 Adds the `wp_pattern_sync_status` postmeta property to the top level of response.
	 *
	 * @param array  $data    Response data to filter.
	 * @param string $context Context defined in the schema.
	 * @return array Filtered response.
	 */
	public function filter_response_by_context( $data, $context ) {
		$data = parent::filter_response_by_context( $data, $context );

		/*
		 * Remove `title.rendered` and `content.rendered` from the response. It
		 * doesn't make sense for a reusable block to have rendered content on its
		 * own, since rendering a block requires it to be inside a post or a page.
		 */
		unset( $data['title']['rendered'] );
		unset( $data['content']['rendered'] );

		// Add the core wp_pattern_sync_status meta as top level property to the response.
		$data['wp_pattern_sync_status'] = isset( $data['meta']['wp_pattern_sync_status'] ) ? $data['meta']['wp_pattern_sync_status'] : '';
		unset( $data['meta']['wp_pattern_sync_status'] );
		return $data;
	}

	/**
	 * Retrieves the block's schema, conforming to JSON Schema.
	 *
	 * @since 5.0.0
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		// Do not cache this schema because all properties are derived from parent controller.
		$schema = parent::get_item_schema();

		/*
		 * Allow all contexts to access `title.raw` and `content.raw`. Clients always
		 * need the raw markup of a reusable block to do anything useful, e.g. parse
		 * it or display it in an editor.
		 */
		$schema['properties']['title']['properties']['raw']['context']   = array( 'view', 'edit' );
		$schema['properties']['content']['properties']['raw']['context'] = array( 'view', 'edit' );

		/*
		 * Remove `title.rendered` and `content.rendered` from the schema. It doesn’t
		 * make sense for a reusable block to have rendered content on its own, since
		 * rendering a block requires it to be inside a post or a page.
		 */
		unset( $schema['properties']['title']['properties']['rendered'] );
		unset( $schema['properties']['content']['properties']['rendered'] );

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}

}
