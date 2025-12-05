<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Tumblr contactmethod.
 */
class Tumblr implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the Tumblr contactmethod.
	 *
	 * @return string The key of the Tumblr contactmethod.
	 */
	public function get_key(): string {
		return 'tumblr';
	}

	/**
	 * Returns the label of the Tumblr field.
	 *
	 * @return string The label of the Tumblr field.
	 */
	public function get_label(): string {
		return \__( 'Tumblr profile URL', 'wordpress-seo' );
	}
}
