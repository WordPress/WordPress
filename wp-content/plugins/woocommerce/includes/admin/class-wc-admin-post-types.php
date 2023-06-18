<?php
/**
 * Post Types Admin
 *
 * @package  WooCommerce\Admin
 * @version  3.3.0
 */

use Automattic\Jetpack\Constants;
use Automattic\WooCommerce\Utilities\NumberUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Admin_Post_Types', false ) ) {
	new WC_Admin_Post_Types();
	return;
}

/**
 * WC_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class WC_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once __DIR__ . '/class-wc-admin-meta-boxes.php';

		if ( ! function_exists( 'duplicate_post_plugin_activation' ) ) {
			include_once __DIR__ . '/class-wc-admin-duplicate-product.php';
		}

		// Load correct list table classes for current screen.
		add_action( 'current_screen', array( $this, 'setup_screen' ) );
		add_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );

		// Admin notices.
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'woocommerce_order_updated_messages', array( $this, 'order_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// Disable Auto Save.
		add_action( 'admin_print_scripts', array( $this, 'disable_autosave' ) );

		// Extra post data and screen elements.
		add_action( 'edit_form_top', array( $this, 'edit_form_top' ) );
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'product_data_visibility' ) );

		// Uploads.
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
		add_filter( 'wp_unique_filename', array( $this, 'update_filename' ), 10, 3 );
		add_action( 'media_upload_downloadable_product', array( $this, 'media_upload_downloadable_product' ) );

		// Hide template for CPT archive.
		add_filter( 'theme_page_templates', array( $this, 'hide_cpt_archive_templates' ), 10, 3 );
		add_action( 'edit_form_top', array( $this, 'show_cpt_archive_notice' ) );

		// Add a post display state for special WC pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );

		// Bulk / quick edit.
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'save_post', array( $this, 'bulk_and_quick_edit_hook' ), 10, 2 );
		add_action( 'woocommerce_product_bulk_and_quick_edit', array( $this, 'bulk_and_quick_edit_save_post' ), 10, 2 );
	}

	/**
	 * Looks at the current screen and loads the correct list table handler.
	 *
	 * @since 3.3.0
	 */
	public function setup_screen() {
		global $wc_list_table;

		$request_data = $this->request_data();

		$screen_id = false;

		if ( function_exists( 'get_current_screen' ) ) {
			$screen    = get_current_screen();
			$screen_id = isset( $screen, $screen->id ) ? $screen->id : '';
		}

		if ( ! empty( $request_data['screen'] ) ) {
			$screen_id = wc_clean( wp_unslash( $request_data['screen'] ) );
		}

		switch ( $screen_id ) {
			case 'edit-shop_order':
				include_once __DIR__ . '/list-tables/class-wc-admin-list-table-orders.php';
				$wc_list_table = new WC_Admin_List_Table_Orders();
				break;
			case 'edit-shop_coupon':
				include_once __DIR__ . '/list-tables/class-wc-admin-list-table-coupons.php';
				$wc_list_table = new WC_Admin_List_Table_Coupons();
				break;
			case 'edit-product':
				include_once __DIR__ . '/list-tables/class-wc-admin-list-table-products.php';
				$wc_list_table = new WC_Admin_List_Table_Products();
				break;
		}

		// Ensure the table handler is only loaded once. Prevents multiple loads if a plugin calls check_ajax_referer many times.
		remove_action( 'current_screen', array( $this, 'setup_screen' ) );
		remove_action( 'check_ajax_referer', array( $this, 'setup_screen' ) );
	}

	/**
	 * Change messages when a post type is updated.
	 *
	 * @param  array $messages Array of messages.
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post;

		$messages['product'] = array(
			0  => '', // Unused. Messages start at index 1.
			/* translators: %1$s: Product link opening tag. %2$s: Product link closing tag.*/
			1  => sprintf( __( 'Product updated. %1$sView Product%2$s', 'woocommerce' ), '<a id="woocommerce-product-updated-message-view-product__link" href="' . esc_url( get_permalink( $post->ID ) ) . '">', '</a>' ),
			2  => __( 'Custom field updated.', 'woocommerce' ),
			3  => __( 'Custom field deleted.', 'woocommerce' ),
			4  => __( 'Product updated.', 'woocommerce' ),
			5  => __( 'Revision restored.', 'woocommerce' ),
			/* translators: %1$s: Product link opening tag. %2$s: Product link closing tag.*/
			6  => sprintf( __( 'Product published. %1$sView Product%2$s', 'woocommerce' ), '<a id="woocommerce-product-updated-message-view-product__link" href="' . esc_url( get_permalink( $post->ID ) ) . '">', '</a>' ),
			7  => __( 'Product saved.', 'woocommerce' ),
			/* translators: %s: product url */
			8  => sprintf( __( 'Product submitted. <a target="_blank" href="%s">Preview product</a>', 'woocommerce' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
			9  => sprintf(
				/* translators: 1: date 2: product url */
				__( 'Product scheduled for: %1$s. <a target="_blank" href="%2$s">Preview product</a>', 'woocommerce' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $post->post_date ) ) . '</strong>',
				esc_url( get_permalink( $post->ID ) )
			),
			/* translators: %s: product url */
			10 => sprintf( __( 'Product draft updated. <a target="_blank" href="%s">Preview product</a>', 'woocommerce' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		);

		$messages = $this->order_updated_messages( $messages );

		$messages['shop_coupon'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Coupon updated.', 'woocommerce' ),
			2  => __( 'Custom field updated.', 'woocommerce' ),
			3  => __( 'Custom field deleted.', 'woocommerce' ),
			4  => __( 'Coupon updated.', 'woocommerce' ),
			5  => __( 'Revision restored.', 'woocommerce' ),
			6  => __( 'Coupon updated.', 'woocommerce' ),
			7  => __( 'Coupon saved.', 'woocommerce' ),
			8  => __( 'Coupon submitted.', 'woocommerce' ),
			9  => sprintf(
				/* translators: %s: date */
				__( 'Coupon scheduled for: %s.', 'woocommerce' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $post->post_date ) ) . '</strong>'
			),
			10 => __( 'Coupon draft updated.', 'woocommerce' ),
		);

		return $messages;
	}

	/**
	 * Add messages when an order is updated.
	 *
	 * @param array $messages Array of messages.
	 *
	 * @return array
	 */
	public function order_updated_messages( array $messages ) {
		global $post, $theorder;

		if ( ! isset( $theorder ) || ! $theorder instanceof WC_Abstract_Order ) {
			if ( ! isset( $post ) || 'shop_order' !== $post->post_type ) {
				return $messages;
			} else {
				\Automattic\WooCommerce\Utilities\OrderUtil::init_theorder_object( $post );
			}
		}

		$messages['shop_order'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Order updated.', 'woocommerce' ),
			2  => __( 'Custom field updated.', 'woocommerce' ),
			3  => __( 'Custom field deleted.', 'woocommerce' ),
			4  => __( 'Order updated.', 'woocommerce' ),
			5  => __( 'Revision restored.', 'woocommerce' ),
			6  => __( 'Order updated.', 'woocommerce' ),
			7  => __( 'Order saved.', 'woocommerce' ),
			8  => __( 'Order submitted.', 'woocommerce' ),
			9  => sprintf(
			/* translators: %s: date */
				__( 'Order scheduled for: %s.', 'woocommerce' ),
				'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $theorder->get_date_created() ) ) . '</strong>'
			),
			10 => __( 'Order draft updated.', 'woocommerce' ),
			11 => __( 'Order updated and sent.', 'woocommerce' ),
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages['product'] = array(
			/* translators: %s: product count */
			'updated'   => _n( '%s product updated.', '%s products updated.', $bulk_counts['updated'], 'woocommerce' ),
			/* translators: %s: product count */
			'locked'    => _n( '%s product not updated, somebody is editing it.', '%s products not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce' ),
			/* translators: %s: product count */
			'deleted'   => _n( '%s product permanently deleted.', '%s products permanently deleted.', $bulk_counts['deleted'], 'woocommerce' ),
			/* translators: %s: product count */
			'trashed'   => _n( '%s product moved to the Trash.', '%s products moved to the Trash.', $bulk_counts['trashed'], 'woocommerce' ),
			/* translators: %s: product count */
			'untrashed' => _n( '%s product restored from the Trash.', '%s products restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce' ),
		);

		$bulk_messages['shop_order'] = array(
			/* translators: %s: order count */
			'updated'   => _n( '%s order updated.', '%s orders updated.', $bulk_counts['updated'], 'woocommerce' ),
			/* translators: %s: order count */
			'locked'    => _n( '%s order not updated, somebody is editing it.', '%s orders not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce' ),
			/* translators: %s: order count */
			'deleted'   => _n( '%s order permanently deleted.', '%s orders permanently deleted.', $bulk_counts['deleted'], 'woocommerce' ),
			/* translators: %s: order count */
			'trashed'   => _n( '%s order moved to the Trash.', '%s orders moved to the Trash.', $bulk_counts['trashed'], 'woocommerce' ),
			/* translators: %s: order count */
			'untrashed' => _n( '%s order restored from the Trash.', '%s orders restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce' ),
		);

		$bulk_messages['shop_coupon'] = array(
			/* translators: %s: coupon count */
			'updated'   => _n( '%s coupon updated.', '%s coupons updated.', $bulk_counts['updated'], 'woocommerce' ),
			/* translators: %s: coupon count */
			'locked'    => _n( '%s coupon not updated, somebody is editing it.', '%s coupons not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce' ),
			/* translators: %s: coupon count */
			'deleted'   => _n( '%s coupon permanently deleted.', '%s coupons permanently deleted.', $bulk_counts['deleted'], 'woocommerce' ),
			/* translators: %s: coupon count */
			'trashed'   => _n( '%s coupon moved to the Trash.', '%s coupons moved to the Trash.', $bulk_counts['trashed'], 'woocommerce' ),
			/* translators: %s: coupon count */
			'untrashed' => _n( '%s coupon restored from the Trash.', '%s coupons restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce' ),
		);

		return $bulk_messages;
	}

	/**
	 * Custom bulk edit - form.
	 *
	 * @param string $column_name Column being shown.
	 * @param string $post_type Post type being shown.
	 */
	public function bulk_edit( $column_name, $post_type ) {
		if ( 'price' !== $column_name || 'product' !== $post_type ) {
			return;
		}

		$shipping_class = get_terms(
			'product_shipping_class',
			array(
				'hide_empty' => false,
			)
		);

		include WC()->plugin_path() . '/includes/admin/views/html-bulk-edit-product.php';
	}

	/**
	 * Custom quick edit - form.
	 *
	 * @param string $column_name Column being shown.
	 * @param string $post_type Post type being shown.
	 */
	public function quick_edit( $column_name, $post_type ) {
		if ( 'price' !== $column_name || 'product' !== $post_type ) {
			return;
		}

		$shipping_class = get_terms(
			'product_shipping_class',
			array(
				'hide_empty' => false,
			)
		);

		include WC()->plugin_path() . '/includes/admin/views/html-quick-edit-product.php';
	}

	/**
	 * Offers a way to hook into save post without causing an infinite loop
	 * when quick/bulk saving product info.
	 *
	 * @since 3.0.0
	 * @param int    $post_id Post ID being saved.
	 * @param object $post Post object being saved.
	 */
	public function bulk_and_quick_edit_hook( $post_id, $post ) {
		remove_action( 'save_post', array( $this, 'bulk_and_quick_edit_hook' ) );
		do_action( 'woocommerce_product_bulk_and_quick_edit', $post_id, $post );
		add_action( 'save_post', array( $this, 'bulk_and_quick_edit_hook' ), 10, 2 );
	}

	/**
	 * Quick and bulk edit saving.
	 *
	 * @param int    $post_id Post ID being saved.
	 * @param object $post Post object being saved.
	 * @return int
	 */
	public function bulk_and_quick_edit_save_post( $post_id, $post ) {
		$request_data = $this->request_data();

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( Constants::is_true( 'DOING_AUTOSAVE' ) ) {
			return $post_id;
		}

		// Don't save revisions and autosaves.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check nonce.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( ! isset( $request_data['woocommerce_quick_edit_nonce'] ) || ! wp_verify_nonce( $request_data['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) {
			return $post_id;
		}

		// Get the product and save.
		$product = wc_get_product( $post );

		if ( ! empty( $request_data['woocommerce_quick_edit'] ) ) { // WPCS: input var ok.
			$this->quick_edit_save( $post_id, $product );
		} else {
			$this->bulk_edit_save( $post_id, $product );
		}

		return $post_id;
	}

	/**
	 * Quick edit.
	 *
	 * @param int        $post_id Post ID being saved.
	 * @param WC_Product $product Product object.
	 */
	private function quick_edit_save( $post_id, $product ) {
		$request_data = $this->request_data();

		$data_store        = $product->get_data_store();
		$old_regular_price = $product->get_regular_price();
		$old_sale_price    = $product->get_sale_price();
		$input_to_props    = array(
			'_weight'     => 'weight',
			'_length'     => 'length',
			'_width'      => 'width',
			'_height'     => 'height',
			'_visibility' => 'catalog_visibility',
			'_tax_class'  => 'tax_class',
			'_tax_status' => 'tax_status',
		);

		foreach ( $input_to_props as $input_var => $prop ) {
			if ( isset( $request_data[ $input_var ] ) ) {
				$product->{"set_{$prop}"}( wc_clean( wp_unslash( $request_data[ $input_var ] ) ) );
			}
		}

		if ( isset( $request_data['_sku'] ) ) {
			$sku = $product->get_sku();
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$new_sku = (string) wc_clean( $request_data['_sku'] );

			if ( $new_sku !== $sku ) {
				if ( ! empty( $new_sku ) ) {
					$unique_sku = wc_product_has_unique_sku( $post_id, $new_sku );
					if ( $unique_sku ) {
						$product->set_sku( wc_clean( wp_unslash( $new_sku ) ) );
					}
				} else {
					$product->set_sku( '' );
				}
			}
		}

		if ( ! empty( $request_data['_shipping_class'] ) ) {
			if ( '_no_shipping_class' === $request_data['_shipping_class'] ) {
				$product->set_shipping_class_id( 0 );
			} else {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$shipping_class_id = $data_store->get_shipping_class_id_by_slug( wc_clean( $request_data['_shipping_class'] ) );
				$product->set_shipping_class_id( $shipping_class_id );
			}
		}

		$product->set_featured( isset( $request_data['_featured'] ) );

		if ( $product->is_type( 'simple' ) || $product->is_type( 'external' ) ) {

			if ( isset( $request_data['_regular_price'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$new_regular_price = ( '' === $request_data['_regular_price'] ) ? '' : wc_format_decimal( $request_data['_regular_price'] );
				$product->set_regular_price( $new_regular_price );
			} else {
				$new_regular_price = null;
			}
			if ( isset( $request_data['_sale_price'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$new_sale_price = ( '' === $request_data['_sale_price'] ) ? '' : wc_format_decimal( $request_data['_sale_price'] );
				$product->set_sale_price( $new_sale_price );
			} else {
				$new_sale_price = null;
			}

			// Handle price - remove dates and set to lowest.
			$price_changed = false;

			if ( ! is_null( $new_regular_price ) && $new_regular_price !== $old_regular_price ) {
				$price_changed = true;
			} elseif ( ! is_null( $new_sale_price ) && $new_sale_price !== $old_sale_price ) {
				$price_changed = true;
			}

			if ( $price_changed ) {
				$product->set_date_on_sale_to( '' );
				$product->set_date_on_sale_from( '' );
			}
		}

		// Handle Stock Data.
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$manage_stock = ! empty( $request_data['_manage_stock'] ) && 'grouped' !== $product->get_type() ? 'yes' : 'no';
		$backorders   = ! empty( $request_data['_backorders'] ) ? wc_clean( $request_data['_backorders'] ) : 'no';
		if ( ! empty( $request_data['_stock_status'] ) ) {
			$stock_status = wc_clean( $request_data['_stock_status'] );
		} else {
			$stock_status = $product->is_type( 'variable' ) ? null : 'instock';
		}
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$product->set_manage_stock( $manage_stock );

		if ( 'external' !== $product->get_type() ) {
			$product->set_backorders( $backorders );
		}

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$stock_amount = 'yes' === $manage_stock && isset( $request_data['_stock'] ) && is_numeric( wp_unslash( $request_data['_stock'] ) ) ? wc_stock_amount( wp_unslash( $request_data['_stock'] ) ) : '';
			$product->set_stock_quantity( $stock_amount );
		}

		$product = $this->maybe_update_stock_status( $product, $stock_status );

		$product->save();

		do_action( 'woocommerce_product_quick_edit_save', $product );
	}

	/**
	 * Bulk edit.
	 *
	 * @param int        $post_id Post ID being saved.
	 * @param WC_Product $product Product object.
	 */
	public function bulk_edit_save( $post_id, $product ) {
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		$request_data = $this->request_data();

		$data_store = $product->get_data_store();

		if ( ! empty( $request_data['change_weight'] ) && isset( $request_data['_weight'] ) ) {
			$product->set_weight( wc_clean( wp_unslash( $request_data['_weight'] ) ) );
		}

		if ( ! empty( $request_data['change_dimensions'] ) ) {
			if ( isset( $request_data['_length'] ) ) {
				$product->set_length( wc_clean( wp_unslash( $request_data['_length'] ) ) );
			}
			if ( isset( $request_data['_width'] ) ) {
				$product->set_width( wc_clean( wp_unslash( $request_data['_width'] ) ) );
			}
			if ( isset( $request_data['_height'] ) ) {
				$product->set_height( wc_clean( wp_unslash( $request_data['_height'] ) ) );
			}
		}

		if ( ! empty( $request_data['_tax_status'] ) ) {
			$product->set_tax_status( wc_clean( $request_data['_tax_status'] ) );
		}

		if ( ! empty( $request_data['_tax_class'] ) ) {
			$tax_class = wc_clean( wp_unslash( $request_data['_tax_class'] ) );
			if ( 'standard' === $tax_class ) {
				$tax_class = '';
			}
			$product->set_tax_class( $tax_class );
		}

		if ( ! empty( $request_data['_shipping_class'] ) ) {
			if ( '_no_shipping_class' === $request_data['_shipping_class'] ) {
				$product->set_shipping_class_id( 0 );
			} else {
				$shipping_class_id = $data_store->get_shipping_class_id_by_slug( wc_clean( $request_data['_shipping_class'] ) );
				$product->set_shipping_class_id( $shipping_class_id );
			}
		}

		if ( ! empty( $request_data['_visibility'] ) ) {
			$product->set_catalog_visibility( wc_clean( $request_data['_visibility'] ) );
		}

		if ( ! empty( $request_data['_featured'] ) ) {
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$product->set_featured( wp_unslash( $request_data['_featured'] ) );
			// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		if ( ! empty( $request_data['_sold_individually'] ) ) {
			if ( 'yes' === $request_data['_sold_individually'] ) {
				$product->set_sold_individually( 'yes' );
			} else {
				$product->set_sold_individually( '' );
			}
		}

		// Handle price - remove dates and set to lowest.
		$change_price_product_types    = apply_filters( 'woocommerce_bulk_edit_save_price_product_types', array( 'simple', 'external' ) );
		$can_product_type_change_price = false;
		foreach ( $change_price_product_types as $product_type ) {
			if ( $product->is_type( $product_type ) ) {
				$can_product_type_change_price = true;
				break;
			}
		}

		if ( $can_product_type_change_price ) {
			$regular_price_changed = $this->set_new_price( $product, 'regular' );
			$sale_price_changed    = $this->set_new_price( $product, 'sale' );

			if ( $regular_price_changed || $sale_price_changed ) {
				$product->set_date_on_sale_to( '' );
				$product->set_date_on_sale_from( '' );

				if ( $product->get_regular_price() < $product->get_sale_price() ) {
					$product->set_sale_price( '' );
				}
			}
		}

		// Handle Stock Data.
		$was_managing_stock = $product->get_manage_stock() ? 'yes' : 'no';
		$backorders         = $product->get_backorders();
		$backorders         = ! empty( $request_data['_backorders'] ) ? wc_clean( $request_data['_backorders'] ) : $backorders;

		if ( ! empty( $request_data['_manage_stock'] ) ) {
			$manage_stock = 'yes' === wc_clean( $request_data['_manage_stock'] ) && 'grouped' !== $product->get_type() ? 'yes' : 'no';
		} else {
			$manage_stock = $was_managing_stock;
		}

		$stock_amount = 'yes' === $manage_stock && ! empty( $request_data['change_stock'] ) && isset( $request_data['_stock'] ) ? wc_stock_amount( $request_data['_stock'] ) : $product->get_stock_quantity();

		$product->set_manage_stock( $manage_stock );

		if ( 'external' !== $product->get_type() ) {
			$product->set_backorders( $backorders );
		}

		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {
			$change_stock = absint( $request_data['change_stock'] );
			switch ( $change_stock ) {
				case 2:
					wc_update_product_stock( $product, $stock_amount, 'increase', true );
					break;
				case 3:
					wc_update_product_stock( $product, $stock_amount, 'decrease', true );
					break;
				default:
					wc_update_product_stock( $product, $stock_amount, 'set', true );
					break;
			}
		} else {
			// Reset values if WooCommerce Setting - Manage Stock status is disabled.
			$product->set_stock_quantity( '' );
			$product->set_manage_stock( 'no' );
		}

		$stock_status = empty( $request_data['_stock_status'] ) ? null : wc_clean( $request_data['_stock_status'] );
		$product      = $this->maybe_update_stock_status( $product, $stock_status );

		$product->save();

		do_action( 'woocommerce_product_bulk_edit_save', $product );

		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	}

	/**
	 * Disable the auto-save functionality for Orders.
	 */
	public function disable_autosave() {
		global $post;

		if ( $post && in_array( get_post_type( $post->ID ), wc_get_order_types( 'order-meta-boxes' ), true ) ) {
			wp_dequeue_script( 'autosave' );
		}
	}

	/**
	 * Output extra data on post forms.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function edit_form_top( $post ) {
		echo '<input type="hidden" id="original_post_title" name="original_post_title" value="' . esc_attr( $post->post_title ) . '" />';
	}

	/**
	 * Change title boxes in admin.
	 *
	 * @param string  $text Text to shown.
	 * @param WP_Post $post Current post object.
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'product':
				$text = esc_html__( 'Product name', 'woocommerce' );
				break;
			case 'shop_coupon':
				$text = esc_html__( 'Coupon code', 'woocommerce' );
				break;
		}
		return $text;
	}

	/**
	 * Print coupon description textarea field.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function edit_form_after_title( $post ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( 'shop_coupon' === $post->post_type ) {
			?>
			<textarea id="woocommerce-coupon-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'woocommerce' ); ?>"><?php echo $post->post_excerpt; ?></textarea>
			<?php
		}
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Hidden default Meta-Boxes.
	 *
	 * @param  array  $hidden Hidden boxes.
	 * @param  object $screen Current screen.
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( 'product' === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}

		return $hidden;
	}

	/**
	 * Output product visibility options.
	 */
	public function product_data_visibility() {
		global $post, $thepostid, $product_object;

		if ( 'product' !== $post->post_type ) {
			return;
		}

		$thepostid          = $post->ID;
		$product_object     = $thepostid ? wc_get_product( $thepostid ) : new WC_Product();
		$current_visibility = $product_object->get_catalog_visibility();
		$current_featured   = wc_bool_to_string( $product_object->get_featured() );
		$visibility_options = wc_get_product_visibility_options();
		?>
		<div class="misc-pub-section" id="catalog-visibility">
			<?php esc_html_e( 'Catalog visibility:', 'woocommerce' ); ?>
			<strong id="catalog-visibility-display">
				<?php

				echo isset( $visibility_options[ $current_visibility ] ) ? esc_html( $visibility_options[ $current_visibility ] ) : esc_html( $current_visibility );

				if ( 'yes' === $current_featured ) {
					echo ', ' . esc_html__( 'Featured', 'woocommerce' );
				}
				?>
			</strong>

			<a href="#catalog-visibility" class="edit-catalog-visibility hide-if-no-js"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a>

			<div id="catalog-visibility-select" class="hide-if-js">

				<input type="hidden" name="current_visibility" id="current_visibility" value="<?php echo esc_attr( $current_visibility ); ?>" />
				<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />

				<?php
				echo '<p>' . esc_html__( 'This setting determines which shop pages products will be listed on.', 'woocommerce' ) . '</p>';

				foreach ( $visibility_options as $name => $label ) {
					echo '<input type="radio" name="_visibility" id="_visibility_' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '" ' . checked( $current_visibility, $name, false ) . ' data-label="' . esc_attr( $label ) . '" /> <label for="_visibility_' . esc_attr( $name ) . '" class="selectit">' . esc_html( $label ) . '</label><br />';
				}

				echo '<br /><input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' /> <label for="_featured">' . esc_html__( 'This is a featured product', 'woocommerce' ) . '</label><br />';
				?>
				<p>
					<a href="#catalog-visibility" class="save-post-visibility hide-if-no-js button"><?php esc_html_e( 'OK', 'woocommerce' ); ?></a>
					<a href="#catalog-visibility" class="cancel-post-visibility hide-if-no-js"><?php esc_html_e( 'Cancel', 'woocommerce' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Change upload dir for downloadable files.
	 *
	 * @param array $pathdata Array of paths.
	 * @return array
	 */
	public function upload_dir( $pathdata ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['type'] ) && 'downloadable_product' === $_POST['type'] ) {

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/woocommerce_uploads';
				$pathdata['url']    = $pathdata['url'] . '/woocommerce_uploads';
				$pathdata['subdir'] = '/woocommerce_uploads';
			} else {
				$new_subdir = '/woocommerce_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}
		return $pathdata;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Change filename for WooCommerce uploads and prepend unique chars for security.
	 *
	 * @param string $full_filename Original filename.
	 * @param string $ext           Extension of file.
	 * @param string $dir           Directory path.
	 *
	 * @return string New filename with unique hash.
	 * @since 4.0
	 */
	public function update_filename( $full_filename, $ext, $dir ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['type'] ) || ! 'downloadable_product' === $_POST['type'] ) {
			return $full_filename;
		}

		if ( ! strpos( $dir, 'woocommerce_uploads' ) ) {
			return $full_filename;
		}

		if ( 'no' === get_option( 'woocommerce_downloads_add_hash_to_filename' ) ) {
			return $full_filename;
		}

		return $this->unique_filename( $full_filename, $ext );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Change filename to append random text.
	 *
	 * @param string $full_filename Original filename with extension.
	 * @param string $ext           Extension.
	 *
	 * @return string Modified filename.
	 */
	public function unique_filename( $full_filename, $ext ) {
		$ideal_random_char_length = 6;   // Not going with a larger length because then downloaded filename will not be pretty.
		$max_filename_length      = 255; // Max file name length for most file systems.
		$length_to_prepend        = min( $ideal_random_char_length, $max_filename_length - strlen( $full_filename ) - 1 );

		if ( 1 > $length_to_prepend ) {
			return $full_filename;
		}

		$suffix   = strtolower( wp_generate_password( $length_to_prepend, false, false ) );
		$filename = $full_filename;

		if ( strlen( $ext ) > 0 ) {
			$filename = substr( $filename, 0, strlen( $filename ) - strlen( $ext ) );
		}

		$full_filename = str_replace(
			$filename,
			"$filename-$suffix",
			$full_filename
		);

		return $full_filename;
	}

	/**
	 * Run a filter when uploading a downloadable product.
	 */
	public function woocommerce_media_upload_downloadable_product() {
		do_action( 'media_upload_file' );
	}

	/**
	 * Grant downloadable file access to any newly added files on any existing.
	 * orders for this product that have previously been granted downloadable file access.
	 *
	 * @param int   $product_id product identifier.
	 * @param int   $variation_id optional product variation identifier.
	 * @param array $downloadable_files newly set files.
	 * @deprecated 3.3.0 and moved to post-data class.
	 */
	public function process_product_file_download_paths( $product_id, $variation_id, $downloadable_files ) {
		wc_deprecated_function( 'WC_Admin_Post_Types::process_product_file_download_paths', '3.3', '' );
		WC_Post_Data::process_product_file_download_paths( $product_id, $variation_id, $downloadable_files );
	}

	/**
	 * When editing the shop page, we should hide templates.
	 *
	 * @param array   $page_templates Templates array.
	 * @param string  $theme Classname.
	 * @param WP_Post $post The current post object.
	 * @return array
	 */
	public function hide_cpt_archive_templates( $page_templates, $theme, $post ) {
		$shop_page_id = wc_get_page_id( 'shop' );

		if ( $post && absint( $post->ID ) === $shop_page_id ) {
			$page_templates = array();
		}

		return $page_templates;
	}

	/**
	 * Show a notice above the CPT archive.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function show_cpt_archive_notice( $post ) {
		$shop_page_id = wc_get_page_id( 'shop' );

		if ( $post && absint( $post->ID ) === $shop_page_id ) {
			echo '<div class="notice notice-info">';
			/* translators: %s: URL to read more about the shop page. */
			echo '<p>' . sprintf( wp_kses_post( __( 'This is the WooCommerce shop page. The shop page is a special archive that lists your products. <a href="%s">You can read more about this here</a>.', 'woocommerce' ) ), 'https://docs.woocommerce.com/document/woocommerce-pages/#section-4' ) . '</p>';
			echo '</div>';
		}
	}

	/**
	 * Add a post display state for special WC pages in the page list table.
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	public function add_display_post_states( $post_states, $post ) {
		if ( wc_get_page_id( 'shop' ) === $post->ID ) {
			$post_states['wc_page_for_shop'] = __( 'Shop Page', 'woocommerce' );
		}

		if ( wc_get_page_id( 'cart' ) === $post->ID ) {
			$post_states['wc_page_for_cart'] = __( 'Cart Page', 'woocommerce' );
		}

		if ( wc_get_page_id( 'checkout' ) === $post->ID ) {
			$post_states['wc_page_for_checkout'] = __( 'Checkout Page', 'woocommerce' );
		}

		if ( wc_get_page_id( 'myaccount' ) === $post->ID ) {
			$post_states['wc_page_for_myaccount'] = __( 'My Account Page', 'woocommerce' );
		}

		if ( wc_get_page_id( 'terms' ) === $post->ID ) {
			$post_states['wc_page_for_terms'] = __( 'Terms and Conditions Page', 'woocommerce' );
		}

		return $post_states;
	}

	/**
	 * Apply product type constraints to stock status.
	 *
	 * @param WC_Product  $product The product whose stock status will be adjusted.
	 * @param string|null $stock_status The stock status to use for adjustment, or null if no new stock status has been supplied in the request.
	 * @return WC_Product The supplied product, or the synced product if it was a variable product.
	 */
	private function maybe_update_stock_status( $product, $stock_status ) {
		if ( $product->is_type( 'external' ) ) {
			// External products are always in stock.
			$product->set_stock_status( 'instock' );
		} elseif ( isset( $stock_status ) ) {
			if ( $product->is_type( 'variable' ) && ! $product->get_manage_stock() ) {
				// Stock status is determined by children.
				foreach ( $product->get_children() as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( ! $product->get_manage_stock() ) {
						$child->set_stock_status( $stock_status );
						$child->save();
					}
				}
				$product = WC_Product_Variable::sync( $product, false );
			} else {
				$product->set_stock_status( $stock_status );
			}
		}

		return $product;
	}

	/**
	 * Set the new regular or sale price if requested.
	 *
	 * @param WC_Product $product The product to set the new price for.
	 * @param string     $price_type 'regular' or 'sale'.
	 * @return bool true if a new price has been set, false otherwise.
	 */
	private function set_new_price( $product, $price_type ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended

		$request_data = $this->request_data();

		if ( empty( $request_data[ "change_{$price_type}_price" ] ) || ! isset( $request_data[ "_{$price_type}_price" ] ) ) {
			return false;
		}

		$old_price     = (float) $product->{"get_{$price_type}_price"}();
		$price_changed = false;

		$change_price  = absint( $request_data[ "change_{$price_type}_price" ] );
		$raw_price     = wc_clean( wp_unslash( $request_data[ "_{$price_type}_price" ] ) );
		$is_percentage = (bool) strstr( $raw_price, '%' );
		$price         = wc_format_decimal( $raw_price );

		switch ( $change_price ) {
			case 1:
				$new_price = $price;
				break;
			case 2:
				if ( $is_percentage ) {
					$percent   = $price / 100;
					$new_price = $old_price + ( $old_price * $percent );
				} else {
					$new_price = $old_price + $price;
				}
				break;
			case 3:
				if ( $is_percentage ) {
					$percent   = $price / 100;
					$new_price = max( 0, $old_price - ( $old_price * $percent ) );
				} else {
					$new_price = max( 0, $old_price - $price );
				}
				break;
			case 4:
				if ( 'sale' !== $price_type ) {
					break;
				}
				$regular_price = $product->get_regular_price();
				if ( $is_percentage && is_numeric( $regular_price ) ) {
					$percent   = $price / 100;
					$new_price = max( 0, $regular_price - ( NumberUtil::round( $regular_price * $percent, wc_get_price_decimals() ) ) );
				} else {
					$new_price = max( 0, (float) $regular_price - (float) $price );
				}
				break;

			default:
				break;
		}

		if ( isset( $new_price ) && $new_price !== $old_price ) {
			$price_changed = true;
			$new_price     = NumberUtil::round( $new_price, wc_get_price_decimals() );
			$product->{"set_{$price_type}_price"}( $new_price );
		}

		return $price_changed;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get the current request data ($_REQUEST superglobal).
	 * This method is added to ease unit testing.
	 *
	 * @return array The $_REQUEST superglobal.
	 */
	protected function request_data() {
		return $_REQUEST;
	}
}

new WC_Admin_Post_Types();
