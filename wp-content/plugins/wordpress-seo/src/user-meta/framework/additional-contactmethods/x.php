<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The X contactmethod.
 */
class X implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the X contactmethod.
	 *
	 * @return string The key of the X contactmethod.
	 */
	public function get_key(): string {
		return 'twitter';
	}

	/**
	 * Returns the label of the X field.
	 *
	 * @return string The label of the X field.
	 */
	public function get_label(): string {
		return \__( 'X username (without @)', 'wordpress-seo' );
	}
}
