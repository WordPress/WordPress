<?php
/**
 * REST API: WP_REST_Revision_Meta_Fields class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 7.2.0
 */

/**
 * Core class used to read the meta values of post revisions via the REST API.
 *
 * Only the meta keys that are revisioned for the parent post type are exposed,
 * because those are the only values that are stored on a revision. Any other
 * meta key registered for the post type would be read from the revision ID,
 * find nothing, and be reported with its default value instead.
 *
 * @since 7.2.0
 *
 * @see WP_REST_Post_Meta_Fields
 * @see wp_post_revision_meta_keys()
 */
class WP_REST_Revision_Meta_Fields extends WP_REST_Post_Meta_Fields {

	/**
	 * Retrieves the meta field values for a revision.
	 *
	 * When an author autosaves their own draft, the autosaves controller updates
	 * the post itself and returns it instead of an autosave. That post stores all
	 * of its meta, so its values are read as regular post meta.
	 *
	 * @since 7.2.0
	 *
	 * @param int             $object_id Object ID to fetch meta for.
	 * @param WP_REST_Request $request   Full details about the request.
	 * @return array Array containing the meta values keyed by name.
	 */
	public function get_value( $object_id, $request ) {
		if ( 'revision' !== get_post_type( $object_id ) ) {
			$post_meta = new WP_REST_Post_Meta_Fields( $this->post_type );
			return $post_meta->get_value( $object_id, $request );
		}

		return parent::get_value( $object_id, $request );
	}

	/**
	 * Retrieves the registered meta fields that are revisioned for the parent post type.
	 *
	 * @since 7.2.0
	 *
	 * @return array Registered fields, limited to the revisioned meta keys.
	 */
	protected function get_registered_fields() {
		// The keys are filterable, so ignore anything that isn't a meta key.
		$revisioned_keys = array_filter( (array) wp_post_revision_meta_keys( $this->post_type ), 'is_string' );

		return array_intersect_key(
			parent::get_registered_fields(),
			array_flip( $revisioned_keys )
		);
	}
}
