<?php

namespace Yoast\WP\SEO\Helpers\Schema;

/**
 * Class Article_Helper.
 */
class Article_Helper {

	/**
	 * Determines whether a given post type should have Article schema.
	 *
	 * @param string|null $post_type Post type to check.
	 *
	 * @return bool True if it has Article schema, false if not.
	 */
	public function is_article_post_type( $post_type = null ) {
		if ( $post_type === null ) {
			$post_type = \get_post_type();
		}

		return $this->is_author_supported( $post_type );
	}

	/**
	 * Checks whether author is supported for the passed object sub type.
	 *
	 * @param string $object_sub_type The sub type of the object to check author support for.
	 *
	 * @return bool True if author is supported for the passed object sub type.
	 */
	public function is_author_supported( $object_sub_type ) {
		return \post_type_supports( $object_sub_type, 'author' );
	}
}
