<?php
/**
 * Order Downloads
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Meta_Box_Order_Downloads
 */
class WC_Meta_Box_Order_Downloads {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $woocommerce, $post, $wpdb;
		?>
		<div class="order_download_permissions wc-metaboxes-wrapper">

			<div class="wc-metaboxes">
				<?php
					$download_permissions = $wpdb->get_results( $wpdb->prepare( "
						SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
						WHERE order_id = %d ORDER BY product_id
					", $post->ID ) );

					$product = null;
					$loop    = 0;
					if ( $download_permissions && sizeof( $download_permissions ) > 0 ) foreach ( $download_permissions as $download ) {

						if ( ! $product || $product->id != $download->product_id ) {
							$product    = get_product( absint( $download->product_id ) );
							$file_counter = 1;
						}

						// don't show permissions to files that have since been removed
						if ( ! $product || ! $product->exists() || ! $product->has_file( $download->download_id ) )
							continue;

						// Show file title instead of count if set
						$file = $product->get_file( $download->download_id );
						if ( isset( $file['name'] ) ) {
							$file_count = $file['name'];
						} else {
							$file_count = sprintf( __( 'File %d', 'woocommerce' ), $file_counter );
						}

						include( 'views/html-order-download-permission.php' );

						$loop++;
						$file_counter++;
					}
				?>
			</div>

			<div class="toolbar">
				<p class="buttons">
					<select name="grant_access_id" id="grant_access_id" class="ajax_chosen_select_downloadable_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a downloadable product&hellip;', 'woocommerce' ) ?>" style="width: 400px"></select>
					<button type="button" class="button grant_access"><?php _e( 'Grant Access', 'woocommerce' ); ?></button>
				</p>
				<div class="clear"></div>
			</div>

		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		global $wpdb, $woocommerce;

		if ( isset( $_POST['download_id'] ) ) {

			// Download data
			$download_ids			= $_POST['download_id'];
			$product_ids			= $_POST['product_id'];
			$downloads_remaining 	= $_POST['downloads_remaining'];
			$access_expires 		= $_POST['access_expires'];

			// Order data
			$order_key 				= get_post_meta( $post->ID, '_order_key', true );
			$customer_email 		= get_post_meta( $post->ID, '_billing_email', true );
			$customer_user 			= get_post_meta( $post->ID, '_customer_user', true );
			$product_ids_count 		= sizeof( $product_ids );

			for ( $i = 0; $i < $product_ids_count; $i ++ ) {
				if ( ! isset( $product_ids[ $i ] ) )
					continue;

	            $data = array(
					'user_id'				=> absint( $customer_user ),
					'user_email' 			=> wc_clean( $customer_email ),
					'downloads_remaining'	=> wc_clean( $downloads_remaining[ $i ] )
	            );

	            $format = array( '%d', '%s', '%s' );

	            $expiry  = ( array_key_exists( $i, $access_expires ) && $access_expires[ $i ] != '' ) ? date_i18n( 'Y-m-d', strtotime( $access_expires[ $i ] ) ) : null;

	            if ( ! is_null( $expiry ) ) {
					$data['access_expires'] = $expiry;
					$format[]               = '%s';
	            }

	            $wpdb->update( $wpdb->prefix . "woocommerce_downloadable_product_permissions",
				    $data,
	                array(
						'order_id' 		=> $post_id,
						'product_id' 	=> absint( $product_ids[ $i ] ),
						'download_id'	=> wc_clean( $download_ids[ $i ] )
						),
					$format, array( '%d', '%d', '%s' )
				);

			}
		}
	}
}