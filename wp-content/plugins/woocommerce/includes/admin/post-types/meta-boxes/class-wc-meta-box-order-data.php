<?php
/**
 * Order Data
 *
 * Functions for displaying the order data meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Meta_Box_Order_Data
 */
class WC_Meta_Box_Order_Data {

	private static $billing_fields;
	private static $shipping_fields;

	/**
	 * Init billing and shipping fields we display + save
	 */
	public static function init_address_fields() {
		self::$billing_fields = apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'	=> false
				),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'	=> false
				),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'	=> false
				),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'	=> false
				),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'	=> false
				),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'	=> false
				),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'	=> false
				),
			'country' => array(
				'label' => __( 'Country', 'woocommerce' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
				),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'show'	=> false
				),
			'email' => array(
				'label' => __( 'Email', 'woocommerce' ),
				),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
				),
		) );

		self::$shipping_fields = apply_filters( 'woocommerce_admin_shipping_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'	=> false
				),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'	=> false
				),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'	=> false
				),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'	=> false
				),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'	=> false
				),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'	=> false
				),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'	=> false
				),
			'country' => array(
				'label' => __( 'Country', 'woocommerce' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries()
				),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'show'	=> false
				),
		) );
	}

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $theorder;

		if ( ! is_object( $theorder ) )
			$theorder = new WC_Order( $post->ID );

		$order = $theorder;

		self::init_address_fields();

		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
		?>
		<style type="text/css">
			#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
		</style>
		<div class="panel-wrap woocommerce">
			<input name="post_title" type="hidden" value="<?php echo empty( $post->post_title ) ? 'Order' : esc_attr( $post->post_title ); ?>" />
			<input name="post_status" type="hidden" value="publish" />
			<div id="order_data" class="panel">

				<h2><?php _e( 'Order Details', 'woocommerce' ); ?></h2>
				<p class="order_number"><?php

					echo __( 'Order number', 'woocommerce' ) . ' ' . esc_html( $order->get_order_number() ) . '. ';

					if ( $ip_address = get_post_meta( $post->ID, '_customer_ip_address', true ) )
						echo __( 'Customer IP:', 'woocommerce' ) . ' ' . esc_html( $ip_address );
				?></p>

				<div class="order_data_column_container">
					<div class="order_data_column">
						<h4><?php _e( 'General Details', 'woocommerce' ); ?></h4>

						<p class="form-field form-field-wide"><label for="order_date"><?php _e( 'Order date:', 'woocommerce' ) ?></label>
							<input type="text" class="date-picker-field" name="order_date" id="order_date" maxlength="10" value="<?php echo date_i18n( 'Y-m-d', strtotime( $post->post_date ) ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />@<input type="text" class="hour" placeholder="<?php _e( 'h', 'woocommerce' ) ?>" name="order_date_hour" id="order_date_hour" maxlength="2" size="2" value="<?php echo date_i18n( 'H', strtotime( $post->post_date ) ); ?>" pattern="\-?\d+(\.\d{0,})?" />:<input type="text" class="minute" placeholder="<?php _e( 'm', 'woocommerce' ) ?>" name="order_date_minute" id="order_date_minute" maxlength="2" size="2" value="<?php echo date_i18n( 'i', strtotime( $post->post_date ) ); ?>" pattern="\-?\d+(\.\d{0,})?" />
						</p>

						<p class="form-field form-field-wide"><label for="order_status"><?php _e( 'Order status:', 'woocommerce' ) ?></label>
						<select id="order_status" name="order_status" class="chosen_select">
							<?php
								$statuses = (array) get_terms( 'shop_order_status', array( 'hide_empty' => 0, 'orderby' => 'id' ) );
								foreach ( $statuses as $status ) {
									echo '<option value="' . esc_attr( $status->slug ) . '" ' . selected( $status->slug, $order->status, false ) . '>' . esc_html__( $status->name, 'woocommerce' ) . '</option>';
								}
							?>
						</select></p>

						<p class="form-field form-field-wide">
							<label for="customer_user"><?php _e( 'Customer:', 'woocommerce' ) ?></label>
							<select id="customer_user" name="customer_user" class="ajax_chosen_select_customer">
								<option value=""><?php _e( 'Guest', 'woocommerce' ) ?></option>
								<?php
									if ( $order->customer_user ) {
										$user = get_user_by( 'id', $order->customer_user );
										echo '<option value="' . esc_attr( $user->ID ) . '" ' . selected( 1, 1, false ) . '>' . esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')</option>';
									}
								?>
							</select>
						</p>

						<?php do_action( 'woocommerce_admin_order_data_after_order_details', $order ); ?>
					</div>
					<div class="order_data_column">
						<h4><?php _e( 'Billing Details', 'woocommerce' ); ?> <a class="edit_address" href="#"><img src="<?php echo WC()->plugin_url(); ?>/assets/images/icons/edit.png" alt="Edit" width="14" /></a></h4>
						<?php
							// Display values
							echo '<div class="address">';

								if ( $order->get_formatted_billing_address() )
									echo '<p><strong>' . __( 'Address', 'woocommerce' ) . ':</strong>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
								else
									echo '<p class="none_set"><strong>' . __( 'Address', 'woocommerce' ) . ':</strong> ' . __( 'No billing address set.', 'woocommerce' ) . '</p>';

								foreach ( self::$billing_fields as $key => $field ) {
									if ( isset( $field['show'] ) && $field['show'] === false )
										continue;

									$field_name = 'billing_' . $key;

									if ( $order->$field_name )
										echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $order->$field_name ) ) . '</p>';
								}

								if ( WC()->payment_gateways() )
									$payment_gateways = WC()->payment_gateways->payment_gateways();

								$payment_method = ! empty( $order->payment_method ) ? $order->payment_method : '';

								if ( $payment_method )
									echo '<p><strong>' . __( 'Payment Method', 'woocommerce' ) . ':</strong> ' . ( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) ) . '</p>';

							echo '</div>';

							// Display form
							echo '<div class="edit_address"><p><button class="button load_customer_billing">'.__( 'Load billing address', 'woocommerce' ).'</button></p>';

							foreach ( self::$billing_fields as $key => $field ) {
								if ( ! isset( $field['type'] ) )
									$field['type'] = 'text';
								switch ( $field['type'] ) {
									case "select" :
										woocommerce_wp_select( array( 'id' => '_billing_' . $key, 'label' => $field['label'], 'options' => $field['options'] ) );
									break;
									default :
										woocommerce_wp_text_input( array( 'id' => '_billing_' . $key, 'label' => $field['label'] ) );
									break;
								}
							}

							?>
							<p class="form-field form-field-wide">
								<label><?php _e( 'Payment Method:', 'woocommerce' ); ?></label>
								<select name="_payment_method" id="_payment_method" class="first">
									<option value=""><?php _e( 'N/A', 'woocommerce' ); ?></option>
									<?php
										$found_method 	= false;

										foreach ( $payment_gateways as $gateway ) {
											if ( $gateway->enabled == "yes" ) {
												echo '<option value="' . esc_attr( $gateway->id ) . '" ' . selected( $payment_method, $gateway->id, false ) . '>' . esc_html( $gateway->get_title() ) . '</option>';
												if ( $payment_method == $gateway->id )
													$found_method = true;
											}
										}

										if ( ! $found_method && ! empty( $payment_method ) ) {
											echo '<option value="' . esc_attr( $payment_method ) . '" selected="selected">' . __( 'Other', 'woocommerce' ) . '</option>';
										} else {
											echo '<option value="other">' . __( 'Other', 'woocommerce' ) . '</option>';
										}
									?>
								</select>
							</p>
							<?php

							echo '</div>';

							do_action( 'woocommerce_admin_order_data_after_billing_address', $order );
						?>
					</div>
					<div class="order_data_column">

						<h4><?php _e( 'Shipping Details', 'woocommerce' ); ?> <a class="edit_address" href="#"><img src="<?php echo WC()->plugin_url(); ?>/assets/images/icons/edit.png" alt="Edit" width="14" /></a></h4>
						<?php
							// Display values
							echo '<div class="address">';

								if ( $order->get_formatted_shipping_address() )
									echo '<p><strong>' . __( 'Address', 'woocommerce' ) . ':</strong>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
								else
									echo '<p class="none_set"><strong>' . __( 'Address', 'woocommerce' ) . ':</strong> ' . __( 'No shipping address set.', 'woocommerce' ) . '</p>';

								if ( self::$shipping_fields ) foreach ( self::$shipping_fields as $key => $field ) {
									if ( isset( $field['show'] ) && $field['show'] === false )
										continue;

									$field_name = 'shipping_' . $key;

									if ( ! empty( $order->$field_name ) )
										echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $order->$field_name ) ) . '</p>';
								}

								if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) == 'yes' ) && $post->post_excerpt )
									echo '<p><strong>' . __( 'Customer Note', 'woocommerce' ) . ':</strong> ' . nl2br( esc_html( $post->post_excerpt ) ) . '</p>';

							echo '</div>';

							// Display form
							echo '<div class="edit_address"><p><button class="button load_customer_shipping">' . __( 'Load shipping address', 'woocommerce' ) . '</button> <button class="button billing-same-as-shipping">'. __( 'Copy from billing', 'woocommerce' ) . '</button></p>';

							if ( self::$shipping_fields ) foreach ( self::$shipping_fields as $key => $field ) {
								if ( ! isset( $field['type'] ) )
									$field['type'] = 'text';
								switch ( $field['type'] ) {
									case "select" :
										woocommerce_wp_select( array( 'id' => '_shipping_' . $key, 'label' => $field['label'], 'options' => $field['options'] ) );
									break;
									default :
										woocommerce_wp_text_input( array( 'id' => '_shipping_' . $key, 'label' => $field['label'] ) );
									break;
								}
							}

							if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) == 'yes' ) ) {
								?>
								<p class="form-field form-field-wide"><label for="excerpt"><?php _e( 'Customer Note:', 'woocommerce' ) ?></label>
								<textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt" placeholder="<?php _e( 'Customer\'s notes about the order', 'woocommerce' ); ?>"><?php echo wp_kses_post( $post->post_excerpt ); ?></textarea></p>
								<?php
							}

							echo '</div>';

							do_action( 'woocommerce_admin_order_data_after_shipping_address', $order );
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php

		// Ajax Chosen Customer Selectors JS
		wc_enqueue_js( "
			jQuery('select.ajax_chosen_select_customer').ajaxChosen({
			    method: 		'GET',
			    url: 			'" . admin_url('admin-ajax.php') . "',
			    dataType: 		'json',
			    afterTypeDelay: 100,
			    minTermLength: 	1,
			    data:		{
			    	action: 	'woocommerce_json_search_customers',
					security: 	'" . wp_create_nonce("search-customers") . "'
			    }
			}, function (data) {

				var terms = {};

			    $.each(data, function (i, val) {
			        terms[i] = val;
			    });

			    return terms;
			});
		" );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		global $wpdb;

		self::init_address_fields();

		// Add key
		add_post_meta( $post_id, '_order_key', uniqid( 'order_' ), true );

		// Update meta
		update_post_meta( $post_id, '_customer_user', absint( $_POST['customer_user'] ) );

		if ( self::$billing_fields )
			foreach ( self::$billing_fields as $key => $field )
				update_post_meta( $post_id, '_billing_' . $key, wc_clean( $_POST[ '_billing_' . $key ] ) );

		if ( self::$shipping_fields )
			foreach ( self::$shipping_fields as $key => $field )
				update_post_meta( $post_id, '_shipping_' . $key, wc_clean( $_POST[ '_shipping_' . $key ] ) );

		// Payment method handling
		if ( get_post_meta( $post_id, '_payment_method', true ) !== stripslashes( $_POST['_payment_method'] ) ) {

			$methods 				= WC()->payment_gateways->payment_gateways();
			$payment_method 		= wc_clean( $_POST['_payment_method'] );
			$payment_method_title 	= $payment_method;

			if ( isset( $methods) && isset( $methods[ $payment_method ] ) )
				$payment_method_title = $methods[ $payment_method ]->get_title();

			update_post_meta( $post_id, '_payment_method', $payment_method );
			update_post_meta( $post_id, '_payment_method_title', $payment_method_title );
		}

		// Update date
		if ( empty( $_POST['order_date'] ) ) {
			$date = current_time('timestamp');
		} else {
			$date = strtotime( $_POST['order_date'] . ' ' . (int) $_POST['order_date_hour'] . ':' . (int) $_POST['order_date_minute'] . ':00' );
		}

		$date = date_i18n( 'Y-m-d H:i:s', $date );

		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %s", $date, get_gmt_from_date( $date ), $post_id ) );

		// Order data saved, now get it so we can manipulate status
		$order = new WC_Order( $post_id );

		// Order status
		$order->update_status( $_POST['order_status'] );

		wc_delete_shop_order_transients( $post_id );
	}
}