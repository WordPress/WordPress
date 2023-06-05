<?php
namespace Automattic\WooCommerce\Blocks;

use Automattic\WooCommerce\Blocks\BlockTypes\AtomicBlock;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry;

/**
 * Library class.
 *
 * @deprecated 5.0.0 This class will be removed in a future release. This has been replaced by BlockTypesController.
 * @internal
 */
class Library {

	/**
	 * Initialize block library features.
	 *
	 * @deprecated 5.0.0
	 */
	public static function init() {
		_deprecated_function( 'Library::init', '5.0.0' );
	}

	/**
	 * Register custom tables within $wpdb object.
	 *
	 * @deprecated 5.0.0
	 */
	public static function define_tables() {
		_deprecated_function( 'Library::define_tables', '5.0.0' );
	}

	/**
	 * Register blocks, hooking up assets and render functions as needed.
	 *
	 * @deprecated 5.0.0
	 */
	public static function register_blocks() {
		_deprecated_function( 'Library::register_blocks', '5.0.0' );
	}
}
