<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\User_Meta\Framework\Additional_Contactmethods;

use Yoast\WP\SEO\User_Meta\Domain\Additional_Contactmethod_Interface;

/**
 * The Soundcloud contactmethod.
 */
class Soundcloud implements Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the SoundCloud contactmethod.
	 *
	 * @return string The key of the SoundCloud contactmethod.
	 */
	public function get_key(): string {
		return 'soundcloud';
	}

	/**
	 * Returns the label of the SoundCloud field.
	 *
	 * @return string The label of the SoundCloud field.
	 */
	public function get_label(): string {
		return \__( 'SoundCloud profile URL', 'wordpress-seo' );
	}
}
