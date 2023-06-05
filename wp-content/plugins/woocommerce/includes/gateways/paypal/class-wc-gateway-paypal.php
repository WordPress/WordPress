<?php
/**
 * PayPal Standard Payment Gateway.
 *
 * Provides a PayPal Standard Payment Gateway.
 *
 * @class       WC_Gateway_Paypal
 * @extends     WC_Payment_Gateway
 * @version     2.3.0
 * @package     WooCommerce\Classes\Payment
 */

use Automattic\Jetpack\Constants;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_Paypal Class.
 */
class WC_Gateway_Paypal extends WC_Payment_Gateway {

	/**
	 * Whether or not logging is enabled
	 *
	 * @var bool
	 */
	public static $log_enabled = false;

	/**
	 * Logger instance
	 *
	 * @var WC_Logger
	 */
	public static $log = false;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                = 'paypal';
		$this->has_fields        = false;
		$this->order_button_text = __( 'Proceed to PayPal', 'woocommerce' );
		$this->method_title      = __( 'PayPal Standard', 'woocommerce' );
		/* translators: %s: Link to WC system status page */
		$this->method_description = __( 'PayPal Standard redirects customers to PayPal to enter their payment information.', 'woocommerce' );
		$this->supports           = array(
			'products',
			'refunds',
		);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		$this->testmode       = 'yes' === $this->get_option( 'testmode', 'no' );
		$this->debug          = 'yes' === $this->get_option( 'debug', 'no' );
		$this->email          = $this->get_option( 'email' );
		$this->receiver_email = $this->get_option( 'receiver_email', $this->email );
		$this->identity_token = $this->get_option( 'identity_token' );
		self::$log_enabled    = $this->debug;

