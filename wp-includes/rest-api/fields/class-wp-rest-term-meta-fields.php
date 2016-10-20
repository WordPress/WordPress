<?php

/**
 * Manage meta values for terms.
 */
class WP_REST_Term_Meta_Fields extends WP_REST_Meta_Fields {
	/**
	 * Taxonomy to register fields for.
	 *
	 * @var string
	 */
	protected $taxonomy;
	/**
	 * Constructor.
	 *
	 * @param string $taxonomy Taxonomy to register fields for.
	 */
	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	/**
	 * Get the object type for meta.
	 *
	 * @return string
	 */
	protected function get_meta_type() {
		return 'term';
	}

	/**
	 * Get the type for `register_rest_field`.
	 *
	 * @return string
	 */
	public function get_rest_field_type() {
		return 'post_tag' === $this->taxonomy ? 'tag' : $this->taxonomy;
	}
}
