<?php
/**
 * Coupon Data
 *
 * Display the coupon data meta box.
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce\Admin\Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Meta_Box_Coupon_Data Class.
 */
class WC_Meta_Box_Coupon_Data {

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {
		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

		$coupon_id = absint( $post->ID );
		$coupon    = new WC_Coupon( $coupon_id );

		?>

		<style type="text/css">
			#edit-slug-box, #minor-publishing-actions { display:none }
		</style>
		<div id="coupon_options" class="panel-wrap coupon_data">

			<div class="wc-tabs-back"></div>

			<ul class="coupon_data_tabs wc-tabs" style="display:none;">
				<?php
				$coupon_data_tabs = apply_filters(
					'woocommerce_coupon_data_tabs',
					array(
						'general'           => array(
							'label'  => __( 'General', 'woocommerce' ),
							'target' => 'general_coupon_data',
							'class'  => 'general_coupon_data',
						),
						'usage_restriction' => array(
							'label'  => __( 'Usage restriction', 'woocommerce' ),
							'target' => 'usage_restriction_coupon_data',
							'class'  => '',
						),
						'usage_limit'       => array(
							'label'  => __( 'Usage limits', 'woocommerce' ),
							'target' => 'usage_limit_coupon_data',
							'class'  => '',
						),
					)
				);

				foreach ( $coupon_data_tabs as $key => $tab ) :
					?>
					<li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ', (array) $tab['class'] ); ?>">
						<a href="#<?php echo $tab['target']; ?>">
							<span><?php echo esc_html( $tab['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div id="general_coupon_data" class="panel woocommerce_options_panel">
				<?php

				// Type.
				woocommerce_wp_select(
					array(
						'id'      => 'discount_type',
						'label'   => __( 'Discount type', 'woocommerce' ),
						'options' => wc_get_coupon_types(),
						'value'   => $coupon->get_discount_type( 'edit' ),
					)
				);

				// Amount.
				woocommerce_wp_text_input(
					array(
						'id'          => 'coupon_amount',
						'label'       => __( 'Coupon amount', 'woocommerce' ),
						'placeholder' => wc_format_localized_price( 0 ),
						'description' => __( 'Value of the coupon.', 'woocommerce' ),
						'data_type'   => 'percent' === $coupon->get_discount_type( 'edit' ) ? 'decimal' : 'price',
						'desc_tip'    => true,
						'value'       => $coupon->get_amount( 'edit' ),
					)
				);

				// Free Shipping.
				if ( wc_shipping_enabled() ) {
					woocommerce_wp_checkbox(
						array(
							'id'          => 'free_shipping',
							'label'       => __( 'Allow free shipping', 'woocommerce' ),
							'description' => sprintf( __( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'woocommerce' ), 'https://docs.woocommerce.com/document/free-shipping/' ),
							'value'       => wc_bool_to_string( $coupon->get_free_shipping( 'edit' ) ),
						)
					);
				}

				// Expiry date.
				$expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( 'Y-m-d' ) : '';
				woocommerce_wp_text_input(
					array(
						'id'                => 'expiry_date',
						'value'             => esc_attr( $expiry_date ),
						'label'             => __( 'Coupon expiry date', 'woocommerce' ),
						'placeholder'       => 'YYYY-MM-DD',
						'description'       => __( 'The coupon will expire at 00:00:00 of this date.', 'woocommerce' ),
						'desc_tip'          => true,
						'class'             => 'date-picker',
						'custom_attributes' => array(
							'pattern' => apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ),
						),
					)
				);

				do_action( 'woocommerce_coupon_options', $coupon->get_id(), $coupon );

				?>
			</div>
			<div id="usage_restriction_coupon_data" class="panel woocommerce_options_panel">
				<?php

				echo '<div class="options_group">';

				// minimum spend.
				woocommerce_wp_text_input(
					array(
						'id'          => 'minimum_amount',
						'label'       => __( 'Minimum spend', 'woocommerce' ),
						'placeholder' => __( 'No minimum', 'woocommerce' ),
						'description' => __( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'woocommerce' ),
						'data_type'   => 'price',
						'desc_tip'    => true,
						'value'       => $coupon->get_minimum_amount( 'edit' ),
					)
				);

				// maximum spend.
				woocommerce_wp_text_input(
					array(
						'id'          => 'maximum_amount',
						'label'       => __( 'Maximum spend', 'woocommerce' ),
						'placeholder' => __( 'No maximum', 'woocommerce' ),
						'description' => __( 'This field allows you to set the maximum spend (subtotal) allowed when using the coupon.', 'woocommerce' ),
						'data_type'   => 'price',
						'desc_tip'    => true,
						'value'       => $coupon->get_maximum_amount( 'edit' ),
					)
				);

				// Individual use.
				woocommerce_wp_checkbox(
					array(
						'id'          => 'individual_use',
						'label'       => __( 'Individual use only', 'woocommerce' ),
						'description' => __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'woocommerce' ),
						'value'       => wc_bool_to_string( $coupon->get_individual_use( 'edit' ) ),
					)
				);

				// Exclude Sale Products.
				woocommerce_wp_checkbox(
					array(
						'id'          => 'exclude_sale_items',
						'label'       => __( 'Exclude sale items', 'woocommerce' ),
						'description' => __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'woocommerce' ),
						'value'       => wc_bool_to_string( $coupon->get_exclude_sale_items( 'edit' ) ),
					)
				);

