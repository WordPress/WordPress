<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;

/**
 * Cart class.
 *
 * @internal
 */
class Cart extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'cart';

	/**
	 * Chunks build folder.
	 *
	 * @var string
	 */
	protected $chunks_folder = 'cart-blocks';

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
		 * Fires before cart block scripts are enqueued.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_blocks_enqueue_cart_block_scripts_before' );
		parent::enqueue_assets( $attributes );
		/**
		 * Fires after cart block scripts are enqueued.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after' );
	}

	/**
	 * Append frontend scripts when rendering the Cart block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		// Deregister core cart scripts and styles.
		wp_dequeue_script( 'wc-cart' );
		wp_dequeue_script( 'wc-password-strength-meter' );
		wp_dequeue_script( 'selectWoo' );
		wp_dequeue_style( 'select2' );

		/**
		 * We need to check if $content has any templates from prior iterations of the block, in order to update to the latest iteration.
		 * We test the iteration version by searching for new blocks brought in by it.
		 * The blocks used for testing should be always available in the block (not removable by the user).
		 */

		$regex_for_filled_cart_block = '/<div[^<]*?data-block-name="woocommerce\/filled-cart-block"[^>]*?>/mi';
		// Filled Cart block was added in i2, so we search for it to see if we have a Cart i1 template.
		$has_i1_template = ! preg_match( $regex_for_filled_cart_block, $content );

		if ( $has_i1_template ) {
			/**
			 * This fallback structure needs to match the defaultTemplate variables defined in the block's edit.tsx files,
			 * starting from the parent block and going down each inner block, in the order the blocks were registered.
			 */
			$inner_blocks_html = '$0
			<div data-block-name="woocommerce/filled-cart-block" class="wp-block-woocommerce-filled-cart-block">
				<div data-block-name="woocommerce/cart-items-block" class="wp-block-woocommerce-cart-items-block">
					<div data-block-name="woocommerce/cart-line-items-block" class="wp-block-woocommerce-cart-line-items-block"></div>
				</div>
				<div data-block-name="woocommerce/cart-totals-block" class="wp-block-woocommerce-cart-totals-block">
					<div data-block-name="woocommerce/cart-order-summary-block" class="wp-block-woocommerce-cart-order-summary-block"></div>
					<div data-block-name="woocommerce/cart-express-payment-block" class="wp-block-woocommerce-cart-express-payment-block"></div>
					<div data-block-name="woocommerce/proceed-to-checkout-block" class="wp-block-woocommerce-proceed-to-checkout-block"></div>
					<div data-block-name="woocommerce/cart-accepted-payment-methods-block" class="wp-block-woocommerce-cart-accepted-payment-methods-block"></div>
				</div>
			</div>
			<div data-block-name="woocommerce/empty-cart-block" class="wp-block-woocommerce-empty-cart-block">
			';

			$content = preg_replace( '/<div class="[a-zA-Z0-9_\- ]*wp-block-woocommerce-cart[a-zA-Z0-9_\- ]*">/mi', $inner_blocks_html, $content );
			$content = $content . '</div>';
		}

		/**
		 * Cart i3 added inner blocks for Order summary. We need to add them to Cart i2 templates.
		 * The order needs to match the order in which these blocks were registered.
		 */
		$order_summary_with_inner_blocks = '$0
			<div data-block-name="woocommerce/cart-order-summary-heading-block" class="wp-block-woocommerce-cart-order-summary-heading-block"></div>
			<div data-block-name="woocommerce/cart-order-summary-subtotal-block" class="wp-block-woocommerce-cart-order-summary-subtotal-block"></div>
			<div data-block-name="woocommerce/cart-order-summary-fee-block" class="wp-block-woocommerce-cart-order-summary-fee-block"></div>
			<div data-block-name="woocommerce/cart-order-summary-discount-block" class="wp-block-woocommerce-cart-order-summary-discount-block"></div>
			<div data-block-name="woocommerce/cart-order-summary-coupon-form-block" class="wp-block-woocommerce-cart-order-summary-coupon-form-block"></div>
			<div data-block-name="woocommerce/cart-order-summary-shipping-form-block" class="wp-block-woocommerce-cart-order-summary-shipping-block"></div>
			<div data-block-name="woocommerce/cart-order-summary-taxes-block" class="wp-block-woocommerce-cart-order-summary-taxes-block"></div>
		';
		// Order summary subtotal block was added in i3, so we search for it to see if we have a Cart i2 template.
		$regex_for_order_summary_subtotal = '/<div[^<]*?data-block-name="woocommerce\/cart-order-summary-subtotal-block"[^>]*?>/mi';
		$regex_for_order_summary          = '/<div[^<]*?data-block-name="woocommerce\/cart-order-summary-block"[^>]*?>/mi';
		$has_i2_template                  = ! preg_match( $regex_for_order_summary_subtotal, $content );

		if ( $has_i2_template ) {
			$content = preg_replace( $regex_for_order_summary, $order_summary_with_inner_blocks, $content );
		}

		return $content;
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
		$this->asset_data_registry->add( 'isShippingCalculatorEnabled', filter_var( get_option( 'woocommerce_enable_shipping_calc' ), FILTER_VALIDATE_BOOLEAN ), true );
		$this->asset_data_registry->add( 'displayItemizedTaxes', 'itemized' === get_option( 'woocommerce_tax_total_display' ), true );
		$this->asset_data_registry->add( 'displayCartPricesIncludingTax', 'incl' === get_option( 'woocommerce_tax_display_cart' ), true );
		$this->asset_data_registry->add( 'taxesEnabled', wc_tax_enabled(), true );
		$this->asset_data_registry->add( 'couponsEnabled', wc_coupons_enabled(), true );
		$this->asset_data_registry->add( 'shippingEnabled', wc_shipping_enabled(), true );
		$this->asset_data_registry->add( 'hasDarkEditorStyleSupport', current_theme_supports( 'dark-editor-style' ), true );
		$this->asset_data_registry->register_page_id( isset( $attributes['checkoutPageId'] ) ? $attributes['checkoutPageId'] : 0 );

		// Hydrate the following data depending on admin or frontend context.
		if ( ! is_admin() && ! WC()->is_rest_api_request() ) {
			$this->hydrate_from_api();
		}

		/**
		 * Fires after cart block data is registered.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_blocks_cart_enqueue_data' );
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

		$array_without_accents = array_map(
			function( $value ) {
				return is_array( $value )
					? $this->deep_sort_with_accents( $value )
					: remove_accents( wc_strtolower( html_entity_decode( $value ) ) );
			},
			$array
		);

		asort( $array_without_accents );
		return array_replace( $array_without_accents, $array );
	}

	/**
	 * Hydrate the cart block with data from the API.
	 */
	protected function hydrate_from_api() {
		$this->asset_data_registry->hydrate_api_request( '/wc/store/v1/cart' );
	}
	/**
	 * Register script and style assets for the block type before it is registered.
	 *
	 * This registers the scripts; it does not enqueue them.
	 */
	protected function register_block_type_assets() {
		parent::register_block_type_assets();
		$chunks        = $this->get_chunks_paths( $this->chunks_folder );
		$vendor_chunks = $this->get_chunks_paths( 'vendors--cart-blocks' );
		$shared_chunks = [];
		$this->register_chunk_translations( array_merge( $chunks, $vendor_chunks, $shared_chunks ) );
	}

	/**
	 * Get list of Cart block & its inner-block types.
	 *
	 * @return array;
	 */
	public static function get_cart_block_types() {
		return [
			'Cart',
			'CartOrderSummaryTaxesBlock',
			'CartOrderSummarySubtotalBlock',
			'FilledCartBlock',
			'EmptyCartBlock',
			'CartTotalsBlock',
			'CartItemsBlock',
			'CartLineItemsBlock',
			'CartOrderSummaryBlock',
			'CartExpressPaymentBlock',
			'ProceedToCheckoutBlock',
			'CartAcceptedPaymentMethodsBlock',
			'CartOrderSummaryCouponFormBlock',
			'CartOrderSummaryDiscountBlock',
			'CartOrderSummaryFeeBlock',
			'CartOrderSummaryHeadingBlock',
			'CartOrderSummaryShippingBlock',
			'CartCrossSellsBlock',
			'CartCrossSellsProductsBlock',
		];
	}
}
