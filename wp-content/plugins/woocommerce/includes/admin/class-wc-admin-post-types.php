<?php
/**
 * Post Types Admin
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Post_Types' ) ) :

/**
 * WC_Admin_Post_Types Class
 */
class WC_Admin_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'include_post_type_handlers' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'admin_print_scripts', array( $this, 'disable_autosave' ) );

		// Status transitions
		add_action( 'delete_post', array( $this, 'delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );
	}

	/**
	 * Conditonally load classes and functions only needed when viewing a post type.
	 */
	public function include_post_type_handlers() {
		include( 'post-types/class-wc-admin-meta-boxes.php' );
		include( 'post-types/class-wc-admin-cpt-product.php' );
		include( 'post-types/class-wc-admin-cpt-shop_order.php' );
		include( 'post-types/class-wc-admin-cpt-shop_coupon.php' );

		if ( ! function_exists( 'duplicate_post_plugin_activation' ) )
			include( 'class-wc-admin-duplicate-product.php' );
	}

	/**
	 * Change messages when a post type is updated.
	 *
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['product'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Product updated. <a href="%s">View Product</a>', 'woocommerce' ), esc_url( get_permalink($post_ID) ) ),
			2 => __( 'Custom field updated.', 'woocommerce' ),
			3 => __( 'Custom field deleted.', 'woocommerce' ),
			4 => __( 'Product updated.', 'woocommerce' ),
			5 => isset($_GET['revision']) ? sprintf( __( 'Product restored to revision from %s', 'woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Product published. <a href="%s">View Product</a>', 'woocommerce' ), esc_url( get_permalink($post_ID) ) ),
			7 => __( 'Product saved.', 'woocommerce' ),
			8 => sprintf( __( 'Product submitted. <a target="_blank" href="%s">Preview Product</a>', 'woocommerce' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __( 'Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Product</a>', 'woocommerce' ),
			  date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __( 'Product draft updated. <a target="_blank" href="%s">Preview Product</a>', 'woocommerce' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);

		$messages['shop_order'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Order updated.', 'woocommerce' ),
			2 => __( 'Custom field updated.', 'woocommerce' ),
			3 => __( 'Custom field deleted.', 'woocommerce' ),
			4 => __( 'Order updated.', 'woocommerce' ),
			5 => isset($_GET['revision']) ? sprintf( __( 'Order restored to revision from %s', 'woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Order updated.', 'woocommerce' ),
			7 => __( 'Order saved.', 'woocommerce' ),
			8 => __( 'Order submitted.', 'woocommerce' ),
			9 => sprintf( __( 'Order scheduled for: <strong>%1$s</strong>.', 'woocommerce' ),
			  date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Order draft updated.', 'woocommerce' )
		);

		$messages['shop_coupon'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __( 'Coupon updated.', 'woocommerce' ),
			2 => __( 'Custom field updated.', 'woocommerce' ),
			3 => __( 'Custom field deleted.', 'woocommerce' ),
			4 => __( 'Coupon updated.', 'woocommerce' ),
			5 => isset($_GET['revision']) ? sprintf( __( 'Coupon restored to revision from %s', 'woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __( 'Coupon updated.', 'woocommerce' ),
			7 => __( 'Coupon saved.', 'woocommerce' ),
			8 => __( 'Coupon submitted.', 'woocommerce' ),
			9 => sprintf( __( 'Coupon scheduled for: <strong>%1$s</strong>.', 'woocommerce' ),
			  date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Coupon draft updated.', 'woocommerce' )
		);

		return $messages;
	}

	/**
	 * Disable the auto-save functionality for Orders.
	 *
	 * @access public
	 * @return void
	 */
	public function disable_autosave(){
	    global $post;

	    if ( $post && get_post_type( $post->ID ) === 'shop_order' ) {
	        wp_dequeue_script( 'autosave' );
	    }
	}

	/**
	 * Removes variations etc belonging to a deleted post, and clears transients
	 *
	 * @access public
	 * @param mixed $id ID of post being deleted
	 * @return void
	 */
	public function delete_post( $id ) {
		global $woocommerce, $wpdb;

		if ( ! current_user_can( 'delete_posts' ) )
			return;

		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			switch( $post_type ) {
				case 'product' :

					$child_product_variations = get_children( 'post_parent=' . $id . '&post_type=product_variation' );

					if ( $child_product_variations ) {
						foreach ( $child_product_variations as $child ) {
							wp_delete_post( $child->ID, true );
						}
					}

					$child_products = get_children( 'post_parent=' . $id . '&post_type=product' );

					if ( $child_products ) {
						foreach ( $child_products as $child ) {
							$child_post = array();
							$child_post['ID'] = $child->ID;
							$child_post['post_parent'] = 0;
							wp_update_post( $child_post );
						}
					}

					wc_delete_product_transients();

				break;
				case 'product_variation' :

					wc_delete_product_transients();

				break;
			}
		}
	}

	/**
	 * woocommerce_trash_post function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function trash_post( $id ) {
		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			if ( 'shop_order' == $post_type ) {

				// Delete count - meta doesn't work on trashed posts
				$user_id = get_post_meta( $id, '_customer_user', true );

				if ( $user_id > 0 ) {
					update_user_meta( $user_id, '_order_count', '' );
					update_user_meta( $user_id, '_money_spent', '' );
				}

				delete_transient( 'woocommerce_processing_order_count' );
				delete_transient( 'wc_term_counts' );
			}

		}
	}

	/**
	 * woocommerce_untrash_post function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function untrash_post( $id ) {
		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			if ( 'shop_order' == $post_type ) {

				// Delete count - meta doesn't work on trashed posts
				$user_id = get_post_meta( $id, '_customer_user', true );

				if ( $user_id > 0 ) {
					update_user_meta( $user_id, '_order_count', '' );
					update_user_meta( $user_id, '_money_spent', '' );
				}

				delete_transient( 'woocommerce_processing_order_count' );
				delete_transient( 'wc_term_counts' );
			}

		}
	}
}

endif;

return new WC_Admin_Post_Types();