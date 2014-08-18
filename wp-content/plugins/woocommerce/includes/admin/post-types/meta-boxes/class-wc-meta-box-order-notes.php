<?php
/**
 * Order Notes
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Meta_Box_Order_Notes
 */
class WC_Meta_Box_Order_Notes {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $woocommerce, $post;

		$args = array(
			'post_id' 	=> $post->ID,
			'approve' 	=> 'approve',
			'type' 		=> 'order_note'
		);

		remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

		echo '<ul class="order_notes">';

		if ( $notes ) {
			foreach( $notes as $note ) {
				$note_classes = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? array( 'customer-note', 'note' ) : array( 'note' );

				?>
				<li rel="<?php echo absint( $note->comment_ID ) ; ?>" class="<?php echo implode( ' ', $note_classes ); ?>">
					<div class="note_content">
						<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
					</div>
					<p class="meta">
						<abbr class="exact-date" title="<?php echo $note->comment_date_gmt; ?> GMT"><?php printf( __( 'added %s ago', 'woocommerce' ), human_time_diff( strtotime( $note->comment_date_gmt ), current_time( 'timestamp', 1 ) ) ); ?></abbr>
						<?php if ( $note->comment_author !== __( 'WooCommerce', 'woocommerce' ) ) printf( ' ' . __( 'by %s', 'woocommerce' ), $note->comment_author ); ?>
						<a href="#" class="delete_note"><?php _e( 'Delete note', 'woocommerce' ); ?></a>
					</p>
				</li>
				<?php
			}
		} else {
			echo '<li>' . __( 'There are no notes for this order yet.', 'woocommerce' ) . '</li>';
		}

		echo '</ul>';
		?>
		<div class="add_note">
			<h4><?php _e( 'Add note', 'woocommerce' ); ?> <img class="help_tip" data-tip='<?php esc_attr_e( 'Add a note for your reference, or add a customer note (the user will be notified).', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></h4>
			<p>
				<textarea type="text" name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
			</p>
			<p>
				<select name="order_note_type" id="order_note_type">
					<option value="customer"><?php _e( 'Customer note', 'woocommerce' ); ?></option>
					<option value=""><?php _e( 'Private note', 'woocommerce' ); ?></option>
				</select>
				<a href="#" class="add_note button"><?php _e( 'Add', 'woocommerce' ); ?></a>
			</p>
		</div>
		<?php
	}
}