<?php
namespace Elementor\Modules\Home;

use Elementor\Includes\EditorAssetsAPI;
use Elementor\Modules\Home\Classes\Transformations_Manager;

class API {
	protected EditorAssetsAPI $editor_assets_api;

	public function __construct( EditorAssetsAPI $editor_assets_api ) {
		$this->editor_assets_api = $editor_assets_api;
	}

	public function get_home_screen_items( $force_request = false ): array {
		$assets_data = $this->editor_assets_api->get_assets_data( $force_request );

		$assets_data = apply_filters( 'elementor/core/admin/homescreen', $assets_data );

		return $this->transform_home_screen_data( $assets_data );
	}

	private function transform_home_screen_data( $json_data ): array {
		$transformers = new Transformations_Manager( $json_data );

		return $transformers->run_transformations();
	}
}
