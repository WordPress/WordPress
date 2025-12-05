<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Facebook contactmethod.
 */
class Facebook implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the Facebook contactmethod.
	 *
	 * @return string The key of the Facebook contactmethod.
	 */
	public function get_key(): string {
		return 'facebook';
	}

	/**
	 * Returns the label of the Facebook field.
	 *
	 * @return string The label of the Facebook field.
	 */
	public function get_label(): string {
		return \__( 'Facebook profile URL', 'wordpress-seo' );
	}
}
