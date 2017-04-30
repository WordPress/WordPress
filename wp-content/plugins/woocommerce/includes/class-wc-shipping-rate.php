<?php
/**
 * WooCommerce Shipping Rate Class
 *
 * Simple Class for storing rates.
 *
 * @class 		WC_Shipping_Rate
 * @version		2.0.0
 * @package		WooCommerce/Classes/Shipping
 * @category	Class
 * @author 		WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Shipping_Rate {

	var $id 		= '';
	var $label 		= '';
	var $cost 		= 0;
	var $taxes 		= array();
	var $method_id 	= '';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $id
	 * @param mixed $label
	 * @param mixed $cost
	 * @param mixed $taxes
	 * @return void
	 */
	public function __construct( $id, $label, $cost, $taxes, $method_id ) {
		$this->id 			= $id;
		$this->label 		= $label;
		$this->cost 		= $cost;
		$this->taxes 		= $taxes ? $taxes : array();
		$this->method_id 	= $method_id;
	}

	/**
	 * get_shipping_tax function.
	 *
	 * @access public
	 * @return array
	 */
	function get_shipping_tax() {
		$taxes = 0;
		if ( $this->taxes && sizeof( $this->taxes ) > 0 && ! WC()->customer->is_vat_exempt() ) {
			$taxes = array_sum( $this->taxes );
		}
		return $taxes;
	}
}