<?php

namespace Yoast\WP\SEO\Integrations;

use Yoast\WP\SEO\Conditionals\Migrations_Conditional;

/**
 * Excludes certain oEmbed Cache-specific post types from the indexable table.
 *
 * Posts with these post types will not be saved to the indexable table.
 */
class Exclude_Oembed_Cache_Post_Type extends Abstract_Exclude_Post_Type {

	/**
	 * This integration is only active when the database migrations have been run.
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Returns the names of the post types to be excluded.
	 * To be used in the wpseo_indexable_excluded_post_types filter.
	 *
	 * @return array The names of the post types.
	 */
	public function get_post_type() {
		return [ 'oembed_cache' ];
	}
}
