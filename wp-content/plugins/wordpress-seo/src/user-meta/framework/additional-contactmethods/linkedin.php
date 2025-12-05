<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Linkedin contactmethod.
 */
class Linkedin implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the Linkedin contactmethod.
	 *
	 * @return string The key of the Linkedin contactmethod.
	 */
	public function get_key(): string {
		return 'linkedin';
	}

	/**
	 * Returns the label of the Linkedin field.
	 *
	 * @return string The label of the Linkedin field.
	 */
	public function get_label(): string {
		return \__( 'LinkedIn profile URL', 'wordpress-seo' );
	}
}
