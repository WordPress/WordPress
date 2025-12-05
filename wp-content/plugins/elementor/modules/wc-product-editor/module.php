<?php

namespace Elementor\Modules\WcProductEditor;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	public function __construct() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_assets' ] );
	}

	public static function is_active() {
		return self::is_new_woocommerce_product_editor_page();
	}

	public function enqueue_assets() {
		$suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'e-wc-product-editor',
			ELEMENTOR_ASSETS_URL . 'js/e-wc-product-editor' . $suffix . '.js',
			[ 'wp-components', 'wp-core-data', 'wc-admin-layout', 'wp-plugins' ],
			ELEMENTOR_VERSION,
			true
		);

		$elementor_settings = [
			'editLink' => admin_url( 'post.php' ),
		];
		Utils::print_js_config( 'e-wc-product-editor', 'ElementorWCProductEditorSettings', $elementor_settings );
	}

	public function get_name() {
		return 'wc-product-editor';
	}

	public static function is_new_woocommerce_product_editor_page() {
		$page = Utils::get_super_global_value( $_GET, 'page' );
		$path = Utils::get_super_global_value( $_GET, 'path' );

		if ( ! isset( $page ) || 'wc-admin' !== $page || ! isset( $path ) ) {
			return false;
		}

		$path_pieces = explode( '/', $path );
		$route       = $path_pieces[1];

		return 'product' === $route || 'add-product' === $route;
	}
}
