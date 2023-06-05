<?php
/**
 * Represents a product attribute
 *
 * Attributes can be global (taxonomy based) or local to the product itself.
 * Uses ArrayAccess to be BW compatible with previous ways of reading attributes.
 *
 * @package WooCommerce\Classes
 * @version 3.0.0
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product attribute class.
 */
class WC_Product_Attribute implements ArrayAccess {

	/**
	 * Data array.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	protected $data = array(
		'id'        => 0,
		'name'      => '',
		'options'   => array(),
		'position'  => 0,
		'visible'   => false,
		'variation' => false,
	);

	/**
	 * Return if this attribute is a taxonomy.
	 *
	 * @return boolean
	 */
	public function is_taxonomy() {
		return 0 < $this->get_id();
	}

	/**
	 * Get taxonomy name if applicable.
	 *
	 * @return string
	 */
	public function get_taxonomy() {
		return $this->is_taxonomy() ? $this->get_name() : '';
	}

	/**
	 * Get taxonomy object.
	 *
	 * @return array|null
	 */
	public function get_taxonomy_object() {
		global $wc_product_attributes;
		return $this->is_taxonomy() ? $wc_product_attributes[ $this->get_name() ] : null;
	}

	/**
	 * Gets terms from the stored options.
	 *
	 * @return array|null
	 */
	public function get_terms() {
		if ( ! $this->is_taxonomy() || ! taxonomy_exists( $this->get_name() ) ) {
			return null;
		}
		$terms = array();
		foreach ( $this->get_options() as $option ) {
			if ( is_int( $option ) ) {
				$term = get_term_by( 'id', $option, $this->get_name() );
			} else {
				// Term names get escaped in WP. See sanitize_term_field.
				$term = get_term_by( 'name', $option, $this->get_name() );

				if ( ! $term || is_wp_error( $term ) ) {
					$new_term = wp_insert_term( $option, $this->get_name() );
					$term     = is_wp_error( $new_term ) ? false : get_term_by( 'id', $new_term['term_id'], $this->get_name() );
				}
			}
			if ( $term && ! is_wp_error( $term ) ) {
				$terms[] = $term;
			}
		}
		return $terms;
	}

	/**
	 * Gets slugs from the stored options, or just the string if text based.
	 *
	 * @return array
	 */
	public function get_slugs() {
		if ( ! $this->is_taxonomy() || ! taxonomy_exists( $this->get_name() ) ) {
			return $this->get_options();
		}
		$terms = array();
		foreach ( $this->get_options() as $option ) {
			if ( is_int( $option ) ) {
				$term = get_term_by( 'id', $option, $this->get_name() );
			} else {
				$term = get_term_by( 'name', $option, $this->get_name() );

				if ( ! $term || is_wp_error( $term ) ) {
					$new_term = wp_insert_term( $option, $this->get_name() );
					$term     = is_wp_error( $new_term ) ? false : get_term_by( 'id', $new_term['term_id'], $this->get_name() );
				}
			}
			if ( $term && ! is_wp_error( $term ) ) {
				$terms[] = $term->slug;
			}
		}
		return $terms;
	}

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 */
	public function get_data() {
		return array_merge(
			$this->data,
			array(
				'is_visible'   => $this->get_visible() ? 1 : 0,
				'is_variation' => $this->get_variation() ? 1 : 0,
				'is_taxonomy'  => $this->is_taxonomy() ? 1 : 0,
				'value'        => $this->is_taxonomy() ? '' : wc_implode_text_attributes( $this->get_options() ),
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set ID (this is the attribute ID).
	 *
	 * @param int $value Attribute ID.
	 */
	public function set_id( $value ) {
		$this->data['id'] = absint( $value );
	}

	/**
	 * Set name (this is the attribute name or taxonomy).
	 *
	 * @param string $value Attribute name.
	 */
	public function set_name( $value ) {
		$this->data['name'] = $value;
	}

	/**
	 * Set options.
	 *
	 * @param array $value Attribute options.
	 */
	public function set_options( $value ) {
		$this->data['options'] = $value;
	}

	/**
	 * Set position.
	 *
	 * @param int $value Attribute position.
	 */
	public function set_position( $value ) {
		$this->data['position'] = absint( $value );
	}

	/**
	 * Set if visible.
	 *
	 * @param bool $value If is visible on Product's additional info tab.
	 */
	public function set_visible( $value ) {
		$this->data['visible'] = wc_string_to_bool( $value );
	}

	/**
	 * Set if variation.
	 *
	 * @param bool $value If is used for variations.
	 */
	public function set_variation( $value ) {
		$this->data['variation'] = wc_string_to_bool( $value );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->data['id'];
	}

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->data['name'];
	}

	/**
	 * Get options.
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->data['options'];
	}

	/**
	 * Get position.
	 *
	 * @return int
	 */
	public function get_position() {
		return $this->data['position'];
	}

	/**
	 * Get if visible.
	 *
	 * @return bool
	 */
	public function get_visible() {
		return $this->data['visible'];
	}

	/**
	 * Get if variation.
	 *
	 * @return bool
	 */
	public function get_variation() {
		return $this->data['variation'];
	}

	/*
	|--------------------------------------------------------------------------
	| ArrayAccess/Backwards compatibility.
	|--------------------------------------------------------------------------
	*/

	/**
	 * OffsetGet.
	 *
	 * @param string $offset Offset.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		switch ( $offset ) {
			case 'is_variation':
				return $this->get_variation() ? 1 : 0;
			case 'is_visible':
				return $this->get_visible() ? 1 : 0;
			case 'is_taxonomy':
				return $this->is_taxonomy() ? 1 : 0;
			case 'value':
				return $this->is_taxonomy() ? '' : wc_implode_text_attributes( $this->get_options() );
			default:
				if ( is_callable( array( $this, "get_$offset" ) ) ) {
					return $this->{"get_$offset"}();
				}
				break;
		}
		return '';
	}

	/**
	 * OffsetSet.
	 *
	 * @param string $offset Offset.
	 * @param mixed  $value  Value.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		switch ( $offset ) {
			case 'is_variation':
				$this->set_variation( $value );
				break;
			case 'is_visible':
				$this->set_visible( $value );
				break;
			case 'value':
				$this->set_options( $value );
				break;
			default:
				if ( is_callable( array( $this, "set_$offset" ) ) ) {
					$this->{"set_$offset"}( $value );
				}
				break;
		}
	}

	/**
	 * OffsetUnset.
	 *
	 * @param string $offset Offset.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {}

	/**
	 * OffsetExists.
	 *
	 * @param string $offset Offset.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return in_array( $offset, array_merge( array( 'is_variation', 'is_visible', 'is_taxonomy', 'value' ), array_keys( $this->data ) ), true );
	}
}
