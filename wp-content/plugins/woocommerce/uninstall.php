<?php
/**
 * WooCommerce Uninstall
 *
 * Uninstalling WooCommerce deletes user roles, pages, tables, and options.
 *
 * @package WooCommerce\Uninstaller
 * @version 2.3.0
 */

use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\ReportsSync;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

wp_clear_scheduled_hook( 'woocommerce_scheduled_sales' );
wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );
wp_clear_scheduled_hook( 'woocommerce_cleanup_sessions' );
wp_clear_scheduled_hook( 'woocommerce_cleanup_personal_data' );
wp_clear_scheduled_hook( 'woocommerce_cleanup_logs' );
wp_clear_scheduled_hook( 'woocommerce_geoip_updater' );
wp_clear_scheduled_hook( 'woocommerce_tracker_send_event' );
wp_clear_scheduled_hook( 'woocommerce_cleanup_rate_limits' );
wp_clear_scheduled_hook( 'wc_admin_daily' );
wp_clear_scheduled_hook( 'generate_category_lookup_table' );
wp_clear_scheduled_hook( 'wc_admin_unsnooze_admin_notes' );

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	// Load WooCommerce so we can access the container, install routines, etc, during uninstall.
	require_once __DIR__ . '/woocommerce.php';

	// Roles + caps.
	WC_Install::remove_roles();

	// Pages.
	wp_trash_post( get_option( 'woocommerce_shop_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_cart_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_checkout_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_myaccount_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_edit_address_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_view_order_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_change_password_page_id' ) );
	wp_trash_post( get_option( 'woocommerce_logout_page_id' ) );

	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_attribute_taxonomies';" ) ) {
		$wc_attributes = array_filter( (array) $wpdb->get_col( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies;" ) );
	} else {
		$wc_attributes = array();
	}

	// Tables.
	WC_Install::drop_tables();

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'woocommerce\_%';" );
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'widget\_woocommerce\_%';" );

	// Delete usermeta.
	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'woocommerce\_%';" );

	// Delete our data from the post and post meta tables, and remove any additional tables we created.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation', 'shop_coupon', 'shop_order', 'shop_order_refund' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

	$wpdb->query( "DELETE FROM {$wpdb->comments} WHERE comment_type IN ( 'order_note' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->commentmeta} meta LEFT JOIN {$wpdb->comments} comments ON comments.comment_ID = meta.comment_id WHERE comments.comment_ID IS NULL;" );

	foreach ( wc_get_container()->get( OrdersTableDataStore::class )->get_all_table_names() as $cot_table ) {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DROP TABLE IF EXISTS {$cot_table}" );
	}

	// Delete terms if > WP 4.2 (term splitting was added in 4.2).
	if ( version_compare( $wp_version, '4.2', '>=' ) ) {
		// Delete term taxonomies.
		foreach ( array( 'product_cat', 'product_tag', 'product_shipping_class', 'product_type' ) as $_taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => $_taxonomy,
				)
			);
		}

		// Delete term attributes.
		foreach ( $wc_attributes as $_taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => 'pa_' . $_taxonomy,
				)
			);
		}

		// Delete orphan relationships.
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

		// Delete orphan terms.
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;" );

		// Delete orphan term meta.
		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;" );
		}
	}

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
