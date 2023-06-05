<?php
/**
 * A constants manager for Jetpack.
 *
 * @package automattic/jetpack-constants
 */

namespace Automattic\Jetpack;

/**
 * Class Automattic\Jetpack\Constants
 *
 * Testing constants is hard. Once you define a constant, it's defined. Constants Manager is an
 * abstraction layer so that unit tests can set "constants" for tests.
 *
 * To test your code, you'll need to swap out `defined( 'CONSTANT' )` with `Automattic\Jetpack\Constants::is_defined( 'CONSTANT' )`
 * and replace `CONSTANT` with `Automattic\Jetpack\Constants::get_constant( 'CONSTANT' )`. Then in the unit test, you can set the
 * constant with `Automattic\Jetpack\Constants::set_constant( 'CONSTANT', $value )` and then clean up after each test with something like
 * this:
 *
 * function tearDown() {
 *     Automattic\Jetpack\Constants::clear_constants();
 * }
 */
class Constants {
	/**
	 * A container for all defined constants.
	 *
	 * @access public
	 * @static
	 *
	 * @var array.
	 */
	public static $set_constants = array();

	/**
	 * Checks if a "constant" has been set in constants Manager
	 * and has the value of true
	 *
	 * @param string $name The name of the constant.
	 *
	 * @return bool
	 */
	public static function is_true( $name ) {
		return self::is_defined( $name ) && self::get_constant( $name );
	}

	/**
	 * Checks if a "constant" has been set in constants Manager, and if not,
	 * checks if the constant was defined with define( 'name', 'value ).
	 *
	 * @param string $name The name of the constant.
	 *
	 * @return bool
	 */
	public static function is_defined( $name ) {
		return array_key_exists( $name, self::$set_constants )
			? true
			: defined( $name );
	}

	/**
	 * Attempts to retrieve the "constant" from constants Manager, and if it hasn't been set,
	 * then attempts to get the constant with the constant() function. If that also hasn't
	 * been set, attempts to get a value from filters.
	 *
	 * @param string $name The name of the constant.
	 *
	 * @return mixed null if the constant does not exist or the value of the constant.
	 */
	public static function get_constant( $name ) {
		if ( array_key_exists( $name, self::$set_constants ) ) {
			return self::$set_constants[ $name ];
		}

		if ( defined( $name ) ) {
			return constant( $name );
		}

		/**
		 * Filters the value of the constant.
		 *
		 * @since 8.5.0
		 *
		 * @param null The constant value to be filtered. The default is null.
		 * @param String $name The constant name.
		 */
		return apply_filters( 'jetpack_constant_default_value', null, $name );
	}

	/**
	 * Sets the value of the "constant" within constants Manager.
	 *
	 * @param string $name The name of the constant.
	 * @param string $value The value of the constant.
	 */
	public static function set_constant( $name, $value ) {
		self::$set_constants[ $name ] = $value;
	}

	/**
	 * Will unset a "constant" from constants Manager if the constant exists.
	 *
	 * @param string $name The name of the constant.
	 *
	 * @return bool Whether the constant was removed.
	 */
	public static function clear_single_constant( $name ) {
		if ( ! array_key_exists( $name, self::$set_constants ) ) {
			return false;
		}

		unset( self::$set_constants[ $name ] );

		return true;
	}

	/**
	 * Resets all of the constants within constants Manager.
	 */
	public static function clear_constants() {
		self::$set_constants = array();
	}
}
