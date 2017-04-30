<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Report_Stock' ) )
	require_once( 'class-wc-report-stock.php' );

/**
 * WC_Report_Out_Of_Stock
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin/Reports
 * @version     2.1.0
 */
class WC_Report_Out_Of_Stock extends WC_Report_Stock {

    /**
     * No items found text
     */
    public function no_items() {
        _e( 'No out of stock products found.', 'woocommerce' );
    }

    /**
     * Get Products matching stock criteria
     *
     * @access public
     */
    public function get_items( $current_page, $per_page ) {
        global $wpdb;

        $this->max_items = 0;
        $this->items     = array();

        // Get products using a query - this is too advanced for get_posts :(
        $stock = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );

        $query_from = "FROM {$wpdb->posts} as posts
            INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
            INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
            WHERE 1=1
                AND posts.post_type IN ('product', 'product_variation')
                AND posts.post_status = 'publish'
                AND (
                    postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}' AND postmeta.meta_value != ''
                )
                AND (
                    ( postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes' ) OR ( posts.post_type = 'product_variation' )
                )
            ";

        $this->items     = $wpdb->get_results( $wpdb->prepare( "SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY posts.post_title DESC LIMIT %d, %d;", ( $current_page - 1 ) * $per_page, $per_page ) );
        $this->max_items = $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" );
    }
}