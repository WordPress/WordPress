<?php
namespace Automattic\WooCommerce\Blocks\BlockTypes;

use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\Assets\Api as AssetApi;
use Automattic\WooCommerce\Blocks\Integrations\IntegrationRegistry;
use Automattic\WooCommerce\Blocks\Utils\StyleAttributesUtils;
use Automattic\WooCommerce\Blocks\Utils\BlockTemplateUtils;

/**
 * Mini Cart class.
 *
 * @internal
 */
class MiniCart extends AbstractBlock {
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = 'mini-cart';

	/**
	 * Chunks build folder.
	 *
	 * @var string
	 */
	protected $chunks_folder = 'mini-cart-contents-block';

	/**
	 * Array of scripts that will be lazy loaded when interacting with the block.
	 *
	 * @var string[]
	 */
	protected $scripts_to_lazy_load = array();

	/**
	 *  Inc Tax label.
	 *
	 * @var string
	 */
	protected $tax_label = '';

	/**
	 *  Visibility of price including tax.
	 *
	 * @var string
	 */
	protected $display_cart_prices_including_tax = false;

	/**
	 * Constructor.
	 *
	 * @param AssetApi            $asset_api Instance of the asset API.
	 * @param AssetDataRegistry   $asset_data_registry Instance of the asset data registry.
	 * @param IntegrationRegistry $integration_registry Instance of the integration registry.
	 */
	public function __construct( AssetApi $asset_api, AssetDataRegistry $asset_data_registry, IntegrationRegistry $integration_registry ) {
		parent::__construct( $asset_api, $asset_data_registry, $integration_registry, $this->block_name );
	}

	/**
	 * Initialize this block type.
	 *
	 * - Hook into WP lifecycle.
	 * - Register the block with WordPress.
	 */
	protected function initialize() {
		parent::initialize();
		add_action( 'wp_loaded', array( $this, 'register_empty_cart_message_block_pattern' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'enqueue_wc_settings' ), 1 );
		// We need this action to run after the equivalent in AssetDataRegistry.
		add_action( 'wp_print_footer_scripts', array( $this, 'print_lazy_load_scripts' ), 3 );
	}

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
		if ( is_cart() || is_checkout() ) {
			return;
		}

