<?php
/**
 * Blocks API: WP_Block_Patterns_Registry class
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 5.5.0
 */

/**
 * Class used for interacting with patterns.
 *
 * @since 5.5.0
 */
final class WP_Block_Patterns_Registry {
	/**
	 * Registered patterns array.
	 *
	 * @var array
	 */
	private $registered_patterns = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @var WP_Block_Patterns_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a pattern.
	 *
	 * @since 5.5.0
	 *
	 * @param string $pattern_name       Pattern name including namespace.
	 * @param array  $pattern_properties Array containing the properties of the pattern: title,
	 *                                   content, description, viewportWidth, categories, keywords.
	 * @return bool True if the pattern was registered with success and false otherwise.
	 */
	public function register( $pattern_name, $pattern_properties ) {
		if ( ! isset( $pattern_name ) || ! is_string( $pattern_name ) ) {
			_doing_it_wrong( __METHOD__, __( 'Pattern name must be a string.' ), '5.5.0' );
			return false;
		}

		if ( ! isset( $pattern_properties['title'] ) || ! is_string( $pattern_properties['title'] ) ) {
			_doing_it_wrong( __METHOD__, __( 'Pattern title must be a string.' ), '5.5.0' );
			return false;
		}

		if ( ! isset( $pattern_properties['content'] ) || ! is_string( $pattern_properties['content'] ) ) {
			_doing_it_wrong( __METHOD__, __( 'Pattern content must be a string.' ), '5.5.0' );
			return false;
		}

		$this->registered_patterns[ $pattern_name ] = array_merge(
			$pattern_properties,
			array( 'name' => $pattern_name )
		);

		return true;
	}

	/**
	 * Unregisters a pattern.
	 *
	 * @since 5.5.0
	 *
	 * @param string $pattern_name Pattern name including namespace.
	 * @return bool True if the pattern was unregistered with success and false otherwise.
	 */
	public function unregister( $pattern_name ) {
		if ( ! $this->is_registered( $pattern_name ) ) {
			/* translators: 1: Pattern name. */
			$message = sprintf( __( 'Pattern "%1$s" not found.' ), $pattern_name );
			_doing_it_wrong( __METHOD__, $message, '5.5.0' );
			return false;
		}

		unset( $this->registered_patterns[ $pattern_name ] );

		return true;
	}

	/**
	 * Retrieves an array containing the properties of a registered pattern.
	 *
	 * @since 5.5.0
	 *
	 * @param string $pattern_name Pattern name including namespace.
	 * @return array Registered pattern properties.
	 */
	public function get_registered( $pattern_name ) {
		if ( ! $this->is_registered( $pattern_name ) ) {
			return null;
		}

		return $this->registered_patterns[ $pattern_name ];
	}

	/**
	 * Retrieves all registered patterns.
	 *
	 * @since 5.5.0
	 *
	 * @return array Array of arrays containing the registered patterns properties,
	 *               and per style.
	 */
	public function get_all_registered() {
		return array_values( $this->registered_patterns );
	}

	/**
	 * Checks if a pattern is registered.
	 *
	 * @since 5.5.0
	 *
	 * @param string $pattern_name Pattern name including namespace.
	 * @return bool True if the pattern is registered, false otherwise.
	 */
	public function is_registered( $pattern_name ) {
		return isset( $this->registered_patterns[ $pattern_name ] );
	}

	public function __wakeup() {
		if ( ! $this->registered_patterns ) {
			return;
		}
		if ( ! is_array( $this->registered_patterns ) ) {
			throw new UnexpectedValueException();
		}
		foreach ( $this->registered_patterns as $value ) {
			if ( ! is_array( $value ) ) {
				throw new UnexpectedValueException();
			}
		}
		$this->registered_patterns_outside_init = array();
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 5.5.0
	 *
	 * @return WP_Block_Patterns_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

/**
 * Registers a new pattern.
 *
 * @since 5.5.0
 *
 * @param string $pattern_name       Pattern name including namespace.
 * @param array  $pattern_properties Array containing the properties of the pattern.
 * @return bool True if the pattern was registered with success and false otherwise.
 */
function register_block_pattern( $pattern_name, $pattern_properties ) {
	return WP_Block_Patterns_Registry::get_instance()->register( $pattern_name, $pattern_properties );
}

/**
 * Unregisters a pattern.
 *
 * @since 5.5.0
 *
 * @param string $pattern_name       Pattern name including namespace.
 * @return bool True if the pattern was unregistered with success and false otherwise.
 */
function unregister_block_pattern( $pattern_name ) {
	return WP_Block_Patterns_Registry::get_instance()->unregister( $pattern_name );
}
