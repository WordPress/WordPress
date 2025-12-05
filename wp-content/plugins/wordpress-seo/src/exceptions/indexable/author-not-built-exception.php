<?php

namespace Yoast\WP\SEO\Exceptions\Indexable;

/**
 * For when an author indexable is not being built.
 */
class Author_Not_Built_Exception extends Not_Built_Exception {

	/**
	 * Named constructor for creating an Author_Not_Built_Exception
	 * when author archives are disabled for users without posts.
	 *
	 * @param string $user_id The user id.
	 *
	 * @return Author_Not_Built_Exception The exception.
	 */
	public static function author_archives_are_not_indexed_for_users_without_posts( $user_id ) {
		return new self(
			'Indexable for author with id ' . $user_id . ' is not being built, since author archives are not indexed for users without posts.'
		);
	}

	/**
	 * Named constructor for creating an Author_Not_Built_Exception
	 * when author archives are disabled.
	 *
	 * @param string $user_id The user id.
	 *
	 * @return Author_Not_Built_Exception The exception.
	 */
	public static function author_archives_are_disabled( $user_id ) {
		return new self(
			'Indexable for author with id ' . $user_id . ' is not being built, since author archives are disabled.'
		);
	}

	/**
	 * Named constructor for creating an Author_Not_Build_Exception
	 * when an author is excluded because of the `'wpseo_should_build_and_save_user_indexable'` filter.
	 *
	 * @param string $user_id The user id.
	 *
	 * @return Author_Not_Built_Exception The exception.
	 */
	public static function author_not_built_because_of_filter( $user_id ) {
		return new self(
			'Indexable for author with id ' . $user_id . ' is not being built, since it is excluded because of the \'wpseo_should_build_and_save_user_indexable\' filter.'
		);
	}
}
