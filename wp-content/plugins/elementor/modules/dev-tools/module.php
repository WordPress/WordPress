<?php
namespace Elementor\Modules\DevTools;

use Elementor\Core\Base\App;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Fix issue with 'Potentially polymorphic call. The code may be inoperable depending on the actual class instance passed as the argument.'.
 * Its tells to the editor that instance() return right module. instead of base module.
 *
 * @method Module instance()
 */
class Module extends App {
	/**
	 * @var Deprecation
	 */
	public $deprecation;

	public function __construct() {
		$this->deprecation = new Deprecation( ELEMENTOR_VERSION );

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/common/after_register_scripts', [ $this, 'register_scripts' ] );
	}

	public function get_name() {
		return 'dev-tools';
	}

	public function register_scripts() {
		wp_register_script(
			'elementor-dev-tools',
			$this->get_js_assets_url( 'dev-tools' ),
			[],
			ELEMENTOR_VERSION,
			true
		);

		$this->print_config( 'elementor-dev-tools' );
	}

	protected function get_init_settings() {
		return [
			'isDebug' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'urls' => [
				'assets' => ELEMENTOR_ASSETS_URL,
			],
			'deprecation' => $this->deprecation->get_settings(),
		];
	}
}
