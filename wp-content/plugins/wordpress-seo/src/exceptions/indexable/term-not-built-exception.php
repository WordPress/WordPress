<?php

namespace Yoast\WP\SEO\Exceptions\Indexable;

/**
 * Exception that is thrown whenever a term could not be built
 * in the context of the indexables.
 */
class Term_Not_Built_Exception extends Not_Built_Exception {

	/**
	 * Throws an exception if the term is not indexable.
	 *
	 * @param int $term_id ID of the term.
	 *
	 * @return Term_Not_Built_Exception
	 */
	public static function because_not_indexable( $term_id ) {
		/* translators: %s: expands to the term id */
		return new self( \sprintf( \__( 'The term %s could not be built because it\'s not indexable.', 'wordpress-seo' ), $term_id ) );
	}
}
