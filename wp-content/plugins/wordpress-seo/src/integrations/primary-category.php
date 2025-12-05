<?php

namespace Yoast\WP\SEO\Integrations;

use stdClass;
use WP_Error;
use WP_Post;
use WPSEO_Primary_Term;
use Yoast\WP\SEO\Conditionals\Primary_Category_Conditional;

/**
 * Adds customizations to the front end for the primary category.
 */
class Primary_Category implements Integration_Interface {

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * In this case only when on the frontend, the post overview, post edit or new post admin page.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [ Primary_Category_Conditional::class ];
	}

	/**
	 * Registers a filter to change a post's primary category.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'post_link_category', [ $this, 'post_link_category' ], 10, 3 );
	}

	/**
	 * Filters post_link_category to change the category to the chosen category by the user.
	 *
	 * @param stdClass     $category   The category that is now used for the post link.
	 * @param array|null   $categories This parameter is not used.
	 * @param WP_Post|null $post       The post in question.
	 *
	 * @return array|object|WP_Error|null The category we want to use for the post link.
	 */
	public function post_link_category( $category, $categories = null, $post = null ) {
		$post = \get_post( $post );
		if ( $post === null ) {
			return $category;
		}

		$primary_category = $this->get_primary_category( $post );
		if ( $primary_category !== false && $primary_category !== $category->cat_ID ) {
			$category = \get_category( $primary_category );
		}

		return $category;
	}

	/**
	 * Get the id of the primary category.
	 *
	 * @codeCoverageIgnore It justs wraps a dependency.
	 *
	 * @param WP_Post $post The post in question.
	 *
	 * @return int Primary category id.
	 */
	protected function get_primary_category( $post ) {
		$primary_term = new WPSEO_Primary_Term( 'category', $post->ID );

		return $primary_term->get_primary_term();
	}
}
