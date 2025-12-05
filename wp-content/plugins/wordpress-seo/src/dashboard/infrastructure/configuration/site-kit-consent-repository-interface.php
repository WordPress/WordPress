<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Configuration;

/**
 * Interface for theSite Kit Consent Repository.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
 */
interface Site_Kit_Consent_Repository_Interface {

	/**
	 * Sets the Site Kit consent.
	 *
	 * @param bool $consent The consent value.
	 *
	 * @return bool False when the update failed, true when the update succeeded.
	 */
	public function set_site_kit_consent( bool $consent ): bool;

	/**
	 * Returns the Site Kit consent status.
	 * *
	 *
	 * @return bool True when the consent has been granted, false when it is not.
	 */
	public function is_consent_granted(): bool;
}
