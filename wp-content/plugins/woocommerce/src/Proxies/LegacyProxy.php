<?php
/**
 * LegacyProxy class file.
 */

namespace Automattic\WooCommerce\Proxies;

use Automattic\WooCommerce\Internal\DependencyManagement\Definition;
use Automattic\WooCommerce\Vendor\Psr\Container\ContainerInterface;

/**
 * Proxy class to access legacy WooCommerce functionality.
 *
 * This class should be used to interact with code outside the `src` directory, especially functions and classes
 * in the `includes` directory, unless a more specific proxy exists for the functionality at hand (e.g. `ActionsProxy`).
 * Idempotent functions can be executed directly.
 */
class LegacyProxy {

	/**
	 * Gets an instance of a given legacy class.
	 * This must not be used to get instances of classes in the `src` directory.
	 *
	 * If a given class needs a special procedure to get an instance of it,
	 * please add a private get_instance_of_(lowercased_class_name) and it will be
	 * automatically invoked. See also how objects of classes having a static `instance`
	 * method are retrieved, similar approaches can be used as needed to make use
	 * of existing factory methods such as e.g. 'load'.
	 *
	 * @param string $class_name The name of the class to get an instance for.
	 * @param mixed  ...$args Parameters to be passed to the class constructor or to the appropriate internal 'get_instance_of_' method.
	 *
	 * @return object The instance of the class.
	 * @throws \Exception The requested class belongs to the `src` directory, or there was an error creating an instance of the class.
	 */
	public function get_instance_of( string $class_name, ...$args ) {
		if ( false !== strpos( $class_name, '\\' ) ) {
			throw new \Exception(
				'The LegacyProxy class is not intended for getting instances of classes in the src directory, please use ' .
				Definition::INJECTION_METHOD . ' method injection or the instance of ' . ContainerInterface::class . ' for that.'
			);
		}

		// If a class has a dedicated method to obtain a instance, use it.
		$method = 'get_instance_of_' . strtolower( $class_name );
		if ( method_exists( __CLASS__, $method ) ) {
			return $this->$method( ...$args );
		}

		// If the class is a singleton, use the "instance" method.
		if ( method_exists( $class_name, 'instance' ) ) {
			return $class_name::instance( ...$args );
		}

		// If the class has a "load" method, use it.
		if ( method_exists( $class_name, 'load' ) ) {
			return $class_name::load( ...$args );
		}

		// Fallback to simply creating a new instance of the class.
		return new $class_name( ...$args );
	}

	/**
	 * Get an instance of a class implementing WC_Queue_Interface.
	 *
	 * @return \WC_Queue_Interface The instance.
	 */
	private function get_instance_of_wc_queue_interface() {
		return \WC_Queue::instance();
	}

	/**
	 * Call a user function. This should be used to execute any non-idempotent function, especially
	 * those in the `includes` directory or provided by WordPress.
	 *
	 * @param string $function_name The function to execute.
	 * @param mixed  ...$parameters The parameters to pass to the function.
	 *
	 * @return mixed The result from the function.
	 */
	public function call_function( $function_name, ...$parameters ) {
		return call_user_func_array( $function_name, $parameters );
	}

	/**
	 * Call a static method in a class. This should be used to execute any non-idempotent method in classes
	 * from the `includes` directory.
	 *
	 * @param string $class_name The name of the class containing the method.
	 * @param string $method_name The name of the method.
	 * @param mixed  ...$parameters The parameters to pass to the method.
	 *
	 * @return mixed The result from the method.
	 */
	public function call_static( $class_name, $method_name, ...$parameters ) {
		return call_user_func_array( "$class_name::$method_name", $parameters );
	}

	/**
	 * Get the value of a global.
	 *
	 * @param string $global_name The name of the global to get the value for.
	 * @return mixed The value of the global.
	 */
	public function get_global( string $global_name ) {
		return $GLOBALS[ $global_name ];
	}
}
