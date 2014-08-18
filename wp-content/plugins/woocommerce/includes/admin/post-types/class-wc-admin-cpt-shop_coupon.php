<?php
/**
 * Admin functions for the shop_coupon post type.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Post Types
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_CPT' ) )
	include( 'class-wc-admin-cpt.php' );

if ( ! class_exists( 'WC_Admin_CPT_Shop_Coupon' ) ) :

/**
 * WC_Admin_CPT_Shop_Coupon Class
 */
class WC_Admin_CPT_Shop_Coupon extends WC_Admin_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->type = 'shop_coupon';

		// Post title fields
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_action( 'edit_form_after_title', array( $this, 'coupon_description_field' ) );

		// Admin Columns
		add_filter( 'manage_edit-shop_coupon_columns', array( $this, 'edit_columns' ) );
		add_action( 'manage_shop_coupon_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'request', array( $this, 'coupons_by_type_query' ) );

		// Product filtering
		add_action( 'restrict_manage_posts', array( $this, 'coupon_filters' ) );

		// Call WC_Admin_CPT constructor
		parent::__construct();
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		if ( $post->post_type == 'shop_coupon' )
			return __( 'Coupon code', 'woocommerce' );

		return $text;
	}

	/**
	 * Print coupon description textarea field
	 * @param WP_Post $post
	 */
	public function coupon_description_field( $post ) {
		if ( $post->post_type != 'shop_coupon' )
			return;
		?>
		<textarea id="woocommerce-coupon-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'woocommerce' ); ?>"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
		<?php
	}

	/**
	 * Change the columns shown in admin.
	 */
	public function edit_columns( $columns ) {
		$columns = array();

		$columns["cb"] 			= "<input type=\"checkbox\" />";
		$columns["coupon_code"] = __( 'Code', 'woocommerce' );
		$columns["type"] 		= __( 'Coupon type', 'woocommerce' );
		$columns["amount"] 		= __( 'Coupon amount', 'woocommerce' );
		$columns["description"] = __( 'Description', 'woocommerce' );
		$columns["products"]	= __( 'Product IDs', 'woocommerce' );
		$columns["usage"] 		= __( 'Usage / Limit', 'woocommerce' );
		$columns["expiry_date"] = __( 'Expiry date', 'woocommerce' );

		return $columns;
	}

	/**
	 * Define our custom columns shown in admin.
	 * @param  string $column
	 */
	public function custom_columns( $column ) {
		global $post, $woocommerce;

		switch ( $column ) {
			case "coupon_code" :
				$edit_link = get_edit_post_link( $post->ID );
				$title = _draft_or_post_title();
				$post_type_object = get_post_type_object( $post->post_type );
				$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );

				echo '<div class="code tips" data-tip="' . __( 'Edit coupon', 'woocommerce' ) . '"><a href="' . esc_attr( $edit_link ) . '"><span>' . esc_html( $title ). '</span></a></div>';

				_post_states( $post );

				// Get actions
				$actions = array();

				if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
					if ( 'trash' == $post->post_status )
						$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'woocommerce' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore', 'woocommerce' ) . "</a>";
					elseif ( EMPTY_TRASH_DAYS )
						$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'woocommerce' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', 'woocommerce' ) . "</a>";
					if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
						$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'woocommerce' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently', 'woocommerce' ) . "</a>";
				}

				$actions = apply_filters( 'post_row_actions', $actions, $post );

				echo '<div class="row-actions">';

				$i = 0;
				$action_count = sizeof($actions);

				foreach ( $actions as $action => $link ) {
					++$i;
					( $i == $action_count ) ? $sep = '' : $sep = ' | ';
					echo "<span class='$action'>$link$sep</span>";
				}
				echo '</div>';

			break;
			case "type" :
				echo esc_html( wc_get_coupon_type( get_post_meta( $post->ID, 'discount_type', true ) ) );
			break;
			case "amount" :
				echo esc_html( get_post_meta( $post->ID, 'coupon_amount', true ) );
			break;
			case "products" :
				$product_ids = get_post_meta( $post->ID, 'product_ids', true );
				$product_ids = $product_ids ? array_map( 'absint', explode( ',', $product_ids ) ) : array();
				if ( sizeof( $product_ids ) > 0 )
					echo esc_html( implode( ', ', $product_ids ) );
				else
					echo '&ndash;';
			break;
			case "usage_limit" :
				$usage_limit = get_post_meta( $post->ID, 'usage_limit', true );

				if ( $usage_limit )
					echo esc_html( $usage_limit );
				else
					echo '&ndash;';
			break;
			case "usage" :
				$usage_count = absint( get_post_meta( $post->ID, 'usage_count', true ) );
				$usage_limit = esc_html( get_post_meta($post->ID, 'usage_limit', true) );

				if ( $usage_limit )
					printf( __( '%s / %s', 'woocommerce' ), $usage_count, $usage_limit );
				else
					printf( __( '%s / &infin;', 'woocommerce' ), $usage_count );
			break;
			case "expiry_date" :
				$expiry_date = get_post_meta($post->ID, 'expiry_date', true);

				if ( $expiry_date )
					echo esc_html( date_i18n( 'F j, Y', strtotime( $expiry_date ) ) );
				else
					echo '&ndash;';
			break;
			case "description" :
				echo wp_kses_post( $post->post_excerpt );
			break;
		}
	}

	/**
	 * Filter the coupons by the type.
	 *
	 * @param array $vars
	 * @return array
	 */
	public function coupons_by_type_query( $vars ) {
		global $typenow, $wp_query;
	    if ( $typenow == 'shop_coupon' && ! empty( $_GET['coupon_type'] ) ) {

			$vars['meta_key'] = 'discount_type';
			$vars['meta_value'] = wc_clean( $_GET['coupon_type'] );

		}

		return $vars;
	}

	/**
	 * Show custom filters to filter coupons by type.
	 */
	public function coupon_filters() {
		global $woocommerce, $typenow, $wp_query;

		if ( $typenow != 'shop_coupon' )
			return;

		// Type
		?>
		<select name='coupon_type' id='dropdown_shop_coupon_type'>
			<option value=""><?php _e( 'Show all types', 'woocommerce' ); ?></option>
			<?php
				$types = wc_get_coupon_types();

				foreach ( $types as $name => $type ) {
					echo '<option value="' . esc_attr( $name ) . '"';

					if ( isset( $_GET['coupon_type'] ) )
						selected( $name, $_GET['coupon_type'] );

					echo '>' . esc_html__( $type, 'woocommerce' ) . '</option>';
				}
			?>
			</select>
		<?php

		wc_enqueue_js( "
			jQuery('select#dropdown_shop_coupon_type, select[name=m]').css('width', '150px').chosen();
		" );
	}
}

endif;

return new WC_Admin_CPT_Shop_Coupon();