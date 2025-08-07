<?php
/**
 * Blocks API: WP_Block_List class
 *
 * @package WordPress
 * @since 5.5.0
 */

/**
 * Class representing a list of block instances.
 *
 * @since 5.5.0
 */
#[AllowDynamicProperties]
class WP_Block_List implements Iterator, ArrayAccess, Countable {

	/**
	 * Original array of parsed block data, or block instances.
	 *
	 * @since 5.5.0
	 * @var array[]|WP_Block[]
	 */
	protected $blocks;

	/**
	 * All available context of the current hierarchy.
	 *
	 * @since 5.5.0
	 * @var array
	 */
	protected $available_context;

	/**
	 * Block type registry to use in constructing block instances.
	 *
	 * @since 5.5.0
	 * @var WP_Block_Type_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * Populates object properties from the provided block instance argument.
	 *
	 * @since 5.5.0
	 *
	 * @param array[]|WP_Block[]     $blocks            Array of parsed block data, or block instances.
	 * @param array                  $available_context Optional array of ancestry context values.
	 * @param WP_Block_Type_Registry $registry          Optional block type registry.
	 */
	public function __construct( $blocks, $available_context = array(), $registry = null ) {
		if ( ! $registry instanceof WP_Block_Type_Registry ) {
			$registry = WP_Block_Type_Registry::get_instance();
		}

		$this->blocks            = $blocks;
		$this->available_context = $available_context;
		$this->registry          = $registry;
	}

	/**
	 * Returns true if a block exists by the specified block offset, or false
	 * otherwise.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param int $offset Offset of block to check for.
	 * @return bool Whether block exists.
	 */
	#[ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->blocks[ $offset ] );
	}

	/**
	 * Returns the value by the specified block offset.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param int $offset Offset of block value to retrieve.
	 * @return WP_Block|null Block value if exists, or null.
	 */
	#[ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		$block = $this->blocks[ $offset ];

		if ( isset( $block ) && is_array( $block ) ) {
			$block = new WP_Block( $block, $this->available_context, $this->registry );

			$this->blocks[ $offset ] = $block;
		}

		return $block;
	}

	/**
	 * Assign a block value by the specified block offset.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param int            $offset Offset of block value to set.
	 * @param array|WP_Block $value  Block value.
	 */
	#[ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->blocks[] = $value;
		} else {
			$this->blocks[ $offset ] = $value;
		}
	}

	/**
	 * Unset a block.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param int $offset Offset of block value to unset.
	 */
	#[ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->blocks[ $offset ] );
	}

	/**
	 * Rewinds back to the first element of the Iterator.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/iterator.rewind.php
	 */
	#[ReturnTypeWillChange]
	public function rewind() {
		reset( $this->blocks );
	}

	/**
	 * Returns the current element of the block list.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/iterator.current.php
	 *
	 * @return WP_Block|null Current element.
	 */
	#[ReturnTypeWillChange]
	public function current() {
		return $this->offsetGet( $this->key() );
	}

	/**
	 * Returns the key of the current element of the block list.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/iterator.key.php
	 *
	 * @return int|null Key of the current element.
	 */
	#[ReturnTypeWillChange]
	public function key() {
		return key( $this->blocks );
	}

	/**
	 * Moves the current position of the block list to the next element.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/iterator.next.php
	 */
	#[ReturnTypeWillChange]
	public function next() {
		next( $this->blocks );
	}

	/**
	 * Checks if current position is valid.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/iterator.valid.php
	 */
	#[ReturnTypeWillChange]
	public function valid() {
		return null !== key( $this->blocks );
	}

	/**
	 * Returns the count of blocks in the list.
	 *
	 * @since 5.5.0
	 *
	 * @link https://www.php.net/manual/en/countable.count.php
	 *
	 * @return int Block count.
	 */
	#[ReturnTypeWillChange]
	public function count() {
		return count( $this->blocks );
	}
}
