<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\StoreApi\Utilities\LocalPickupUtils;

/**
 * Checkout class.
 *
 * @internal
 */
class Checkout extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'checkout';

	/**
	 * Chunks build folder.
	 *
	 * @var string
	 */
	protected $chunks_folder = 'checkout-blocks';

	/**
	 * Get the editor script handle for this block type.
	 *
	 * @param string $key Data to get, or default to everything.
	 * @return array|string;
	 */
	protected function get_block_type_editor_script( $key = null ) {
		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name ),
			'dependencies' => [ 'wc-blocks' ],
		];
		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Get the frontend script handle for this block type.
	 *
	 * @see $this->register_block_type()
	 * @param string $key Data to get, or default to everything.
	 * @return array|string
	 */
	protected function get_block_type_script( $key = null ) {
		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block-frontend',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name . '-frontend' ),
			'dependencies' => [],
		];
		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Enqueue frontend assets for this block, just in time for rendering.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 */
	protected function enqueue_assets( array $attributes ) {
		/**
		 * Fires before checkout block scripts are enqueued.
		 *
		 * @since 4.6.0
		 */
		do_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_before' );
		parent::enqueue_assets( $attributes );
		/**
		 * Fires after checkout block scripts are enqueued.
		 *
		 * @since 4.6.0
		 */
		do_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_after' );
	}

	/**
	 * Append frontend scripts when rendering the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		if ( $this->is_checkout_endpoint() ) {
			// Note: Currently the block only takes care of the main checkout form -- if an endpoint is set, refer to the
			// legacy shortcode instead and do not render block.
			return '[woocommerce_checkout]';
		}

		// Deregister core checkout scripts and styles.
		wp_dequeue_script( 'wc-checkout' );
		wp_dequeue_script( 'wc-password-strength-meter' );
		wp_dequeue_script( 'selectWoo' );
		wp_dequeue_style( 'select2' );

		/**
		 * We need to check if $content has any templates from prior iterations of the block, in order to update to the latest iteration.
		 * We test the iteration version by searching for new blocks brought in by it.
		 * The blocks used for testing should be always available in the block (not removable by the user).
		 * Checkout i1's content was returning an empty div, with no data-block-name attribute
		 */
		$regex_for_empty_block = '/<div class="[a-zA-Z0-9_\- ]*wp-block-woocommerce-checkout[a-zA-Z0-9_\- ]*"><\/div>/mi';
		$has_i1_template       = preg_match( $regex_for_empty_block, $content );

		if ( $has_i1_template ) {
			// This fallback needs to match the default templates defined in our Blocks.
			$inner_blocks_html = '
				<div data-block-name="woocommerce/checkout-fields-block" class="wp-block-woocommerce-checkout-fields-block">
					<div data-block-name="woocommerce/checkout-express-payment-block" class="wp-block-woocommerce-checkout-express-payment-block"></div>
					<div data-block-name="woocommerce/checkout-contact-information-block" class="wp-block-woocommerce-checkout-contact-information-block"></div>
					<div data-block-name="woocommerce/checkout-shipping-address-block" class="wp-block-woocommerce-checkout-shipping-address-block"></div>
					<div data-block-name="woocommerce/checkout-billing-address-block" class="wp-block-woocommerce-checkout-billing-address-block"></div>
					<div data-block-name="woocommerce/checkout-shipping-methods-block" class="wp-block-woocommerce-checkout-shipping-methods-block"></div>
					<div data-block-name="woocommerce/checkout-payment-block" class="wp-block-woocommerce-checkout-payment-block"></div>' .
					( isset( $attributes['showOrderNotes'] ) && false === $attributes['showOrderNotes'] ? '' : '<div data-block-name="woocommerce/checkout-order-note-block" class="wp-block-woocommerce-checkout-order-note-block"></div>' ) .
					( isset( $attributes['showPolicyLinks'] ) && false === $attributes['showPolicyLinks'] ? '' : '<div data-block-name="woocommerce/checkout-terms-block" class="wp-block-woocommerce-checkout-terms-block"></div>' ) .
					'<div data-block-name="woocommerce/checkout-actions-block" class="wp-block-woocommerce-checkout-actions-block"></div>
				</div>
				<div data-block-name="woocommerce/checkout-totals-block" class="wp-block-woocommerce-checkout-totals-block">
					<div data-block-name="woocommerce/checkout-order-summary-block" class="wp-block-woocommerce-checkout-order-summary-block"></div>
				</div>
			';

			$content = str_replace( '</div>', $inner_blocks_html . '</div>', $content );
		}

		/**
		 * Checkout i3 added inner blocks for Order summary.
		 * We need to add them to Checkout i2 templates.
		 * The order needs to match the order in which these blocks were registered.
		 */
		$order_summary_with_inner_blocks = '$0
			<div data-block-name="woocommerce/checkout-order-summary-cart-items-block" class="wp-block-woocommerce-checkout-order-summary-cart-items-block"></div>
			<div data-block-name="woocommerce/checkout-order-summary-subtotal-block" class="wp-block-woocommerce-checkout-order-summary-subtotal-block"></div>
			<div data-block-name="woocommerce/checkout-order-summary-fee-block" class="wp-block-woocommerce-checkout-order-summary-fee-block"></div>
			<div data-block-name="woocommerce/checkout-order-summary-discount-block" class="wp-block-woocommerce-checkout-order-summary-discount-block"></div>
			<div data-block-name="woocommerce/checkout-order-summary-coupon-form-block" class="wp-block-woocommerce-checkout-order-summary-coupon-form-block"></div>
			<div data-block-name="woocommerce/checkout-order-summary-shipping-block" class="wp-block-woocommerce-checkout-order-summary-shipping-block"></div>
			<div data-block-name="woocommerce/checkout-order-summary-taxes-block" class="wp-block-woocommerce-checkout-order-summary-taxes-block"></div>
		';
		// Order summary subtotal block was added in i3, so we search for it to see if we have a Checkout i2 template.
		$regex_for_order_summary_subtotal = '/<div[^<]*?data-block-name="woocommerce\/checkout-order-summary-subtotal-block"[^>]*?>/mi';
		$regex_for_order_summary          = '/<div[^<]*?data-block-name="woocommerce\/checkout-order-summary-block"[^>]*?>/mi';
		$has_i2_template                  = ! preg_match( $regex_for_order_summary_subtotal, $content );

		if ( $has_i2_template ) {
			$content = preg_replace( $regex_for_order_summary, $order_summary_with_inner_blocks, $content );
		}

		/**
		 * Add the Local Pickup toggle to checkouts missing this forced template.
		 */
		$local_pickup_inner_blocks = '<div data-block-name="woocommerce/checkout-shipping-method-block" class="wp-block-woocommerce-checkout-shipping-method-block"></div>' . PHP_EOL . PHP_EOL . '<div data-block-name="woocommerce/checkout-pickup-options-block" class="wp-block-woocommerce-checkout-pickup-options-block"></div>' . PHP_EOL . PHP_EOL . '$0';
		$has_local_pickup_regex    = '/<div[^<]*?data-block-name="woocommerce\/checkout-shipping-method-block"[^>]*?>/mi';
		$has_local_pickup          = preg_match( $has_local_pickup_regex, $content );

		if ( ! $has_local_pickup ) {
			$shipping_address_block_regex = '/<div[^<]*?data-block-name="woocommerce\/checkout-shipping-address-block" class="wp-block-woocommerce-checkout-shipping-address-block"[^>]*?><\/div>/mi';
			$content                      = preg_replace( $shipping_address_block_regex, $local_pickup_inner_blocks, $content );
		}

		return $content;
	}

	/**
	 * Check if we're viewing a checkout page endpoint, rather than the main checkout page itself.
	 *
	 * @return boolean
	 */
	protected function is_checkout_endpoint() {
		return is_wc_endpoint_url( 'order-pay' ) || is_wc_endpoint_url( 'order-received' );
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		parent::enqueue_data( $attributes );

		$this->asset_data_registry->add(
			'allowedCountries',
			function() {
				return $this->deep_sort_with_accents( WC()->countries->get_allowed_countries() );
			},
			true
		);
		$this->asset_data_registry->add(
			'allowedStates',
			function() {
				return $this->deep_sort_with_accents( WC()->countries->get_allowed_country_states() );
			},
			true
		);
		if ( wc_shipping_enabled() ) {
			$this->asset_data_registry->add(
				'shippingCountries',
				function() {
					return $this->deep_sort_with_accents( WC()->countries->get_shipping_countries() );
				},
				true
			);
			$this->asset_data_registry->add(
				'shippingStates',
				function() {
					return $this->deep_sort_with_accents( WC()->countries->get_shipping_country_states() );
				},
				true
			);
		}

		$this->asset_data_registry->add(
			'countryLocale',
			function() {
				// Merge country and state data to work around https://github.com/woocommerce/woocommerce/issues/28944.
				$country_locale = wc()->countries->get_country_locale();
				$states         = wc()->countries->get_states();

				foreach ( $states as $country => $states ) {
					if ( empty( $states ) ) {
						$country_locale[ $country ]['state']['required'] = false;
						$country_locale[ $country ]['state']['hidden']   = true;
					}
				}
				return $country_locale;
			},
			true
		);
		$this->asset_data_registry->add( 'baseLocation', wc_get_base_location(), true );
		$this->asset_data_registry->add(
			'checkoutAllowsGuest',
			false === filter_var(
				wc()->checkout()->is_registration_required(),
				FILTER_VALIDATE_BOOLEAN
			),
			true
		);
		$this->asset_data_registry->add(
			'checkoutAllowsSignup',
			filter_var(
				wc()->checkout()->is_registration_enabled(),
				FILTER_VALIDATE_BOOLEAN
			),
			true
		);
		$this->asset_data_registry->add( 'checkoutShowLoginReminder', filter_var( get_option( 'woocommerce_enable_checkout_login_reminder' ), FILTER_VALIDATE_BOOLEAN ), true );
		$this->asset_data_registry->add( 'displayCartPricesIncludingTax', 'incl' === get_option( 'woocommerce_tax_display_cart' ), true );
		$this->asset_data_registry->add( 'displayItemizedTaxes', 'itemized' === get_option( 'woocommerce_tax_total_display' ), true );
		$this->asset_data_registry->add( 'forcedBillingAddress', 'billing_only' === get_option( 'woocommerce_ship_to_destination' ), true );
		$this->asset_data_registry->add( 'taxesEnabled', wc_tax_enabled(), true );
		$this->asset_data_registry->add( 'couponsEnabled', wc_coupons_enabled(), true );
		$this->asset_data_registry->add( 'shippingEnabled', wc_shipping_enabled(), true );
		$this->asset_data_registry->add( 'hasDarkEditorStyleSupport', current_theme_supports( 'dark-editor-style' ), true );
		$this->asset_data_registry->register_page_id( isset( $attributes['cartPageId'] ) ? $attributes['cartPageId'] : 0 );

		$pickup_location_settings = get_option( 'woocommerce_pickup_location_settings', [] );
		$local_pickup_enabled     = wc_string_to_bool( $pickup_location_settings['enabled'] ?? 'no' );

		$this->asset_data_registry->add( 'localPickupEnabled', $local_pickup_enabled, true );

		$is_block_editor = $this->is_block_editor();

		// Hydrate the following data depending on admin or frontend context.
		if ( $is_block_editor && ! $this->asset_data_registry->exists( 'shippingMethodsExist' ) ) {
			$methods_exist = wc_get_shipping_method_count( false, true ) > 0;
			$this->asset_data_registry->add( 'shippingMethodsExist', $methods_exist );
		}

		if ( $is_block_editor && ! $this->asset_data_registry->exists( 'globalShippingMethods' ) ) {
			$shipping_methods           = WC()->shipping()->get_shipping_methods();
			$formatted_shipping_methods = array_reduce(
				$shipping_methods,
				function( $acc, $method ) {
					if ( in_array( $method->id, LocalPickupUtils::get_local_pickup_method_ids(), true ) ) {
						return $acc;
					}
					if ( $method->supports( 'settings' ) ) {
						$acc[] = [
							'id'          => $method->id,
							'title'       => $method->method_title,
							'description' => $method->method_description,
						];
					}
					return $acc;
				},
				[]
			);
			$this->asset_data_registry->add( 'globalShippingMethods', $formatted_shipping_methods );
		}

		if ( $is_block_editor && ! $this->asset_data_registry->exists( 'activeShippingZones' ) && class_exists( '\WC_Shipping_Zones' ) ) {
			$shipping_zones             = \WC_Shipping_Zones::get_zones();
			$formatted_shipping_zones   = array_reduce(
				$shipping_zones,
				function( $acc, $zone ) {
					$acc[] = [
						'id'          => $zone['id'],
						'title'       => $zone['zone_name'],
						'description' => $zone['formatted_zone_location'],
					];
					return $acc;
				},
				[]
			);
			$formatted_shipping_zones[] = [
				'id'          => 0,
				'title'       => __( 'International', 'woocommerce' ),
				'description' => __( 'Locations outside all other zones', 'woocommerce' ),
			];
			$this->asset_data_registry->add( 'activeShippingZones', $formatted_shipping_zones );
		}

		if ( $is_block_editor && ! $this->asset_data_registry->exists( 'globalPaymentMethods' ) ) {
			// These are used to show options in the sidebar. We want to get the full list of enabled payment methods,
			// not just the ones that are available for the current cart (which may not exist yet).
			$payment_methods           = $this->get_enabled_payment_gateways();
			$formatted_payment_methods = array_reduce(
				$payment_methods,
				function( $acc, $method ) {
					$acc[] = [
						'id'          => $method->id,
						'title'       => $method->method_title,
						'description' => $method->method_description,
					];
					return $acc;
				},
				[]
			);
			$this->asset_data_registry->add( 'globalPaymentMethods', $formatted_payment_methods );
		}

		if ( ! is_admin() && ! WC()->is_rest_api_request() ) {
			$this->hydrate_from_api();
			$this->hydrate_customer_payment_methods();
		}

		/**
		 * Fires after checkout block data is registered.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_blocks_checkout_enqueue_data' );
	}

	/**
	 * Get payment methods that are enabled in settings.
	 *
	 * @return array
	 */
	protected function get_enabled_payment_gateways() {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		return array_filter(
			$payment_gateways,
			function( $payment_gateway ) {
				return 'yes' === $payment_gateway->enabled;
			}
		);
	}

	/**
	 * Are we currently on the admin block editor screen?
	 */
	protected function is_block_editor() {
		if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
			return false;
		}
		$screen = get_current_screen();

		return $screen && $screen->is_block_editor();
	}

	/**
	 * Removes accents from an array of values, sorts by the values, then returns the original array values sorted.
	 *
	 * @param array $array Array of values to sort.
	 * @return array Sorted array.
	 */
	protected function deep_sort_with_accents( $array ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return $array;
		}

		if ( is_array( reset( $array ) ) ) {
			return array_map( [ $this, 'deep_sort_with_accents' ], $array );
		}

		$array_without_accents = array_map( 'remove_accents', array_map( 'wc_strtolower', array_map( 'html_entity_decode', $array ) ) );
		asort( $array_without_accents );
		return array_replace( $array_without_accents, $array );
	}

	/**
	 * Get saved customer payment methods for use in checkout.
	 */
	protected function hydrate_customer_payment_methods() {
		if ( ! is_user_logged_in() || $this->asset_data_registry->exists( 'customerPaymentMethods' ) ) {
			return;
		}
		add_filter( 'woocommerce_payment_methods_list_item', [ $this, 'include_token_id_with_payment_methods' ], 10, 2 );

		$payment_gateways = $this->get_enabled_payment_gateways();
		$payment_methods  = wc_get_customer_saved_methods_list( get_current_user_id() );

		// Filter out payment methods that are not enabled.
		foreach ( $payment_methods as $payment_method_group => $saved_payment_methods ) {
			$payment_methods[ $payment_method_group ] = array_filter(
				$saved_payment_methods,
				function( $saved_payment_method ) use ( $payment_gateways ) {
					return in_array( $saved_payment_method['method']['gateway'], array_keys( $payment_gateways ), true );
				}
			);
		}

		$this->asset_data_registry->add(
			'customerPaymentMethods',
			$payment_methods
		);
		remove_filter( 'woocommerce_payment_methods_list_item', [ $this, 'include_token_id_with_payment_methods' ], 10, 2 );
	}

	/**
	 * Hydrate the checkout block with data from the API.
	 */
	protected function hydrate_from_api() {
		$this->asset_data_registry->hydrate_api_request( '/wc/store/v1/cart' );

		// Print existing notices now, otherwise they are caught by the Cart
		// Controller and converted to exceptions.
		wc_print_notices();
		add_filter( 'woocommerce_store_api_disable_nonce_check', '__return_true' );

		$rest_preload_api_requests = rest_preload_api_request( [], '/wc/store/v1/checkout' );
		$this->asset_data_registry->add( 'checkoutData', $rest_preload_api_requests['/wc/store/v1/checkout']['body'] ?? [] );

		remove_filter( 'woocommerce_store_api_disable_nonce_check', '__return_true' );
	}

	/**
	 * Callback for woocommerce_payment_methods_list_item filter to add token id
	 * to the generated list.
	 *
	 * @param array     $list_item The current list item for the saved payment method.
	 * @param \WC_Token $token     The token for the current list item.
	 *
	 * @return array The list item with the token id added.
	 */
	public static function include_token_id_with_payment_methods( $list_item, $token ) {
		$list_item['tokenId'] = $token->get_id();
		$brand                = ! empty( $list_item['method']['brand'] ) ?
			strtolower( $list_item['method']['brand'] ) :
			'';
		// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch -- need to match on translated value from core.
		if ( ! empty( $brand ) && esc_html__( 'Credit card', 'woocommerce' ) !== $brand ) {
			$list_item['method']['brand'] = wc_get_credit_card_type_label( $brand );
		}
		return $list_item;
	}
	/**
	 * Register script and style assets for the block type before it is registered.
	 *
	 * This registers the scripts; it does not enqueue them.
	 */
	protected function register_block_type_assets() {
		parent::register_block_type_assets();
		$chunks        = $this->get_chunks_paths( $this->chunks_folder );
		$vendor_chunks = $this->get_chunks_paths( 'vendors--checkout-blocks' );
		$shared_chunks = [ 'cart-blocks/cart-express-payment--checkout-blocks/express-payment-frontend' ];
		$this->register_chunk_translations( array_merge( $chunks, $vendor_chunks, $shared_chunks ) );
	}

	/**
	 * Get list of Checkout block & its inner-block types.
	 *
	 * @return array;
	 */
	public static function get_checkout_block_types() {
		return [
			'Checkout',
			'CheckoutActionsBlock',
			'CheckoutBillingAddressBlock',
			'CheckoutContactInformationBlock',
			'CheckoutExpressPaymentBlock',
			'CheckoutFieldsBlock',
			'CheckoutOrderNoteBlock',
			'CheckoutOrderSummaryBlock',
			'CheckoutOrderSummaryCartItemsBlock',
			'CheckoutOrderSummaryCouponFormBlock',
			'CheckoutOrderSummaryDiscountBlock',
			'CheckoutOrderSummaryFeeBlock',
			'CheckoutOrderSummaryShippingBlock',
			'CheckoutOrderSummarySubtotalBlock',
			'CheckoutOrderSummaryTaxesBlock',
			'CheckoutPaymentBlock',
			'CheckoutShippingAddressBlock',
			'CheckoutShippingMethodsBlock',
			'CheckoutShippingMethodBlock',
			'CheckoutPickupOptionsBlock',
			'CheckoutTermsBlock',
			'CheckoutTotalsBlock',
		];
	}
}
