<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Youtube contactmethod.
 */
class Youtube implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the YouTube contactmethod.
	 *
	 * @return string The key of the YouTube contactmethod.
	 */
	public function get_key(): string {
		return 'youtube';
	}

	/**
	 * Returns the label of the YouTube field.
	 *
	 * @return string The label of the YouTube field.
	 */
	public function get_label(): string {
		return \__( 'YouTube profile URL', 'wordpress-seo' );
	}
}
