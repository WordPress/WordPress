<?php
/**
 * Order Data
 *
 * Functions for displaying the order data meta box.
 *
 * @package     WooCommerce\Admin\Meta Boxes
 * @version     2.2.0
 */

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Meta_Box_Order_Data Class.
 */
class WC_Meta_Box_Order_Data {

	/**
	 * Billing fields.
	 *
	 * @var array
	 */
	protected static $billing_fields = array();

	/**
	 * Shipping fields.
	 *
	 * @var array
	 */
	protected static $shipping_fields = array();

	/**
	 * Init billing and shipping fields we display + save.
	 */
	public static function init_address_fields() {

		/**
		 * Provides an opportunity to modify the list of order billing fields displayed on the admin.
		 *
		 * @since 1.4.0
		 *
		 * @param array Billing fields.
		 */
		self::$billing_fields = apply_filters(
			'woocommerce_admin_billing_fields',
			array(
				'first_name' => array(
					'label' => __( 'First name', 'woocommerce' ),
					'show'  => false,
				),
				'last_name'  => array(
					'label' => __( 'Last name', 'woocommerce' ),
					'show'  => false,
				),
				'company'    => array(
					'label' => __( 'Company', 'woocommerce' ),
					'show'  => false,
				),
				'address_1'  => array(
					'label' => __( 'Address line 1', 'woocommerce' ),
					'show'  => false,
				),
				'address_2'  => array(
					'label' => __( 'Address line 2', 'woocommerce' ),
					'show'  => false,
				),
				'city'       => array(
					'label' => __( 'City', 'woocommerce' ),
					'show'  => false,
				),
				'postcode'   => array(
					'label' => __( 'Postcode / ZIP', 'woocommerce' ),
					'show'  => false,
				),
				'country'    => array(
					'label'   => __( 'Country / Region', 'woocommerce' ),
					'show'    => false,
					'class'   => 'js_field-country select short',
					'type'    => 'select',
					'options' => array( '' => __( 'Select a country / region&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries(),
				),
				'state'      => array(
					'label' => __( 'State / County', 'woocommerce' ),
					'class' => 'js_field-state select short',
					'show'  => false,
				),
				'email'      => array(
					'label' => __( 'Email address', 'woocommerce' ),
				),
				'phone'      => array(
					'label' => __( 'Phone', 'woocommerce' ),
				),
			)
		);

		/**
		 * Provides an opportunity to modify the list of order shipping fields displayed on the admin.
		 *
		 * @since 1.4.0
		 *
		 * @param array Shipping fields.
		 */
		self::$shipping_fields = apply_filters(
			'woocommerce_admin_shipping_fields',
			array(
				'first_name' => array(
					'label' => __( 'First name', 'woocommerce' ),
					'show'  => false,
				),
				'last_name'  => array(
					'label' => __( 'Last name', 'woocommerce' ),
					'show'  => false,
				),
				'company'    => array(
					'label' => __( 'Company', 'woocommerce' ),
					'show'  => false,
				),
				'address_1'  => array(
					'label' => __( 'Address line 1', 'woocommerce' ),
					'show'  => false,
				),
				'address_2'  => array(
					'label' => __( 'Address line 2', 'woocommerce' ),
					'show'  => false,
				),
				'city'       => array(
					'label' => __( 'City', 'woocommerce' ),
					'show'  => false,
				),
				'postcode'   => array(
					'label' => __( 'Postcode / ZIP', 'woocommerce' ),
					'show'  => false,
				),
				'country'    => array(
					'label'   => __( 'Country / Region', 'woocommerce' ),
					'show'    => false,
					'type'    => 'select',
					'class'   => 'js_field-country select short',
					'options' => array( '' => __( 'Select a country / region&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries(),
				),
				'state'      => array(
					'label' => __( 'State / County', 'woocommerce' ),
					'class' => 'js_field-state select short',
					'show'  => false,
				),
				'phone'      => array(
					'label' => __( 'Phone', 'woocommerce' ),
				),
			)
		);
	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post|WC_Order $post Post or order object.
	 */
	public static function output( $post ) {
		global $theorder;

		OrderUtil::init_theorder_object( $post );

		$order = $theorder;

		self::init_address_fields();

		if ( WC()->payment_gateways() ) {
			$payment_gateways = WC()->payment_gateways->payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $order->get_payment_method();

		$order_type_object = get_post_type_object( $order->get_type() );
		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
		?>
		<style type="text/css">
			#post-body-content, #titlediv { display:none }
		</style>
		<div class="panel-wrap woocommerce">
			<input name="post_title" type="hidden" value="<?php echo esc_attr( empty( $order->get_title() ) ? __( 'Order', 'woocommerce' ) : $order->get_title() ); ?>" />
			<input name="post_status" type="hidden" value="<?php echo esc_attr( $order->get_status() ); ?>" />
			<div id="order_data" class="panel woocommerce-order-data">
				<h2 class="woocommerce-order-data__heading">
					<?php

					printf(
						/* translators: 1: order type 2: order number */
						esc_html__( '%1$s #%2$s details', 'woocommerce' ),
						esc_html( $order_type_object->labels->singular_name ),
						esc_html( $order->get_order_number() )
					);

					?>
				</h2>
				<p class="woocommerce-order-data__meta order_number">
					<?php

					$meta_list = array();

					if ( $payment_method && 'other' !== $payment_method ) {
						$payment_method_string = sprintf(
							/* translators: %s: payment method */
							__( 'Payment via %s', 'woocommerce' ),
							esc_html( isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method )
						);

						$transaction_id = $order->get_transaction_id();
						if ( $transaction_id ) {

							$to_add = null;
							if ( isset( $payment_gateways[ $payment_method ] ) ) {
								$url = $payment_gateways[ $payment_method ]->get_transaction_url( $order );
								if ( $url ) {
									$to_add .= ' (<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $transaction_id ) . '</a>)';
								}
							}

							$to_add                 = $to_add ?? ' (' . esc_html( $transaction_id ) . ')';
							$payment_method_string .= $to_add;
						}

						$meta_list[] = $payment_method_string;
					}

					if ( $order->get_date_paid() ) {
						$meta_list[] = sprintf(
							/* translators: 1: date 2: time */
							__( 'Paid on %1$s @ %2$s', 'woocommerce' ),
							wc_format_datetime( $order->get_date_paid() ),
							wc_format_datetime( $order->get_date_paid(), get_option( 'time_format' ) )
						);
					}

					$ip_address = $order->get_customer_ip_address();
					if ( $ip_address ) {
						$meta_list[] = sprintf(
							/* translators: %s: IP address */
							__( 'Customer IP: %s', 'woocommerce' ),
							'<span class="woocommerce-Order-customerIP">' . esc_html( $ip_address ) . '</span>'
						);
					}

					echo wp_kses_post( implode( '. ', $meta_list ) );

					?>
				</p>
				<div class="order_data_column_container">
					<div class="order_data_column">
						<h3><?php esc_html_e( 'General', 'woocommerce' ); ?></h3>

						<p class="form-field form-field-wide">
							<?php
							$order_date_created_localised = ! is_null( $order->get_date_created() ) ? $order->get_date_created()->getOffsetTimestamp() : '';
							?>
							<label for="order_date"><?php esc_html_e( 'Date created:', 'woocommerce' ); ?></label>
							<input type="text" class="date-picker" name="order_date" maxlength="10" value="<?php echo esc_attr( date_i18n( 'Y-m-d', $order_date_created_localised ) ); ?>" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>" />@
							&lrm;
							<input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'woocommerce' ); ?>" name="order_date_hour" min="0" max="23" step="1" value="<?php echo esc_attr( date_i18n( 'H', $order_date_created_localised ) ); ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})" />:
							<input type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'woocommerce' ); ?>" name="order_date_minute" min="0" max="59" step="1" value="<?php echo esc_attr( date_i18n( 'i', $order_date_created_localised ) ); ?>" pattern="[0-5]{1}[0-9]{1}" />
							<input type="hidden" name="order_date_second" value="<?php echo esc_attr( date_i18n( 's', $order_date_created_localised ) ); ?>" />
						</p>

						<p class="form-field form-field-wide wc-order-status">
							<label for="order_status">
								<?php
								esc_html_e( 'Status:', 'woocommerce' );
								if ( $order->needs_payment() ) {
									printf(
										'<a href="%s">%s</a>',
										esc_url( $order->get_checkout_payment_url() ),
										esc_html__( 'Customer payment page &rarr;', 'woocommerce' )
									);
								}
								?>
							</label>
							<select id="order_status" name="order_status" class="wc-enhanced-select">
								<?php
								$statuses = wc_get_order_statuses();
								foreach ( $statuses as $status => $status_name ) {
									echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, 'wc-' . $order->get_status( 'edit' ), false ) . '>' . esc_html( $status_name ) . '</option>';
								}
								?>
							</select>
						</p>

						<p class="form-field form-field-wide wc-customer-user">
							<!--email_off--> <!-- Disable CloudFlare email obfuscation -->
							<label for="customer_user">
								<?php
								esc_html_e( 'Customer:', 'woocommerce' );
								if ( $order->get_user_id( 'edit' ) ) {
									$args = array(
										'post_status'    => 'all',
										'post_type'      => 'shop_order',
										'_customer_user' => $order->get_user_id( 'edit' ),
									);
									printf(
										'<a href="%s">%s</a>',
										esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ),
										' ' . esc_html__( 'View other orders &rarr;', 'woocommerce' )
									);
									printf(
										'<a href="%s">%s</a>',
										esc_url( add_query_arg( 'user_id', $order->get_user_id( 'edit' ), admin_url( 'user-edit.php' ) ) ),
										' ' . esc_html__( 'Profile &rarr;', 'woocommerce' )
									);
								}
								?>
							</label>
							<?php
							$user_string = '';
							$user_id     = '';
							if ( $order->get_user_id() ) {
								$user_id  = absint( $order->get_user_id() );
								$customer = new WC_Customer( $user_id );
								/* translators: 1: user display name 2: user ID 3: user email */
								$user_string = sprintf(
									/* translators: 1: customer name, 2 customer id, 3: customer email */
									esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
									$customer->get_first_name() . ' ' . $customer->get_last_name(),
									$customer->get_id(),
									$customer->get_email()
								);
							}
							?>
							<select class="wc-customer-search" id="customer_user" name="customer_user" data-placeholder="<?php esc_attr_e( 'Guest', 'woocommerce' ); ?>" data-allow_clear="true">
								<?php
								// phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment
								/**
								 * Filter to customize the display of the currently selected customer for an order in the order edit page.
								 * This is the same filter used in the ajax call for customer search in the same metabox.
								 *
								 * @since 7.2.0 (this instance of the filter)
								 *
								 * @param array @user_info An array containing one item with the name and email of the user currently selected as the customer for the order.
								 */
								?>
								<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo esc_html( htmlspecialchars( wp_kses_post( current( apply_filters( 'woocommerce_json_search_found_customers', array( $user_string ) ) ) ) ) ); ?></option>
								<?php // phpcs:enable WooCommerce.Commenting.CommentHooks.MissingHookComment ?>
							</select>
							<!--/email_off-->
						</p>
						<?php do_action( 'woocommerce_admin_order_data_after_order_details', $order ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>
					</div>
					<div class="order_data_column">
						<h3>
							<?php esc_html_e( 'Billing', 'woocommerce' ); ?>
							<a href="#" class="edit_address"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>
							<span>
								<a href="#" class="load_customer_billing" style="display:none;"><?php esc_html_e( 'Load billing address', 'woocommerce' ); ?></a>
							</span>
						</h3>
						<div class="address">
							<?php

							// Display values.
							if ( $order->get_formatted_billing_address() ) {
								echo '<p>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
							} else {
								echo '<p class="none_set"><strong>' . esc_html__( 'Address:', 'woocommerce' ) . '</strong> ' . esc_html__( 'No billing address set.', 'woocommerce' ) . '</p>';
							}

							foreach ( self::$billing_fields as $key => $field ) {
								if ( isset( $field['show'] ) && false === $field['show'] ) {
									continue;
								}

								$field_name = 'billing_' . $key;

								if ( isset( $field['value'] ) ) {
									$field_value = $field['value'];
								} elseif ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
									$field_value = $order->{"get_$field_name"}( 'edit' );
								} else {
									$field_value = $order->get_meta( '_' . $field_name );
								}

								if ( 'billing_phone' === $field_name ) {
									$field_value = wc_make_phone_clickable( $field_value );
								} elseif ( 'billing_email' === $field_name ) {
									$field_value = '<a href="' . esc_url( 'mailto:' . $field_value ) . '">' . $field_value . '</a>';
								} else {
									$field_value = make_clickable( esc_html( $field_value ) );
								}

								if ( $field_value ) {
									echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p>';
								}
							}
							?>
						</div>