		$script = [
			'handle'       => 'wc-' . $this->block_name . '-block-frontend',
			'path'         => $this->asset_api->get_block_asset_build_path( $this->block_name . '-frontend' ),
			'dependencies' => [],
		];
		return $key ? $script[ $key ] : $script;
	}

	/**
	 * Extra data passed through from server to client for block.
	 *
	 * @param array $attributes  Any attributes that currently are available from the block.
	 *                           Note, this will be empty in the editor context when the block is
	 *                           not in the post content on editor load.
	 */
	protected function enqueue_data( array $attributes = [] ) {
		if ( is_cart() || is_checkout() ) {
			return;
		}

		parent::enqueue_data( $attributes );

		// Hydrate the following data depending on admin or frontend context.
		if ( ! is_admin() && ! WC()->is_rest_api_request() ) {
			$label_info = $this->get_tax_label();

			$this->tax_label                         = $label_info['tax_label'];
			$this->display_cart_prices_including_tax = $label_info['display_cart_prices_including_tax'];

			$this->asset_data_registry->add(
				'taxLabel',
				$this->tax_label,
				''
			);

			$cart_payload = $this->get_cart_payload();

			$this->asset_data_registry->add(
				'cartTotals',
				isset( $cart_payload['totals'] ) ? $cart_payload['totals'] : null,
				null
			);

			$this->asset_data_registry->add(
				'cartItemsCount',
				isset( $cart_payload['items_count'] ) ? $cart_payload['items_count'] : null,
				null
			);
		}

		$this->asset_data_registry->add(
			'displayCartPricesIncludingTax',
			$this->display_cart_prices_including_tax,
			true
		);

		$template_part_edit_uri = '';

		if (
			current_user_can( 'edit_theme_options' ) &&
			wc_current_theme_is_fse_theme()
		) {
			$theme_slug = BlockTemplateUtils::theme_has_template_part( 'mini-cart' ) ? wp_get_theme()->get_stylesheet() : BlockTemplateUtils::PLUGIN_SLUG;

			if ( version_compare( get_bloginfo( 'version' ), '5.9', '<' ) ) {
				$site_editor_uri = add_query_arg(
					array( 'page' => 'gutenberg-edit-site' ),
					admin_url( 'themes.php' )
				);
			} else {
				$site_editor_uri = add_query_arg(
					array(
						'canvas' => 'edit',
						'path'   => '/template-parts/single',
					),
					admin_url( 'site-editor.php' )
				);
			}

			$template_part_edit_uri = add_query_arg(
				array(
					'postId'   => sprintf( '%s//%s', $theme_slug, 'mini-cart' ),
					'postType' => 'wp_template_part',
				),
				$site_editor_uri
			);
		}

		$this->asset_data_registry->add(
			'templatePartEditUri',
			$template_part_edit_uri,
			''
		);

		/**
		 * Fires after cart block data is registered.
		 *
		 * @since 5.8.0
		 */
		do_action( 'woocommerce_blocks_cart_enqueue_data' );
	}

	/**
	 * Function to enqueue `wc-settings` script and dequeue it later on so when
	 * AssetDataRegistry runs, it appears enqueued- This allows the necessary
	 * data to be printed to the page.
	 */
	public function enqueue_wc_settings() {
		// Return early if another block has already enqueued `wc-settings`.
		if ( wp_script_is( 'wc-settings', 'enqueued' ) ) {
			return;
		}
		// We are lazy-loading `wc-settings`, but we need to enqueue it here so
		// AssetDataRegistry knows it's going to load.
		wp_enqueue_script( 'wc-settings' );
		// After AssetDataRegistry function runs, we dequeue `wc-settings`.
		add_action( 'wp_print_footer_scripts', array( $this, 'dequeue_wc_settings' ), 4 );
	}

	/**
	 * Function to dequeue `wc-settings` script.
	 */
	public function dequeue_wc_settings() {
		wp_dequeue_script( 'wc-settings' );
	}

	/**
	 * Prints the variable containing information about the scripts to lazy load.
	 */
	public function print_lazy_load_scripts() {
		$script_data = $this->asset_api->get_script_data( 'build/mini-cart-component-frontend.js' );

		$num_dependencies = count( $script_data['dependencies'] );
		$wp_scripts       = wp_scripts();

		for ( $i = 0; $i < $num_dependencies; $i++ ) {
			$dependency = $script_data['dependencies'][ $i ];

			foreach ( $wp_scripts->registered as $script ) {
				if ( $script->handle === $dependency ) {
					$this->append_script_and_deps_src( $script );
					break;
				}
			}
		}

		$payment_method_registry = Package::container()->get( PaymentMethodRegistry::class );
		$payment_methods         = $payment_method_registry->get_all_active_payment_method_script_dependencies();

		foreach ( $payment_methods as $payment_method ) {
			$payment_method_script = $this->get_script_from_handle( $payment_method );

			if ( ! is_null( $payment_method_script ) ) {
				$this->append_script_and_deps_src( $payment_method_script );
			}
		}

		$this->scripts_to_lazy_load['wc-block-mini-cart-component-frontend'] = array(
			'src'          => $script_data['src'],
			'version'      => $script_data['version'],
			'translations' => $this->get_inner_blocks_translations(),
		);

		$inner_blocks_frontend_scripts = array();
		$cart                          = $this->get_cart_instance();
		if ( $cart ) {
			// Preload inner blocks frontend scripts.
			$inner_blocks_frontend_scripts = $cart->is_empty() ? array(
				'empty-cart-frontend',
				'filled-cart-frontend',
				'shopping-button-frontend',
			) : array(
				'empty-cart-frontend',
				'filled-cart-frontend',
				'title-frontend',
				'items-frontend',
				'footer-frontend',
				'products-table-frontend',
				'cart-button-frontend',
				'checkout-button-frontend',
				'title-label-frontend',
				'title-items-counter-frontend',
			);
		}
		foreach ( $inner_blocks_frontend_scripts as $inner_block_frontend_script ) {
			$script_data = $this->asset_api->get_script_data( 'build/mini-cart-contents-block/' . $inner_block_frontend_script . '.js' );
			$this->scripts_to_lazy_load[ 'wc-block-' . $inner_block_frontend_script ] = array(
				'src'     => $script_data['src'],
				'version' => $script_data['version'],
			);
		}

		$data                          = rawurlencode( wp_json_encode( $this->scripts_to_lazy_load ) );
		$mini_cart_dependencies_script = "var wcBlocksMiniCartFrontendDependencies = JSON.parse( decodeURIComponent( '" . esc_js( $data ) . "' ) );";

		wp_add_inline_script(
			'wc-mini-cart-block-frontend',
			$mini_cart_dependencies_script,
			'before'
		);
	}

	/**
	 * Returns the script data given its handle.
	 *
	 * @param string $handle Handle of the script.
	 *
	 * @return \_WP_Dependency|null Object containing the script data if found, or null.
	 */
	protected function get_script_from_handle( $handle ) {
		$wp_scripts = wp_scripts();
		foreach ( $wp_scripts->registered as $script ) {
			if ( $script->handle === $handle ) {
				return $script;
			}
		}
		return null;
	}

	/**
	 * Recursively appends a scripts and its dependencies into the scripts_to_lazy_load array.
	 *
	 * @param \_WP_Dependency $script Object containing script data.
	 */
	protected function append_script_and_deps_src( $script ) {
		$wp_scripts = wp_scripts();

		// This script and its dependencies have already been appended.
		if ( ! $script || array_key_exists( $script->handle, $this->scripts_to_lazy_load ) || isset( $wp_scripts->queue[ $script->handle ] ) ) {
			return;
		}

		if ( count( $script->deps ) ) {
			foreach ( $script->deps as $dep ) {
				if ( ! array_key_exists( $dep, $this->scripts_to_lazy_load ) ) {
					$dep_script = $this->get_script_from_handle( $dep );

					if ( ! is_null( $dep_script ) ) {
						$this->append_script_and_deps_src( $dep_script );
					}
				}
			}
		}
		if ( ! $script->src ) {
			return;
		}

		$site_url = site_url() ?? wp_guess_url();

		$this->scripts_to_lazy_load[ $script->handle ] = array(
			'src'          => preg_match( '|^(https?:)?//|', $script->src ) ? $script->src : $site_url . $script->src,
			'version'      => $script->ver,
			'before'       => $wp_scripts->print_inline_script( $script->handle, 'before', false ),
			'after'        => $wp_scripts->print_inline_script( $script->handle, 'after', false ),
			'translations' => $wp_scripts->print_translations( $script->handle, false ),
		);
	}

	/**
	 * Returns the markup for the cart price.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return string
	 */
	protected function get_cart_price_markup( $attributes ) {
		if ( isset( $attributes['hasHiddenPrice'] ) && false !== $attributes['hasHiddenPrice'] ) {
			return;
		}

		$cart                = $this->get_cart_instance();
		$cart_contents_total = $cart->get_subtotal();

		if ( $cart->display_prices_including_tax() ) {
			$cart_contents_total += $cart->get_subtotal_tax();
		}

		return '<span class="wc-block-mini-cart__amount">' . esc_html( wp_strip_all_tags( wc_price( $cart_contents_total ) ) ) . '</span>
		' . $this->get_include_tax_label_markup();
	}

	/**
	 * Returns the markup for render the tax label.
	 *
	 * @return string
	 */
	protected function get_include_tax_label_markup() {
		$cart                = $this->get_cart_instance();
		$cart_contents_total = $cart->get_subtotal();

		return ( ! empty( $this->tax_label ) && 0 !== $cart_contents_total ) ? ( "<small class='wc-block-mini-cart__tax-label'>" . esc_html( $this->tax_label ) . '</small>' ) : '';
	}

	/**
	 * Append frontend scripts when rendering the Mini Cart block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string Rendered block type output.
	 */
	protected function render( $attributes, $content, $block ) {
		return $content . $this->get_markup( $attributes );
	}

	/**
	 * Render the markup for the Mini Cart block.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return string The HTML markup.
	 */
	protected function get_markup( $attributes ) {
		if ( is_admin() || WC()->is_rest_api_request() ) {
			// In the editor we will display the placeholder, so no need to load
			// real cart data and to print the markup.
			return '';
		}

		$cart                = $this->get_cart_instance();
		$cart_contents_count = $cart->get_cart_contents_count();
		$cart_contents_total = $cart->get_subtotal();

		if ( $cart->display_prices_including_tax() ) {
			$cart_contents_total += $cart->get_subtotal_tax();
		}

		$classes_styles  = StyleAttributesUtils::get_classes_and_styles_by_attributes( $attributes, array( 'text_color', 'background_color', 'font_size', 'font_weight', 'font_family' ) );
		$wrapper_classes = sprintf( 'wc-block-mini-cart wp-block-woocommerce-mini-cart %s', $classes_styles['classes'] );
		if ( ! empty( $attributes['className'] ) ) {
			$wrapper_classes .= ' ' . $attributes['className'];
		}
		$wrapper_styles = $classes_styles['styles'];

		$aria_label = sprintf(
			/* translators: %1$d is the number of products in the cart. %2$s is the cart total */
			_n(
				'%1$d item in cart, total price of %2$s',
				'%1$d items in cart, total price of %2$s',
				$cart_contents_count,
				'woocommerce'
			),
			$cart_contents_count,
			wp_strip_all_tags( wc_price( $cart_contents_total ) )
		);
		$icon        = '<svg class="wc-block-mini-cart__icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M7.84614 18.2769C7.89712 18.2769 7.93845 18.2356 7.93845 18.1846C7.93845 18.1336 7.89712 18.0923 7.84614 18.0923C7.79516 18.0923 7.75384 18.1336 7.75384 18.1846C7.75384 18.2356 7.79516 18.2769 7.84614 18.2769ZM6.03076 18.1846C6.03076 17.182 6.84353 16.3692 7.84614 16.3692C8.84875 16.3692 9.66152 17.182 9.66152 18.1846C9.66152 19.1872 8.84875 20 7.84614 20C6.84353 20 6.03076 19.1872 6.03076 18.1846Z" fill="currentColor"/>
			<path fill-rule="evenodd" clip-rule="evenodd" d="M17.3231 18.2769C17.3741 18.2769 17.4154 18.2356 17.4154 18.1846C17.4154 18.1336 17.3741 18.0923 17.3231 18.0923C17.2721 18.0923 17.2308 18.1336 17.2308 18.1846C17.2308 18.2356 17.2721 18.2769 17.3231 18.2769ZM15.5077 18.1846C15.5077 17.182 16.3205 16.3692 17.3231 16.3692C18.3257 16.3692 19.1385 17.182 19.1385 18.1846C19.1385 19.1872 18.3257 20 17.3231 20C16.3205 20 15.5077 19.1872 15.5077 18.1846Z" fill="currentColor"/>
			<path fill-rule="evenodd" clip-rule="evenodd" d="M20.0631 9.53835L19.4662 12.6685L19.4648 12.6757L19.4648 12.6757C19.3424 13.2919 19.0072 13.8454 18.5178 14.2394C18.031 14.6312 17.4226 14.8404 16.798 14.8308H8.44017C7.81556 14.8404 7.20714 14.6312 6.72038 14.2394C6.2312 13.8456 5.89605 13.2924 5.77352 12.6765L5.77335 12.6757L4.33477 5.48814C4.3286 5.46282 4.32345 5.43711 4.31934 5.41104L3.61815 1.90768H0.953842C0.42705 1.90768 0 1.48063 0 0.953842C0 0.42705 0.42705 0 0.953842 0H4.4C4.85462 0 5.24607 0.320858 5.33529 0.766644L6.04403 4.30769H12.785C13.0114 4.99157 13.3319 5.63258 13.7312 6.21538H6.42585L7.64421 12.3026L7.64449 12.304C7.67966 12.4811 7.77599 12.6402 7.91662 12.7534C8.05725 12.8666 8.23322 12.9267 8.41372 12.9233L8.432 12.9231H16.8062L16.8244 12.9233C17.0049 12.9267 17.1809 12.8666 17.3215 12.7534C17.4614 12.6408 17.5575 12.4828 17.5931 12.3068L17.5937 12.304L18.1649 9.30867C18.762 9.45873 19.387 9.53842 20.0307 9.53842C20.0415 9.53842 20.0523 9.5384 20.0631 9.53835Z" fill="currentColor"/>
		</svg>';
		$button_html = $this->get_cart_price_markup( $attributes ) . '
		<span class="wc-block-mini-cart__quantity-badge">
			' . $icon . '
			<span class="wc-block-mini-cart__badge">' . $cart_contents_count . '</span>
		</span>';

		if ( is_cart() || is_checkout() ) {
			if ( $this->should_not_render_mini_cart( $attributes ) ) {
				return '';
			}

			// It is not necessary to load the Mini Cart Block on Cart and Checkout page.
			return '<div class="' . $wrapper_classes . '" style="visibility:hidden" aria-hidden="true">
				<button class="wc-block-mini-cart__button" aria-label="' . esc_attr( $aria_label ) . '" disabled>' . $button_html . '</button>
			</div>';
		}

		$template_part_contents = '';

		// Determine if we need to load the template part from the DB, the theme or WooCommerce in that order.
		$templates_from_db = BlockTemplateUtils::get_block_templates_from_db( array( 'mini-cart' ), 'wp_template_part' );
		if ( count( $templates_from_db ) > 0 ) {
			$template_slug_to_load = $templates_from_db[0]->theme;
		} else {
			$theme_has_mini_cart   = BlockTemplateUtils::theme_has_template_part( 'mini-cart' );
			$template_slug_to_load = $theme_has_mini_cart ? get_stylesheet() : BlockTemplateUtils::PLUGIN_SLUG;
		}
		$template_part = BlockTemplateUtils::get_block_template( $template_slug_to_load . '//mini-cart', 'wp_template_part' );

		if ( $template_part && ! empty( $template_part->content ) ) {
			$template_part_contents = do_blocks( $template_part->content );
		}

		if ( '' === $template_part_contents ) {
			$template_part_contents = do_blocks(
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				file_get_contents( Package::get_path() . 'templates/' . BlockTemplateUtils::DIRECTORY_NAMES['TEMPLATE_PARTS'] . '/mini-cart.html' )
			);
		}

		return '<div class="' . esc_attr( $wrapper_classes ) . '" style="' . esc_attr( $wrapper_styles ) . '">
			<button class="wc-block-mini-cart__button" aria-label="' . esc_attr( $aria_label ) . '">' . $button_html . '</button>
			<div class="wc-block-mini-cart__drawer is-loading is-mobile wc-block-components-drawer__screen-overlay wc-block-components-drawer__screen-overlay--is-hidden" aria-hidden="true">
				<div class="components-modal__frame wc-block-components-drawer">
					<div class="components-modal__content">
						<div class="components-modal__header">
							<div class="components-modal__header-heading-container"></div>
						</div>
						<div class="wc-block-mini-cart__template-part">'
						. wp_kses_post( $template_part_contents ) .
						'</div>
					</div>
				</div>
			</div>
		</div>';
	}

	/**
	 * Return the main instance of WC_Cart class.
	 *
	 * @return \WC_Cart CartController class instance.
	 */
	protected function get_cart_instance() {
		$cart = WC()->cart;

		if ( $cart && $cart instanceof \WC_Cart ) {
			return $cart;
		}

		return null;
	}

	/**
	 * Get array with data for handle the tax label.
	 * the entire logic of this function is was taken from:
	 * https://github.com/woocommerce/woocommerce/blob/e730f7463c25b50258e97bf56e31e9d7d3bc7ae7/includes/class-wc-cart.php#L1582
	 *
	 * @return array;
	 */
	protected function get_tax_label() {
		$cart = $this->get_cart_instance();

		if ( $cart && $cart->display_prices_including_tax() ) {
			if ( ! wc_prices_include_tax() ) {
				$tax_label                         = WC()->countries->inc_tax_or_vat();
				$display_cart_prices_including_tax = true;
				return array(
					'tax_label'                         => $tax_label,
					'display_cart_prices_including_tax' => $display_cart_prices_including_tax,
				);
			}
			return array(
				'tax_label'                         => '',
				'display_cart_prices_including_tax' => true,
			);
		}

		if ( wc_prices_include_tax() ) {
			$tax_label = WC()->countries->ex_tax_or_vat();
			return array(
				'tax_label'                         => $tax_label,
				'display_cart_prices_including_tax' => false,
			);
		};

		return array(
			'tax_label'                         => '',
			'display_cart_prices_including_tax' => false,
		);
	}

	/**
	 * Get Cart Payload.
	 *
	 * @return object;
	 */
	protected function get_cart_payload() {
		$notices = wc_get_notices(); // Backup the notices because StoreAPI will remove them.
		$payload = WC()->api->get_endpoint_data( '/wc/store/cart' );

		if ( ! empty( $notices ) ) {
			wc_set_notices( $notices ); // Restore the notices.
		}

		return $payload;
	}

	/**
	 * Prepare translations for inner blocks and dependencies.
	 */
	protected function get_inner_blocks_translations() {
		$wp_scripts   = wp_scripts();
		$translations = array();

		$chunks        = $this->get_chunks_paths( $this->chunks_folder );
		$vendor_chunks = $this->get_chunks_paths( 'vendors--mini-cart-contents-block' );
		$shared_chunks = [ 'cart-blocks/cart-line-items--mini-cart-contents-block/products-table-frontend' ];

		foreach ( array_merge( $chunks, $vendor_chunks, $shared_chunks ) as $chunk ) {
			$handle = 'wc-blocks-' . $chunk . '-chunk';
			$this->asset_api->register_script( $handle, $this->asset_api->get_block_asset_build_path( $chunk ), [], true );
			$translations[] = $wp_scripts->print_translations( $handle, false );
			wp_deregister_script( $handle );
		}

		$translations = array_filter( $translations );

		return implode( '', $translations );
	}

	/**
	 * Register block pattern for Empty Cart Message to make it translatable.
	 */
	public function register_empty_cart_message_block_pattern() {
		register_block_pattern(
			'woocommerce/mini-cart-empty-cart-message',
			array(
				'title'    => __( 'Empty Mini Cart Message', 'woocommerce' ),
				'inserter' => false,
				'content'  => '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center"><strong>' . __( 'Your cart is currently empty!', 'woocommerce' ) . '</strong></p><!-- /wp:paragraph -->',
			)
		);
	}

	/**
	 * Returns whether the mini cart should be rendered or not.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return bool
	 */
	public function should_not_render_mini_cart( array $attributes ) {
		return isset( $attributes['cartAndCheckoutRenderStyle'] ) && 'hidden' !== $attributes['cartAndCheckoutRenderStyle'];
	}
}
