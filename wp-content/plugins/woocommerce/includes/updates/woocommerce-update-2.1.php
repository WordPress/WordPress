<?php
/**
 * Update WC to 2.1.0
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Updates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $woocommerce;

// Pages no longer used
wp_trash_post( get_option('woocommerce_pay_page_id') );
wp_trash_post( get_option('woocommerce_thanks_page_id') );
wp_trash_post( get_option('woocommerce_view_order_page_id') );
wp_trash_post( get_option('woocommerce_change_password_page_id') );
wp_trash_post( get_option('woocommerce_edit_address_page_id') );
wp_trash_post( get_option('woocommerce_lost_password_page_id') );

// Upgrade file paths to support multiple file paths + names etc
$existing_file_paths = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = '_file_paths' AND meta_value != '';" );

if ( $existing_file_paths ) {

	foreach( $existing_file_paths as $existing_file_path ) {

		$needs_update = false;
		$new_value    = array();
		$value        = maybe_unserialize( trim( $existing_file_path->meta_value ) );

		if ( $value ) {
			foreach ( $value as $key => $file ) {
				if ( ! is_array( $file ) ) {
					$needs_update      = true;
					$new_value[ $key ] = array(
						'file' => $file,
						'name' => wc_get_filename_from_url( $file )
					);
				} else {
					$new_value[ $key ] = $file;
				}
			}
			if ( $needs_update ) {
				$new_value = serialize( $new_value );

				$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_key = %s, meta_value = %s WHERE meta_id = %d", '_downloadable_files', $new_value, $existing_file_path->meta_id ) );
			}
		}
	}
}