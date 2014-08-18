<?php
/**
 * Order Reviews
 *
 * Functions for displaying the order reviews data meta box.
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Meta_Box_Order_Reviews
 */
class WC_Meta_Box_Order_Reviews {

	/**
	 * Output the metabox
	 */
	public static function output( $comment ) {
		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

		$current = get_comment_meta( $comment->comment_ID, 'rating', true );
		?>
		<select name="rating" id="rating">
			<?php for ( $rating = 0; $rating <= 5; $rating++ ) {
				echo sprintf( '<option value="%1$s"%2$s>%1$s</option>', $rating, selected( $current, $rating, false ) );
			} ?>
		</select>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $location, $comment_id ) {
		// Not allowed, return regular value without updating meta
		if ( ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) && ! isset( $_POST['rating'] ) ) {
			return $location;
		}

		// Update meta
		update_comment_meta(
			$comment_id,
			'rating',
			intval( $_POST['rating'] )
		);

		// Return regular value after updating
		return $location;
	}
}
