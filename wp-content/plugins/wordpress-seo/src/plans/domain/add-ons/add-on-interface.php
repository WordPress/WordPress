<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Plans\Domain\Add_Ons;

/**
 * This interface describes an add-on for a plan.
 */
interface Add_On_Interface {

	/**
	 * Returns the ID.
	 *
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Returns whether the add-on is installed and activated.
	 *
	 * @return bool
	 */
	public function is_active(): bool;

	/**
	 * Returns whether the add-on has an valid license.
	 *
	 * @return bool
	 */
	public function has_license(): bool;

	/**
	 * Returns the click-to-buy action.
	 *
	 * @return string
	 */
	public function get_ctb_action(): string;

	/**
	 * Returns the click-to-buy ID.
	 *
	 * @return string
	 */
	public function get_ctb_id(): string;
}
