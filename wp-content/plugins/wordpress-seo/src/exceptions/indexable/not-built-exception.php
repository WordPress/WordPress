<?php

namespace Yoast\WP\SEO\Exceptions\Indexable;

/**
 * Class Not_Built_Exception
 */
class Not_Built_Exception extends Indexable_Exception {

	/**
	 * Creates an exception that should be thrown when an indexable
	 * was not built because of an invalid object id.
	 *
	 * @param int $object_id The invalid object id.
	 *
	 * @return Not_Built_Exception The exception.
	 */
	public static function invalid_object_id( $object_id ) {
		return new self(
			"Indexable was not built because it had an invalid object id of $object_id."
		);
	}
}
