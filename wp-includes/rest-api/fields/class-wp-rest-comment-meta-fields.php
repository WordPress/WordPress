<?php
/**
 * REST API: WP_REST_Comment_Meta_Fields class
 *
 * @package WordPress
 * @subpackage REST_API
 * @since 4.7.0
 */

/**
 * Core class to manage comment meta via the REST API.
 *
 * @since 4.7.0
 *
 * @see WP_REST_Meta_Fields
 */
class WP_REST_Comment_Meta_Fields extends WP_REST_Meta_Fields {

	/**
	 * Retrieves the comment type for comment meta.
	 *
	 * @since 4.7.0
	 *
	 * @return string The meta type.
	 */
	protected function get_meta_type() {
		return 'comment';
	}

	/**
	 * Retrieves the comment meta subtype.
	 *
	 * @since 4.9.8
	 *
	 * @return string 'comment' There are no subtypes.
	 */
	protected function get_meta_subtype() {
		return 'comment';
	}

	/**
	 * Retrieves the type for register_rest_field() in the context of comments.
	 *
	 * @since 4.7.0
	 *
	 * @return string The REST field type.
	 */
	public function get_rest_field_type() {
		return 'comment';
	}
}
