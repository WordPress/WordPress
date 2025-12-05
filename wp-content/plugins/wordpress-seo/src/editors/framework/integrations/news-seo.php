<?php
// @phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- This namespace should reflect the namespace of the original class.
namespace Yoast\WP\SEO\Editors\Framework\Integrations;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Editors\Domain\Integrations\Integration_Data_Provider_Interface;

/**
 * Describes if the News SEO plugin is enabled.
 */
class News_SEO implements Integration_Data_Provider_Interface {

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	private $addon_manager;

	/**
	 * The constructor.
	 *
	 * @param WPSEO_Addon_Manager $addon_manager The addon manager.
	 */
	public function __construct( WPSEO_Addon_Manager $addon_manager ) {
		$this->addon_manager = $addon_manager;
	}

	/**
	 * If the plugin is activated.
	 *
	 * @return bool If the plugin is activated.
	 */
	public function is_enabled(): bool {
		return \is_plugin_active( $this->addon_manager->get_plugin_file( WPSEO_Addon_Manager::NEWS_SLUG ) );
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		return [ 'isNewsSeoActive' => $this->is_enabled() ];
	}

	/**
	 * Returns this object represented by a key value structure that is compliant with the script data array.
	 *
	 * @return array<string, bool> Returns the legacy key and if the feature is enabled.
	 */
	public function to_legacy_array(): array {
		return [ 'isNewsSeoActive' => $this->is_enabled() ];
	}
}
