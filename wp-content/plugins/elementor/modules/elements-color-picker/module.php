<?php
namespace Elementor\Modules\ElementsColorPicker;

use Elementor\Core\Experiments\Manager;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Experiments\Manager as Experiments_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	/**
	 * Retrieve the module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'elements-color-picker';
	}

	/**
	 * Enqueue the `Color-Thief` library to pick colors from images.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'color-thief',
			$this->get_js_assets_url( 'color-thief', 'assets/lib/color-thief/', true ),
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION,
			true
		);
	}

	/**
	 * Module constructor - Initialize the Eye-Dropper module.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}
}
