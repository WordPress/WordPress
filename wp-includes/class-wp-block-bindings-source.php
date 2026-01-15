<?php
/**
 * Block Bindings API: WP_Block_Bindings_Source class.
 *
 * @package WordPress
 * @subpackage Block Bindings
 * @since 6.5.0
 */

/**
 * Class representing block bindings source.
 *
 * This class is designed for internal use by the Block Bindings registry.
 *
 * @since 6.5.0
 * @access private
 *
 * @see WP_Block_Bindings_Registry
 */
final class WP_Block_Bindings_Source {

	/**
	 * The name of the source.
	 *
	 * @since 6.5.0
	 * @var string
	 */
	public $name;

	/**
	 * The label of the source.
	 *
	 * @since 6.5.0
	 * @var string
	 */
	public $label;

	/**
	 * The function used to get the value from the source.
	 *
	 * @since 6.5.0
	 * @var callable
	 */
	private $get_value_callback;

	/**
	 * The context added to the blocks needed by the source.
	 *
	 * @since 6.5.0
	 * @var string[]|null
	 */
	public $uses_context = null;

	/**
	 * Constructor.
	 *
	 * Do not use this constructor directly. Instead, use the
	 * `WP_Block_Bindings_Registry::register` method or the `register_block_bindings_source` function.
	 *
	 * @since 6.5.0
	 *
	 * @param string $name              The name of the source.
	 * @param array  $source_properties The properties of the source.
	 */
	public function __construct( string $name, array $source_properties ) {
		$this->name = $name;
		foreach ( $source_properties as $property_name => $property_value ) {
			$this->$property_name = $property_value;
		}
	}

	/**
	 * Calls the callback function specified in the `$get_value_callback` property
	 * with the given arguments and returns the result. It can be modified with
	 * `block_bindings_source_value` filter.
	 *
	 * @since 6.5.0
	 * @since 6.7.0 `block_bindings_source_value` filter was added.
	 *
	 * @param array    $source_args    Array containing source arguments used to look up the override value, i.e. {"key": "foo"}.
	 * @param WP_Block $block_instance The block instance.
	 * @param string   $attribute_name The name of the target attribute.
	 * @return mixed The value of the source.
	 */
	public function get_value( array $source_args, $block_instance, string $attribute_name ) {
		$value = call_user_func_array( $this->get_value_callback, array( $source_args, $block_instance, $attribute_name ) );
		/**
		 * Filters the output of a block bindings source.
		 *
		 * @since 6.7.0
		 *
		 * @param mixed    $value          The computed value for the source.
		 * @param string   $name           The name of the source.
		 * @param array    $source_args    Array containing source arguments used to look up the override value, i.e. { "key": "foo" }.
		 * @param WP_Block $block_instance The block instance.
		 * @param string   $attribute_name The name of an attribute.
		 */
		return apply_filters( 'block_bindings_source_value', $value, $this->name, $source_args, $block_instance, $attribute_name );
	}

	/**
	 * Wakeup magic method.
	 *
	 * @since 6.5.0
	 */
	public function __wakeup() {
		throw new \LogicException( __CLASS__ . ' should never be unserialized' );
	}
}
