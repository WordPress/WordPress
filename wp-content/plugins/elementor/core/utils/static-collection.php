<?php
namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Static_Collection {

	/**
	 * The current Collection instance.
	 *
	 * @var Collection
	 */
	protected $collection;

	/**
	 * Return only unique values.
	 *
	 * @var bool
	 */
	protected $unique_values = false;

	/**
	 * @inheritDoc
	 */
	public function __construct( array $items = [], $unique_values = false ) {
		$this->collection = new Collection( $items );
		$this->unique_values = $unique_values;
	}

	/**
	 * Since this class is a wrapper, every call will be forwarded to wrapped class.
	 * Most of the collection methods returns a new collection instance, and therefore
	 * it will be assigned as the current collection instance after executing any method.
	 *
	 * @param string $name
	 * @param array  $arguments
	 */
	public function __call( $name, $arguments ) {
		$call = call_user_func_array( [ $this->collection, $name ], $arguments );

		if ( $call instanceof Collection ) {
			$this->collection = $this->unique_values ?
				$call->unique() :
				$call;
		}

		return $call;
	}
}
