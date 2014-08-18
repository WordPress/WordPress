<?php
/**
 * WooCommerce Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types)
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Admin_Meta_Boxes
 */
class WC_Admin_Meta_Boxes {

	private static $meta_box_errors = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'add_meta_boxes', array( 'WC_Gateway_Mijireh', 'add_page_slurp_meta' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		/**
		 * Save Order Meta Boxes
		 *
		 * In order:
		 * 		Save the order items
		 * 		Save the order totals
		 * 		Save the order downloads
		 * 		Save order data - also updates status and sends out admin emails if needed. Last to show latest data.
		 * 		Save actions - sends out other emails. Last to show latest data.
		 */
		add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Items::save', 10, 2 );
		add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Totals::save', 20, 2 );
		add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Downloads::save', 30, 2 );
		add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Data::save', 40, 2 );
		add_action( 'woocommerce_process_shop_order_meta', 'WC_Meta_Box_Order_Actions::save', 50, 2 );

		// Save Product Meta Boxes
		add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Data::save', 10, 2 );
		add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Images::save', 20, 2 );

		// Save Coupon Meta Boxes
		add_action( 'woocommerce_process_shop_coupon_meta', 'WC_Meta_Box_Coupon_Data::save', 10, 2 );

		// Save Rating Meta Boxes
		add_action( 'comment_edit_redirect',  'WC_Meta_Box_Order_Reviews::save', 1, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option
	 */
	public function save_errors() {
		update_option( 'woocommerce_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'woocommerce_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error fade">';
			foreach ( $errors as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';

			// Clear
			delete_option( 'woocommerce_meta_box_errors' );
		}
	}

	/**
	 * Add WC Meta boxes
	 */
	public function add_meta_boxes() {
		// Products
		add_meta_box( 'postexcerpt', __( 'Product Short Description', 'woocommerce' ), 'WC_Meta_Box_Product_Short_Description::output', 'product', 'normal' );
		add_meta_box( 'woocommerce-product-data', __( 'Product Data', 'woocommerce' ), 'WC_Meta_Box_Product_Data::output', 'product', 'normal', 'high' );
		add_meta_box( 'woocommerce-product-images', __( 'Product Gallery', 'woocommerce' ), 'WC_Meta_Box_Product_Images::output', 'product', 'side' );

		// Orders
		add_meta_box( 'woocommerce-order-data', __( 'Order Data', 'woocommerce' ), 'WC_Meta_Box_Order_Data::output', 'shop_order', 'normal', 'high' );
		add_meta_box( 'woocommerce-order-items', __( 'Order Items', 'woocommerce' ), 'WC_Meta_Box_Order_Items::output', 'shop_order', 'normal', 'high' );
		add_meta_box( 'woocommerce-order-totals', __( 'Order Totals', 'woocommerce' ), 'WC_Meta_Box_Order_Totals::output', 'shop_order', 'side', 'default' );
		add_meta_box( 'woocommerce-order-notes', __( 'Order Notes', 'woocommerce' ), 'WC_Meta_Box_Order_Notes::output', 'shop_order', 'side', 'default' );
		add_meta_box( 'woocommerce-order-downloads', __( 'Downloadable Product Permissions', 'woocommerce' ) . ' <span class="tips" data-tip="' . __( 'Note: Permissions for order items will automatically be granted when the order status changes to processing/completed.', 'woocommerce' ) . '">[?]</span>', 'WC_Meta_Box_Order_Downloads::output', 'shop_order', 'normal', 'default' );
		add_meta_box( 'woocommerce-order-actions', __( 'Order Actions', 'woocommerce' ), 'WC_Meta_Box_Order_Actions::output', 'shop_order', 'side', 'high' );

		// Coupons
		add_meta_box( 'woocommerce-coupon-data', __( 'Coupon Data', 'woocommerce' ), 'WC_Meta_Box_Coupon_Data::output', 'shop_coupon', 'normal', 'high' );

		// Reviews
		if ( 'comment' == get_current_screen()->id && isset( $_GET['c'] ) ) {
			if ( get_comment_meta( intval( $_GET['c'] ), 'rating', true ) ) {
				add_meta_box( 'woocommerce-rating', __( 'Rating', 'woocommerce' ), 'WC_Meta_Box_Order_Reviews::output', 'comment', 'normal', 'high' );
			}
		}
	}

	/**
	 * Remove bloat
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		remove_meta_box( 'product_shipping_classdiv', 'product', 'side' );
		remove_meta_box( 'pageparentdiv', 'product', 'side' );
		remove_meta_box( 'commentstatusdiv', 'product', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'product', 'side' );
		remove_meta_box( 'woothemes-settings', 'shop_coupon' , 'normal' );
		remove_meta_box( 'commentstatusdiv', 'shop_coupon' , 'normal' );
		remove_meta_box( 'slugdiv', 'shop_coupon' , 'normal' );
		remove_meta_box( 'commentsdiv', 'shop_order' , 'normal' );
		remove_meta_box( 'woothemes-settings', 'shop_order' , 'normal' );
		remove_meta_box( 'commentstatusdiv', 'shop_order' , 'normal' );
		remove_meta_box( 'slugdiv', 'shop_order' , 'normal' );
	}

	/**
	 * Rename core meta boxes
	 */
	public function rename_meta_boxes() {
		global $post;

		// Comments/Reviews
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) ) {
			remove_meta_box( 'commentsdiv', 'product', 'normal' );

			add_meta_box( 'commentsdiv', __( 'Reviews', 'woocommerce' ), 'post_comment_meta_box', 'product', 'normal' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		
		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return;
		} 

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check the post type
		if ( ! in_array( $post->post_type, array( 'product', 'shop_order', 'shop_coupon' ) ) ) {
			return;
		}

		do_action( 'woocommerce_process_' . $post->post_type . '_meta', $post_id, $post );
	}

}

new WC_Admin_Meta_Boxes();
