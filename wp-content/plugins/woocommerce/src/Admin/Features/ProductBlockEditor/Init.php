<?php
/**
 * WooCommerce Product Block Editor
 */

namespace Automattic\WooCommerce\Admin\Features\ProductBlockEditor;

use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Admin\Features\TransientNotices;
use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Internal\Admin\Loader;
use WP_Block_Editor_Context;

/**
 * Loads assets related to the product block editor.
 */
class Init {
	/**
	 * The context name used to identify the editor.
	 */
	const EDITOR_CONTEXT_NAME = 'woocommerce/edit-product';

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( \Automattic\WooCommerce\Utilities\FeaturesUtil::feature_is_enabled( 'product_block_editor' ) ) {
			if ( ! Features::is_enabled( 'new-product-management-experience' ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
				add_action( 'get_edit_post_link', array( $this, 'update_edit_product_link' ), 10, 2 );
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_filter( 'woocommerce_register_post_type_product', array( $this, 'add_product_template' ) );
			$block_registry = new BlockRegistry();
			$block_registry->init();
		}
	}

	/**
	 * Enqueue scripts needed for the product form block editor.
	 */
	public function enqueue_scripts() {
		if ( ! PageController::is_admin_or_embed_page() ) {
			return;
		}
		$post_type_object     = get_post_type_object( 'product' );
		$block_editor_context = new WP_Block_Editor_Context( array( 'name' => self::EDITOR_CONTEXT_NAME ) );

		$editor_settings = array();
		if ( ! empty( $post_type_object->template ) ) {
			$editor_settings['template']                 = $post_type_object->template;
			$editor_settings['templateLock']             = ! empty( $post_type_object->template_lock ) ? $post_type_object->template_lock : false;
			$editor_settings['__unstableResolvedAssets'] = $this->get_resolved_assets();
		}

		$editor_settings = get_block_editor_settings( $editor_settings, $block_editor_context );

		$script_handle = 'wc-admin-edit-product';
		wp_register_script( $script_handle, '', array(), '0.1.0', true );
		wp_enqueue_script( $script_handle );
		wp_add_inline_script(
			$script_handle,
			'var productBlockEditorSettings = productBlockEditorSettings || ' . wp_json_encode( $editor_settings ) . ';',
			'before'
		);
		wp_add_inline_script(
			$script_handle,
			sprintf( 'wp.blocks.setCategories( %s );', wp_json_encode( $editor_settings['blockCategories'] ) ),
			'before'
		);
		wp_tinymce_inline_scripts();
	}

	/**
	 * Enqueue styles needed for the rich text editor.
	 */
	public function enqueue_styles() {
		if ( ! PageController::is_admin_or_embed_page() ) {
			return;
		}
		wp_enqueue_style( 'wp-edit-blocks' );
		wp_enqueue_style( 'wp-format-library' );
		wp_enqueue_editor();
		/**
		 * Enqueue any block editor related assets.
		 *
		 * @since 7.1.0
		*/
		do_action( 'enqueue_block_editor_assets' );
	}

	/**
	 * Update the edit product links when the new experience is enabled.
	 *
	 * @param string $link    The edit link.
	 * @param int    $post_id Post ID.
	 * @return string
	 */
	public function update_edit_product_link( $link, $post_id ) {
		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return $link;
		}

		if ( $product->get_type() === 'simple' ) {
			return admin_url( 'admin.php?page=wc-admin&path=/product/' . $product->get_id() );
		}

		return $link;
	}

