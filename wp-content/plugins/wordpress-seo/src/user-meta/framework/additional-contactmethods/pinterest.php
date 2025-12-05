<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Pinterest contactmethod.
 */
class Pinterest implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the Pinterest contactmethod.
	 *
	 * @return string The key of the Pinterest contactmethod.
	 */
	public function get_key(): string {
		return 'pinterest';
	}

	/**
	 * Returns the label of the Pinterest field.
	 *
	 * @return string The label of the Pinterest field.
	 */
	public function get_label(): string {
		return \__( 'Pinterest profile URL', 'wordpress-seo' );
	}
}
