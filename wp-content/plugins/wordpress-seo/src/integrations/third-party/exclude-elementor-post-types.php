<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Third_Party\Elementor_Activated_Conditional;
use Yoast\WP\SEO\Integrations\Abstract_Exclude_Post_Type;

/**
 * Excludes certain Elementor-specific post types from the indexable table.
 *
 * Posts with these post types will not be saved to the indexable table.
 */
class Exclude_Elementor_Post_Types extends Abstract_Exclude_Post_Type {

	/**
	 * This integration is only active when the Elementor plugin
	 * is installed and activated.
	 *
	 * @return string[] The conditionals.
	 */
	public static function get_conditionals() {
		return [ Elementor_Activated_Conditional::class ];
	}

	/**
	 * Returns the names of the post types to be excluded.
	 * To be used in the wpseo_indexable_excluded_post_types filter.
	 *
	 * @return array The names of the post types.
	 */
	public function get_post_type() {
		return [ 'elementor_library' ];
	}
}
