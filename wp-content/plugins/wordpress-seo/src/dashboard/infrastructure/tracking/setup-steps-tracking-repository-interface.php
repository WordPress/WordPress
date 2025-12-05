<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Tracking;

/**
 * Interface for the Site Kit Usage Tracking Repository.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
interface Setup_Steps_Tracking_Repository_Interface {

	/**
	 * Sets an option inside the Site Kit usage options array..
	 *
	 * @param string $element_name  The name of the array element to set.
	 * @param string $element_value The value of the array element  to set.
	 *
	 * @return bool False when the update failed, true when the update succeeded.
	 */
	public function set_setup_steps_tracking_element( string $element_name, string $element_value ): bool;

	/**
	 * Gets an option inside the Site Kit usage options array..
	 *
	 * @param string $element_name The name of the array element to get.
	 *
	 * @return string The value if present, empty string if not.
	 */
	public function get_setup_steps_tracking_element( string $element_name ): string;
}
