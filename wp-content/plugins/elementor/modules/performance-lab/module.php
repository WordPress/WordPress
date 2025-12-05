<?php
namespace Elementor\Modules\PerformanceLab;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	const PERFORMANCE_LAB_FUNCTION_NAME = 'webp_uploads_img_tag_update_mime_type';
	const PERFORMANCE_LAB_OPTION_NAME = 'site-health/webp-support';

	public function get_name() {
		return 'performance-lab';
	}

	private function is_performance_lab_is_active() {
		if ( function_exists( self::PERFORMANCE_LAB_FUNCTION_NAME ) ) {
			$perflab_modules_settings = get_option( self::PERFORMANCE_LAB_OPTION_NAME, [] );
			if ( isset( $perflab_modules_settings ) && isset( $perflab_modules_settings[ self::PERFORMANCE_LAB_OPTION_NAME ] ) &&
							'1' === $perflab_modules_settings[ self::PERFORMANCE_LAB_OPTION_NAME ]['enabled'] ) {
				return true;
			}
		}
		return false;
	}

	private function performance_lab_get_webp_src( $attachment_id, $size, $url ) {
		$image_object = wp_get_attachment_image_src( $attachment_id, $size );
		$image_src = call_user_func( self::PERFORMANCE_LAB_FUNCTION_NAME, $image_object[0], 'webp', $attachment_id );
		if ( ! empty( $image_src ) ) {
			return $image_src;
		}
		return $url;
	}

	private function replace_css_with_webp( $value, $css_property, $matches ) {
		if ( 0 === strpos( $css_property, 'background-image' ) && '{{URL}}' === $matches[0] ) {
			$value['url'] = $this->performance_lab_get_webp_src( $value['id'], 'full', $value['url'] );
		}
		return $value;
	}

	public function __construct() {
		parent::__construct();

		if ( $this->is_performance_lab_is_active() ) {
			add_filter( 'elementor/files/css/property', function( $value, $css_property, $matches ) {
				return $this->replace_css_with_webp( $value, $css_property, $matches );
			}, 10, 3 );
		}
	}
}
