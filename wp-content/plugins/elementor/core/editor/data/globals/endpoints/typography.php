<?php
namespace Elementor\Core\Editor\Data\Globals\Endpoints;

use Elementor\Plugin;

class Typography extends Base {
	public function get_name() {
		return 'typography';
	}

	public function get_format() {
		return 'globals/typography/{id}';
	}

	protected function get_kit_items() {
		$result = [];

		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		// Use raw settings that doesn't have default values.
		$kit_raw_settings = $kit->get_data( 'settings' );

		if ( isset( $kit_raw_settings['system_typography'] ) ) {
			$system_items = $kit_raw_settings['system_typography'];
		} else {
			// Get default items, but without empty defaults.
			$control = $kit->get_controls( 'system_typography' );
			$system_items = $control['default'];
		}

		$custom_items = $kit->get_settings( 'custom_typography' );

		if ( ! $custom_items ) {
			$custom_items = [];
		}

		$items = array_merge( $system_items, $custom_items );

		foreach ( $items as $index => &$item ) {
			foreach ( $item as $setting => $value ) {
				$new_setting = str_replace( 'styles_', '', $setting, $count );
				if ( $count ) {
					$item[ $new_setting ] = $value;
					unset( $item[ $setting ] );
				}
			}

			$id = $item['_id'];

			$result[ $id ] = [
				'title' => $item['title'] ?? '',
				'id' => $id,
			];

			unset( $item['_id'], $item['title'] );

			$result[ $id ]['value'] = $item;
		}

		return $result;
	}

	protected function convert_db_format( $item ) {
		$db_format = [
			'_id' => $item['id'],
			'title' => $item['title'] ?? '',
		];

		$db_format = array_merge( $item['value'], $db_format );

		return $db_format;
	}
}
