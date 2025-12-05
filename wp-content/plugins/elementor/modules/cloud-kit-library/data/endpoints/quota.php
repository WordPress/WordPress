<?php
namespace Elementor\Modules\CloudKitLibrary\Data\Endpoints;

use Elementor\Modules\CloudKitLibrary\Data\Controller;
use Elementor\Modules\CloudKitLibrary\Module as CloudKitLibrary;
use Elementor\Data\V2\Base\Endpoint;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @property Controller $controller
 */
class Quota extends Endpoint {
	public function get_name() {
		return 'quota';
	}

	public function get_format() {
		return 'cloud-kits/quota';
	}

	public function get_items( $request ) {
		return CloudKitLibrary::get_app()->get_quota();
	}
}
