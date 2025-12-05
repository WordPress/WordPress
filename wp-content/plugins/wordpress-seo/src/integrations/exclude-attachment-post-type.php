<?php

namespace Yoast\WP\SEO\Integrations;

use Yoast\WP\SEO\Conditionals\Attachment_Redirections_Enabled_Conditional;

/**
 * Excludes Attachment post types from the indexable table.
 *
 * Posts with these post types will not be saved to the indexable table.
 */
class Exclude_Attachment_Post_Type extends Abstract_Exclude_Post_Type {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Attachment_Redirections_Enabled_Conditional::class ];
	}

	/**
	 * Returns the names of the post types to be excluded.
	 * To be used in the wpseo_indexable_excluded_post_types filter.
	 *
	 * @return array The names of the post types.
	 */
	public function get_post_type() {
		return [ 'attachment' ];
	}
}
