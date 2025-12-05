<?php

namespace Yoast\WP\SEO\Integrations;

/**
 * Abstract class for excluding certain post types from being indexed.
 */
abstract class Abstract_Exclude_Post_Type implements Integration_Interface {

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_indexable_excluded_post_types', [ $this, 'exclude_post_types' ] );
	}

	/**
	 * Exclude the post type from the indexable table.
	 *
	 * @param array $excluded_post_types The excluded post types.
	 *
	 * @return array The excluded post types, including the specific post type.
	 */
	public function exclude_post_types( $excluded_post_types ) {
		return \array_merge( $excluded_post_types, $this->get_post_type() );
	}

	/**
	 * This integration is only active when the child class's conditionals are met.
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [];
	}

	/**
	 * Returns the names of the post types to be excluded.
	 * To be used in the wpseo_indexable_excluded_post_types filter.
	 *
	 * @return array The names of the post types.
	 */
	abstract public function get_post_type();
}
