<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Myspace contactmethod.
 */
class Myspace implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the MySpace contactmethod.
	 *
	 * @return string The key of the MySpace contactmethod.
	 */
	public function get_key(): string {
		return 'myspace';
	}

	/**
	 * Returns the label of the MySpace field.
	 *
	 * @return string The label of the MySpace field.
	 */
	public function get_label(): string {
		return \__( 'MySpace profile URL', 'wordpress-seo' );
	}
}