	/**
	 * Get the resolved assets needed for the iframe editor.
	 *
	 * @return array Styles and scripts.
	 */
	private function get_resolved_assets() {
		if ( function_exists( 'gutenberg_resolve_assets_override' ) ) {
			return gutenberg_resolve_assets_override();
		}

		global $pagenow;

		$script_handles = array(
			'wp-polyfill',
		);
		// Note for core merge: only 'wp-edit-blocks' should be in this array.
		$style_handles = array(
			'wp-edit-blocks',
		);

		if ( current_theme_supports( 'wp-block-styles' ) ) {
			$style_handles[] = 'wp-block-library-theme';
		}

		if ( 'widgets.php' === $pagenow || 'customize.php' === $pagenow ) {
			$style_handles[] = 'wp-widgets';
			$style_handles[] = 'wp-edit-widgets';
		}

		$block_registry = \WP_Block_Type_Registry::get_instance();

		foreach ( $block_registry->get_all_registered() as $block_type ) {
			// In older WordPress versions, like 6.0, these properties are not defined.
			if ( isset( $block_type->style_handles ) && is_array( $block_type->style_handles ) ) {
				$style_handles = array_merge( $style_handles, $block_type->style_handles );
			}

			if ( isset( $block_type->editor_style_handles ) && is_array( $block_type->editor_style_handles ) ) {
				$style_handles = array_merge( $style_handles, $block_type->editor_style_handles );
			}

			if ( isset( $block_type->script_handles ) && is_array( $block_type->script_handles ) ) {
				$script_handles = array_merge( $script_handles, $block_type->script_handles );
			}
		}

		$style_handles = array_unique( $style_handles );
		$done          = wp_styles()->done;

		ob_start();

		// We do not need reset styles for the iframed editor.
		wp_styles()->done = array( 'wp-reset-editor-styles' );
		wp_styles()->do_items( $style_handles );
		wp_styles()->done = $done;

		$styles = ob_get_clean();

		$script_handles = array_unique( $script_handles );
		$done           = wp_scripts()->done;

		ob_start();

		wp_scripts()->done = array();
		wp_scripts()->do_items( $script_handles );
		wp_scripts()->done = $done;

		$scripts = ob_get_clean();

		/*
		 * Generate font @font-face styles for the site editor iframe.
		 * Use the registered font families for printing.
		 */
		if ( class_exists( '\WP_Fonts' ) ) {
			$wp_fonts   = wp_fonts();
			$registered = $wp_fonts->get_registered_font_families();
			if ( ! empty( $registered ) ) {
				$queue = $wp_fonts->queue;
				$done  = $wp_fonts->done;

				$wp_fonts->done  = array();
				$wp_fonts->queue = $registered;

				ob_start();
				$wp_fonts->do_items();
				$styles .= ob_get_clean();

				// Reset the Web Fonts API.
				$wp_fonts->done  = $done;
				$wp_fonts->queue = $queue;
			}
		}

		return array(
			'styles'  => $styles,
			'scripts' => $scripts,
		);
	}

