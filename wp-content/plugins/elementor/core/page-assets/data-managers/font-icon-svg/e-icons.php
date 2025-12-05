<?php
namespace Elementor\Core\Page_Assets\Data_Managers\Font_Icon_Svg;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * E-Icons Svg.
 *
 * @since 3.5.0
 */
class E_Icons extends Base {
	const LIBRARY_CURRENT_VERSION = '5.13.0';

	protected function get_config( $icon ) {
		return [
			'key' => $icon['value'],
			'version' => self::LIBRARY_CURRENT_VERSION,
			'file_path' => ELEMENTOR_ASSETS_PATH . 'lib/eicons/eicons.json',
			'data' => [
				'icon_data' => [
					'name' => $icon['value'],
					'library' => $icon['library'],
				],
			],
		];
	}

	protected function get_asset_content() {
		$icon_data = $this->get_config_data( 'icon_data' );

		$file_data = json_decode( $this->get_file_data( 'content', $icon_data['library'] ), true );

		$icon_name = str_replace( 'eicon-', '', $icon_data['name'] );

		$svg_data = $file_data[ $icon_name ];

		return [
			'width' => $svg_data['width'],
			'height' => $svg_data['height'],
			'path' => $svg_data['path'],
			'key' => $this->get_key(),
		];
	}
}
