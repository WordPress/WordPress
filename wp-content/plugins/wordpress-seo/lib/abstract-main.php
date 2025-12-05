<?php

namespace Yoast\WP\Lib;

use Exception;
use WPSEO_Utils;
use Yoast\WP\Lib\Dependency_Injection\Container_Registry;
use Yoast\WP\SEO\Exceptions\Forbidden_Property_Mutation_Exception;
use Yoast\WP\SEO\Loader;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract class to extend for the main class in a plugin.
 */
abstract class Abstract_Main {

	/**
	 * The DI container.
	 *
	 * @var ContainerInterface|null
	 */
	protected $container;

	/**
	 * A cache for previously requested and constructed surfaces.
	 *
	 * @var mixed[]
	 */
	private $cached_surfaces = [];

	/**
	 * Loads the plugin.
	 *
	 * @return void
	 *
	 * @throws Exception If loading fails and YOAST_ENVIRONMENT is development.
	 */
	public function load() {
		if ( $this->container ) {
			return;
		}

		try {
			$this->container = $this->get_container();
			Container_Registry::register( $this->get_name(), $this->container );

			if ( ! $this->container ) {
				return;
			}
			if ( ! $this->container->has( Loader::class ) ) {
				return;
			}

			$this->container->get( Loader::class )->load();
		} catch ( Exception $e ) {
			if ( $this->is_development() ) {
				throw $e;
			}
			// Don't crash the entire site, simply don't load.
		}
	}

	/**
	 * Magic getter for retrieving a property from a surface.
	 *
	 * @param string $property The property to retrieve.
	 *
	 * @return mixed The value of the property.
	 *
	 * @throws Exception When the property doesn't exist.
	 */
	public function __get( $property ) {
		if ( \array_key_exists( $property, $this->cached_surfaces ) ) {
			return $this->cached_surfaces[ $property ];
		}

		$surfaces = $this->get_surfaces();

		if ( isset( $surfaces[ $property ] ) ) {
			$this->cached_surfaces[ $property ] = $this->container->get( $surfaces[ $property ] );

			return $this->cached_surfaces[ $property ];
		}
		throw new Exception( \sprintf( 'Property $%s does not exist.', $property ) );
	}

	/**
	 * Checks if the given property exists as a surface.
	 *
	 * @param string $property The property to retrieve.
	 *
	 * @return bool True when property is set.
	 */
	public function __isset( $property ) {
		if ( \array_key_exists( $property, $this->cached_surfaces ) ) {
			return true;
		}

		$surfaces = $this->get_surfaces();

		if ( ! isset( $surfaces[ $property ] ) ) {
			return false;
		}

		return $this->container->has( $surfaces[ $property ] );
	}

	/**
	 * Prevents setting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name  The property name.
	 * @param mixed  $value The property value.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Set is never meant to be called.
	 */
	public function __set( $name, $value ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- __set must have a name and value - PHPCS #3715.
		throw Forbidden_Property_Mutation_Exception::cannot_set_because_property_is_immutable( $name );
	}

	/**
	 * Prevents unsetting dynamic properties and unsetting declared properties
	 * from an inaccessible context.
	 *
	 * @param string $name The property name.
	 *
	 * @return void
	 *
	 * @throws Forbidden_Property_Mutation_Exception Unset is never meant to be called.
	 */
	public function __unset( $name ) {
		throw Forbidden_Property_Mutation_Exception::cannot_unset_because_property_is_immutable( $name );
	}

	/**
	 * Loads the DI container.
	 *
	 * @return ContainerInterface|null The DI container.
	 *
	 * @throws Exception If something goes wrong generating the DI container.
	 */
	abstract protected function get_container();

	/**
	 * Gets the name of the plugin.
	 *
	 * @return string The name.
	 */
	abstract protected function get_name();

	/**
	 * Gets the surfaces of this plugin.
	 *
	 * @return array A mapping of surface name to the responsible class.
	 */
	abstract protected function get_surfaces();

	/**
	 * Returns whether or not we're in an environment for Yoast development.
	 *
	 * @return bool Whether or not to load in development mode.
	 */
	protected function is_development() {
		try {
			return WPSEO_Utils::is_development_mode();
		} catch ( Exception $exception ) {
			// E.g. when WordPress and/or WordPress SEO are not loaded.
			return \defined( 'YOAST_ENVIRONMENT' ) && \YOAST_ENVIRONMENT === 'development';
		}
	}
}
