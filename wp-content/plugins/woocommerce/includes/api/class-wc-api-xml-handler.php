<?php
/**
 * WooCommerce API
 *
 * Handles parsing XML request bodies and generating XML responses
 *
 * @author      WooThemes
 * @category    API
 * @package     WooCommerce/API
 * @since       2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_API_XML_Handler implements WC_API_Handler {

	/** @var XMLWriter instance */
	private $xml;

	/**
	 * Add some response filters
	 *
	 * @since 2.1
	 */
	public function __construct() {

		// tweak sales report response data
		add_filter( 'woocommerce_api_report_response', array( $this, 'format_sales_report_data' ), 100 );

		// tweak product response data
		add_filter( 'woocommerce_api_product_response', array( $this, 'format_product_data' ), 100 );
	}

	/**
	 * Get the content type for the response
	 *
	 * @since 2.1
	 * @return string
	 */
	public function get_content_type() {

		return 'application/xml; charset=' . get_option( 'blog_charset' );
	}

	/**
	 * Parse the raw request body entity
	 *
	 * @since 2.1
	 * @param string $data the raw request body
	 * @return array
	 */
	public function parse_body( $data ) {

		// TODO: implement simpleXML parsing
	}

	/**
	 * Generate an XML response given an array of data
	 *
	 * @since 2.1
	 * @param array $data the response data
	 * @return string
	 */
	public function generate_response( $data ) {

		$this->xml = new XMLWriter();

		$this->xml->openMemory();

		$this->xml->setIndent(true);

		$this->xml->startDocument( '1.0', 'UTF-8' );

		$root_element = key( $data );

		$data = $data[ $root_element ];

		switch ( $root_element ) {

			case 'orders':
				$data = array( 'order' => $data );
				break;

			case 'order_notes':
				$data = array( 'order_note' => $data );
				break;

			case 'customers':
				$data = array( 'customer' => $data );
				break;

			case 'coupons':
				$data = array( 'coupon' => $data );
				break;

			case 'products':
				$data = array( 'product' => $data );
				break;

			case 'product_reviews':
				$data = array( 'product_review' => $data );
				break;

			default:
				$data = apply_filters( 'woocommerce_api_xml_data', $data, $root_element );
				break;
		}

		// generate xml starting with the root element and recursively generating child elements
		$this->array_to_xml( $root_element, $data );

		$this->xml->endDocument();

		return $this->xml->outputMemory();
	}

	/**
	 * Convert array into XML by recursively generating child elements
	 *
	 * @since 2.1
	 * @param string|array $element_key - name for element, e.g. <OrderID>
	 * @param string|array $element_value - value for element, e.g. 1234
	 * @return string - generated XML
	 */
	private function array_to_xml( $element_key, $element_value = array() ) {

		if ( is_array( $element_value ) ) {

			// handle attributes
			if ( '@attributes' === $element_key ) {
				foreach ( $element_value as $attribute_key => $attribute_value ) {

					$this->xml->startAttribute( $attribute_key );
					$this->xml->text( $attribute_value );
					$this->xml->endAttribute();
				}
				return;
			}

			// handle multi-elements (e.g. multiple <Order> elements)
			if ( is_numeric( key( $element_value ) ) ) {

				// recursively generate child elements
				foreach ( $element_value as $child_element_key => $child_element_value ) {

					$this->xml->startElement( $element_key );

					foreach ( $child_element_value as $sibling_element_key => $sibling_element_value ) {
						$this->array_to_xml( $sibling_element_key, $sibling_element_value );
					}

					$this->xml->endElement();
				}

			} else {

				// start root element
				$this->xml->startElement( $element_key );

				// recursively generate child elements
				foreach ( $element_value as $child_element_key => $child_element_value ) {
					$this->array_to_xml( $child_element_key, $child_element_value );
				}

				// end root element
				$this->xml->endElement();
			}

		} else {

			// handle single elements
			if ( '@value' == $element_key ) {

				$this->xml->text( $element_value );

			} else {

				// wrap element in CDATA tags if it contains illegal characters
				if ( false !== strpos( $element_value, '<' ) || false !== strpos( $element_value, '>' ) ) {

					$this->xml->startElement( $element_key );
					$this->xml->writeCdata( $element_value );
					$this->xml->endElement();

				} else {

					$this->xml->writeElement( $element_key, $element_value );
				}

			}

			return;
		}
	}

	/**
	 * Adjust the sales report array format to change totals keyed with the sales date to become an
	 * attribute for the totals element instead
	 *
	 * @since 2.1
	 * @param array $data
	 * @return array
	 */
	public function format_sales_report_data( $data ) {

		if ( ! empty( $data['totals'] ) ) {

			foreach ( $data['totals'] as $date => $totals ) {

				unset( $data['totals'][ $date ] );

				$data['totals'][] = array_merge( array( '@attributes' => array( 'date' => $date ) ), $totals );
			}
		}

		return $data;
	}

	/**
	 * Adjust the product data to handle options for attributes without a named child element and other
	 * fields that have no named child elements (e.g. categories = array( 'cat1', 'cat2' ) )
	 *
	 * Note that the parent product data for variations is also adjusted in the same manner as needed
	 *
	 * @since 2.1
	 * @param array $data
	 * @return array
	 */
	public function format_product_data( $data ) {

		// handle attribute values
		if ( ! empty( $data['attributes'] ) ) {

			foreach ( $data['attributes'] as $attribute_key => $attribute ) {

				if ( ! empty( $attribute['options'] ) && is_array( $attribute['options'] ) ) {

					foreach ( $attribute['options'] as $option_key => $option ) {

						unset( $data['attributes'][ $attribute_key ]['options'][ $option_key ] );

						$data['attributes'][ $attribute_key ]['options']['option'][] = array( $option );
					}
				}
			}
		}

		// simple arrays are fine for JSON, but XML requires a child element name, so this adjusts the data
		// array to define a child element name for each field
		$fields_to_fix = array(
			'related_ids'    => 'related_id',
			'upsell_ids'     => 'upsell_id',
			'cross_sell_ids' => 'cross_sell_id',
			'categories'     => 'category',
			'tags'           => 'tag'
		);

		foreach ( $fields_to_fix as $parent_field_name => $child_field_name ) {

			if ( ! empty( $data[ $parent_field_name ] ) ) {

				foreach ( $data[ $parent_field_name ] as $field_key => $field ) {

					unset( $data[ $parent_field_name ][ $field_key ] );

					$data[ $parent_field_name ][ $child_field_name ][] = array( $field );
				}
			}
		}

		// handle adjusting the parent product for variations
		if ( ! empty( $data['parent'] ) ) {

			// attributes
			if ( ! empty( $data['parent']['attributes'] ) ) {

				foreach ( $data['parent']['attributes'] as $attribute_key => $attribute ) {

					if ( ! empty( $attribute['options'] ) && is_array( $attribute['options'] ) ) {

						foreach ( $attribute['options'] as $option_key => $option ) {

							unset( $data['parent']['attributes'][ $attribute_key ]['options'][ $option_key ] );

							$data['parent']['attributes'][ $attribute_key ]['options']['option'][] = array( $option );
						}
					}
				}
			}

			// fields
			foreach ( $fields_to_fix as $parent_field_name => $child_field_name ) {

				if ( ! empty( $data['parent'][ $parent_field_name ] ) ) {

					foreach ( $data['parent'][ $parent_field_name ] as $field_key => $field ) {

						unset( $data['parent'][ $parent_field_name ][ $field_key ] );

						$data['parent'][ $parent_field_name ][ $child_field_name ][] = array( $field );
					}
				}
			}
		}

		return $data;
	}

}
