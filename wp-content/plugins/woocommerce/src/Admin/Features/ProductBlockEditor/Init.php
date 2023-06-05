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

	const FEATURE_ID = 'product-block-editor';

	/**
	 * Option name used to toggle this feature.
	 */
	const TOGGLE_OPTION_NAME = 'woocommerce_' . self::FEATURE_ID . '_enabled';

	/**
	 * The context name used to identify the editor.
	 */
	const EDITOR_CONTEXT_NAME = 'woocommerce/edit-product';

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! Features::is_enabled( 'new-product-management-experience' ) && Features::is_enabled( self::FEATURE_ID ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'get_edit_post_link', array( $this, 'update_edit_product_link' ), 10, 2 );
		}
		if ( Features::is_enabled( self::FEATURE_ID ) ) {
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
			$editor_settings['template']     = $post_type_object->template;
			$editor_settings['templateLock'] = ! empty( $post_type_object->template_lock ) ? $post_type_object->template_lock : false;
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
					),
					array(
						array(
							'woocommerce/product-section',
							array(
								'title'       => __( 'Basic details', 'woocommerce' ),
								'description' => __( 'This info will be displayed on the product page, category pages, social media, and search results.', 'woocommerce' ),
								'icon'        => array(
									'src' => '<svg xmlns="http://www.w3.org/2000/svg" view-box="0 0 24 24"><path fill-rule="evenodd" d="M5 5.5h14a.5.5 0 01.5.5v1.5a.5.5 0 01-.5.5H5a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM4 9.232A2 2 0 013 7.5V6a2 2 0 012-2h14a2 2 0 012 2v1.5a2 2 0 01-1 1.732V18a2 2 0 01-2 2H6a2 2 0 01-2-2V9.232zm1.5.268V18a.5.5 0 00.5.5h12a.5.5 0 00.5-.5V9.5h-13z" clip-rule="evenodd" /></svg>',
								),
							),
							array(
								array(
									'woocommerce/product-name',
									array(
										'name' => 'Product name',
									),
								),
								array(
									'woocommerce/product-summary',
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
													'woocommerce/product-pricing',
													array(
														'name' => 'regular_price',
														'label' => __( 'List price', 'woocommerce' ),
														'showPricingSection' => true,
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
													'woocommerce/product-pricing',
													array(
														'name' => 'sale_price',
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
								'title'       => __( 'Images', 'woocommerce' ),
								'description' => sprintf(
									/* translators: %1$s: Images guide link opening tag. %2$s: Images guide link closing tag.*/
									__( 'Drag images, upload new ones or select files from your library. For best results, use JPEG files that are 1000 by 1000 pixels or larger. %1$sHow to prepare images?%2$s.', 'woocommerce' ),
									'<a href="http://woocommerce.com/#" target="_blank" rel="noreferrer">',
									'</a>'
								),
							),
							array(
								array(
									'woocommerce/product-images',
									array(
										'images' => array(),
									),
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
									'src' => '<svg xmlns="http://www.w3.org/2000/svg" view-box="0 0 24 24"><path fill-rule="evenodd" d="M16.83 6.342l.602.3.625-.25.443-.176v12.569l-.443-.178-.625-.25-.603.301-1.444.723-2.41-.804-.475-.158-.474.158-2.41.803-1.445-.722-.603-.3-.625.25-.443.177V6.215l.443.178.625.25.603-.301 1.444-.722 2.41.803.475.158.474-.158 2.41-.803 1.445.722zM20 4l-1.5.6-1 .4-2-1-3 1-3-1-2 1-1-.4L5 4v17l1.5-.6 1-.4 2 1 3-1 3 1 2-1 1 .4 1.5.6V4zm-3.5 6.25v-1.5h-8v1.5h8zm0 3v-1.5h-8v1.5h8zm-8 3v-1.5h8v1.5h-8z" clip-rule="evenodd" /></svg>',
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
													'woocommerce/product-pricing',
													array(
														'name' => 'regular_price',
														'label' => __( 'List price', 'woocommerce' ),
														'showPricingSection' => true,
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
													'woocommerce/product-pricing',
													array(
														'name' => 'sale_price',
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
									'woocommerce/product-radio',
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
									'woocommerce/collapsible',
									array(
										'toggleText'       => __( 'Advanced', 'woocommerce' ),
										'initialCollapsed' => true,
										'persistRender'    => true,
									),
									array(
										array(
											'woocommerce/product-radio',
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
									'src' => '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M2 2H14C14.2761 2 14.5 2.22386 14.5 2.5V9.5H11.5H10C10 10.6046 9.10457 11.5 8 11.5C6.89543 11.5 6 10.6046 6 9.5H4.5H1.5V2.5C1.5 2.22386 1.72386 2 2 2ZM1.5 11V14.5C1.5 14.7761 1.72386 15 2 15H14C14.2761 15 14.5 14.7761 14.5 14.5V11H11.1632C10.6015 12.1825 9.3962 13 8 13C6.6038 13 5.39855 12.1825 4.83682 11H1.5ZM0 9.5V2.5C0 1.39543 0.895431 0.5 2 0.5H14C15.1046 0.5 16 1.39543 16 2.5V9.5V11V14.5C16 15.6046 15.1046 16.5 14 16.5H2C0.895431 16.5 0 15.6046 0 14.5V11V9.5Z" fill="#1E1E1E"/></svg>',
								),
							),
							array(
								array(
									'woocommerce/product-sku',
								),
								array(
									'woocommerce/product-track-inventory-fields',
								),
								array(
									'woocommerce/collapsible',
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
													'woocommerce/product-radio',
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
													'woocommerce/product-inventory-email',
												),
											),
										),
										array(
											'woocommerce/product-checkbox',
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
												'tooltip' => __(
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
									'src' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.5 7.75C3.5 6.7835 4.2835 6 5.25 6H14.75H15.5V6.75V9H17.25H17.5607L17.7803 9.21967L20.7803 12.2197L21 12.4393V12.75V14.75C21 15.7165 20.2165 16.5 19.25 16.5H19.2377C19.2458 16.5822 19.25 16.6656 19.25 16.75C19.25 18.1307 18.1307 19.25 16.75 19.25C15.3693 19.25 14.25 18.1307 14.25 16.75C14.25 16.6656 14.2542 16.5822 14.2623 16.5H14H10.2377C10.2458 16.5822 10.25 16.6656 10.25 16.75C10.25 18.1307 9.13071 19.25 7.75 19.25C6.36929 19.25 5.25 18.1307 5.25 16.75C5.25 16.6656 5.25418 16.5822 5.26234 16.5H4.25H3.5V15.75V7.75ZM14 15V9.75V9V7.5H5.25C5.11193 7.5 5 7.61193 5 7.75V15H5.96464C6.41837 14.5372 7.05065 14.25 7.75 14.25C8.44935 14.25 9.08163 14.5372 9.53536 15H14ZM18.5354 15H19.25C19.3881 15 19.5 14.8881 19.5 14.75V13.0607L16.9393 10.5H15.5V14.5845C15.8677 14.3717 16.2946 14.25 16.75 14.25C17.4493 14.25 18.0816 14.5372 18.5354 15ZM6.7815 16.5C6.76094 16.5799 6.75 16.6637 6.75 16.75C6.75 17.3023 7.19772 17.75 7.75 17.75C8.30228 17.75 8.75 17.3023 8.75 16.75C8.75 16.6637 8.73906 16.5799 8.7185 16.5C8.60749 16.0687 8.21596 15.75 7.75 15.75C7.28404 15.75 6.89251 16.0687 6.7815 16.5ZM15.7815 16.5C15.7609 16.5799 15.75 16.6637 15.75 16.75C15.75 17.3023 16.1977 17.75 16.75 17.75C17.3023 17.75 17.75 17.3023 17.75 16.75C17.75 16.6637 17.7391 16.5799 17.7185 16.5C17.7144 16.4841 17.7099 16.4683 17.705 16.4526C17.5784 16.0456 17.1987 15.75 16.75 15.75C16.284 15.75 15.8925 16.0687 15.7815 16.5Z" fill="#1E1E1E"/></svg>',
								),
							),
							array(
								array(
									'woocommerce/product-shipping-fee-fields',
									array(
										'title' => __( 'Shipping fee', 'woocommerce' ),
									),
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
