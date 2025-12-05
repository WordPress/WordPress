<?php

namespace Elementor\App\Modules\ImportExportCustomization\Runners\Import;

use Elementor\Plugin;
use Elementor\Core\Settings\Page\Manager as PageManager;
use Elementor\App\Modules\ImportExportCustomization\Utils;
use Elementor\Core\Experiments\Manager as ExperimentsManager;

class Site_Settings extends Import_Runner_Base {

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

	/**
	 * @var int
	 */
	private $previous_kit_id;

	/**
	 * @var int
	 */
	private $active_kit_id;

	/**
	 * @var int
	 */
	private $imported_kit_id;

	/**
	 * @var string|null
	 */
	private ?string $installed_theme = null;

	/**
	 * @var string|null
	 */
	private ?string $activated_theme = null;

	/**
	 * @var array|null
	 */
	private ?array $previous_active_theme = null;

	/**
	 * @var array
	 */
	private $previous_experiments = [];

	/**
	 * @var array
	 */
	private $imported_experiments = [];

	public function get_theme_upgrader(): \Theme_Upgrader {
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! class_exists( '\Theme_Upgrader' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		if ( ! class_exists( '\WP_Ajax_Upgrader_Skin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
		}

		return new \Theme_Upgrader( new \WP_Ajax_Upgrader_Skin() );
	}

	public static function get_name(): string {
		return 'site-settings';
	}

	public function should_import( array $data ) {
		return (
			isset( $data['include'] ) &&
			in_array( 'settings', $data['include'], true ) &&
			! empty( $data['site_settings']['settings'] )
		);
	}

	public function import( array $data, array $imported_data ) {
		$customization = $data['customization']['settings'] ?? null;

		if ( $customization ) {
			return $this->import_with_customization( $data, $imported_data, $customization );
		}

		return $this->import_with_manifest( $data, $imported_data );
	}

	private function import_with_customization( array $data, array $imported_data, array $customization ) {
		$result = apply_filters( 'elementor/import-export-customization/import/site-settings/customization', null, $data, $imported_data, $customization, $this );

		if ( is_array( $result ) ) {
			return $result;
		}

		return $this->import_with_manifest( $data, $imported_data, ! empty( $customization['theme'] ) );
	}

	private function import_with_manifest( array $data, array $imported_data, $include_theme = true ) {
		$new_site_settings = $data['site_settings']['settings'];
		$title = $data['manifest']['title'] ?? 'Imported Kit';
		$manifest_settings = $data['manifest']['site-settings'] ?? [];

		$active_kit = Plugin::$instance->kits_manager->get_active_kit();

		$this->active_kit_id = (int) $active_kit->get_id();
		$this->previous_kit_id = (int) Plugin::$instance->kits_manager->get_previous_id();

		$result = [];

		$old_settings = $active_kit->get_meta( PageManager::META_KEY );

		if ( ! $old_settings ) {
			$old_settings = [];
		}

		$new_site_settings = $this->filter_settings_by_manifest( $new_site_settings, $manifest_settings );

		if ( ( $manifest_settings['globalColors'] ?? false ) && ! empty( $old_settings['custom_colors'] ) && ! empty( $new_site_settings['custom_colors'] ) ) {
			$new_site_settings['custom_colors'] = array_merge( $old_settings['custom_colors'], $new_site_settings['custom_colors'] );
		}

		if ( ( $manifest_settings['globalFonts'] ?? false ) && ! empty( $old_settings['custom_typography'] ) && ! empty( $new_site_settings['custom_typography'] ) ) {
			$new_site_settings['custom_typography'] = array_merge( $old_settings['custom_typography'], $new_site_settings['custom_typography'] );
		}

		if ( ( $manifest_settings['generalSettings'] ?? false ) && ! empty( $new_site_settings['space_between_widgets'] ) ) {
			$new_site_settings['space_between_widgets'] = Utils::update_space_between_widgets_values( $new_site_settings['space_between_widgets'] );
		}

		$new_site_settings = array_replace_recursive( $old_settings, $new_site_settings );

		$new_kit = Plugin::$instance->kits_manager->create_new_kit( $title, $new_site_settings );

		$this->imported_kit_id = (int) $new_kit;

		$result['site-settings']['imported_kit_id'] = $this->imported_kit_id;

		foreach ( $new_site_settings as $key => $value ) {
			$result['site-settings'][ $key ] = $value;
		}

		if ( ( $manifest_settings['theme'] ?? false ) && $include_theme ) {
			$import_theme_result = $this->import_theme( $data );

			if ( ! empty( $import_theme_result ) ) {
				$result['theme'] = $import_theme_result;
			}
		}

		if ( $manifest_settings['experiments'] ?? false ) {
			$this->import_experiments( $data );

			if ( ! empty( $this->imported_experiments ) ) {
				$result['experiments'] = $this->imported_experiments;
			}
		}

		return $result;
	}

	private function filter_settings_by_manifest( array $settings, array $manifest_settings ): array {
		foreach ( self::ALLOWED_SETTINGS as $setting_key ) {
			if ( ! ( $manifest_settings[ $setting_key ] ?? false ) ) {
				$settings = $this->remove_setting_by_key( $settings, $setting_key );
			}
		}

		return $settings;
	}

	private function remove_setting_by_key( array $settings, string $setting_key ): array {
		switch ( $setting_key ) {
			case 'globalColors':
				$settings = $this->remove_global_colors( $settings );
				break;
			case 'globalFonts':
				$settings = $this->remove_global_fonts( $settings );
				break;
			case 'themeStyleSettings':
				$settings = $this->remove_theme_style( $settings );
				break;
			case 'generalSettings':
				$settings = $this->remove_other_settings( $settings );
				break;
		}

		return $settings;
	}

	private function remove_global_colors( array $settings ): array {
		$color_keys = [ 'system_colors', 'custom_colors' ];

		foreach ( $color_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				unset( $settings[ $key ] );
			}
		}

		return $settings;
	}

	private function remove_global_fonts( array $settings ): array {
		$typography_keys = [ 'system_typography', 'custom_typography', 'default_generic_fonts' ];

		foreach ( $typography_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				unset( $settings[ $key ] );
			}
		}

		return $settings;
	}

