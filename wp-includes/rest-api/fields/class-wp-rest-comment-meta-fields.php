<?php

class WP_REST_Comment_Meta_Fields extends WP_REST_Meta_Fields {
	/**
	 * Get the object type for meta.
	 *
	 * @return string
	 */
	protected function get_meta_type() {
		return 'comment';
	}

	/**
	 * Get the type for `register_rest_field`.
	 *
	 * @return string
	 */
	public function get_rest_field_type() {
		return 'comment';
	}
}
