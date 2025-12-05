<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Editors\Domain\Integrations;

/**
 * Describes the interface for integration domain objects which can be enabled or not
 */
interface Integration_Data_Provider_Interface {

	/**
	 * If the integration is activated.
	 *
	 * @return bool If the integration is activated.
	 */
	public function is_enabled(): bool;

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array;

	/**
	 * Returns this object represented by a key value structure that is compliant with the script data array.
	 *
	 * @return array<string, bool> Returns the legacy key and if the feature is enabled.
	 */
	public function to_legacy_array(): array;
}
