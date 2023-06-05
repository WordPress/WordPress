<?php
/**
 * An extension to the Definition class to prevent constructor injection from being possible.
 */

namespace Automattic\WooCommerce\Internal\DependencyManagement;

use Automattic\WooCommerce\Vendor\League\Container\Definition\Definition as BaseDefinition;

/**
 * An extension of the definition class that replaces constructor injection with method injection.
 */
class Definition extends BaseDefinition {

	/**
	 * The standard method that we use for dependency injection.
	 */
	public const INJECTION_METHOD = 'init';

	/**
	 * Resolve a class using method injection instead of constructor injection.
	 *
	 * @param string $concrete The concrete to instantiate.
	 *
	 * @return object
	 */
	protected function resolveClass( string $concrete ) {
		$resolved = $this->resolveArguments( $this->arguments );
		$concrete = new $concrete();

		// Constructor injection causes backwards compatibility problems
		// so we will rely on method injection via an internal method.
		if ( method_exists( $concrete, static::INJECTION_METHOD ) ) {
			call_user_func_array( array( $concrete, static::INJECTION_METHOD ), $resolved );
		}

		return $concrete;
	}
}
