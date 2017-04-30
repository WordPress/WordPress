<?php
/**
 * WooCommerce Attribute Functions
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get attribute taxonomies.
 *
 * @return object
 */
function wc_get_attribute_taxonomies() {

	$transient_name = 'wc_attribute_taxonomies';

	if ( false === ( $attribute_taxonomies = get_transient( $transient_name ) ) ) {

		global $wpdb;

		$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies" );

		set_transient( $transient_name, $attribute_taxonomies );
	}

	return apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );
}

/**
 * Get a product attributes name.
 *
 * @param mixed $name
 * @return string
 */
function wc_attribute_taxonomy_name( $name ) {
	return 'pa_' . wc_sanitize_taxonomy_name( $name );
}

/**
 * Get a product attributes label.
 *
 * @param mixed $name
 * @return string
 */
function wc_attribute_label( $name ) {
	global $wpdb;

	if ( taxonomy_is_product_attribute( $name ) ) {
		$name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $name ) );

		$label = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $name ) );

		if ( ! $label ) {
			$label = ucfirst( $name );
		}
	} else {
		$label = $name;
	}

	return apply_filters( 'woocommerce_attribute_label', $label, $name );
}

/**
 * Get a product attributes orderby setting.
 *
 * @param mixed $name
 * @return string
 */
function wc_attribute_orderby( $name ) {
	global $wpdb;

	$name = str_replace( 'pa_', '', sanitize_title( $name ) );

	$orderby = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_orderby FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $name ) );

	return apply_filters( 'woocommerce_attribute_orderby', $orderby, $name );
}

/**
 * Get an array of product attribute taxonomies.
 *
 * @access public
 * @return array
 */
function wc_get_attribute_taxonomy_names() {
	$taxonomy_names = array();
	$attribute_taxonomies = wc_get_attribute_taxonomies();
	if ( $attribute_taxonomies ) {
		foreach ( $attribute_taxonomies as $tax ) {
			$taxonomy_names[] = wc_attribute_taxonomy_name( $tax->attribute_name );
		}
	}
	return $taxonomy_names;
}
