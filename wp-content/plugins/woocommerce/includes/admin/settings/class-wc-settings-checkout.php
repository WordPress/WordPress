<?php
/**
 * WooCommerce Shipping Settings
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Payment_Gateways' ) ) :

/**
 * WC_Settings_Payment_Gateways
 */
class WC_Settings_Payment_Gateways extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'checkout';
		$this->label = _x( 'Checkout', 'Settings tab label', 'woocommerce' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_admin_field_payment_gateways', array( $this, 'payment_gateways_setting' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''         => __( 'Checkout Options', 'woocommerce' )
		);

		// Load shipping methods so we can show any global options they may have
		$payment_gateways = WC()->payment_gateways->payment_gateways();

		foreach ( $payment_gateways as $gateway ) {

			$title = empty( $gateway->method_title ) ? ucfirst( $gateway->id ) : $gateway->method_title;

			$sections[ strtolower( get_class( $gateway ) ) ] = esc_html( $title );
		}

		return $sections;
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		return apply_filters( 'woocommerce_payment_gateways_settings', array(

			array(	'title' => __( 'Checkout Process', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_process_options' ),

			array(
				'title' => __( 'Coupons', 'woocommerce' ),
				'desc'          => __( 'Enable the use of coupons', 'woocommerce' ),
				'id'            => 'woocommerce_enable_coupons',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'desc_tip'		=>  __( 'Coupons can be applied from the cart and checkout pages.', 'woocommerce' ),
				'autoload'      => false
			),

			array(
				'title'     => _x( 'Checkout', 'Settings group label', 'woocommerce' ),
				'desc' 		=> __( 'Enable guest checkout', 'woocommerce' ),
				'desc_tip'	=>  __( 'Allows customers to checkout without creating an account.', 'woocommerce' ),
				'id' 		=> 'woocommerce_enable_guest_checkout',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'	=> 'start',
				'autoload'  => false
			),

			array(
				'desc' 		=> __( 'Force secure checkout', 'woocommerce' ),
				'id' 		=> 'woocommerce_force_ssl_checkout',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> '',
				'show_if_checked' => 'option',
				'desc_tip'	=>  __( 'Force SSL (HTTPS) on the checkout pages (an SSL Certificate is required).', 'woocommerce' ),
			),

			array(
				'desc' 		=> __( 'Un-force HTTPS when leaving the checkout', 'woocommerce' ),
				'id' 		=> 'woocommerce_unforce_ssl_checkout',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'end',
				'show_if_checked' => 'yes',
			),

			array( 'type' => 'sectionend', 'id' => 'checkout_process_options'),

			array(	'title' => __( 'Checkout Pages', 'woocommerce' ), 'desc' => __( 'These pages need to be set so that WooCommerce knows where to send users to checkout.', 'woocommerce' ), 'type' => 'title', 'id' => 'checkout_page_options' ),

			array(
				'title' => __( 'Cart Page', 'woocommerce' ),
				'desc' 		=> __( 'Page contents:', 'woocommerce' ) . ' [' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']',
				'id' 		=> 'woocommerce_cart_page_id',
				'type' 		=> 'single_select_page',
				'default'	=> '',
				'class'		=> 'chosen_select_nostd',
				'css' 		=> 'min-width:300px;',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Checkout Page', 'woocommerce' ),
				'desc' 		=> __( 'Page contents:', 'woocommerce' ) . ' [' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']',
				'id' 		=> 'woocommerce_checkout_page_id',
				'type' 		=> 'single_select_page',
				'default'	=> '',
				'class'		=> 'chosen_select_nostd',
				'css' 		=> 'min-width:300px;',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Terms and Conditions', 'woocommerce' ),
				'desc' 		=> __( 'If you define a "Terms" page the customer will be asked if they accept them when checking out.', 'woocommerce' ),
				'id' 		=> 'woocommerce_terms_page_id',
				'default'	=> '',
				'class'		=> 'chosen_select_nostd',
				'css' 		=> 'min-width:300px;',
				'type' 		=> 'single_select_page',
				'desc_tip'	=> true,
				'autoload'  => false
			),

			array( 'type' => 'sectionend', 'id' => 'checkout_page_options' ),

			array( 'title' => __( 'Checkout Endpoints', 'woocommerce' ), 'type' => 'title', 'desc' => __( 'Endpoints are appended to your page URLs to handle specific actions during the checkout process. They should be unique.', 'woocommerce' ), 'id' => 'account_endpoint_options' ),

			array(
				'title' => __( 'Pay', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the Checkout &rarr; Pay page', 'woocommerce' ),
				'id' 		=> 'woocommerce_checkout_pay_endpoint',
				'type' 		=> 'text',
				'default'	=> 'order-pay',
				'desc_tip'	=> true,
			),

			array(
				'title' => __( 'Order Received', 'woocommerce' ),
				'desc' 		=> __( 'Endpoint for the Checkout &rarr; Order Received page', 'woocommerce' ),
				'id' 		=> 'woocommerce_checkout_order_received_endpoint',
				'type' 		=> 'text',
				'default'	=> 'order-received',
				'desc_tip'	=> true,
			),

			array(
				'title'    => __( 'Add Payment Method', 'woocommerce' ),
				'desc'     => __( 'Endpoint for the Checkout &rarr; Add Payment Method page', 'woocommerce' ),
				'id'       => 'woocommerce_myaccount_add_payment_method_endpoint',
				'type'     => 'text',
				'default'  => 'add-payment-method',
				'desc_tip' => true,
			),

			array( 'type' => 'sectionend', 'id' => 'checkout_endpoint_options' ),

			array( 'title' => __( 'Payment Gateways', 'woocommerce' ),  'desc' => __( 'Installed gateways are listed below. Drag and drop gateways to control their display order on the frontend.', 'woocommerce' ), 'type' => 'title', 'id' => 'payment_gateways_options' ),

			array( 'type' => 'payment_gateways' ),

			array( 'type' => 'sectionend', 'id' => 'payment_gateways_options' ),

		)); // End payment_gateway settings
	}

	/**
	 * Output the settings
	 */
	public function output() {
		global $current_section;

		// Load shipping methods so we can show any global options they may have
		$payment_gateways = WC()->payment_gateways->payment_gateways();

		if ( $current_section ) {
 			foreach ( $payment_gateways as $gateway ) {
				if ( strtolower( get_class( $gateway ) ) == strtolower( $current_section ) ) {
					$gateway->admin_options();
					break;
				}
			}
 		} else {
			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Output payment gateway settings.
	 *
	 * @access public
	 * @return void
	 */
	public function payment_gateways_setting() {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Gateway Display', 'woocommerce' ) ?></th>
		    <td class="forminp">
				<table class="wc_gateways widefat" cellspacing="0">
					<thead>
						<tr>
							<?php
								$columns = apply_filters( 'woocommerce_payment_gateways_setting_columns', array(
									'default'  => __( 'Default', 'woocommerce' ),
									'name'     => __( 'Gateway', 'woocommerce' ),
									'id'       => __( 'Gateway ID', 'woocommerce' ),
									'status'   => __( 'Status', 'woocommerce' ),
									'settings' => ''
								) );

								foreach ( $columns as $key => $column ) {
									echo '<th class="' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
								}
							?>
						</tr>
					</thead>
					<tbody>
			        	<?php
			        	$default_gateway = get_option( 'woocommerce_default_gateway' );

			        	foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) {

			        		echo '<tr>';

			        		foreach ( $columns as $key => $column ) {
								switch ( $key ) {
									case 'default' :
										echo '<td width="1%" class="default">
					        				<input type="radio" name="default_gateway" value="' . esc_attr( $gateway->id ) . '" ' . checked( $default_gateway, esc_attr( $gateway->id ), false ) . ' />
					        				<input type="hidden" name="gateway_order[]" value="' . esc_attr( $gateway->id ) . '" />
					        			</td>';
									break;
									case 'name' :
										echo '<td class="name">
					        				' . $gateway->get_title() . '
					        			</td>';
									break;
									case 'id' :
										echo '<td class="id">
					        				' . esc_html( $gateway->id ) . '
					        			</td>';
									break;
									case 'status' :
										echo '<td class="status">';

						        		if ( $gateway->enabled == 'yes' )
						        			echo '<span class="status-enabled tips" data-tip="' . __ ( 'Enabled', 'woocommerce' ) . '">' . __ ( 'Enabled', 'woocommerce' ) . '</span>';
						        		else
						        			echo '-';

						        		echo '</td>';
									break;
									case 'settings' :
										echo '<td class="settings">
					        				<a class="button" href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . strtolower( get_class( $gateway ) ) ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>
					        			</td>';
									break;
									default :
										do_action( 'woocommerce_payment_gateways_setting_column_' . $key, $gateway );
									break;
								}
							}

							echo '</tr>';
			        	}
			        	?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {

			$settings = $this->get_settings();

			WC_Admin_Settings::save_fields( $settings );
			WC()->payment_gateways->process_admin_options();

		} elseif ( class_exists( $current_section ) ) {

			$current_section_class = new $current_section();

			do_action( 'woocommerce_update_options_payment_gateways_' . $current_section_class->id );

			WC()->payment_gateways()->init();
		}
	}
}

endif;

return new WC_Settings_Payment_Gateways();