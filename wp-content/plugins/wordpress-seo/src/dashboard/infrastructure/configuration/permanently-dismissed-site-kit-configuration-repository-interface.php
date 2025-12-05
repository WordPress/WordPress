<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Configuration;

/**
 * Interface for the Permanently Dismissed Site Kit configuration Repository.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
interface Permanently_Dismissed_Site_Kit_Configuration_Repository_Interface {

	/**
	 * Sets the Site Kit configuration dismissal status.
	 *
	 * @param bool $is_dismissed The dismissal status.
	 *
	 * @return bool False when the update failed, true when the update succeeded.
	 */
	public function set_site_kit_configuration_dismissal( bool $is_dismissed ): bool;

	/**
	 * Checks if the Site Kit configuration is dismissed permanently.
	 * *
	 *
	 * @return bool True when the configuration is dismissed, false when it is not.
	 */
	public function is_site_kit_configuration_dismissed(): bool;
}
