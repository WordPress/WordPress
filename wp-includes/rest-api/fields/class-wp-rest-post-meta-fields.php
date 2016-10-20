<?php

class WP_REST_Post_Meta_Fields extends WP_REST_Meta_Fields {
	/**
	 * Post type to register fields for.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Constructor.
	 *
	 * @param string $post_type Post type to register fields for.
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Get the object type for meta.
	 *
	 * @return string
	 */
	protected function get_meta_type() {
		return 'post';
	}

	/**
	 * Get the type for `register_rest_field`.
	 *
	 * @return string Custom post type slug.
	 */
	public function get_rest_field_type() {
		return $this->post_type;
	}
}
