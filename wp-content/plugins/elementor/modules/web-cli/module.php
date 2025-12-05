<?php
namespace Elementor\Modules\WebCli;

use Elementor\Core\Base\App;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends App {

	public function get_name() {
		return 'web-cli';
	}

	public function __construct() {
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
	}

	public function register_scripts() {
		wp_register_script(
			'elementor-web-cli',
			$this->get_js_assets_url( 'web-cli' ),
			[
				'jquery',
			],
			ELEMENTOR_VERSION,
			true
		);

		$this->print_config( 'elementor-web-cli' );
	}

	protected function get_init_settings() {
		return [
			'isDebug' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'urls' => [
				'rest' => get_rest_url(),
				'assets' => ELEMENTOR_ASSETS_URL,
			],
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'version' => ELEMENTOR_VERSION,
		];
	}
}
