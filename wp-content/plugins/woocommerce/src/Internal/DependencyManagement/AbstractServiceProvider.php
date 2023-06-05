<?php
/**
 * AbstractServiceProvider class file.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement;

use Automattic\WooCommerce\Vendor\League\Container\Argument\RawArgument;
use Automattic\WooCommerce\Vendor\League\Container\Definition\DefinitionInterface;
use Automattic\WooCommerce\Vendor\League\Container\ServiceProvider\AbstractServiceProvider as BaseServiceProvider;

/**
 * Base class for the service providers used to register classes in the container.
 *
 * See the documentation of the original class this one is based on (https://container.thephpleague.com/3.x/service-providers)
 * for basic usage details. What this class adds is:
 *
 * - The `add_with_auto_arguments` method that allows to register classes without having to specify the injection method arguments.
 * - The `share_with_auto_arguments` method, sibling of the above.
 * - Convenience `add` and `share` methods that are just proxies for the same methods in `$this->getContainer()`.
 */
abstract class AbstractServiceProvider extends BaseServiceProvider {

	/**
	 * Register a class in the container and use reflection to guess the injection method arguments.
	 *
	 * WARNING: this method uses reflection, so please have performance in mind when using it.
	 *
	 * @param string $class_name Class name to register.
	 * @param mixed  $concrete   The concrete to register. Can be a shared instance, a factory callback, or a class name.
	 * @param bool   $shared Whether to register the class as shared (`get` always returns the same instance) or not.
	 *
	 * @return DefinitionInterface The generated container definition.
	 *
	 * @throws ContainerException Error when reflecting the class, or class injection method is not public, or an argument has no valid type hint.
	 */
	protected function add_with_auto_arguments( string $class_name, $concrete = null, bool $shared = false ) : DefinitionInterface {
		$definition = new Definition( $class_name, $concrete );

		$function = $this->reflect_class_or_callable( $class_name, $concrete );

		if ( ! is_null( $function ) ) {
			$arguments = $function->getParameters();
			foreach ( $arguments as $argument ) {
				if ( $argument->isDefaultValueAvailable() ) {
					$default_value = $argument->getDefaultValue();
					$definition->addArgument( new RawArgument( $default_value ) );
				} else {
					$argument_class = $this->get_class( $argument );
					if ( is_null( $argument_class ) ) {
						throw new ContainerException( "Argument '{$argument->getName()}' of class '$class_name' doesn't have a type hint or has one that doesn't specify a class." );
					}

					$definition->addArgument( $argument_class->name );
				}
			}
		}

		// Register the definition only after being sure that no exception will be thrown.
		$this->getContainer()->add( $definition->getAlias(), $definition, $shared );

		return $definition;
	}

	/**
	 * Gets the class of a parameter.
	 *
	 * This method is a replacement for ReflectionParameter::getClass,
	 * which is deprecated as of PHP 8.
	 *
	 * @param \ReflectionParameter $parameter The parameter to get the class for.
	 *
	 * @return \ReflectionClass|null The class of the parameter, or null if it hasn't any.
	 */
	private function get_class( \ReflectionParameter $parameter ) {
		return $parameter->getType() && ! $parameter->getType()->isBuiltin()
			? new \ReflectionClass( $parameter->getType()->getName() )
			: null;
	}

	/**
	 * Check if a combination of class name and concrete is valid for registration.
	 * Also return the class injection method if the concrete is either a class name or null (then use the supplied class name).
	 *
	 * @param string $class_name The class name to check.
	 * @param mixed  $concrete   The concrete to check.
	 *
	 * @return \ReflectionFunctionAbstract|null A reflection instance for the $class_name injection method or $concrete injection method or callable; null otherwise.
	 * @throws ContainerException Class has a private injection method, can't reflect class, or the concrete is invalid.
	 */
	private function reflect_class_or_callable( string $class_name, $concrete ) {
		if ( ! isset( $concrete ) || is_string( $concrete ) && class_exists( $concrete ) ) {
			$class = $concrete ?? $class_name;

			if ( ! method_exists( $class, Definition::INJECTION_METHOD ) ) {
				return null;
			}

			$method = new \ReflectionMethod( $class, Definition::INJECTION_METHOD );

			$missing_modifiers = array();
			if ( ! $method->isFinal() ) {
				$missing_modifiers[] = 'final';
			}
			if ( ! $method->isPublic() ) {
				$missing_modifiers[] = 'public';
			}
			if ( ! empty( $missing_modifiers ) ) {
				throw new ContainerException( "Method '" . Definition::INJECTION_METHOD . "' of class '$class' isn't '" . implode( ' ', $missing_modifiers ) . "', instances can't be created." );
			}

			return $method;
		} elseif ( is_callable( $concrete ) ) {
			try {
				return new \ReflectionFunction( $concrete );
			} catch ( \ReflectionException $ex ) {
				throw new ContainerException( "Error when reflecting callable: {$ex->getMessage()}" );
			}
		}

		return null;
	}

	/**
	 * Register a class in the container and use reflection to guess the injection method arguments.
	 * The class is registered as shared, so `get` on the container always returns the same instance.
	 *
	 * WARNING: this method uses reflection, so please have performance in mind when using it.
	 *
	 * @param string $class_name Class name to register.
	 * @param mixed  $concrete   The concrete to register. Can be a shared instance, a factory callback, or a class name.
	 *
	 * @return DefinitionInterface The generated container definition.
	 *
	 * @throws ContainerException Error when reflecting the class, or class injection method is not public, or an argument has no valid type hint.
	 */
	protected function share_with_auto_arguments( string $class_name, $concrete = null ) : DefinitionInterface {
		return $this->add_with_auto_arguments( $class_name, $concrete, true );
	}

	/**
	 * Register an entry in the container.
	 *
	 * @param string     $id Entry id (typically a class or interface name).
	 * @param mixed|null $concrete Concrete entity to register under that id, null for automatic creation.
	 * @param bool|null  $shared Whether to register the class as shared (`get` always returns the same instance) or not.
	 *
	 * @return DefinitionInterface The generated container definition.
	 */
	protected function add( string $id, $concrete = null, bool $shared = null ) : DefinitionInterface {
		return $this->getContainer()->add( $id, $concrete, $shared );
	}

	/**
	 * Register a shared entry in the container (`get` always returns the same instance).
	 *
	 * @param string     $id Entry id (typically a class or interface name).
	 * @param mixed|null $concrete Concrete entity to register under that id, null for automatic creation.
	 *
	 * @return DefinitionInterface The generated container definition.
	 */
	protected function share( string $id, $concrete = null ) : DefinitionInterface {
		return $this->add( $id, $concrete, true );
	}
}
