<?php
namespace Elementor\Core\App\Modules\KitLibrary\Connect;

use Elementor\App\Modules\KitLibrary\Connect\Kit_Library as Kit_Library_Connect;
use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This App class exists for backwards compatibility with 3rd parties.
 *
 * @deprecated 3.8.0
 */
class Kit_Library extends Library {

	/**
	 * @deprecated 3.8.0
	 */
	public function is_connected() {
		/** @var Kit_Library_Connect $kit_library */
		$kit_library = Plugin::$instance->common->get_component( 'connect' )->get_app( 'kit-library' );

		return $kit_library && $kit_library->is_connected();
	}
}
