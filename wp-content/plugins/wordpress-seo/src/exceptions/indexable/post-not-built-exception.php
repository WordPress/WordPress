<?php

namespace Yoast\WP\SEO\Exceptions\Indexable;

/**
 * Exception that is thrown whenever a post could not be built
 * in the context of the indexables.
 */
class Post_Not_Built_Exception extends Not_Built_Exception {

	/**
	 * Throws an exception if the post is not indexable.
	 *
	 * @param int $post_id ID of the post.
	 *
	 * @return Post_Not_Built_Exception
	 */
	public static function because_not_indexable( $post_id ) {
		/* translators: %s: expands to the post id */
		return new self( \sprintf( \__( 'The post %s could not be indexed because it does not meet indexing requirements.', 'wordpress-seo' ), $post_id ) );
	}

	/**
	 * Throws an exception if the post type is excluded from indexing.
	 *
	 * @param int $post_id ID of the post.
	 *
	 * @return Post_Not_Built_Exception
	 */
	public static function because_post_type_excluded( $post_id ) {
		/* translators: %s: expands to the post id */
		return new self( \sprintf( \__( 'The post %s could not be indexed because it\'s post type is excluded from indexing.', 'wordpress-seo' ), $post_id ) );
	}
}