		if ( $this->testmode ) {
			/* translators: %s: Link to PayPal sandbox testing guide page */
			$this->description .= ' ' . sprintf( __( 'SANDBOX ENABLED. You can use sandbox testing accounts only. See the <a href="%s">PayPal Sandbox Testing Guide</a> for more details.', 'woocommerce' ), 'https://developer.paypal.com/docs/classic/lifecycle/ug_sandbox/' );
			$this->description  = trim( $this->description );
		}

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_order_status_processing', array( $this, 'capture_payment' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'capture_payment' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		if ( ! $this->is_valid_for_use() ) {
			$this->enabled = 'no';
		} else {
			include_once dirname( __FILE__ ) . '/includes/class-wc-gateway-paypal-ipn-handler.php';
			new WC_Gateway_Paypal_IPN_Handler( $this->testmode, $this->receiver_email );

			if ( $this->identity_token ) {
				include_once dirname( __FILE__ ) . '/includes/class-wc-gateway-paypal-pdt-handler.php';
				$pdt_handler = new WC_Gateway_Paypal_PDT_Handler( $this->testmode, $this->identity_token );
				$pdt_handler->set_receiver_email( $this->receiver_email );
			}
		}

		if ( 'yes' === $this->enabled ) {
			add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'order_received_text' ), 10, 2 );
		}
	}

	/**
	 * Return whether or not this gateway still requires setup to function.
	 *
	 * When this gateway is toggled on via AJAX, if this returns true a
	 * redirect will occur to the settings page instead.
	 *
	 * @since 3.4.0
	 * @return bool
	 */
	public function needs_setup() {
		return ! is_email( $this->email );
	}

	/**
	 * Logging method.
	 *
	 * @param string $message Log message.
	 * @param string $level Optional. Default 'info'. Possible values:
	 *                      emergency|alert|critical|error|warning|notice|info|debug.
	 */
	public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log( $level, $message, array( 'source' => 'paypal' ) );
		}
	}

	/**
	 * Processes and saves options.
	 * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		$saved = parent::process_admin_options();

		// Maybe clear logs.
		if ( 'yes' !== $this->get_option( 'debug', 'no' ) ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->clear( 'paypal' );
		}

		return $saved;
	}

	/**
	 * Get gateway icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		// We need a base country for the link to work, bail if in the unlikely event no country is set.
		$base_country = WC()->countries->get_base_country();
		if ( empty( $base_country ) ) {
			return '';
		}
		$icon_html = '';
		$icon      = (array) $this->get_icon_image( $base_country );

		foreach ( $icon as $i ) {
			$icon_html .= '<img src="' . esc_attr( $i ) . '" alt="' . esc_attr__( 'PayPal acceptance mark', 'woocommerce' ) . '" />';
		}

		$icon_html .= sprintf( '<a href="%1$s" class="about_paypal" onclick="javascript:window.open(\'%1$s\',\'WIPaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700\'); return false;">' . esc_attr__( 'What is PayPal?', 'woocommerce' ) . '</a>', esc_url( $this->get_icon_url( $base_country ) ) );

		return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
	}

	/**
	 * Get the link for an icon based on country.
	 *
	 * @param  string $country Country two letter code.
	 * @return string
	 */
	protected function get_icon_url( $country ) {
		$url           = 'https://www.paypal.com/' . strtolower( $country );
		$home_counties = array( 'BE', 'CZ', 'DK', 'HU', 'IT', 'JP', 'NL', 'NO', 'ES', 'SE', 'TR', 'IN' );
		$countries     = array( 'DZ', 'AU', 'BH', 'BQ', 'BW', 'CA', 'CN', 'CW', 'FI', 'FR', 'DE', 'GR', 'HK', 'ID', 'JO', 'KE', 'KW', 'LU', 'MY', 'MA', 'OM', 'PH', 'PL', 'PT', 'QA', 'IE', 'RU', 'BL', 'SX', 'MF', 'SA', 'SG', 'SK', 'KR', 'SS', 'TW', 'TH', 'AE', 'GB', 'US', 'VN' );

		if ( in_array( $country, $home_counties, true ) ) {
			return $url . '/webapps/mpp/home';
		} elseif ( in_array( $country, $countries, true ) ) {
			return $url . '/webapps/mpp/paypal-popup';
		} else {
			return $url . '/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside';
		}
	}

	/**
	 * Get PayPal images for a country.
	 *
	 * @param string $country Country code.
	 * @return array of image URLs
	 */
	protected function get_icon_image( $country ) {
		switch ( $country ) {
			case 'US':
			case 'NZ':
			case 'CZ':
			case 'HU':
			case 'MY':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TR':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_odeme_secenekleri.jpg';
				break;
			case 'GB':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_mc_vs_ms_ae_UK.png';
				break;
			case 'MX':
				$icon = array(
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_visa_mastercard_amex.png',
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_debit_card_275x60.gif',
				);
				break;
			case 'FR':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg';
				break;
			case 'AU':
				$icon = 'https://www.paypalobjects.com/webstatic/en_AU/mktg/logo/Solutions-graphics-1-184x80.jpg';
				break;
			case 'DK':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_PayPal_betalingsmuligheder_dk.jpg';
				break;
			case 'RU':
				$icon = 'https://www.paypalobjects.com/webstatic/ru_RU/mktg/business/pages/logo-center/AM_mc_vs_dc_ae.jpg';
				break;
			case 'NO':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/banner_pl_just_pp_319x110.jpg';
				break;
			case 'CA':
				$icon = 'https://www.paypalobjects.com/webstatic/en_CA/mktg/logo-image/AM_mc_vs_dc_ae.jpg';
				break;
			case 'HK':
				$icon = 'https://www.paypalobjects.com/webstatic/en_HK/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'SG':
				$icon = 'https://www.paypalobjects.com/webstatic/en_SG/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TW':
				$icon = 'https://www.paypalobjects.com/webstatic/en_TW/mktg/logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TH':
				$icon = 'https://www.paypalobjects.com/webstatic/en_TH/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'JP':
				$icon = 'https://www.paypal.com/ja_JP/JP/i/bnr/horizontal_solution_4_jcb.gif';
				break;
			case 'IN':
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			default:
				$icon = WC_HTTPS::force_https_url( WC()->plugin_url() . '/includes/gateways/paypal/assets/images/paypal.png' );
				break;
		}
		return apply_filters( 'woocommerce_paypal_icon', $icon );
	}

	/**
	 * Check if this gateway is available in the user's country based on currency.
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {
		return in_array(
			get_woocommerce_currency(),
			apply_filters(
				'woocommerce_paypal_supported_currencies',
				array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB', 'INR' )
			),
			true
		);
	}

	/**
	 * Admin Panel Options.
	 * - Options for bits like 'title' and availability on a country-by-country basis.
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
		if ( $this->is_valid_for_use() ) {
			parent::admin_options();
		} else {
			?>
			<div class="inline error">
				<p>
					<strong><?php esc_html_e( 'Gateway disabled', 'woocommerce' ); ?></strong>: <?php esc_html_e( 'PayPal Standard does not support your store currency.', 'woocommerce' ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include __DIR__ . '/includes/settings-paypal.php';
	}

	/**
	 * Get the transaction URL.
	 *
	 * @param  WC_Order $order Order object.
	 * @return string
	 */
	public function get_transaction_url( $order ) {
		if ( $this->testmode ) {
			$this->view_transaction_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		} else {
			$this->view_transaction_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		}
		return parent::get_transaction_url( $order );
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		include_once dirname( __FILE__ ) . '/includes/class-wc-gateway-paypal-request.php';

		$order          = wc_get_order( $order_id );
		$paypal_request = new WC_Gateway_Paypal_Request( $this );

		return array(
			'result'   => 'success',
			'redirect' => $paypal_request->get_request_url( $order, $this->testmode ),
		);
	}

	/**
	 * Can the order be refunded via PayPal?
	 *
	 * @param  WC_Order $order Order object.
	 * @return bool
	 */
	public function can_refund_order( $order ) {
		$has_api_creds = false;

		if ( $this->testmode ) {
			$has_api_creds = $this->get_option( 'sandbox_api_username' ) && $this->get_option( 'sandbox_api_password' ) && $this->get_option( 'sandbox_api_signature' );
		} else {
			$has_api_creds = $this->get_option( 'api_username' ) && $this->get_option( 'api_password' ) && $this->get_option( 'api_signature' );
		}

		return $order && $order->get_transaction_id() && $has_api_creds;
	}

	/**
	 * Init the API class and set the username/password etc.
	 */
	protected function init_api() {
		include_once dirname( __FILE__ ) . '/includes/class-wc-gateway-paypal-api-handler.php';

		WC_Gateway_Paypal_API_Handler::$api_username  = $this->testmode ? $this->get_option( 'sandbox_api_username' ) : $this->get_option( 'api_username' );
		WC_Gateway_Paypal_API_Handler::$api_password  = $this->testmode ? $this->get_option( 'sandbox_api_password' ) : $this->get_option( 'api_password' );
		WC_Gateway_Paypal_API_Handler::$api_signature = $this->testmode ? $this->get_option( 'sandbox_api_signature' ) : $this->get_option( 'api_signature' );
		WC_Gateway_Paypal_API_Handler::$sandbox       = $this->testmode;
	}

	/**
	 * Process a refund if supported.
	 *
	 * @param  int    $order_id Order ID.
	 * @param  float  $amount Refund amount.
	 * @param  string $reason Refund reason.
	 * @return bool|WP_Error
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		if ( ! $this->can_refund_order( $order ) ) {
			return new WP_Error( 'error', __( 'Refund failed.', 'woocommerce' ) );
		}

		$this->init_api();

		$result = WC_Gateway_Paypal_API_Handler::refund_transaction( $order, $amount, $reason );

		if ( is_wp_error( $result ) ) {
			$this->log( 'Refund Failed: ' . $result->get_error_message(), 'error' );
			return new WP_Error( 'error', $result->get_error_message() );
		}

		$this->log( 'Refund Result: ' . wc_print_r( $result, true ) );

		switch ( strtolower( $result->ACK ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			case 'success':
			case 'successwithwarning':
				$order->add_order_note(
					/* translators: 1: Refund amount, 2: Refund ID */
					sprintf( __( 'Refunded %1$s - Refund ID: %2$s', 'woocommerce' ), $result->GROSSREFUNDAMT, $result->REFUNDTRANSACTIONID ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				);
				return true;
		}

		return isset( $result->L_LONGMESSAGE0 ) ? new WP_Error( 'error', $result->L_LONGMESSAGE0 ) : false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}

	/**
	 * Capture payment when the order is changed from on-hold to complete or processing
	 *
	 * @param  int $order_id Order ID.
	 */
	public function capture_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( 'paypal' === $order->get_payment_method() && 'pending' === $order->get_meta( '_paypal_status', true ) && $order->get_transaction_id() ) {
			$this->init_api();
			$result = WC_Gateway_Paypal_API_Handler::do_capture( $order );

			if ( is_wp_error( $result ) ) {
				$this->log( 'Capture Failed: ' . $result->get_error_message(), 'error' );
				/* translators: %s: Paypal gateway error message */
				$order->add_order_note( sprintf( __( 'Payment could not be captured: %s', 'woocommerce' ), $result->get_error_message() ) );
				return;
			}

			$this->log( 'Capture Result: ' . wc_print_r( $result, true ) );

			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( ! empty( $result->PAYMENTSTATUS ) ) {
				switch ( $result->PAYMENTSTATUS ) {
					case 'Completed':
						/* translators: 1: Amount, 2: Authorization ID, 3: Transaction ID */
						$order->add_order_note( sprintf( __( 'Payment of %1$s was captured - Auth ID: %2$s, Transaction ID: %3$s', 'woocommerce' ), $result->AMT, $result->AUTHORIZATIONID, $result->TRANSACTIONID ) );
						$order->update_meta_data( '_paypal_status', $result->PAYMENTSTATUS );
						$order->set_transaction_id( $result->TRANSACTIONID );
						$order->save();
						break;
					default:
						/* translators: 1: Authorization ID, 2: Payment status */
						$order->add_order_note( sprintf( __( 'Payment could not be captured - Auth ID: %1$s, Status: %2$s', 'woocommerce' ), $result->AUTHORIZATIONID, $result->PAYMENTSTATUS ) );
						break;
				}
			}
			// phpcs:enable
		}
	}

	/**
	 * Load admin scripts.
	 *
	 * @since 3.3.0
	 */
	public function admin_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'woocommerce_page_wc-settings' !== $screen_id ) {
			return;
		}

		$suffix  = Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';
		$version = Constants::get_constant( 'WC_VERSION' );

		wp_enqueue_script( 'woocommerce_paypal_admin', WC()->plugin_url() . '/includes/gateways/paypal/assets/js/paypal-admin' . $suffix . '.js', array(), $version, true );
	}

	/**
	 * Custom PayPal order received text.
	 *
	 * @since 3.9.0
	 * @param string   $text Default text.
	 * @param WC_Order $order Order data.
	 * @return string
	 */
	public function order_received_text( $text, $order ) {
		if ( $order && $this->id === $order->get_payment_method() ) {
			return esc_html__( 'Thank you for your payment. Your transaction has been completed, and a receipt for your purchase has been emailed to you. Log into your PayPal account to view transaction details.', 'woocommerce' );
		}

		return $text;
	}

	/**
	 * Determines whether PayPal Standard should be loaded or not.
	 *
	 * By default PayPal Standard isn't loaded on new installs or on existing sites which haven't set up the gateway.
	 *
	 * @since 5.5.0
	 *
	 * @return bool Whether PayPal Standard should be loaded.
	 */
	public function should_load() {
		$option_key  = '_should_load';
		$should_load = $this->get_option( $option_key );

		if ( '' === $should_load ) {

			// New installs without PayPal Standard enabled don't load it.
			if ( 'no' === $this->enabled && WC_Install::is_new_install() ) {
				$should_load = false;
			} else {
				$should_load = true;
			}

			$this->update_option( $option_key, wc_bool_to_string( $should_load ) );
		} else {
			$should_load = wc_string_to_bool( $should_load );
		}

		/**
		 * Allow third-parties to filter whether PayPal Standard should be loaded or not.
		 *
		 * @since 5.5.0
		 *
		 * @param bool              $should_load Whether PayPal Standard should be loaded.
		 * @param WC_Gateway_Paypal $this        The WC_Gateway_Paypal instance.
		 */
		return apply_filters( 'woocommerce_should_load_paypal_standard', $should_load, $this );
	}
}
