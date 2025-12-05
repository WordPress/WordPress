<?php
/**
 * Inspired by Laravel Collection.
 *
 * @link https://github.com/illuminate/collections
 * @package Elementor\Core\Utils
 */

namespace Elementor\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Inspired by Laravel Collection.
 *
 * @link https://github.com/illuminate/collections
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {
	/**
	 * The items contained in the collection.
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Collection constructor.
	 *
	 * @param array $items
	 */
	public function __construct( array $items = [] ) {
		$this->items = $items;
	}

	/**
	 * @param array $items
	 *
	 * @return static
	 */
	public static function make( array $items = [] ) {
		return new static( $items );
	}

	/**
	 * @param callable|null $callback
	 *
	 * @return $this
	 */
	public function filter( ?callable $callback = null ) {
		if ( ! $callback ) {
			return new static( array_filter( $this->items ) );
		}

		return new static( array_filter( $this->items, $callback, ARRAY_FILTER_USE_BOTH ) );
	}

	/**
	 * @param $items
	 *
	 * @return $this
	 */
	public function merge( $items ) {
		if ( $items instanceof Collection ) {
			$items = $items->all();
		}

		return new static( array_merge( $this->items, $items ) );
	}

	/**
	 * Union the collection with the given items.
	 *
	 * @param array $items
	 *
	 * @return $this
	 */
	public function union( array $items ) {
		return new static( $this->all() + $items );
	}

	/**
	 * Merge array recursively
	 *
	 * @param $items
	 *
	 * @return $this
	 */
	public function merge_recursive( $items ) {
		if ( $items instanceof Collection ) {
			$items = $items->all();
		}

		return new static( array_merge_recursive( $this->items, $items ) );
	}

	/**
	 * Replace array recursively
	 *
	 * @param $items
	 *
	 * @return $this
	 */
	public function replace_recursive( $items ) {
		if ( $items instanceof Collection ) {
			$items = $items->all();
		}

		return new static( array_replace_recursive( $this->items, $items ) );
	}

	/**
	 * Implode the items
	 *
	 * @param $glue
	 *
	 * @return string
	 */
	public function implode( $glue ) {
		return implode( $glue, $this->items );
	}

	/**
	 * Run a map over each of the items.
	 *
	 * @param  callable $callback
	 * @return $this
	 */
	public function map( callable $callback ) {
		$keys = array_keys( $this->items );

		$items = array_map( $callback, $this->items, $keys );

		return new static( array_combine( $keys, $items ) );
	}

	/**
	 * Run a callback over each of the items.
	 *
	 * @param callable $callback
	 * @return $this
	 */
	public function each( callable $callback ) {
		foreach ( $this->items as $key => $value ) {
			if ( false === $callback( $value, $key ) ) {
				break;
			}
		}

		return $this;
	}

	/**
	 * @param callable $callback
	 * @param null     $initial
	 *
	 * @return mixed|null
	 */
	public function reduce( callable $callback, $initial = null ) {
		$result = $initial;

		foreach ( $this->all() as $key => $value ) {
			$result = $callback( $result, $value, $key );
		}

		return $result;
	}

	public function reverse() {
		return new static( array_reverse( $this->items ) );
	}

	/**
	 * @param callable $callback
	 *
	 * @return $this
	 */
	public function map_with_keys( callable $callback ) {
		$result = [];

		foreach ( $this->items as $key => $value ) {
			$assoc = $callback( $value, $key );

			foreach ( $assoc as $map_key => $map_value ) {
				$result[ $map_key ] = $map_value;
			}
		}

		return new static( $result );
	}

	/**
	 * Get all items except for those with the specified keys.
	 *
	 * @param array $keys
	 *
	 * @return $this
	 */
	public function except( array $keys ) {
		return $this->filter( function ( $value, $key ) use ( $keys ) {
			return ! in_array( $key, $keys, true );
		} );
	}

	/**
	 * Get the items with the specified keys.
	 *
	 * @param array $keys
	 *
	 * @return $this
	 */
	public function only( array $keys ) {
		return $this->filter( function ( $value, $key ) use ( $keys ) {
			return in_array( $key, $keys, true );
		} );
	}

	/**
	 * Run over the collection to get specific prop from the collection item.
	 *
	 * @param $key
	 *
	 * @return $this
	 */
	public function pluck( $key ) {
		$result = [];

		foreach ( $this->items as $item ) {
			$result[] = $this->get_item_value( $item, $key );
		}

		return new static( $result );
	}

	/**
	 * Group the collection items by specific key in each collection item.
	 *
	 * @param $group_by
	 *
	 * @return $this
	 */
	public function group_by( $group_by ) {
		$result = [];

		foreach ( $this->items as $item ) {
			$group_key = $this->get_item_value( $item, $group_by, 0 );

			$result[ $group_key ][] = $item;
		}

		return new static( $result );
	}

	/**
	 * Sort keys
	 *
	 * @param false $descending
	 *
	 * @return $this
	 */
	public function sort_keys( $descending = false ) {
		$items = $this->items;

		if ( $descending ) {
			krsort( $items );
		} else {
			ksort( $items );
		}

		return new static( $items );
	}

	/**
	 * Get specific item from the collection.
	 *
	 * @param      $key
	 * @param null $fallback
	 *
	 * @return mixed|null
	 */
	public function get( $key, $fallback = null ) {
		if ( ! array_key_exists( $key, $this->items ) ) {
			return $fallback;
		}

		return $this->items[ $key ];
	}

	/**
	 * Get the first item.
	 *
	 * @param null $fallback
	 *
	 * @return mixed|null
	 */
	public function first( $fallback = null ) {
		if ( $this->is_empty() ) {
			return $fallback;
		}

		foreach ( $this->items as $item ) {
			return $item;
		}
	}

	/**
	 * Find an element from the items.
	 *
	 * @param callable $callback
	 * @param null     $fallback
	 *
	 * @return mixed|null
	 */
	public function find( callable $callback, $fallback = null ) {
		foreach ( $this->all() as $key => $item ) {
			if ( $callback( $item, $key ) ) {
				return $item;
			}
		}

		return $fallback;
	}

	/**
	 * @param callable|string|int $value
	 *
	 * @return bool
	 */
	public function contains( $value ) {
		$callback = $value instanceof \Closure
			? $value
			: function ( $item ) use ( $value ) {
				return $item === $value;
			};

		foreach ( $this->all() as $key => $item ) {
			if ( $callback( $item, $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Run array_diff between the collection and other array or collection.
	 *
	 * @param $filter
	 *
	 * @return $this
	 */
	public function diff( $filter ) {
		if ( $filter instanceof self ) {
			$filter = $filter->all();
		}

		return new static( array_diff( $this->all(), $filter ) );
	}

	/**
	 * Make sure all the values inside the array are uniques.
	 *
	 * @param null|string|string[] $keys
	 *
	 * @return $this
	 */
	public function unique( $keys = null ) {
		if ( ! $keys ) {
			return new static(
				array_unique( $this->items )
			);
		}

		if ( ! is_array( $keys ) ) {
			$keys = [ $keys ];
		}

		$exists = [];

		return $this->filter( function ( $item ) use ( $keys, &$exists ) {
			$value = null;

			foreach ( $keys as $key ) {
				$current_value = $this->get_item_value( $item, $key );

				$value .= "{$key}:{$current_value};";
			}

			// If no value for the specific key return the item.
			if ( null === $value ) {
				return true;
			}

			// If value is not exists, add to the exists array and return the item.
			if ( ! in_array( $value, $exists, true ) ) {
				$exists[] = $value;

				return true;
			}

			return false;
		} );
	}

	public function keys() {
		return new static( array_keys( $this->items ) );
	}

	/**
	 * @return bool
	 */
	public function is_empty() {
		return empty( $this->items );
	}

	/**
	 * @return array
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * @return array
	 */
	public function values() {
		return array_values( $this->all() );
	}

	/**
	 * Support only one level depth.
	 *
	 * @return $this
	 */
	public function flatten() {
		$result = [];

		foreach ( $this->all() as $item ) {
			$item = $item instanceof Collection ? $item->all() : $item;

			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = array_values( $item );

				foreach ( $values as $value ) {
					$result[] = $value;
				}
			}
		}

		return new static( $result );
	}

	/**
	 * @param array ...$values
	 *
	 * @return $this
	 */
	public function push( ...$values ) {
		foreach ( $values as $value ) {
			$this->items[] = $value;
		}

		return $this;
	}

	public function prepend( ...$values ) {
		$this->items = array_merge( $values, $this->items );

		return $this;
	}

	public function some( callable $callback ) {
		foreach ( $this->items as $key => $item ) {
			if ( $callback( $item, $key ) ) {
				return true;
			}
		}

		return false;
	}

	public function every( callable $callback ) {
		foreach ( $this->items as $key => $item ) {
			if ( ! $callback( $item, $key ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->items[ $offset ] );
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->items[ $offset ];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->items[] = $value;
		} else {
			$this->items[ $offset ] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->items[ $offset ] );
	}

	/**
	 * @return \ArrayIterator|\Traversable
	 */
	#[\ReturnTypeWillChange]
	public function getIterator() {
		return new \ArrayIterator( $this->items );
	}

	/**
	 * @return int|void
	 */
	#[\ReturnTypeWillChange]
	public function count() {
		return count( $this->items );
	}

	/**
	 * @param      $item
	 * @param      $key
	 * @param null $fallback
	 *
	 * @return mixed|null
	 */
	private function get_item_value( $item, $key, $fallback = null ) {
		$value = $fallback;

		if ( is_object( $item ) && isset( $item->{$key} ) ) {
			$value = $item->{$key};
		} elseif ( is_array( $item ) && isset( $item[ $key ] ) ) {
			$value = $item[ $key ];
		}

		return $value;
	}
}
