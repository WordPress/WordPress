<?php

namespace Yoast\WP\SEO;

use Yoast\WP\Lib\Abstract_Main;
use Yoast\WP\SEO\Dependency_Injection\Container_Compiler;
use Yoast\WP\SEO\Generated\Cached_Container;
use Yoast\WP\SEO\Surfaces\Classes_Surface;
use Yoast\WP\SEO\Surfaces\Helpers_Surface;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

if ( ! \defined( 'WPSEO_VERSION' ) ) {
	\header( 'Status: 403 Forbidden' );
	\header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * Class Main.
 *
 * @property Classes_Surface $classes      The classes surface.
 * @property Meta_Surface    $meta         The meta surface.
 * @property Helpers_Surface $helpers      The helpers surface.
 */
class Main extends Abstract_Main {

	/**
	 * The API namespace constant.
	 *
	 * @var string
	 */
	public const API_V1_NAMESPACE = 'yoast/v1';

	/**
	 * The WP CLI namespace constant.
	 *
	 * @var string
	 */
	public const WP_CLI_NAMESPACE = 'yoast';

	/**
	 * {@inheritDoc}
	 */
	protected function get_container() {
		if ( $this->is_development() && \class_exists( '\Yoast\WP\SEO\Dependency_Injection\Container_Compiler' ) ) {
			// Exception here is unhandled as it will only occur in development.
			Container_Compiler::compile(
				$this->is_development(),
				__DIR__ . '/generated/container.php',
				__DIR__ . '/../config/dependency-injection/services.php',
				__DIR__ . '/../vendor/composer/autoload_classmap.php',
				'Yoast\WP\SEO\Generated'
			);
		}

		if ( \file_exists( __DIR__ . '/generated/container.php' ) ) {
			require_once __DIR__ . '/generated/container.php';

			return new Cached_Container();
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_name() {
		return 'yoast-seo';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_surfaces() {
		return [
			'classes' => Classes_Surface::class,
			'meta'    => Meta_Surface::class,
			'helpers' => Helpers_Surface::class,
		];
	}
}
