<?php
/**
 * Order Data
 *
 * Functions for displaying the order items meta box.
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce\Admin\Meta Boxes
 * @version     2.1.0
 */

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Meta_Box_Order_Items Class.
 */
class WC_Meta_Box_Order_Items {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post|WC_Order $post Post or order object.
	 */
	public static function output( $post ) {
		global $post, $thepostid, $theorder;

		OrderUtil::init_theorder_object( $post );
		if ( ! is_int( $thepostid ) && ( $post instanceof WP_Post ) ) {
			$thepostid = $post->ID;
		}

		$order = $theorder;
		$data  = ( $post instanceof WP_Post ) ? get_post_meta( $post->ID ) : array();

		include __DIR__ . '/views/html-order-items.php';
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $post_id
	 */
	public static function save( $post_id ) {
		/**
		 * This $_POST variable's data has been validated and escaped
		 * inside `wc_save_order_items()` function.
		 */
		wc_save_order_items( $post_id, $_POST );
	}
}
