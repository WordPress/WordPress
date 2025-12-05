<?php

namespace Yoast\WP\SEO\User_Meta\Domain;

/**
 * This interface describes an additional contactmethod.
 */
interface Additional_Contactmethod_Interface {

	/**
	 * Returns the key of the contactmethod.
	 *
	 * @return string
	 */
	public function get_key(): string;

	/**
	 * Returns the label of the contactmethod field.
	 *
	 * @return string
	 */
	public function get_label(): string;
}
