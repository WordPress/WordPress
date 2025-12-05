<?php
namespace Elementor\Core\Page_Assets\Data_Managers\Font_Icon_Svg;

use Elementor\Core\Base\Base_Object;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Font Icon Svg Manager.
 *
 * @since 3.4.0
 */
class Manager extends Base_Object {
	private static $data = [];

	private static function get_data() {
		if ( ! self::$data ) {
			self::$data = [
				'font-awesome' => [
					'regex' => '/^fa-/',
					'manager' => new Font_Awesome(),
				],
				'eicons' => [
					'regex' => '/^eicons$/',
					'manager' => new E_Icons(),
				],
			];
		}

		return self::$data;
	}

	public static function get_font_icon_svg_data( $icon ) {
		$data = self::get_data();

		$font_family = $icon['font_family'];

		$font_family_manager = $data[ $font_family ]['manager'];

		return $font_family_manager->get_asset_data( $icon );
	}

	public static function get_font_family( $icon_library ) {
		foreach ( self::get_data() as $family => $data ) {
			if ( preg_match( $data['regex'], $icon_library ) ) {
				return $family;
			}
		}

		return '';
	}
}
