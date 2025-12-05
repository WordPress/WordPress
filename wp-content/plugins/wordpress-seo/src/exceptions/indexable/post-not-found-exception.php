<?php

namespace Yoast\WP\SEO\Exceptions\Indexable;

/**
 * Exception that is thrown whenever a post could not be found
 * in the context of the indexables.
 */
class Post_Not_Found_Exception extends Source_Exception {

	/**
	 * Exception that is thrown whenever a post could not be found
	 * in the context of the indexables.
	 */
	public function __construct() {
		parent::__construct( \__( 'The post could not be found.', 'wordpress-seo' ) );
	}
}