				echo '</div><div class="options_group">';

				// Product ids.
				?>
				<p class="form-field">
					<label><?php _e( 'Products', 'woocommerce' ); ?></label>
					<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations">
						<?php
						$product_ids = $coupon->get_product_ids( 'edit' );

						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
							}
						}
						?>
					</select>
					<?php echo wc_help_tip( __( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?>
				</p>

				<?php // Exclude Product ids. ?>
				<p class="form-field">
					<label><?php _e( 'Exclude products', 'woocommerce' ); ?></label>
					<select class="wc-product-search" multiple="multiple" style="width: 50%;" name="exclude_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations">
						<?php
						$product_ids = $coupon->get_excluded_product_ids( 'edit' );

						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
							}
						}
						?>
					</select>
					<?php echo wc_help_tip( __( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?>
				</p>
				<?php

				echo '</div><div class="options_group">';

				// Categories.
				?>
				<p class="form-field">
					<label for="product_categories"><?php _e( 'Product categories', 'woocommerce' ); ?></label>
					<select id="product_categories" name="product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any category', 'woocommerce' ); ?>">
						<?php
						$category_ids = $coupon->get_product_categories( 'edit' );
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

						if ( $categories ) {
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
							}
						}
						?>
					</select> <?php echo wc_help_tip( __( 'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?>
				</p>

				<?php // Exclude Categories. ?>
				<p class="form-field">
					<label for="exclude_product_categories"><?php _e( 'Exclude categories', 'woocommerce' ); ?></label>
					<select id="exclude_product_categories" name="exclude_product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No categories', 'woocommerce' ); ?>">
						<?php
						$category_ids = $coupon->get_excluded_product_categories( 'edit' );
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

						if ( $categories ) {
							foreach ( $categories as $cat ) {
								echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
							}
						}
						?>
					</select>
					<?php echo wc_help_tip( __( 'Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce' ) ); ?>
				</p>
			</div>
			<div class="options_group">
				<?php
				// Customers.
				woocommerce_wp_text_input(
					array(
						'id'                => 'customer_email',
						'label'             => __( 'Allowed emails', 'woocommerce' ),
						'placeholder'       => __( 'No restrictions', 'woocommerce' ),
						'description'       => __( 'List of allowed billing emails to check against when an order is placed. Separate email addresses with commas. You can also use an asterisk (*) to match parts of an email. For example "*@gmail.com" would match all gmail addresses.', 'woocommerce' ),
						'value'             => implode( ', ', (array) $coupon->get_email_restrictions( 'edit' ) ),
						'desc_tip'          => true,
						'type'              => 'email',
						'class'             => '',
						'custom_attributes' => array(
							'multiple' => 'multiple',
						),
					)
				);
				?>
			</div>
			<?php do_action( 'woocommerce_coupon_options_usage_restriction', $coupon->get_id(), $coupon ); ?>
			</div>
			<div id="usage_limit_coupon_data" class="panel woocommerce_options_panel">
				<div class="options_group">
					<?php
					// Usage limit per coupons.
					woocommerce_wp_text_input(
						array(
							'id'                => 'usage_limit',
							'label'             => __( 'Usage limit per coupon', 'woocommerce' ),
							'placeholder'       => esc_attr__( 'Unlimited usage', 'woocommerce' ),
							'description'       => __( 'How many times this coupon can be used before it is void.', 'woocommerce' ),
							'type'              => 'number',
							'desc_tip'          => true,
							'class'             => 'short',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
							'value'             => $coupon->get_usage_limit( 'edit' ) ? $coupon->get_usage_limit( 'edit' ) : '',
						)
					);

					// Usage limit per product.
					woocommerce_wp_text_input(
						array(
							'id'                => 'limit_usage_to_x_items',
							'label'             => __( 'Limit usage to X items', 'woocommerce' ),
							'placeholder'       => esc_attr__( 'Apply to all qualifying items in cart', 'woocommerce' ),
							'description'       => __( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'woocommerce' ),
							'desc_tip'          => true,
							'class'             => 'short',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
							'value'             => $coupon->get_limit_usage_to_x_items( 'edit' ) ? $coupon->get_limit_usage_to_x_items( 'edit' ) : '',
						)
					);

					// Usage limit per users.
					woocommerce_wp_text_input(
						array(
							'id'                => 'usage_limit_per_user',
							'label'             => __( 'Usage limit per user', 'woocommerce' ),
							'placeholder'       => esc_attr__( 'Unlimited usage', 'woocommerce' ),
							'description'       => __( 'How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users.', 'woocommerce' ),
							'desc_tip'          => true,
							'class'             => 'short',
							'type'              => 'number',
							'custom_attributes' => array(
								'step' => 1,
								'min'  => 0,
							),
							'value'             => $coupon->get_usage_limit_per_user( 'edit' ) ? $coupon->get_usage_limit_per_user( 'edit' ) : '',
						)
					);
					?>
				</div>
				<?php do_action( 'woocommerce_coupon_options_usage_limit', $coupon->get_id(), $coupon ); ?>
			</div>
			<?php do_action( 'woocommerce_coupon_data_panels', $coupon->get_id(), $coupon ); ?>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		// Check for dupe coupons.
		$coupon_code  = wc_format_coupon_code( $post->post_title );
		$id_from_code = wc_get_coupon_id_by_code( $coupon_code, $post_id );

		if ( $id_from_code ) {
			WC_Admin_Meta_Boxes::add_error( __( 'Coupon code already exists - customers will use the latest coupon with this code.', 'woocommerce' ) );
		}

		$product_categories         = isset( $_POST['product_categories'] ) ? (array) $_POST['product_categories'] : array();
		$exclude_product_categories = isset( $_POST['exclude_product_categories'] ) ? (array) $_POST['exclude_product_categories'] : array();

		$coupon = new WC_Coupon( $post_id );
		$coupon->set_props(
			array(
				'code'                        => $post->post_title,
				'discount_type'               => wc_clean( $_POST['discount_type'] ),
				'amount'                      => wc_format_decimal( $_POST['coupon_amount'] ),
				'date_expires'                => wc_clean( $_POST['expiry_date'] ),
				'individual_use'              => isset( $_POST['individual_use'] ),
				'product_ids'                 => isset( $_POST['product_ids'] ) ? array_filter( array_map( 'intval', (array) $_POST['product_ids'] ) ) : array(),
				'excluded_product_ids'        => isset( $_POST['exclude_product_ids'] ) ? array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) : array(),
				'usage_limit'                 => absint( $_POST['usage_limit'] ),
				'usage_limit_per_user'        => absint( $_POST['usage_limit_per_user'] ),
				'limit_usage_to_x_items'      => absint( $_POST['limit_usage_to_x_items'] ),
				'free_shipping'               => isset( $_POST['free_shipping'] ),
				'product_categories'          => array_filter( array_map( 'intval', $product_categories ) ),
				'excluded_product_categories' => array_filter( array_map( 'intval', $exclude_product_categories ) ),
				'exclude_sale_items'          => isset( $_POST['exclude_sale_items'] ),
				'minimum_amount'              => wc_format_decimal( $_POST['minimum_amount'] ),
				'maximum_amount'              => wc_format_decimal( $_POST['maximum_amount'] ),
				'email_restrictions'          => array_filter( array_map( 'trim', explode( ',', wc_clean( $_POST['customer_email'] ) ) ) ),
			)
		);
		$coupon->save();
		do_action( 'woocommerce_coupon_options_save', $post_id, $coupon );
	}
}