						<div class="edit_address">
							<?php

							// Display form.
							foreach ( self::$billing_fields as $key => $field ) {
								if ( ! isset( $field['type'] ) ) {
									$field['type'] = 'text';
								}
								if ( ! isset( $field['id'] ) ) {
									$field['id'] = '_billing_' . $key;
								}

								$field_name = 'billing_' . $key;

								if ( ! isset( $field['value'] ) ) {
									if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
										$field['value'] = $order->{"get_$field_name"}( 'edit' );
									} else {
										$field['value'] = $order->get_meta( '_' . $field_name );
									}
								}

								switch ( $field['type'] ) {
									case 'select':
										woocommerce_wp_select( $field, $order );
										break;
									default:
										woocommerce_wp_text_input( $field, $order );
										break;
								}
							}
							?>
							<p class="form-field form-field-wide">
								<label><?php esc_html_e( 'Payment method:', 'woocommerce' ); ?></label>
								<select name="_payment_method" id="_payment_method" class="first">
									<option value=""><?php esc_html_e( 'N/A', 'woocommerce' ); ?></option>
									<?php
									$found_method = false;

									foreach ( $payment_gateways as $gateway ) {
										if ( 'yes' === $gateway->enabled ) {
											echo '<option value="' . esc_attr( $gateway->id ) . '" ' . selected( $payment_method, $gateway->id, false ) . '>' . esc_html( $gateway->get_title() ) . '</option>';
											if ( $payment_method === $gateway->id ) {
												$found_method = true;
											}
										}
									}

									if ( ! $found_method && ! empty( $payment_method ) ) {
										echo '<option value="' . esc_attr( $payment_method ) . '" selected="selected">' . esc_html__( 'Other', 'woocommerce' ) . '</option>';
									} else {
										echo '<option value="other">' . esc_html__( 'Other', 'woocommerce' ) . '</option>';
									}
									?>
								</select>
							</p>
							<?php

							woocommerce_wp_text_input(
								array(
									'id'    => '_transaction_id',
									'label' => __( 'Transaction ID', 'woocommerce' ),
									'value' => $order->get_transaction_id( 'edit' ),
								),
								$order
							);
							?>

						</div>
						<?php do_action( 'woocommerce_admin_order_data_after_billing_address', $order ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>
					</div>
					<div class="order_data_column">
						<h3>
							<?php esc_html_e( 'Shipping', 'woocommerce' ); ?>
							<a href="#" class="edit_address"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>
							<span>
								<a href="#" class="load_customer_shipping" style="display:none;"><?php esc_html_e( 'Load shipping address', 'woocommerce' ); ?></a>
								<a href="#" class="billing-same-as-shipping" style="display:none;"><?php esc_html_e( 'Copy billing address', 'woocommerce' ); ?></a>
							</span>
						</h3>
						<div class="address">
							<?php

							// Display values.
							if ( $order->get_formatted_shipping_address() ) {
								echo '<p>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
							} else {
								echo '<p class="none_set"><strong>' . esc_html__( 'Address:', 'woocommerce' ) . '</strong> ' . esc_html__( 'No shipping address set.', 'woocommerce' ) . '</p>';
							}

							if ( ! empty( self::$shipping_fields ) ) {
								foreach ( self::$shipping_fields as $key => $field ) {
									if ( isset( $field['show'] ) && false === $field['show'] ) {
										continue;
									}

									$field_name = 'shipping_' . $key;

									if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
										$field_value = $order->{"get_$field_name"}( 'edit' );
									} else {
										$field_value = $order->get_meta( '_' . $field_name );
									}

									if ( 'shipping_phone' === $field_name ) {
										$field_value = wc_make_phone_clickable( $field_value );
									}

									if ( $field_value ) {
										echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p>';
									}
								}
							}

							if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) && $order->get_customer_note() ) { // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
								echo '<p class="order_note"><strong>' . esc_html( __( 'Customer provided note:', 'woocommerce' ) ) . '</strong> ' . nl2br( esc_html( $order->get_customer_note() ) ) . '</p>';
							}
							?>
						</div>
						<div class="edit_address">
							<?php

							// Display form.
							if ( ! empty( self::$shipping_fields ) ) {
								foreach ( self::$shipping_fields as $key => $field ) {
									if ( ! isset( $field['type'] ) ) {
										$field['type'] = 'text';
									}
									if ( ! isset( $field['id'] ) ) {
										$field['id'] = '_shipping_' . $key;
									}

									$field_name = 'shipping_' . $key;

									if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
										$field['value'] = $order->{"get_$field_name"}( 'edit' );
									} else {
										$field['value'] = $order->get_meta( '_' . $field_name );
									}

									switch ( $field['type'] ) {
										case 'select':
											woocommerce_wp_select( $field, $order );
											break;
										default:
											woocommerce_wp_text_input( $field, $order );
											break;
									}
								}
							}

							/**
							 * Allows 3rd parties to alter whether the customer note should be displayed on the admin.
							 *
							 * @since 2.1.0
							 *
							 * @param bool TRUE if the note should be displayed. FALSE otherwise.
							 */
							if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) :
								?>
								<p class="form-field form-field-wide">
									<label for="customer_note"><?php esc_html_e( 'Customer provided note', 'woocommerce' ); ?>:</label>
									<textarea rows="1" cols="40" name="customer_note" tabindex="6" id="excerpt" placeholder="<?php esc_attr_e( 'Customer notes about the order', 'woocommerce' ); ?>"><?php echo wp_kses_post( $order->get_customer_note() ); ?></textarea>
								</p>
							<?php endif; ?>
						</div>

						<?php do_action( 'woocommerce_admin_order_data_after_shipping_address', $order ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 *
	 * @param int $order_id Order ID.
	 * @throws Exception Required request data is missing.
	 */
	public static function save( $order_id ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing

		if ( ! isset( $_POST['order_status'] ) ) {
			throw new Exception( __( 'Order status is missing.', 'woocommerce' ), 400 );
		}

		if ( ! isset( $_POST['_payment_method'] ) ) {
			throw new Exception( __( 'Payment method is missing.', 'woocommerce' ), 400 );
		}

		self::init_address_fields();

		// Ensure gateways are loaded in case they need to insert data into the emails.
		WC()->payment_gateways();
		WC()->shipping();

		// Get order object.
		$order = wc_get_order( $order_id );
		$props = array();

		// Create order key.
		if ( ! $order->get_order_key() ) {
			$props['order_key'] = wc_generate_order_key();
		}

		// Update customer.
		$customer_id = isset( $_POST['customer_user'] ) ? absint( $_POST['customer_user'] ) : 0;
		if ( $customer_id !== $order->get_customer_id() ) {
			$props['customer_id'] = $customer_id;
		}

		// Update billing fields.
		if ( ! empty( self::$billing_fields ) ) {
			foreach ( self::$billing_fields as $key => $field ) {
				if ( ! isset( $field['id'] ) ) {
					$field['id'] = '_billing_' . $key;
				}

				if ( ! isset( $_POST[ $field['id'] ] ) ) {
					continue;
				}

				if ( is_callable( array( $order, 'set_billing_' . $key ) ) ) {
					$props[ 'billing_' . $key ] = wc_clean( wp_unslash( $_POST[ $field['id'] ] ) );
				} else {
					$order->update_meta_data( $field['id'], wc_clean( wp_unslash( $_POST[ $field['id'] ] ) ) );
				}
			}
		}

		// Update shipping fields.
		if ( ! empty( self::$shipping_fields ) ) {
			foreach ( self::$shipping_fields as $key => $field ) {
				if ( ! isset( $field['id'] ) ) {
					$field['id'] = '_shipping_' . $key;
				}

				if ( ! isset( $_POST[ $field['id'] ] ) ) {
					continue;
				}

				if ( is_callable( array( $order, 'set_shipping_' . $key ) ) ) {
					$props[ 'shipping_' . $key ] = wc_clean( wp_unslash( $_POST[ $field['id'] ] ) );
				} else {
					$order->update_meta_data( $field['id'], wc_clean( wp_unslash( $_POST[ $field['id'] ] ) ) );
				}
			}
		}

		if ( isset( $_POST['_transaction_id'] ) ) {
			$props['transaction_id'] = wc_clean( wp_unslash( $_POST['_transaction_id'] ) );
		}

		// Payment method handling.
		if ( $order->get_payment_method() !== wc_clean( wp_unslash( $_POST['_payment_method'] ) ) ) {
			$methods              = WC()->payment_gateways->payment_gateways();
			$payment_method       = wc_clean( wp_unslash( $_POST['_payment_method'] ) );
			$payment_method_title = $payment_method;

			if ( isset( $methods ) && isset( $methods[ $payment_method ] ) ) {
				$payment_method_title = $methods[ $payment_method ]->get_title();
			}

			if ( 'other' === $payment_method ) {
				$payment_method_title = esc_html__( 'Other', 'woocommerce' );
			}

			$props['payment_method']       = $payment_method;
			$props['payment_method_title'] = $payment_method_title;
		}

		// Update date.
		if ( empty( $_POST['order_date'] ) ) {
			$date = time();
		} else {
			if ( ! isset( $_POST['order_date_hour'] ) || ! isset( $_POST['order_date_minute'] ) || ! isset( $_POST['order_date_second'] ) ) {
				throw new Exception( __( 'Order date, hour, minute and/or second are missing.', 'woocommerce' ), 400 );
			}
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$date = gmdate( 'Y-m-d H:i:s', strtotime( $_POST['order_date'] . ' ' . (int) $_POST['order_date_hour'] . ':' . (int) $_POST['order_date_minute'] . ':' . (int) $_POST['order_date_second'] ) );
		}

		$props['date_created'] = $date;

		// Set created via prop if new post.
		if ( isset( $_POST['original_post_status'] ) && 'auto-draft' === $_POST['original_post_status'] ) {
			$props['created_via'] = 'admin';
		}

		// Customer note.
		if ( isset( $_POST['customer_note'] ) ) {
			$props['customer_note'] = sanitize_textarea_field( wp_unslash( $_POST['customer_note'] ) );
		}

		// Save order data.
		$order->set_props( $props );
		$order->set_status( wc_clean( wp_unslash( $_POST['order_status'] ) ), '', true );
		$order->save();

		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}
}
