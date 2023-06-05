<?php
namespace Automattic\WooCommerce\Blocks\Payments;

use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Payments\Integrations\BankTransfer;
use Automattic\WooCommerce\Blocks\Payments\Integrations\CashOnDelivery;
use Automattic\WooCommerce\Blocks\Payments\Integrations\Cheque;
use Automattic\WooCommerce\Blocks\Payments\Integrations\PayPal;

/**
 *  The Api class provides an interface to payment method registration.
 *
 * @since 2.6.0
 */
class Api {
	/**
	 * Reference to the PaymentMethodRegistry instance.
	 *
	 * @var PaymentMethodRegistry
	 */
	private $payment_method_registry;

	/**
	 * Reference to the AssetDataRegistry instance.
	 *
	 * @var AssetDataRegistry
	 */
	private $asset_registry;

	/**
	 * Constructor
	 *
	 * @param PaymentMethodRegistry $payment_method_registry An instance of Payment Method Registry.
	 * @param AssetDataRegistry     $asset_registry  Used for registering data to pass along to the request.
	 */
	public function __construct( PaymentMethodRegistry $payment_method_registry, AssetDataRegistry $asset_registry ) {
		$this->payment_method_registry = $payment_method_registry;
		$this->asset_registry          = $asset_registry;
		$this->init();
	}

	/**
	 * Initialize class features.
	 */
	protected function init() {
		add_action( 'init', array( $this->payment_method_registry, 'initialize' ), 5 );
		add_filter( 'woocommerce_blocks_register_script_dependencies', array( $this, 'add_payment_method_script_dependencies' ), 10, 2 );
		add_action( 'woocommerce_blocks_checkout_enqueue_data', array( $this, 'add_payment_method_script_data' ) );
		add_action( 'woocommerce_blocks_cart_enqueue_data', array( $this, 'add_payment_method_script_data' ) );
		add_action( 'woocommerce_blocks_payment_method_type_registration', array( $this, 'register_payment_method_integrations' ) );
		add_action( 'wp_print_scripts', array( $this, 'verify_payment_methods_dependencies' ), 1 );
	}

	/**
	 * Add payment method script handles as script dependencies.
	 *
	 * @param array  $dependencies Array of script dependencies.
	 * @param string $handle Script handle.
	 * @return array
	 */
	public function add_payment_method_script_dependencies( $dependencies, $handle ) {
		if ( ! in_array( $handle, [ 'wc-checkout-block', 'wc-checkout-block-frontend', 'wc-cart-block', 'wc-cart-block-frontend' ], true ) ) {
			return $dependencies;
		}
		return array_merge( $dependencies, $this->payment_method_registry->get_all_active_payment_method_script_dependencies() );
	}

	/**
	 * Returns true if the payment gateway is enabled.
	 *
	 * @param object $gateway Payment gateway.
	 * @return boolean
	 */
	private function is_payment_gateway_enabled( $gateway ) {
		return filter_var( $gateway->enabled, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Add payment method data to Asset Registry.
	 */
	public function add_payment_method_script_data() {
		// Enqueue the order of enabled gateways as `paymentGatewaySortOrder`.
		if ( ! $this->asset_registry->exists( 'paymentGatewaySortOrder' ) ) {
			// We use payment_gateways() here to get the sort order of all enabled gateways. Some may be
			// programmatically disabled later on, but we still need to know where the enabled ones are in the list.
			$payment_gateways = WC()->payment_gateways->payment_gateways();
			$enabled_gateways = array_filter( $payment_gateways, array( $this, 'is_payment_gateway_enabled' ) );
			$this->asset_registry->add( 'paymentGatewaySortOrder', array_keys( $enabled_gateways ) );
		}

		// Enqueue all registered gateway data (settings/config etc).
		$script_data = $this->payment_method_registry->get_all_registered_script_data();
		foreach ( $script_data as $asset_data_key => $asset_data_value ) {
			if ( ! $this->asset_registry->exists( $asset_data_key ) ) {
				$this->asset_registry->add( $asset_data_key, $asset_data_value );
			}
		}
	}

	/**
	 * Register payment method integrations bundled with blocks.
	 *
	 * @param PaymentMethodRegistry $payment_method_registry Payment method registry instance.
	 */
	public function register_payment_method_integrations( PaymentMethodRegistry $payment_method_registry ) {
		$payment_method_registry->register(
			Package::container()->get( Cheque::class )
		);
		$payment_method_registry->register(
			Package::container()->get( PayPal::class )
		);
		$payment_method_registry->register(
			Package::container()->get( BankTransfer::class )
		);
		$payment_method_registry->register(
			Package::container()->get( CashOnDelivery::class )
		);
	}

	/**
	 * Verify all dependencies of registered payment methods have been registered.
	 * If not, remove that payment method script from the list of dependencies
	 * of Cart and Checkout block scripts so it doesn't break the blocks and show
	 * an error in the admin.
	 */
	public function verify_payment_methods_dependencies() {
		// Check that the wc-blocks script is registered before continuing. Some extensions may cause this function to run
		// before the payment method scripts' dependencies are registered.
		if ( ! wp_script_is( 'wc-blocks', 'registered' ) ) {
			return;
		}
		$wp_scripts             = wp_scripts();
		$payment_method_scripts = $this->payment_method_registry->get_all_active_payment_method_script_dependencies();

		foreach ( $payment_method_scripts as $payment_method_script ) {
			if (
				! array_key_exists( $payment_method_script, $wp_scripts->registered ) ||
				! property_exists( $wp_scripts->registered[ $payment_method_script ], 'deps' )
			) {
				continue;
			}
			$deps = $wp_scripts->registered[ $payment_method_script ]->deps;
			foreach ( $deps as $dep ) {
				if ( ! wp_script_is( $dep, 'registered' ) ) {
					$error_handle  = $dep . '-dependency-error';
					$error_message = sprintf(
						'Payment gateway with handle \'%1$s\' has been deactivated in Cart and Checkout blocks because its dependency \'%2$s\' is not registered. Read the docs about registering assets for payment methods: https://github.com/woocommerce/woocommerce-blocks/blob/060f63c04f0f34f645200b5d4da9212125c49177/docs/third-party-developers/extensibility/checkout-payment-methods/payment-method-integration.md#registering-assets',
						esc_html( $payment_method_script ),
						esc_html( $dep )
					);

					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log( $error_message );

					// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter,WordPress.WP.EnqueuedResourceParameters.MissingVersion
					wp_register_script( $error_handle, '' );
					wp_enqueue_script( $error_handle );
					wp_add_inline_script(
						$error_handle,
						sprintf( 'console.error( "%s" );', $error_message )
					);

					$cart_checkout_scripts = [ 'wc-cart-block', 'wc-cart-block-frontend', 'wc-checkout-block', 'wc-checkout-block-frontend' ];
					foreach ( $cart_checkout_scripts as $script_handle ) {
						if (
							! array_key_exists( $script_handle, $wp_scripts->registered ) ||
							! property_exists( $wp_scripts->registered[ $script_handle ], 'deps' )
						) {
							continue;
						}
						// Remove payment method script from dependencies.
						$wp_scripts->registered[ $script_handle ]->deps = array_diff(
							$wp_scripts->registered[ $script_handle ]->deps,
							[ $payment_method_script ]
						);
					}
				}
			}
		}
	}
}
