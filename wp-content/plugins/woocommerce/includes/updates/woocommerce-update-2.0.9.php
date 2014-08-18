<?php
/**
 * Update WC to 2.0.9
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Updates
 * @version     2.0.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $woocommerce;

// Update brazillian state codes
$wpdb->update(
	$wpdb->postmeta,
	array(
		'meta_value' => 'BA'
	),
	array(
		'meta_key'   => '_billing_state',
		'meta_value' => 'BH'
	)
);
$wpdb->update(
	$wpdb->postmeta,
	array(
		'meta_value' => 'BA'
	),
	array(
		'meta_key'   => '_shipping_state',
		'meta_value' => 'BH'
	)
);
$wpdb->update(
	$wpdb->usermeta,
	array(
		'meta_value' => 'BA'
	),
	array(
		'meta_key'   => 'billing_state',
		'meta_value' => 'BH'
	)
);
$wpdb->update(
	$wpdb->usermeta,
	array(
		'meta_value' => 'BA'
	),
	array(
		'meta_key'   => 'shipping_state',
		'meta_value' => 'BH'
	)
);