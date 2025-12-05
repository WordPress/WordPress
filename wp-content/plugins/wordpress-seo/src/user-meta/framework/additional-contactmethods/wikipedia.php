<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Wikipedia contactmethod.
 */
class Wikipedia implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the Wikipedia contactmethod.
	 *
	 * @return string The key of the Wikipedia contactmethod.
	 */
	public function get_key(): string {
		return 'wikipedia';
	}

	/**
	 * Returns the label of the Wikipedia field.
	 *
	 * @return string The label of the Wikipedia field.
	 */
	public function get_label(): string {
		return \__( 'Wikipedia page about you', 'wordpress-seo' ) . '<br/><small>' . \__( '(if one exists)', 'wordpress-seo' ) . '</small>';
	}
}
