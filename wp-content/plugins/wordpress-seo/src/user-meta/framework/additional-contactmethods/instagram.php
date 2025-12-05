<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Instagram contactmethod.
 */
class Instagram implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the Instagram contactmethod.
	 *
	 * @return string The key of the Instagram contactmethod.
	 */
	public function get_key(): string {
		return 'instagram';
	}

	/**
	 * Returns the label of the Instagram field.
	 *
	 * @return string The label of the Instagram field.
	 */
	public function get_label(): string {
		return \__( 'Instagram profile URL', 'wordpress-seo' );
	}
}
