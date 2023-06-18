<?php
/**
 * Load assets
 *
 * @package WooCommerce\Admin
 * @version 3.7.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Admin_Assets', false ) ) :

	/**
	 * WC_Admin_Assets Class.
	 */
	class WC_Admin_Assets {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		/**
		 * Enqueue styles.
		 */
		public function admin_styles() {
			global $wp_scripts;

			$version   = Constants::get_constant( 'WC_VERSION' );
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			// Register admin styles.
			wp_register_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), $version );
			wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), $version );
			wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), $version );
			wp_register_style( 'woocommerce_admin_dashboard_styles', WC()->plugin_url() . '/assets/css/dashboard.css', array(), $version );
			wp_register_style( 'woocommerce_admin_print_reports_styles', WC()->plugin_url() . '/assets/css/reports-print.css', array(), $version, 'print' );
			wp_register_style( 'woocommerce_admin_marketplace_styles', WC()->plugin_url() . '/assets/css/marketplace-suggestions.css', array(), $version );
			wp_register_style( 'woocommerce_admin_privacy_styles', WC()->plugin_url() . '/assets/css/privacy.css', array(), $version );

			// Add RTL support for admin styles.
			wp_style_add_data( 'woocommerce_admin_menu_styles', 'rtl', 'replace' );
			wp_style_add_data( 'woocommerce_admin_styles', 'rtl', 'replace' );
			wp_style_add_data( 'woocommerce_admin_dashboard_styles', 'rtl', 'replace' );
			wp_style_add_data( 'woocommerce_admin_print_reports_styles', 'rtl', 'replace' );
			wp_style_add_data( 'woocommerce_admin_marketplace_styles', 'rtl', 'replace' );
			wp_style_add_data( 'woocommerce_admin_privacy_styles', 'rtl', 'replace' );

			if ( $screen && $screen->is_block_editor() ) {
				$styles = WC_Frontend_Scripts::get_styles();

				if ( $styles ) {
					foreach ( $styles as $handle => $args ) {
						wp_register_style(
							$handle,
							$args['src'],
							$args['deps'],
							$args['version'],
							$args['media']
						);

						if ( ! isset( $args['has_rtl'] ) ) {
							wp_style_add_data( $handle, 'rtl', 'replace' );
						}
					}
				}
			}

			// Sitewide menu CSS.
			wp_enqueue_style( 'woocommerce_admin_menu_styles' );

			// Admin styles for WC pages only.
			if ( in_array( $screen_id, wc_get_screen_ids() ) ) {
				wp_enqueue_style( 'woocommerce_admin_styles' );
				wp_enqueue_style( 'jquery-ui-style' );
				wp_enqueue_style( 'wp-color-picker' );
			}

			if ( in_array( $screen_id, array( 'dashboard' ) ) ) {
				wp_enqueue_style( 'woocommerce_admin_dashboard_styles' );
			}

			if ( in_array( $screen_id, array( 'woocommerce_page_wc-reports', 'toplevel_page_wc-reports' ) ) ) {
				wp_enqueue_style( 'woocommerce_admin_print_reports_styles' );
			}

			// Privacy Policy Guide css for back-compat.
			if ( isset( $_GET['wp-privacy-policy-guide'] ) || in_array( $screen_id, array( 'privacy-policy-guide' ) ) ) {
				wp_enqueue_style( 'woocommerce_admin_privacy_styles' );
			}

			// @deprecated 2.3.
			if ( has_action( 'woocommerce_admin_css' ) ) {
				/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
				do_action( 'woocommerce_admin_css' );
				/* phpcs: enable */
				wc_deprecated_function( 'The woocommerce_admin_css action', '2.3', 'admin_enqueue_scripts' );
			}

			if ( WC_Marketplace_Suggestions::show_suggestions_for_screen( $screen_id ) ) {
				wp_enqueue_style( 'woocommerce_admin_marketplace_styles' );
			}
		}


		/**
		 * Enqueue scripts.
		 */
		public function admin_scripts() {
			global $wp_query, $post, $theorder;

			$screen       = get_current_screen();
			$screen_id    = $screen ? $screen->id : '';
			$wc_screen_id = 'woocommerce';
			$suffix       = Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';
			$version      = Constants::get_constant( 'WC_VERSION' );

			// Register scripts.
			wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $version );
			wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
			wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $version, true );
			wp_register_script( 'round', WC()->plugin_url() . '/assets/js/round/round' . $suffix . '.js', array( 'jquery' ), $version );
			wp_register_script( 'wc-admin-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'wc-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ), $version );
			wp_register_script( 'qrcode', WC()->plugin_url() . '/assets/js/jquery-qrcode/jquery.qrcode' . $suffix . '.js', array( 'jquery' ), $version );
			wp_register_script( 'stupidtable', WC()->plugin_url() . '/assets/js/stupidtable/stupidtable' . $suffix . '.js', array( 'jquery' ), $version );
			wp_register_script( 'serializejson', WC()->plugin_url() . '/assets/js/jquery-serializejson/jquery.serializejson' . $suffix . '.js', array( 'jquery' ), '2.8.1' );
			wp_register_script( 'flot', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot' . $suffix . '.js', array( 'jquery' ), $version );
			wp_register_script( 'flot-resize', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.resize' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
			wp_register_script( 'flot-time', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
			wp_register_script( 'flot-pie', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
			wp_register_script( 'flot-stack', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
			wp_register_script( 'wc-settings-tax', WC()->plugin_url() . '/assets/js/admin/settings-views-html-settings-tax' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), $version );
			wp_register_script( 'wc-backbone-modal', WC()->plugin_url() . '/assets/js/admin/backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), $version );
			wp_register_script( 'wc-shipping-zones', WC()->plugin_url() . '/assets/js/admin/wc-shipping-zones' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-enhanced-select', 'wc-backbone-modal' ), $version );
			wp_register_script( 'wc-shipping-zone-methods', WC()->plugin_url() . '/assets/js/admin/wc-shipping-zone-methods' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-backbone-modal' ), $version );
			wp_register_script( 'wc-shipping-classes', WC()->plugin_url() . '/assets/js/admin/wc-shipping-classes' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone' ), $version );
			wp_register_script( 'wc-clipboard', WC()->plugin_url() . '/assets/js/admin/wc-clipboard' . $suffix . '.js', array( 'jquery' ), $version );
			wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
			wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.6' );
			wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), $version );
			wp_register_script( 'js-cookie', WC()->plugin_url() . '/assets/js/js-cookie/js.cookie' . $suffix . '.js', array(), '2.1.4', true );

			wp_localize_script(
				'wc-enhanced-select',
				'wc_enhanced_select_params',
				array(
					'i18n_no_matches'                 => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
					'i18n_ajax_error'                 => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_1'          => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_n'          => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_1'           => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_n'           => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_1'       => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_n'       => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
					'i18n_load_more'                  => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
					'i18n_searching'                  => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
					'ajax_url'                        => admin_url( 'admin-ajax.php' ),
					'search_products_nonce'           => wp_create_nonce( 'search-products' ),
					'search_customers_nonce'          => wp_create_nonce( 'search-customers' ),
					'search_categories_nonce'         => wp_create_nonce( 'search-categories' ),
					'search_taxonomy_terms_nonce'     => wp_create_nonce( 'search-taxonomy-terms' ),
					'search_product_attributes_nonce' => wp_create_nonce( 'search-product-attributes' ),
					'search_pages_nonce'              => wp_create_nonce( 'search-pages' ),
				)
			);

			wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2' );
			wp_localize_script(
				'accounting',
				'accounting_params',
				array(
					'mon_decimal_point' => wc_get_price_decimal_separator(),
				)
			);

			wp_register_script( 'wc-orders', WC()->plugin_url() . '/assets/js/admin/wc-orders' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), $version );
			wp_localize_script(
				'wc-orders',
				'wc_orders_params',
				array(
					'ajax_url'      => admin_url( 'admin-ajax.php' ),
					'preview_nonce' => wp_create_nonce( 'woocommerce-preview-order' ),
				)
			);

			// WooCommerce admin pages.
			if ( in_array( $screen_id, wc_get_screen_ids() ) ) {
				wp_enqueue_script( 'iris' );
				wp_enqueue_script( 'woocommerce_admin' );
				wp_enqueue_script( 'wc-enhanced-select' );

				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-autocomplete' );

				$locale        = localeconv();
				$decimal_point = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
				$decimal       = ( ! empty( wc_get_price_decimal_separator() ) ) ? wc_get_price_decimal_separator() : $decimal_point;

				$params = array(
					/* translators: %s: decimal */
					'i18n_decimal_error'                => sprintf( __( 'Please enter a value with one decimal point (%s) without thousand separators.', 'woocommerce' ), $decimal ),
					/* translators: %s: price decimal separator */
					'i18n_mon_decimal_error'            => sprintf( __( 'Please enter a value with one monetary decimal point (%s) without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
					'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
					'i18n_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
					'i18n_delete_product_notice'        => __( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'woocommerce' ),
					'i18n_remove_personal_data_notice'  => __( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'woocommerce' ),
					'i18n_confirm_delete'               => __( 'Are you sure you wish to delete this item?', 'woocommerce' ),
					'decimal_point'                     => $decimal,
					'mon_decimal_point'                 => wc_get_price_decimal_separator(),
					'ajax_url'                          => admin_url( 'admin-ajax.php' ),
					'strings'                           => array(
						'import_products' => __( 'Import', 'woocommerce' ),
						'export_products' => __( 'Export', 'woocommerce' ),
					),
					'nonces'                            => array(
						'gateway_toggle' => current_user_can( 'manage_woocommerce' ) ? wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ) : null,
					),
					'urls'                              => array(
						'add_product'     => Features::is_enabled( 'new-product-management-experience' ) || \Automattic\WooCommerce\Utilities\FeaturesUtil::feature_is_enabled( 'product_block_editor' ) ? esc_url_raw( admin_url( 'admin.php?page=wc-admin&path=/add-product' ) ) : null,
						'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
						'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
					),
				);

				wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
			}

			// Edit product category pages.
			if ( in_array( $screen_id, array( 'edit-product_cat' ) ) ) {
				wp_enqueue_media();
			}

			// Products.
			if ( in_array( $screen_id, array( 'edit-product' ) ) ) {
				wp_enqueue_script( 'woocommerce_quick-edit', WC()->plugin_url() . '/assets/js/admin/quick-edit' . $suffix . '.js', array( 'jquery', 'woocommerce_admin' ), $version );

				$params = array(
					'strings' => array(
						'allow_reviews' => esc_js( __( 'Enable reviews', 'woocommerce' ) ),
					),
				);

				wp_localize_script( 'woocommerce_quick-edit', 'woocommerce_quick_edit', $params );
			}

			// Product description.
			if ( in_array( $screen_id, array( 'product' ), true ) ) {
				wp_enqueue_script( 'wc-admin-product-editor', WC()->plugin_url() . '/assets/js/admin/product-editor' . $suffix . '.js', array( 'jquery' ), $version, false );

				wp_localize_script(
					'wc-admin-product-editor',
					'woocommerce_admin_product_editor',
					array(
						'i18n_description' => esc_js( __( 'Product description', 'woocommerce' ) ),
					)
				);
			}

			// Meta boxes.
			/* phpcs:disable */
			if ( in_array( $screen_id, array( 'product', 'edit-product' ) ) ) {
				wp_enqueue_media();
				wp_register_script( 'wc-admin-product-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'media-models' ), $version );
				wp_register_script( 'wc-admin-variation-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product-variation' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'serializejson', 'media-models', 'backbone', 'jquery-ui-sortable', 'wc-backbone-modal' ), $version );

				wp_enqueue_script( 'wc-admin-product-meta-boxes' );
				wp_enqueue_script( 'wc-admin-variation-meta-boxes' );

				$params = array(
					'post_id'                             => isset( $post->ID ) ? $post->ID : '',
					'plugin_url'                          => WC()->plugin_url(),
					'ajax_url'                            => admin_url( 'admin-ajax.php' ),
					'woocommerce_placeholder_img_src'     => wc_placeholder_img_src(),
					'add_variation_nonce'                 => wp_create_nonce( 'add-variation' ),
					'link_variation_nonce'                => wp_create_nonce( 'link-variations' ),
					'delete_variations_nonce'             => wp_create_nonce( 'delete-variations' ),
					'load_variations_nonce'               => wp_create_nonce( 'load-variations' ),
					'save_variations_nonce'               => wp_create_nonce( 'save-variations' ),
					'bulk_edit_variations_nonce'          => wp_create_nonce( 'bulk-edit-variations' ),
					/* translators: %d: Number of variations */
					'i18n_link_all_variations'            => esc_js( sprintf( __( 'Do you want to generate all variations? This will create a new variation for each and every possible combination of variation attributes (max %d per run).', 'woocommerce' ), Constants::is_defined( 'WC_MAX_LINKED_VARIATIONS' ) ? Constants::get_constant( 'WC_MAX_LINKED_VARIATIONS' ) : 50 ) ),
					'i18n_enter_a_value'                  => esc_js( __( 'Enter a value', 'woocommerce' ) ),
					'i18n_enter_menu_order'               => esc_js( __( 'Variation menu order (determines position in the list of variations)', 'woocommerce' ) ),
					'i18n_enter_a_value_fixed_or_percent' => esc_js( __( 'Enter a value (fixed or %)', 'woocommerce' ) ),
					'i18n_delete_all_variations'          => esc_js( __( 'Are you sure you want to delete all variations? This cannot be undone.', 'woocommerce' ) ),
					'i18n_last_warning'                   => esc_js( __( 'Last warning, are you sure?', 'woocommerce' ) ),
					'i18n_choose_image'                   => esc_js( __( 'Choose an image', 'woocommerce' ) ),
					'i18n_set_image'                      => esc_js( __( 'Set variation image', 'woocommerce' ) ),
					'i18n_variation_added'                => esc_js( __( '1 variation added', 'woocommerce' ) ),
					'i18n_variations_added'               => esc_js( __( '%qty% variations added', 'woocommerce' ) ),
					'i18n_remove_variation'               => esc_js( __( 'Are you sure you want to remove this variation?', 'woocommerce' ) ),
					'i18n_scheduled_sale_start'           => esc_js( __( 'Sale start date (YYYY-MM-DD format or leave blank)', 'woocommerce' ) ),
					'i18n_scheduled_sale_end'             => esc_js( __( 'Sale end date (YYYY-MM-DD format or leave blank)', 'woocommerce' ) ),
					'i18n_edited_variations'              => esc_js( __( 'Save changes before changing page?', 'woocommerce' ) ),
					'i18n_variation_count_single'         => esc_js( __( '1 variation', 'woocommerce' ) ),
					'i18n_variation_count_plural'         => esc_js( __( '%qty% variations', 'woocommerce' ) ),
					'variations_per_page'                 => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) ),
				);

				wp_localize_script( 'wc-admin-variation-meta-boxes', 'woocommerce_admin_meta_boxes_variations', $params );
			}
			/* phpcs: enable */
			if ( $this->is_order_meta_box_screen( $screen_id ) ) {
				$default_location = wc_get_customer_default_location();

				wp_enqueue_script( 'wc-admin-order-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'wc-backbone-modal', 'selectWoo', 'wc-clipboard' ), $version );
				wp_localize_script(
					'wc-admin-order-meta-boxes',
					'woocommerce_admin_meta_boxes_order',
					array(
						'countries'              => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
						'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
						'default_country'        => isset( $default_location['country'] ) ? $default_location['country'] : '',
						'default_state'          => isset( $default_location['state'] ) ? $default_location['state'] : '',
						'placeholder_name'       => esc_attr__( 'Name (required)', 'woocommerce' ),
						'placeholder_value'      => esc_attr__( 'Value (required)', 'woocommerce' ),
					)
				);
			}
			/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
			if ( in_array( $screen_id, array( 'shop_coupon', 'edit-shop_coupon' ) ) ) {
				wp_enqueue_script( 'wc-admin-coupon-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-coupon' . $suffix . '.js', array( 'wc-admin-meta-boxes' ), $version );
				wp_localize_script(
					'wc-admin-coupon-meta-boxes',
					'woocommerce_admin_meta_boxes_coupon',
					array(
						'generate_button_text' => esc_html__( 'Generate coupon code', 'woocommerce' ),
						'characters'           => apply_filters( 'woocommerce_coupon_code_generator_characters', 'ABCDEFGHJKMNPQRSTUVWXYZ23456789' ),
						'char_length'          => apply_filters( 'woocommerce_coupon_code_generator_character_length', 8 ),
						'prefix'               => apply_filters( 'woocommerce_coupon_code_generator_prefix', '' ),
						'suffix'               => apply_filters( 'woocommerce_coupon_code_generator_suffix', '' ),
					)
				);
			}
			/* phpcs: enable */
			if ( in_array( str_replace( 'edit-', '', $screen_id ), array( 'shop_coupon', 'product' ), true ) || $this->is_order_meta_box_screen( $screen_id ) ) {
				$post_id                = isset( $post->ID ) ? $post->ID : '';
				$currency               = '';
				$remove_item_notice     = __( 'Are you sure you want to remove the selected items?', 'woocommerce' );
				$remove_fee_notice      = __( 'Are you sure you want to remove the selected fees?', 'woocommerce' );
				$remove_shipping_notice = __( 'Are you sure you want to remove the selected shipping?', 'woocommerce' );
				$product                = wc_get_product( $post_id );

				// Eventually this will become wc_data_or_post object as we implement more custom tables.
				$order_or_post_object = $post;
				if ( ( $theorder instanceof WC_Order ) && $this->is_order_meta_box_screen( $screen_id ) ) {
					$order_or_post_object = $theorder;
					if ( $order_or_post_object ) {
						$currency = $order_or_post_object->get_currency();

						if ( ! $order_or_post_object->has_status( array( 'pending', 'failed', 'cancelled' ) ) ) {
							$remove_item_notice = $remove_item_notice . ' ' . __( "You may need to manually restore the item's stock.", 'woocommerce' );
						}
					}
				}

				$params = array(
					'remove_item_notice'                 => $remove_item_notice,
					'remove_fee_notice'                  => $remove_fee_notice,
					'remove_shipping_notice'             => $remove_shipping_notice,
					'i18n_select_items'                  => __( 'Please select some items.', 'woocommerce' ),
					'i18n_do_refund'                     => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce' ),
					'i18n_delete_refund'                 => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce' ),
					'i18n_delete_tax'                    => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce' ),
					'remove_item_meta'                   => __( 'Remove this item meta?', 'woocommerce' ),
					'remove_attribute'                   => __( 'Remove this attribute?', 'woocommerce' ),
					'name_label'                         => __( 'Name', 'woocommerce' ),
					'remove_label'                       => __( 'Remove', 'woocommerce' ),
					'click_to_toggle'                    => __( 'Click to toggle', 'woocommerce' ),
					'values_label'                       => __( 'Value(s)', 'woocommerce' ),
					'text_attribute_tip'                 => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
					'visible_label'                      => __( 'Visible on the product page', 'woocommerce' ),
					'used_for_variations_label'          => __( 'Used for variations', 'woocommerce' ),
					'new_attribute_prompt'               => __( 'Enter a name for the new attribute term:', 'woocommerce' ),
					'calc_totals'                        => __( 'Recalculate totals? This will calculate taxes based on the customers country (or the store base country) and update totals.', 'woocommerce' ),
					'copy_billing'                       => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
					'load_billing'                       => __( "Load the customer's billing information? This will remove any currently entered billing information.", 'woocommerce' ),
					'load_shipping'                      => __( "Load the customer's shipping information? This will remove any currently entered shipping information.", 'woocommerce' ),
					'featured_label'                     => __( 'Featured', 'woocommerce' ),
					'prices_include_tax'                 => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
					'tax_based_on'                       => esc_attr( get_option( 'woocommerce_tax_based_on' ) ),
					'round_at_subtotal'                  => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
					'no_customer_selected'               => __( 'No customer selected', 'woocommerce' ),
					'plugin_url'                         => WC()->plugin_url(),
					'ajax_url'                           => admin_url( 'admin-ajax.php' ),
					'order_item_nonce'                   => wp_create_nonce( 'order-item' ),
					'add_attribute_nonce'                => wp_create_nonce( 'add-attribute' ),
					'save_attributes_nonce'              => wp_create_nonce( 'save-attributes' ),
					'add_attributes_and_variations'      => wp_create_nonce( 'add-attributes-and-variations' ),
					'calc_totals_nonce'                  => wp_create_nonce( 'calc-totals' ),
					'get_customer_details_nonce'         => wp_create_nonce( 'get-customer-details' ),
					'search_products_nonce'              => wp_create_nonce( 'search-products' ),
					'grant_access_nonce'                 => wp_create_nonce( 'grant-access' ),
					'revoke_access_nonce'                => wp_create_nonce( 'revoke-access' ),
					'add_order_note_nonce'               => wp_create_nonce( 'add-order-note' ),
					'delete_order_note_nonce'            => wp_create_nonce( 'delete-order-note' ),
					'calendar_image'                     => WC()->plugin_url() . '/assets/images/calendar.png',
					'post_id'                            => $this->is_order_meta_box_screen( $screen_id ) && isset( $order_or_post_object ) ? \Automattic\WooCommerce\Utilities\OrderUtil::get_post_or_order_id( $order_or_post_object ) : $post_id,
					'base_country'                       => WC()->countries->get_base_country(),
					'currency_format_num_decimals'       => wc_get_price_decimals(),
					'currency_format_symbol'             => get_woocommerce_currency_symbol( $currency ),
					'currency_format_decimal_sep'        => esc_attr( wc_get_price_decimal_separator() ),
					'currency_format_thousand_sep'       => esc_attr( wc_get_price_thousand_separator() ),
					'currency_format'                    => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS.
					'rounding_precision'                 => wc_get_rounding_precision(),
					'tax_rounding_mode'                  => wc_get_tax_rounding_mode(),
					'product_types'                      => array_unique( array_merge( array( 'simple', 'grouped', 'variable', 'external' ), array_keys( wc_get_product_types() ) ) ),
					'i18n_download_permission_fail'      => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
					'i18n_permission_revoke'             => __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
					'i18n_tax_rate_already_exists'       => __( 'You cannot add the same tax rate twice!', 'woocommerce' ),
					'i18n_delete_note'                   => __( 'Are you sure you wish to delete this note? This action cannot be undone.', 'woocommerce' ),
					'i18n_apply_coupon'                  => __( 'Enter a coupon code to apply. Discounts are applied to line totals, before taxes.', 'woocommerce' ),
					'i18n_add_fee'                       => __( 'Enter a fixed amount or percentage to apply as a fee.', 'woocommerce' ),
					'i18n_attribute_name_placeholder'    => __( 'New attribute', 'woocommerce' ),
					'i18n_product_simple_tip'            => __( '<b>Simple –</b> covers the vast majority of any products you may sell. Simple products are shipped and have no options. For example, a book.', 'woocommerce' ),
					'i18n_product_grouped_tip'           => __( '<b>Grouped –</b> a collection of related products that can be purchased individually and only consist of simple products. For example, a set of six drinking glasses.', 'woocommerce' ),
					'i18n_product_external_tip'          => __( '<b>External or Affiliate –</b> one that you list and describe on your website but is sold elsewhere.', 'woocommerce' ),
					'i18n_product_variable_tip'          => __( '<b>Variable –</b> a product with variations, each of which may have a different SKU, price, stock option, etc. For example, a t-shirt available in different colors and/or sizes.', 'woocommerce' ),
					'i18n_product_other_tip'             => __( 'Product types define available product details and attributes, such as downloadable files and variations. They’re also used for analytics and inventory management.', 'woocommerce' ),
					'i18n_product_description_tip'       => __( 'Describe this product. What makes it unique? What are its most important features?', 'woocommerce' ),
					'i18n_product_short_description_tip' => __( 'Summarize this product in 1-2 short sentences. We’ll show it at the top of the page.', 'woocommerce' ),
					'i18n_save_attribute_variation_tip'  => __( 'Make sure you enter the name and values for each attribute.', 'woocommerce' ),
					/* translators: %1$s: maximum file size */
					'i18n_product_image_tip'             => sprintf( __( 'For best results, upload JPEG or PNG files that are 1000 by 1000 pixels or larger. Maximum upload file size: %1$s.', 'woocommerce' ) , size_format( wp_max_upload_size() ) ),
					'i18n_remove_used_attribute_confirmation_message' => __( 'If you remove this attribute, customers will no longer be able to purchase some variations of this product.', 'woocommerce' ),
					'i18n_add_attribute_error_notice'    => __( 'Adding new attribute failed.', 'woocommerce' ),
				);

				wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $params );
			}

			// Term ordering - only when sorting by term_order.
			/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
			if ( ( strstr( $screen_id, 'edit-pa_' ) || ( ! empty( $_GET['taxonomy'] ) && in_array( wp_unslash( $_GET['taxonomy'] ), apply_filters( 'woocommerce_sortable_taxonomies', array( 'product_cat' ) ) ) ) ) && ! isset( $_GET['orderby'] ) ) {

				wp_register_script( 'woocommerce_term_ordering', WC()->plugin_url() . '/assets/js/admin/term-ordering' . $suffix . '.js', array( 'jquery-ui-sortable' ), $version );
				wp_enqueue_script( 'woocommerce_term_ordering' );

				$taxonomy = isset( $_GET['taxonomy'] ) ? wc_clean( wp_unslash( $_GET['taxonomy'] ) ) : '';

				$woocommerce_term_order_params = array(
					'taxonomy' => $taxonomy,
				);

				wp_localize_script( 'woocommerce_term_ordering', 'woocommerce_term_ordering_params', $woocommerce_term_order_params );
			}
			/* phpcs: enable */

			// Product sorting - only when sorting by menu order on the products page.
			if ( current_user_can( 'edit_others_pages' ) && 'edit-product' === $screen_id && isset( $wp_query->query['orderby'] ) && 'menu_order title' === $wp_query->query['orderby'] ) {
				wp_register_script( 'woocommerce_product_ordering', WC()->plugin_url() . '/assets/js/admin/product-ordering' . $suffix . '.js', array( 'jquery-ui-sortable' ), $version, true );
				wp_enqueue_script( 'woocommerce_product_ordering' );
			}

			// Reports Pages.
			/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
			if ( in_array( $screen_id, apply_filters( 'woocommerce_reports_screen_ids', array( $wc_screen_id . '_page_wc-reports', 'toplevel_page_wc-reports', 'dashboard' ) ) ) ) {
				wp_register_script( 'wc-reports', WC()->plugin_url() . '/assets/js/admin/reports' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker' ), $version );

				wp_enqueue_script( 'wc-reports' );
				wp_enqueue_script( 'flot' );
				wp_enqueue_script( 'flot-resize' );
				wp_enqueue_script( 'flot-time' );
				wp_enqueue_script( 'flot-pie' );
				wp_enqueue_script( 'flot-stack' );
			}
			/* phpcs: enable */

			// API settings.
			if ( $wc_screen_id . '_page_wc-settings' === $screen_id && isset( $_GET['section'] ) && 'keys' == $_GET['section'] ) {
				wp_register_script( 'wc-api-keys', WC()->plugin_url() . '/assets/js/admin/api-keys' . $suffix . '.js', array( 'jquery', 'woocommerce_admin', 'underscore', 'backbone', 'wp-util', 'qrcode', 'wc-clipboard' ), $version, true );
				wp_enqueue_script( 'wc-api-keys' );
				wp_localize_script(
					'wc-api-keys',
					'woocommerce_admin_api_keys',
					array(
						'ajax_url'         => admin_url( 'admin-ajax.php' ),
						'update_api_nonce' => wp_create_nonce( 'update-api-key' ),
						'clipboard_failed' => esc_html__( 'Copying to clipboard failed. Please press Ctrl/Cmd+C to copy.', 'woocommerce' ),
					)
				);
			}

			// System status.
			if ( $wc_screen_id . '_page_wc-status' === $screen_id ) {
				wp_register_script( 'wc-admin-system-status', WC()->plugin_url() . '/assets/js/admin/system-status' . $suffix . '.js', array( 'wc-clipboard' ), $version );
				wp_enqueue_script( 'wc-admin-system-status' );
				wp_localize_script(
					'wc-admin-system-status',
					'woocommerce_admin_system_status',
					array(
						'delete_log_confirmation' => esc_js( __( 'Are you sure you want to delete this log?', 'woocommerce' ) ),
						'run_tool_confirmation'   => esc_js( __( 'Are you sure you want to run this tool?', 'woocommerce' ) ),
					)
				);
			}

			if ( in_array( $screen_id, array( 'user-edit', 'profile' ) ) ) {
				wp_register_script( 'wc-users', WC()->plugin_url() . '/assets/js/admin/users' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select', 'selectWoo' ), $version, true );
				wp_enqueue_script( 'wc-users' );
				wp_localize_script(
					'wc-users',
					'wc_users_params',
					array(
						'countries'              => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
						'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
					)
				);
			}

			if ( WC_Marketplace_Suggestions::show_suggestions_for_screen( $screen_id ) ) {
				$active_plugin_slugs = array_map( 'dirname', get_option( 'active_plugins' ) );
				wp_register_script(
					'marketplace-suggestions',
					WC()->plugin_url() . '/assets/js/admin/marketplace-suggestions' . $suffix . '.js',
					array( 'jquery', 'underscore', 'js-cookie' ),
					$version,
					true
				);
				wp_localize_script(
					'marketplace-suggestions',
					'marketplace_suggestions',
					array(
						'dismiss_suggestion_nonce' => wp_create_nonce( 'add_dismissed_marketplace_suggestion' ),
						'active_plugins'           => $active_plugin_slugs,
						'dismissed_suggestions'    => WC_Marketplace_Suggestions::get_dismissed_suggestions(),
						'suggestions_data'         => WC_Marketplace_Suggestions::get_suggestions_api_data(),
						'manage_suggestions_url'   => admin_url( 'admin.php?page=wc-settings&tab=advanced&section=woocommerce_com' ),
						'in_app_purchase_params'   => WC_Admin_Addons::get_in_app_purchase_url_params(),
						'i18n_marketplace_suggestions_default_cta'
							=> esc_html__( 'Learn More', 'woocommerce' ),
						'i18n_marketplace_suggestions_dismiss_tooltip'
							=> esc_attr__( 'Dismiss this suggestion', 'woocommerce' ),
						'i18n_marketplace_suggestions_manage_suggestions'
							=> esc_html__( 'Manage suggestions', 'woocommerce' ),
					)
				);
				wp_enqueue_script( 'marketplace-suggestions' );
			}

		}

		/**
		 * Helper function to determine whether the current screen is an order edit screen.
		 *
		 * @param string $screen_id Screen ID.
		 *
		 * @return bool Whether the current screen is an order edit screen.
		 */
		private function is_order_meta_box_screen( $screen_id ) {
			$screen_id = str_replace( 'edit-', '', $screen_id );

			$types_with_metaboxes_screen_ids = array_filter(
				array_map(
					'wc_get_page_screen_id',
					wc_get_order_types( 'order-meta-boxes' )
				)
			);

			return in_array( $screen_id, $types_with_metaboxes_screen_ids, true );
		}

	}

endif;

return new WC_Admin_Assets();