	private function remove_theme_style( array $settings ): array {
		$theme_style_patterns = [
			'/^body_/',
			'/^h[1-6]_/',
			'/^button_/',
			'/^link_/',
			'/^form_field_/',
		];

		foreach ( $settings as $key => $value ) {
			foreach ( $theme_style_patterns as $pattern ) {
				if ( preg_match( $pattern, $key ) ) {
					unset( $settings[ $key ] );
					break;
				}
			}
		}

		return $settings;
	}

	private function remove_other_settings( array $settings ): array {
		$settings_keys = [
			'template',
			'container_width',
			'container_padding',
			'space_between_widgets',
			'viewport_md',
			'viewport_lg',
			'page_title_selector',
			'activeItemIndex',
		];

		foreach ( $settings_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				unset( $settings[ $key ] );
			}
		}

		return $settings;
	}

	protected function install_theme( $slug, $version ) {
		$download_url = "https://downloads.wordpress.org/theme/{$slug}.{$version}.zip";

		return $this->get_theme_upgrader()->install( $download_url );
	}

	protected function activate_theme( $slug ) {
		switch_theme( $slug );
	}

	public function import_theme( array $data ) {
		if ( empty( $data['site_settings']['theme'] ) ) {
			return null;
		}

		if ( ! function_exists( 'wp_get_theme' ) ) {
			require_once ABSPATH . 'wp-admin/includes/theme.php';
		}

		$theme = $data['site_settings']['theme'];
		$theme_slug = $theme['slug'];
		$theme_name = $theme['name'];

		$current_theme = wp_get_theme();
		$this->previous_active_theme = [];
		$this->previous_active_theme['slug'] = $current_theme->get_stylesheet();
		$this->previous_active_theme['version'] = $current_theme->get( 'Version' );

		if ( $current_theme->get_stylesheet() === $theme_slug ) {
			$result['succeed'][ $theme_slug ] = sprintf(
				/* translators: %s: Theme name. */
				__( 'Theme: %s is already used', 'elementor' ),
				$theme_name
			);
			return $result;
		}

		try {
			if ( wp_get_theme( $theme_slug )->exists() ) {
				$this->activate_theme( $theme_slug );
				$this->activated_theme = $theme_slug;
				$result['succeed'][ $theme_slug ] = sprintf(
					/* translators: %s: Theme name. */
					__( 'Theme: %s has already been installed and activated', 'elementor' ),
					$theme_name
				);
				return $result;
			}

			$import = $this->install_theme( $theme_slug, $theme['version'] );

			if ( is_wp_error( $import ) ) {
				$result['failed'][ $theme_slug ] = sprintf(
					/* translators: %s: Theme name. */
					__( 'Failed to install theme: %s', 'elementor' ),
					$theme_name
				);
				return $result;
			}

			$result['succeed'][ $theme_slug ] = sprintf(
				/* translators: %s: Theme name. */
				__( 'Theme: %s has been successfully installed', 'elementor' ),
				$theme_name
			);
			$this->installed_theme = $theme_slug;
			$this->activate_theme( $theme_slug );
		} catch ( \Exception $error ) {
			$result['failed'][ $theme_slug ] = $error->getMessage();
		}

		return $result;
	}

	public function import_experiments( array $data ) {
		if ( empty( $data['site_settings']['experiments'] ) ) {
			return null;
		}

		$experiments_data = $data['site_settings']['experiments'];
		$experiments_manager = Plugin::$instance->experiments;
		$current_features = $experiments_manager->get_features();

		$this->save_previous_experiments_state( $current_features );

		foreach ( $experiments_data as $feature_name => $feature_data ) {
			if ( ! isset( $current_features[ $feature_name ] ) ) {
				continue;
			}

			$current_feature = $current_features[ $feature_name ];

			$current_feature_state = $current_feature['state'];
			$new_state = $feature_data['state'];

			if ( $current_feature_state === $new_state ) {
				continue;
			}

			if ( ! in_array( $new_state, [ ExperimentsManager::STATE_DEFAULT, ExperimentsManager::STATE_ACTIVE, ExperimentsManager::STATE_ACTIVE ], true ) ) {
				continue;
			}

			$option_key = $experiments_manager->get_feature_option_key( $feature_name );

			if ( 'default' === $new_state ) {
				delete_option( $option_key );
			} else {
				update_option( $option_key, $new_state );
			}

			$this->imported_experiments[ $feature_name ] = $feature_data;
		}
	}

	private function save_previous_experiments_state( array $current_features ) {
		$experiments_manager = Plugin::$instance->experiments;

		foreach ( $current_features as $feature_name => $feature ) {
			if ( ! $feature['mutable'] ) {
				continue;
			}

			$option_key = $experiments_manager->get_feature_option_key( $feature_name );
			$saved_state = get_option( $option_key );

			$this->previous_experiments[ $feature_name ] = [
				'name' => $feature_name,
				'title' => $feature['title'],
				'state' => empty( $saved_state ) ? 'default' : $saved_state,
				'default' => $feature['default'],
				'release_status' => $feature['release_status'],
			];
		}
	}

	public function get_import_session_metadata(): array {
		return [
			'previous_kit_id' => $this->previous_kit_id,
			'active_kit_id' => $this->active_kit_id,
			'imported_kit_id' => $this->imported_kit_id,
			'installed_theme' => $this->installed_theme,
			'activated_theme' => $this->activated_theme,
			'previous_active_theme' => $this->previous_active_theme,
			'previous_experiments' => $this->previous_experiments,
			'imported_experiments' => $this->imported_experiments,
		];
	}
}
