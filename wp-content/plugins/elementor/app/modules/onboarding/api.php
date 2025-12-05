<?php
namespace Elementor\App\Modules\Onboarding;

use Elementor\Includes\EditorAssetsAPI;

class API {
	protected EditorAssetsAPI $editor_assets_api;

	public function __construct( EditorAssetsAPI $editor_assets_api ) {
		$this->editor_assets_api = $editor_assets_api;
	}

	public function get_ab_testing_data( $force_request = false ): array {
		$assets_data = $this->editor_assets_api->get_assets_data( $force_request );

		return $this->extract_ab_testing_config( $assets_data );
	}

	private function extract_ab_testing_config( array $json_data ): array {
		if ( empty( $json_data ) || ! is_array( $json_data ) ) {
			return [];
		}

		return $json_data[0] ?? [];
	}

	public function is_theme_selection_experiment_enabled( $force_request = false ): bool {
		$ab_testing_data = $this->get_ab_testing_data( $force_request );

		return $ab_testing_data['coreOnboardingThemeSelection'] ?? false;
	}

	public function is_good_to_go_experiment_enabled( $force_request = false ): bool {
		$ab_testing_data = $this->get_ab_testing_data( $force_request );

		return $ab_testing_data['coreOnboardingGoodToGo'] ?? false;
	}
}
