<?php

namespace Elementor\App\Modules\ImportExportCustomization\Compatibility;

use Elementor\App\Modules\ImportExportCustomization\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles conversion from manifest format v2.0 to v3.0
 * Main change: site-settings changed from array of tab keys to object with boolean values
 */
class Customization extends Base_Adapter {

	/**
	 * Check if compatibility is needed based on manifest version
	 *
	 * @param array $manifest_data
	 * @param array $meta
	 * @return bool
	 */
	public static function is_compatibility_needed( array $manifest_data, array $meta ) {
		// Check if we have an old version (2.0 or lower)
		$version = $manifest_data['version'] ?? '1.0';
		return version_compare( $version, '3.0', '<' );
	}

	/**
	 * Adapt the manifest from old format to new format
	 *
	 * @param array $manifest_data
	 * @return array
	 */
	public function adapt_manifest( array $manifest_data ) {
		// Check if site-settings needs adaptation
		if ( isset( $manifest_data['site-settings'] ) && is_array( $manifest_data['site-settings'] ) ) {
			// Old format: array of tab keys
			// New format: object with boolean values for each setting type

			$old_site_settings = $manifest_data['site-settings'];

			// Initialize new format with all settings as false
			$new_site_settings = [
				'theme' => false,
				'globalColors' => false,
				'globalFonts' => false,
				'themeStyleSettings' => false,
				'generalSettings' => false,
				'experiments' => false,
			];

			// Map old tab keys to new setting types
			$tab_mapping = [
				'settings-global-colors' => 'globalColors',
				'settings-global-typography' => 'globalFonts',
				'theme-style-typography' => 'themeStyleSettings',
				'settings-general' => 'generalSettings',
			];

			// If we have tab keys, assume all were exported (true)
			if ( ! empty( $old_site_settings ) ) {
				// In the old format, if site-settings was included, all settings were exported
				$new_site_settings = [
					'theme' => true,
					'globalColors' => true,
					'globalFonts' => true,
					'themeStyleSettings' => true,
					'generalSettings' => true,
					'experiments' => true,
				];
			}

			$manifest_data['site-settings'] = $new_site_settings;
		}

		return $manifest_data;
	}
}
