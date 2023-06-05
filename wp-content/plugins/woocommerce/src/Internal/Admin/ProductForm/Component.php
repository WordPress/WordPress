<?php
/**
 * Abstract class for product form components.
 */

namespace Automattic\WooCommerce\Internal\Admin\ProductForm;

/**
 * Component class.
 */
abstract class Component {
	/**
	 * Product Component traits.
	 */
	use ComponentTrait;

	/**
	 * Component additional arguments.
	 *
	 * @var array
	 */
	protected $additional_args;

	/**
	 * Constructor
	 *
	 * @param string $id Component id.
	 * @param string $plugin_id Plugin id.
	 * @param array  $additional_args Array containing additional arguments.
	 */
	public function __construct( $id, $plugin_id, $additional_args ) {
		$this->id              = $id;
		$this->plugin_id       = $plugin_id;
		$this->additional_args = $additional_args;
	}

	/**
	 * Component arguments.
	 *
	 * @return array
	 */
	public function get_additional_args() {
		return $this->additional_args;
	}

	/**
	 * Component arguments.
	 *
	 * @param string $key key of argument.
	 * @return mixed
	 */
	public function get_additional_argument( $key ) {
		return self::get_argument_from_path( $this->additional_args, $key );
	}

	/**
	 * Get the component as JSON.
	 *
	 * @return array
	 */
	public function get_json() {
		return array_merge(
			array(
				'id'        => $this->get_id(),
				'plugin_id' => $this->get_plugin_id(),
			),
			$this->get_additional_args()
		);
	}

	/**
	 * Sorting function for product form component.
	 *
	 * @param Component $a Component a.
	 * @param Component $b Component b.
	 * @param array     $sort_by key and order to sort by.
	 * @return int
	 */
	public static function sort( $a, $b, $sort_by = array() ) {
		$key   = $sort_by['key'];
		$a_val = $a->get_additional_argument( $key );
		$b_val = $b->get_additional_argument( $key );
		if ( 'asc' === $sort_by['order'] ) {
			return $a_val <=> $b_val;
		} else {
			return $b_val <=> $a_val;
		}
	}

	/**
	 * Gets argument by dot notation path.
	 *
	 * @param array  $arguments Arguments array.
	 * @param string $path Path for argument key.
	 * @param string $delimiter Path delimiter, default: '.'.
	 * @return mixed|null
	 */
	public static function get_argument_from_path( $arguments, $path, $delimiter = '.' ) {
		$path_keys = explode( $delimiter, $path );
		$num_keys  = count( $path_keys );

		$val = $arguments;
		for ( $i = 0; $i < $num_keys; $i++ ) {
			$key = $path_keys[ $i ];
			if ( array_key_exists( $key, $val ) ) {
				$val = $val[ $key ];
			} else {
				$val = null;
				break;
			}
		}
		return $val;
	}

	/**
	 * Array of required arguments.
	 *
	 * @var array
	 */
	protected $required_arguments = array();

	/**
	 * Get missing arguments of args array.
	 *
	 * @param array $args field arguments.
	 * @return array
	 */
	public function get_missing_arguments( $args ) {
		return array_values(
			array_filter(
				$this->required_arguments,
				function( $arg_key ) use ( $args ) {
					return null === self::get_argument_from_path( $args, $arg_key );
				}
			)
		);
	}
}
