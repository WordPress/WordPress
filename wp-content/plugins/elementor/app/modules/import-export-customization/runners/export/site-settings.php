<?php
namespace Elementor\App\Modules\ImportExportCustomization\Runners\Export;

use Elementor\Plugin;

class Site_Settings extends Export_Runner_Base {
	const ALLOWED_SETTINGS = [
		'theme',
		'globalColors',
		'globalFonts',
		'themeStyleSettings',
		'generalSettings',
		'experiments',
		'customCode',
		'customIcons',
		'customFonts',
	];

	public static function get_name(): string {
		return 'site-settings';
	}

	public function should_export( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'settings', $data['include'], true )
		);
	}

	public function export( array $data ) {
		$customization = $data['customization']['settings'] ?? null;
		if ( $customization ) {
			return $this->export_customization( $data, $customization );
		}

		return $this->export_all( $data );
	}

	private function export_all( $data, $include_theme = true ) {
		$kit = Plugin::$instance->kits_manager->get_active_kit();
		$kit_data = $kit->get_export_data();

		$excluded_kit_settings_keys = [
			'site_name',
			'site_description',
			'site_logo',
			'site_favicon',
		];

		foreach ( $excluded_kit_settings_keys as $setting_key ) {
			unset( $kit_data['settings'][ $setting_key ] );
		}

		if ( $include_theme ) {
			$theme_data = $this->export_theme();

			if ( $theme_data ) {
				$kit_data['theme'] = $theme_data;
				$manifest_data['theme'] = $theme_data;
			}
		}

		$experiments_data = $this->export_experiments();

		if ( $experiments_data ) {
			$kit_data['experiments'] = $experiments_data;
			$manifest_data['experiments'] = array_keys( $experiments_data );
		}

		$manifest_data['site-settings'] = array_fill_keys( self::ALLOWED_SETTINGS, true );

		if ( ! $include_theme ) {
			$manifest_data['site-settings']['theme'] = false;
		}

		return [
			'files' => [
				'path' => 'site-settings',
				'data' => $kit_data,
			],
			'manifest' => [
				$manifest_data,
			],
		];
	}

	private function export_customization( $data, $customization ) {
		$result = apply_filters( 'elementor/import-export-customization/export/site-settings/customization', null, $data, $customization, $this );

		if ( is_array( $result ) ) {
			return $result;
		}

		return $this->export_all( $data, ! empty( $customization['theme'] ) );
	}

	public function export_theme() {
		$theme = wp_get_theme();

		if ( empty( $theme ) || empty( $theme->get( 'ThemeURI' ) ) ) {
			return null;
		}

		$theme_data['name'] = $theme->get( 'Name' );
		$theme_data['theme_uri'] = $theme->get( 'ThemeURI' );
		$theme_data['version'] = $theme->get( 'Version' );
		$theme_data['slug'] = $theme->get_stylesheet();

		return $theme_data;
	}

	public function export_experiments() {
		$features = Plugin::$instance->experiments->get_features();

		if ( empty( $features ) ) {
			return null;
		}

		$experiments_data = [];

		foreach ( $features as $feature_name => $feature ) {
			$experiments_data[ $feature_name ] = [
				'name' => $feature_name,
				'title' => $feature['title'],
				'state' => $feature['state'],
				'default' => $feature['default'],
				'release_status' => $feature['release_status'],
			];
		}

		return empty( $experiments_data ) ? null : $experiments_data;
	}
}
