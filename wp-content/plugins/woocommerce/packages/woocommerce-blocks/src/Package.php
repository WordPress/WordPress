<?php

namespace Automattic\WooCommerce\Blocks;

use Automattic\WooCommerce\Blocks\Domain\Package as NewPackage;
use Automattic\WooCommerce\Blocks\Domain\Bootstrap;
use Automattic\WooCommerce\Blocks\Registry\Container;
use Automattic\WooCommerce\Blocks\Domain\Services\FeatureGating;

/**
 * Main package class.
 *
 * Returns information about the package and handles init.
 *
 * In the context of this plugin, it handles init and is called from the main
 * plugin file (woocommerce-gutenberg-products-block.php).
 *
 * In the context of WooCommere core, it handles init and is called from
 * WooCommerce's package loader. The main plugin file is _not_ loaded.
 *
 * @since 2.5.0
 */
class Package {


	/**
	 * For back compat this is provided. Ideally, you should register your
	 * class with Automattic\Woocommerce\Blocks\Container and make Package a
	 * dependency.
	 *
	 * @since 2.5.0
	 * @return Package  The Package instance class
	 */
	protected static function get_package() {
		return self::container()->get( NewPackage::class );
	}

	/**
	 * Init the package - load the blocks library and define constants.
	 *
	 * @since 2.5.0 Handled by new NewPackage.
	 */
	public static function init() {
		self::container()->get( Bootstrap::class );
	}

	/**
	 * Return the version of the package.
	 *
	 * @return string
	 */
	public static function get_version() {
		return self::get_package()->get_version();
	}

	/**
	 * Return the path to the package.
	 *
	 * @return string
	 */
	public static function get_path() {
		return self::get_package()->get_path();
	}

	/**
	 * Returns an instance of the FeatureGating class.
	 *
	 * @return FeatureGating
	 */
	public static function feature() {
		return self::get_package()->feature();
	}

	/**
	 * Checks if we're executing the code in an experimental build mode.
	 *
	 * @return boolean
	 */
	public static function is_experimental_build() {
		return self::get_package()->is_experimental_build();
	}

	/**
	 * Checks if we're executing the code in a feature plugin or experimental build mode.
	 *
	 * @return boolean
	 */
	public static function is_feature_plugin_build() {
		return self::get_package()->is_feature_plugin_build();
	}


	/**
	 * Loads the dependency injection container for woocommerce blocks.
	 *
	 * @param boolean $reset Used to reset the container to a fresh instance.
	 *                       Note: this means all dependencies will be
	 *                       reconstructed.
	 */
	public static function container( $reset = false ) {
		static $container;
		if (
			! $container instanceof Container
			|| $reset
		) {
			$container = new Container();
			// register Package.
			$container->register(
				NewPackage::class,
				function ( $container ) {
					// leave for automated version bumping.
					$version = '10.2.4';
					return new NewPackage(
						$version,
						dirname( __DIR__ ),
						new FeatureGating()
					);
				}
			);
			// register Bootstrap.
			$container->register(
				Bootstrap::class,
				function ( $container ) {
					return new Bootstrap(
						$container
					);
				}
			);
			// register Bootstrap.
			$container->register(
				Migration::class,
				function () {
					return new Migration();
				}
			);
		}
		return $container;
	}
}
