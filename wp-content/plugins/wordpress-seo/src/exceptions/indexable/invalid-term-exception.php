<?php

namespace Yoast\WP\SEO\Exceptions\Indexable;

/**
 * Exception that can be thrown whenever a term is considered invalid by WordPress
 * within the context of the indexables.
 */
class Invalid_Term_Exception extends Source_Exception {

	/**
	 * Exception that can be thrown whenever a term is considered invalid by WordPress
	 * within the context of the indexables.
	 *
	 * @param string $reason The reason given by WordPress why the term is invalid.
	 */
	public function __construct( $reason ) {
		parent::__construct(
			\sprintf(
				/* translators: %s is the reason given by WordPress. */
				\esc_html__( 'The term is considered invalid. The following reason was given by WordPress: %s', 'wordpress-seo' ),
				$reason
			)
		);
	}
}
