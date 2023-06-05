<?php
namespace Automattic\WooCommerce\Blocks\Registry;

use Closure;
use Exception;

/**
 * A simple Dependency Injection Container
 *
 * This is used to manage dependencies used throughout the plugin.
 *
 * @since 2.5.0
 */
class Container {

	/**
	 * A map of Dependency Type objects used to resolve dependencies.
	 *
	 * @var AbstractDependencyType[]
	 */
	private $registry = [];

	/**
	 * Public api for adding a factory to the container.
	 *
	 * Factory dependencies will have the instantiation callback invoked
	 * every time the dependency is requested.
	 *
	 * Typical Usage:
	 *
	 * ```
	 * $container->register( MyClass::class, $container->factory( $mycallback ) );
	 * ```
	 *
	 * @param Closure $instantiation_callback  This will be invoked when the
	 *                                         dependency is required.  It will
	 *                                         receive an instance of this
	 *                                         container so the callback can
	 *                                         retrieve dependencies from the
	 *                                         container.
	 *
	 * @return FactoryType  An instance of the FactoryType dependency.
	 */
	public function factory( Closure $instantiation_callback ) {
		return new FactoryType( $instantiation_callback );
	}

	/**
	 * Interface for registering a new dependency with the container.
	 *
	 * By default, the $value will be added as a shared dependency.  This means
	 * that it will be a single instance shared among any other classes having
	 * that dependency.
	 *
	 * If you want a new instance every time it's required, then wrap the value
	 * in a call to the factory method (@see Container::factory for example)
	 *
	 * Note: Currently if the provided id already is registered in the container,
	 * the provided value is ignored.
	 *
	 * @param string $id    A unique string identifier for the provided value.
	 *                      Typically it's the fully qualified name for the
	 *                      dependency.
	 * @param mixed  $value The value for the dependency. Typically, this is a
	 *                      closure that will create the class instance needed.
	 */
	public function register( $id, $value ) {
		if ( empty( $this->registry[ $id ] ) ) {
			if ( ! $value instanceof FactoryType ) {
				$value = new SharedType( $value );
			}
			$this->registry[ $id ] = $value;
		}
	}

	/**
	 * Interface for retrieving the dependency stored in the container for the
	 * given identifier.
	 *
	 * @param string $id  The identifier for the dependency being retrieved.
	 * @throws Exception  If there is no dependency for the given identifier in
	 *                    the container.
	 *
	 * @return mixed  Typically a class instance.
	 */
	public function get( $id ) {
		if ( ! isset( $this->registry[ $id ] ) ) {
			// this is a developer facing exception, hence it is not localized.
			throw new Exception(
				sprintf(
					'Cannot construct an instance of %s because it has not been registered.',
					$id
				)
			);
		}
		return $this->registry[ $id ]->get( $this );
	}
}
