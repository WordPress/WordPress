<?php
namespace Elementor\Core\Files\Assets\Json;

use Elementor\Core\Files\Assets\Files_Upload_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Json Handler
 *
 * @deprecated 3.5.0 Use `Elementor\Core\Files\File_Types\Svg` instead, accessed by calling `Plugin::$instance->uploads_manager->get_file_type_handlers( 'svg' );`
 */
class Json_Handler extends Files_Upload_Handler {

	/**
	 * @deprecated 3.5.0
	 */
	public static function get_name() {
		return 'json-handler';
	}

	/**
	 * @deprecated 3.5.0
	 */
	public function get_mime_type() {
		return 'application/json';
	}

	/**
	 * @deprecated 3.5.0
	 */
	public function get_file_type() {
		return 'json';
	}
}
