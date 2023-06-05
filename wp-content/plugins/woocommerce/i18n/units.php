<?php
/**
 * Units
 *
 * Returns a multidimensional array of measurement units and their labels.
 * Unit labels should be defined in English and translated native through localization files.
 *
 * @package WooCommerce\i18n
 * @version
 */

defined( 'ABSPATH' ) || exit;

return array(
	'weight'     => array(
		'kg'  => __( 'kg', 'woocommerce' ),
		'g'   => __( 'g', 'woocommerce' ),
		'lbs' => __( 'lbs', 'woocommerce' ),
		'oz'  => __( 'oz', 'woocommerce' ),
	),
	'dimensions' => array(
		'm'  => __( 'm', 'woocommerce' ),
		'cm' => __( 'cm', 'woocommerce' ),
		'mm' => __( 'mm', 'woocommerce' ),
		'in' => __( 'in', 'woocommerce' ),
		'yd' => __( 'yd', 'woocommerce' ),
	),
);
