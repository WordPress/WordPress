<?php
namespace Elementor\Core\Editor\Data\Globals\Endpoints;

use Elementor\Plugin;

class Colors extends Base {
	public function get_name() {
		return 'colors';
	}

	public function get_format() {
		return 'globals/colors/{id}';
	}

	protected function get_kit_items() {
		$result = [];
		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		$system_items = $kit->get_settings_for_display( 'system_colors' );
		$custom_items = $kit->get_settings_for_display( 'custom_colors' );

		if ( ! $system_items ) {
			$system_items = [];
		}

		if ( ! $custom_items ) {
			$custom_items = [];
		}

		$items = array_merge( $system_items, $custom_items );

		foreach ( $items as $index => $item ) {
			$id = $item['_id'];
			$result[ $id ] = [
				'id' => $id,
				'title' => $item['title'] ?? '',
				'value' => $item['color'] ?? '',
			];
		}

		return $result;
	}

	protected function convert_db_format( $item ) {
		return [
			'_id' => $item['id'],
			'title' => $item['title'] ?? '',
			'color' => $item['value'] ?? '',
		];
	}
}
