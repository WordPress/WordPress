<?php
namespace Elementor\Modules\KitElementsDefaults;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Usage {

	public function register() {
		add_filter( 'elementor/tracker/send_tracking_data_params', function ( array $params ) {
			$params['usages']['kit']['defaults'] = $this->get_usage_data();

			return $params;
		} );
	}

	private function get_usage_data() {
		$elements_defaults = $this->get_elements_defaults() ?? [];

		return [
			'count' => count( $elements_defaults ),
			'elements' => array_keys( $elements_defaults ),
		];
	}

	private function get_elements_defaults() {
		$kit = Plugin::$instance->kits_manager->get_active_kit();

		return $kit->get_json_meta( Module::META_KEY );
	}
}