	/**
	 * Enqueue styles needed for the rich text editor.
	 *
	 * @param array $args Array of post type arguments.
	 * @return array Array of post type arguments.
	 */
	public function add_product_template( $args ) {
		if ( ! isset( $args['template'] ) ) {
			$args['template_lock'] = 'all';
			$args['template']      = array(
				array(
					'woocommerce/product-tab',
					array(
						'id'    => 'general',
						'title' => __( 'General', 'woocommerce' ),
						'order' => 10,
					),
					array(
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Basic details', 'woocommerce' ),
								'description' => __( 'This info will be displayed on the product page, category pages, social media, and search results.', 'woocommerce' ),
								'icon'        => array(
									'src' => plugins_url( '/assets/client/admin/product-editor/icons/section_basic.svg', WC_PLUGIN_FILE ),
								),
							),
							array(
								array(
									'woocommerce/product-name-field',
									array(
										'name' => 'Product name',
									),
								),
								array(
									'woocommerce/product-summary-field',
								),
								array(
									'core/columns',
									array(),
									array(
										array(
											'core/column',
											array(
												'templateLock' => 'all',
											),
											array(
												array(
													'woocommerce/product-regular-price-field',
													array(
														'name'  => 'regular_price',
														'label' => __( 'List price', 'woocommerce' ),
														'help'  => __( 'Manage more settings in <PricingTab>Pricing.</PricingTab>', 'woocommerce' ),
													),
												),
											),
										),
										array(
											'core/column',
											array(
												'templateLock' => 'all',
											),
											array(
												array(
													'woocommerce/product-sale-price-field',
													array(
														'label' => __( 'Sale price', 'woocommerce' ),
													),
												),
											),
										),
									),
								),
							),
						),
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Description', 'woocommerce' ),
								'description' => __( 'What makes this product unique? What are its most important features? Enrich the product page by adding rich content using blocks.', 'woocommerce' ),
								'icon'        => array(
									'src' => '<svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg"><line x1="9.91663" y1="16.5" x2="9.91663" y2="1.38889" stroke="#1E1E1E" stroke-width="1.5"/><line x1="5.47217" y1="16.5" x2="5.47217" y2="1.38889" stroke="#1E1E1E" stroke-width="1.5"/><line x1="13.3334" y1="1.25" x2="4.44448" y2="1.25" stroke="#1E1E1E" stroke-width="1.5"/><path d="M4.13889 5.38889V9.46C2.21109 9.10713 0.75 7.41864 0.75 5.38889C0.75 3.35914 2.21109 1.67065 4.13889 1.31778V5.38889Z" fill="#1E1E1E" stroke="#1E1E1E" stroke-width="1.5"/></svg>',
								),
							),
							array(
								array(
									'woocommerce/product-description-field',
								),
							),
						),
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Images', 'woocommerce' ),
								'description' => sprintf(
									/* translators: %1$s: Images guide link opening tag. %2$s: Images guide link closing tag.*/
									__( 'Drag images, upload new ones or select files from your library. For best results, use JPEG files that are 1000 by 1000 pixels or larger. %1$sHow to prepare images?%2$s', 'woocommerce' ),
									'<a href="http://woocommerce.com/#" target="_blank" rel="noreferrer">',
									'</a>'
								),
								'icon'        => array(
									'src' => plugins_url( '/assets/client/admin/product-editor/icons/section_images.svg', WC_PLUGIN_FILE ),
								),
							),
							array(
								array(
									'woocommerce/product-images-field',
									array(
										'images' => array(),
									),
								),
							),
						),
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Organization & visibility', 'woocommerce' ),
								'description' => __( 'Help customers find this product by assigning it to categories or featuring it across your sales channels.', 'woocommerce' ),
							),
							array(
								array(
									'woocommerce/product-category-field',
									array(
										'name' => 'categories',
									),
								),
							),
						),
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Attributes', 'woocommerce' ),
								'description' => sprintf(
									/* translators: %1$s: Attributes guide link opening tag. %2$s: Attributes guide link closing tag.*/
									__( 'Add descriptive pieces of information that customers can use to filter and search for this product. %1$sLearn more%2$s', 'woocommerce' ),
									'<a href="https://woocommerce.com/document/managing-product-taxonomies/#product-attributes" target="_blank" rel="noreferrer">',
									'</a>'
								),
								'icon'        => array(
									'src' => plugins_url( '/assets/client/admin/product-editor/icons/section_attributes.svg', WC_PLUGIN_FILE ),
								),
							),
							array(
								array(
									'woocommerce/product-attributes-field',
								),
							),
						),
					),
				),
				array(
					'woocommerce/product-tab',
					array(
						'id'    => 'pricing',
						'title' => __( 'Pricing', 'woocommerce' ),
						'order' => 20,
					),
					array(
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Pricing', 'woocommerce' ),
								'description' => sprintf(
									/* translators: %1$s: Images guide link opening tag. %2$s: Images guide link closing tag.*/
									__( 'Set a competitive price, put the product on sale, and manage tax calculations. %1$sHow to price your product?%2$s', 'woocommerce' ),
									'<a href="https://woocommerce.com/posts/how-to-price-products-strategies-expert-tips/" target="_blank" rel="noreferrer">',
									'</a>'
								),
								'icon'        => array(
									'src' => plugins_url( '/assets/client/admin/product-editor/icons/section_pricing.svg', WC_PLUGIN_FILE ),
								),
							),
							array(
								array(
									'core/columns',
									array(),
									array(
										array(
											'core/column',
											array(
												'templateLock' => 'all',
											),
											array(
												array(
													'woocommerce/product-regular-price-field',
													array(
														'name'  => 'regular_price',
														'label' => __( 'List price', 'woocommerce' ),
													),
												),
											),
										),
										array(
											'core/column',
											array(
												'templateLock' => 'all',
											),
											array(
												array(
													'woocommerce/product-sale-price-field',
													array(
														'label' => __( 'Sale price', 'woocommerce' ),
													),
												),
											),
										),
									),
								),
								array(
									'woocommerce/product-schedule-sale-fields',
								),
								array(
									'woocommerce/product-radio-field',
									array(
										'title'    => __( 'Charge sales tax on', 'woocommerce' ),
										'property' => 'tax_status',
										'options'  => array(
											array(
												'label' => __( 'Product and shipping', 'woocommerce' ),
												'value' => 'taxable',
											),
											array(
												'label' => __( 'Only shipping', 'woocommerce' ),
												'value' => 'shipping',
											),
											array(
												'label' => __( "Don't charge tax", 'woocommerce' ),
												'value' => 'none',
											),
										),
									),
								),
								array(
									'woocommerce/product-collapsible',
									array(
										'toggleText'       => __( 'Advanced', 'woocommerce' ),
										'initialCollapsed' => true,
										'persistRender'    => true,
									),
									array(
										array(
											'woocommerce/product-radio-field',
											array(
												'title'    => __( 'Tax class', 'woocommerce' ),
												'description' => sprintf(
													/* translators: %1$s: Learn more link opening tag. %2$s: Learn more link closing tag.*/
													__( 'Apply a tax rate if this product qualifies for tax reduction or exemption. %1$sLearn more%2$s.', 'woocommerce' ),
													'<a href="https://woocommerce.com/document/setting-up-taxes-in-woocommerce/#shipping-tax-class" target="_blank" rel="noreferrer">',
													'</a>'
												),
												'property' => 'tax_class',
												'options'  => array(
													array(
														'label' => __( 'Standard', 'woocommerce' ),
														'value' => '',
													),
													array(
														'label' => __( 'Reduced rate', 'woocommerce' ),
														'value' => 'reduced-rate',
													),
													array(
														'label' => __( 'Zero rate', 'woocommerce' ),
														'value' => 'zero-rate',
													),
												),
											),
										),
									),
								),
							),
						),
					),
				),
				array(
					'woocommerce/product-tab',
					array(
						'id'    => 'inventory',
						'title' => __( 'Inventory', 'woocommerce' ),
						'order' => 30,
					),
					array(
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Inventory', 'woocommerce' ),
								'description' => sprintf(
									/* translators: %1$s: Inventory settings link opening tag. %2$s: Inventory settings link closing tag.*/
									__( 'Set up and manage inventory for this product, including status and available quantity. %1$sManage store inventory settings%2$s', 'woocommerce' ),
									'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=inventory' ) . '" target="_blank" rel="noreferrer">',
									'</a>'
								),
								'icon'        => array(
									'src' => plugins_url( '/assets/client/admin/product-editor/icons/section_inventory.svg', WC_PLUGIN_FILE ),
								),
							),
							array(
								array(
									'woocommerce/product-sku-field',
								),
								array(
									'woocommerce/product-toggle-field',
									array(
										'label'    => __( 'Track stock quantity for this product', 'woocommerce' ),
										'property' => 'manage_stock',
										'disabled' => 'yes' !== get_option( 'woocommerce_manage_stock' ),
									),
								),
								array(
									'woocommerce/conditional',
									array(
										'mustMatch' => array(
											'manage_stock' => array( true ),
										),
									),
									array(
										array(
											'woocommerce/product-inventory-quantity-field',
										),
									),
								),
								array(
									'woocommerce/conditional',
									array(
										'mustMatch' => array(
											'manage_stock' => array( false ),
										),
									),
									array(
										array(
											'woocommerce/product-radio-field',
											array(
												'title'    => __( 'Stock status', 'woocommerce' ),
												'property' => 'stock_status',
												'options'  => array(
													array(
														'label' => __( 'In stock', 'woocommerce' ),
														'value' => 'instock',
													),
													array(
														'label' => __( 'Out of stock', 'woocommerce' ),
														'value' => 'outofstock',
													),
													array(
														'label' => __( 'On backorder', 'woocommerce' ),
														'value' => 'onbackorder',
													),
												),
											),
										),
									),
								),
								array(
									'woocommerce/product-collapsible',
									array(
										'toggleText'       => __( 'Advanced', 'woocommerce' ),
										'initialCollapsed' => true,
										'persistRender'    => true,
									),
									array(
										array(
											'woocommerce/conditional',
											array(
												'mustMatch' => array(
													'manage_stock' => array( true ),
												),
											),
											array(
												array(
													'woocommerce/product-radio-field',
													array(
														'title'    => __( 'When out of stock', 'woocommerce' ),
														'property' => 'backorders',
														'options'  => array(
															array(
																'label' => __( 'Allow purchases', 'woocommerce' ),
																'value' => 'yes',
															),
															array(
																'label' => __(
																	'Allow purchases, but notify customers',
																	'woocommerce'
																),
																'value' => 'notify',
															),
															array(
																'label' => __( "Don't allow purchases", 'woocommerce' ),
																'value' => 'no',
															),
														),
													),
												),
												array(
													'woocommerce/product-inventory-email-field',
												),
											),
										),
										array(
											'woocommerce/product-checkbox-field',
											array(
												'title'    => __(
													'Restrictions',
													'woocommerce'
												),
												'label'    => __(
													'Limit purchases to 1 item per order',
													'woocommerce'
												),
												'property' => 'sold_individually',
												'tooltip'  => __(
													'When checked, customers will be able to purchase only 1 item in a single order. This is particularly useful for items that have limited quantity, like art or handmade goods.',
													'woocommerce'
												),
											),
										),

									),
								),

							),
						),
					),

				),
				array(
					'woocommerce/product-tab',
					array(
						'id'    => 'shipping',
						'title' => __( 'Shipping', 'woocommerce' ),
						'order' => 40,
					),
					array(
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Fees & dimensions', 'woocommerce' ),
								'description' => sprintf(
									/* translators: %1$s: How to get started? link opening tag. %2$s: How to get started? link closing tag.*/
									__( 'Set up shipping costs and enter dimensions used for accurate rate calculations. %1$sHow to get started?%2$s.', 'woocommerce' ),
									'<a href="https://woocommerce.com/posts/how-to-calculate-shipping-costs-for-your-woocommerce-store/" target="_blank" rel="noreferrer">',
									'</a>'
								),
								'icon'        => array(
									'src' => plugins_url( '/assets/client/admin/product-editor/icons/section_shipping.svg', WC_PLUGIN_FILE ),
								),
							),
							array(
								array(
									'woocommerce/product-shipping-class-field',
								),
								array(
									'woocommerce/product-shipping-dimensions-fields',
								),
							),
						),
					),
				),
			);
		}
		return $args;
	}
}
