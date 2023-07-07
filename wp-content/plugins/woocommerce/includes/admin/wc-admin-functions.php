<?php
/**
 * WooCommerce Admin Functions
 *
 * @package  WooCommerce\Admin\Functions
 * @version  2.4.0
 */

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all WooCommerce screen ids.
 *
 * @return array
 */
function wc_get_screen_ids() {
	$wc_screen_id = 'woocommerce';
	$screen_ids   = array(
		'toplevel_page_' . $wc_screen_id,
		$wc_screen_id . '_page_wc-orders',
		$wc_screen_id . '_page_wc-reports',
		$wc_screen_id . '_page_wc-shipping',
		$wc_screen_id . '_page_wc-settings',
		$wc_screen_id . '_page_wc-status',
		$wc_screen_id . '_page_wc-addons',
		'toplevel_page_wc-reports',
		'product_page_product_attributes',
		'product_page_product_exporter',
		'product_page_product_importer',
		'product_page_product-reviews',
		'edit-product',
		'product',
		'edit-shop_coupon',
		'shop_coupon',
		'edit-product_cat',
		'edit-product_tag',
		'profile',
		'user-edit',
	);

	foreach ( wc_get_order_types() as $type ) {
		$screen_ids[] = $type;
		$screen_ids[] = 'edit-' . $type;
		$screen_ids[] = wc_get_page_screen_id( $type );
	}

	$attributes = wc_get_attribute_taxonomies();

	if ( $attributes ) {
		foreach ( $attributes as $attribute ) {
			$screen_ids[] = 'edit-' . wc_attribute_taxonomy_name( $attribute->attribute_name );
		}
	}

	/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
	return apply_filters( 'woocommerce_screen_ids', $screen_ids );
	/* phpcs: enable */
}

/**
 * Get page ID for a specific WC resource.
 *
 * @param string $for Name of the resource.
 *
 * @return string Page ID. Empty string if resource not found.
 */
function wc_get_page_screen_id( $for ) {
	$screen_id = '';
	$for       = str_replace( '-', '_', $for );

	if ( in_array( $for, wc_get_order_types( 'admin-menu' ), true ) ) {
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$screen_id = 'woocommerce_page_wc-orders' . ( 'shop_order' === $for ? '' : '--' . $for );
		} else {
			$screen_id = $for;
		}
	}

	return $screen_id;
}

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed  $slug Slug for the new page.
 * @param string $option Option name to store the page's ID.
 * @param string $page_title (default: '') Title for the new page.
 * @param string $page_content (default: '') Content for the new page.
 * @param int    $post_parent (default: 0) Parent for the new page.
 * @param string $post_status (default: publish) The post status of the new page.
 * @return int page ID.
 */
function wc_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0, $post_status = 'publish' ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 ) {
		$page_object = get_post( $option_value );

		if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
			// Valid page is already in place.
			return $page_object->ID;
		}
	}

	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode).
		$shortcode        = str_replace( array( '<!-- wp:shortcode -->', '<!-- /wp:shortcode -->' ), '', $page_content );
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%" ) );
	} else {
		// Search for an existing page with the specified page slug.
		$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
	}

	/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
	$valid_page_found = apply_filters( 'woocommerce_create_page_id', $valid_page_found, $slug, $page_content );
	/* phpcs: enable */

	if ( $valid_page_found ) {
		if ( $option ) {
			update_option( $option, $valid_page_found );
		}
		return $valid_page_found;
	}

	// Search for a matching valid trashed page.
	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode).
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug.
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'          => $page_id,
			'post_status' => $post_status,
		);
		wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => $post_status,
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );

		/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
		do_action( 'woocommerce_page_created', $page_id, $page_data );
		/* phpcs: enable */
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}

/**
 * Output admin fields.
 *
 * Loops through the woocommerce options array and outputs each field.
 *
 * @param array $options Opens array to output.
 */
function woocommerce_admin_fields( $options ) {

	if ( ! class_exists( 'WC_Admin_Settings', false ) ) {
		include dirname( __FILE__ ) . '/class-wc-admin-settings.php';
	}

	WC_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 *
 * @param array $options Option fields to save.
 * @param array $data Passed data.
 */
function woocommerce_update_options( $options, $data = null ) {

	if ( ! class_exists( 'WC_Admin_Settings', false ) ) {
		include dirname( __FILE__ ) . '/class-wc-admin-settings.php';
	}

	WC_Admin_Settings::save_fields( $options, $data );
}

/**
 * Get a setting from the settings API.
 *
 * @param mixed $option_name Option name to save.
 * @param mixed $default Default value to save.
 * @return string
 */
function woocommerce_settings_get_option( $option_name, $default = '' ) {

	if ( ! class_exists( 'WC_Admin_Settings', false ) ) {
		include dirname( __FILE__ ) . '/class-wc-admin-settings.php';
	}

	return WC_Admin_Settings::get_option( $option_name, $default );
}

/**
 * Sees if line item stock has already reduced stock, and whether those values need adjusting e.g. after changing item qty.
 *
 * @since 3.6.0
 * @param WC_Order_Item $item Item object.
 * @param integer       $item_quantity Optional quantity to check against. Read from object if not passed.
 * @return boolean|array|WP_Error Array of changes or error object when stock is updated (@see wc_update_product_stock). False if nothing changes.
 */
function wc_maybe_adjust_line_item_product_stock( $item, $item_quantity = -1 ) {
	if ( 'line_item' !== $item->get_type() ) {
		return false;
	}

	/**
	 * Prevent adjust line item product stock.
	 *
	 * @since 3.7.1
	 * @param bool $prevent If should prevent.
	 * @param WC_Order_Item $item Item object.
	 * @param int           $item_quantity Optional quantity to check against.
	 */
	if ( apply_filters( 'woocommerce_prevent_adjust_line_item_product_stock', false, $item, $item_quantity ) ) {
		return false;
	}

	$product = $item->get_product();

	if ( ! $product || ! $product->managing_stock() ) {
		return false;
	}

	$item_quantity          = wc_stock_amount( $item_quantity >= 0 ? $item_quantity : $item->get_quantity() );
	$already_reduced_stock  = wc_stock_amount( $item->get_meta( '_reduced_stock', true ) );
	$restock_refunded_items = wc_stock_amount( $item->get_meta( '_restock_refunded_items', true ) );
	$order                  = $item->get_order();
	$refunded_item_quantity = $order->get_qty_refunded_for_item( $item->get_id() );

	$diff = $item_quantity - $restock_refunded_items - $already_reduced_stock;

	/*
	 * 0 as $item_quantity usually indicates we're deleting the order item.
	 * Let's restore back the reduced count.
	 */
	if ( 0 === $item_quantity ) {
		$diff = $already_reduced_stock * -1;
	}

	if ( $diff < 0 ) {
		$new_stock = wc_update_product_stock( $product, $diff * -1, 'increase' );
	} elseif ( $diff > 0 ) {
		$new_stock = wc_update_product_stock( $product, $diff, 'decrease' );
	} else {
		return false;
	}

	if ( is_wp_error( $new_stock ) ) {
		return $new_stock;
	}

	$item->update_meta_data( '_reduced_stock', $item_quantity - $restock_refunded_items );
	$item->save();

	if ( $item_quantity > 0 ) {
		// If stock was reduced, then we need to mark this on parent order object as well so that cancel logic works properly.
		$order_data_store = WC_Data_Store::load( 'order' );
		if ( $item->get_order_id() && ! $order_data_store->get_stock_reduced( $item->get_order_id() ) ) {
			$order_data_store->set_stock_reduced( $item->get_order_id(), true );
		}
	}

	return array(
		'from' => $new_stock + $diff,
		'to'   => $new_stock,
	);
}

/**
 * Save order items. Uses the CRUD.
 *
 * @since 2.2
 * @param int   $order_id Order ID.
 * @param array $items Order items to save.
 */
function wc_save_order_items( $order_id, $items ) {
	// Allow other plugins to check change in order items before they are saved.
	/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
	do_action( 'woocommerce_before_save_order_items', $order_id, $items );
	/* phpcs: enable */

	$qty_change_order_notes = array();
	$order                  = wc_get_order( $order_id );

	// Line items and fees.
	if ( isset( $items['order_item_id'] ) ) {
		$data_keys = array(
			'line_tax'             => array(),
			'line_subtotal_tax'    => array(),
			'order_item_name'      => null,
			'order_item_qty'       => null,
			'order_item_tax_class' => null,
			'line_total'           => null,
			'line_subtotal'        => null,
		);
		foreach ( $items['order_item_id'] as $item_id ) {
			$item = WC_Order_Factory::get_order_item( absint( $item_id ) );

			if ( ! $item ) {
				continue;
			}

			$item_data = array();

			foreach ( $data_keys as $key => $default ) {
				$item_data[ $key ] = isset( $items[ $key ][ $item_id ] ) ? wc_check_invalid_utf8( wp_unslash( $items[ $key ][ $item_id ] ) ) : $default;
			}

			if ( '0' === $item_data['order_item_qty'] ) {
				$changed_stock = wc_maybe_adjust_line_item_product_stock( $item, 0 );
				if ( $changed_stock && ! is_wp_error( $changed_stock ) ) {
					$qty_change_order_notes[] = $item->get_name() . ' &ndash; ' . $changed_stock['from'] . '&rarr;' . $changed_stock['to'];
				}
				$item->delete();
				continue;
			}

			$item->set_props(
				array(
					'name'      => $item_data['order_item_name'],
					'quantity'  => $item_data['order_item_qty'],
					'tax_class' => $item_data['order_item_tax_class'],
					'total'     => $item_data['line_total'],
					'subtotal'  => $item_data['line_subtotal'],
					'taxes'     => array(
						'total'    => $item_data['line_tax'],
						'subtotal' => $item_data['line_subtotal_tax'],
					),
				)
			);

			if ( 'fee' === $item->get_type() ) {
				$item->set_amount( $item_data['line_total'] );
			}

			if ( isset( $items['meta_key'][ $item_id ], $items['meta_value'][ $item_id ] ) ) {
				foreach ( $items['meta_key'][ $item_id ] as $meta_id => $meta_key ) {
					$meta_key   = substr( wp_unslash( $meta_key ), 0, 255 );
					$meta_value = isset( $items['meta_value'][ $item_id ][ $meta_id ] ) ? wp_unslash( $items['meta_value'][ $item_id ][ $meta_id ] ) : '';

					if ( '' === $meta_key && '' === $meta_value ) {
						if ( ! strstr( $meta_id, 'new-' ) ) {
							$item->delete_meta_data_by_mid( $meta_id );
						}
					} elseif ( strstr( $meta_id, 'new-' ) ) {
						$item->add_meta_data( $meta_key, $meta_value, false );
					} else {
						$item->update_meta_data( $meta_key, $meta_value, $meta_id );
					}
				}
			}

			// Allow other plugins to change item object before it is saved.
			/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
			do_action( 'woocommerce_before_save_order_item', $item );
			/* phpcs: enable */

			$item->save();

			if ( in_array( $order->get_status(), array( 'processing', 'completed', 'on-hold' ), true ) ) {
				$changed_stock = wc_maybe_adjust_line_item_product_stock( $item );
				if ( $changed_stock && ! is_wp_error( $changed_stock ) ) {
					$qty_change_order_notes[] = $item->get_name() . ' (' . $changed_stock['from'] . '&rarr;' . $changed_stock['to'] . ')';
				}
			}
		}
	}

	// Shipping Rows.
	if ( isset( $items['shipping_method_id'] ) ) {
		$data_keys = array(
			'shipping_method'       => null,
			'shipping_method_title' => null,
			'shipping_cost'         => 0,
			'shipping_taxes'        => array(),
		);

		foreach ( $items['shipping_method_id'] as $item_id ) {
			$item = WC_Order_Factory::get_order_item( absint( $item_id ) );

			if ( ! $item ) {
				continue;
			}

			$item_data = array();

			foreach ( $data_keys as $key => $default ) {
				$item_data[ $key ] = isset( $items[ $key ][ $item_id ] ) ? wc_clean( wp_unslash( $items[ $key ][ $item_id ] ) ) : $default;
			}

			$item->set_props(
				array(
					'method_id'    => $item_data['shipping_method'],
					'method_title' => $item_data['shipping_method_title'],
					'total'        => $item_data['shipping_cost'],
					'taxes'        => array(
						'total' => $item_data['shipping_taxes'],
					),
				)
			);

			if ( isset( $items['meta_key'][ $item_id ], $items['meta_value'][ $item_id ] ) ) {
				foreach ( $items['meta_key'][ $item_id ] as $meta_id => $meta_key ) {
					$meta_value = isset( $items['meta_value'][ $item_id ][ $meta_id ] ) ? wp_unslash( $items['meta_value'][ $item_id ][ $meta_id ] ) : '';

					if ( '' === $meta_key && '' === $meta_value ) {
						if ( ! strstr( $meta_id, 'new-' ) ) {
							$item->delete_meta_data_by_mid( $meta_id );
						}
					} elseif ( strstr( $meta_id, 'new-' ) ) {
						$item->add_meta_data( $meta_key, $meta_value, false );
					} else {
						$item->update_meta_data( $meta_key, $meta_value, $meta_id );
					}
				}
			}

			$item->save();
		}
	}

	$order = wc_get_order( $order_id );

	if ( ! empty( $qty_change_order_notes ) ) {
		/* translators: %s item name. */
		$order->add_order_note( sprintf( __( 'Adjusted stock: %s', 'woocommerce' ), implode( ', ', $qty_change_order_notes ) ), false, true );
	}

	$order->update_taxes();
	$order->calculate_totals( false );

	// Inform other plugins that the items have been saved.
	/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
	do_action( 'woocommerce_saved_order_items', $order_id, $items );
	/* phpcs: enable */
}

/**
 * Get HTML for some action buttons. Used in list tables.
 *
 * @since 3.3.0
 * @param array $actions Actions to output.
 * @return string
 */
function wc_render_action_buttons( $actions ) {
	$actions_html = '';

	foreach ( $actions as $action ) {
		if ( isset( $action['group'] ) ) {
			$actions_html .= '<div class="wc-action-button-group"><label>' . $action['group'] . '</label> <span class="wc-action-button-group__items">' . wc_render_action_buttons( $action['actions'] ) . '</span></div>';
		} elseif ( isset( $action['action'], $action['url'], $action['name'] ) ) {
			$actions_html .= sprintf( '<a class="button wc-action-button wc-action-button-%1$s %1$s" href="%2$s" aria-label="%3$s" title="%3$s">%4$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( isset( $action['title'] ) ? $action['title'] : $action['name'] ), esc_html( $action['name'] ) );
		}
	}

	return $actions_html;
}

/**
 * Shows a notice if variations are missing prices.
 *
 * @since 3.6.0
 * @param WC_Product $product_object Product object.
 */
function wc_render_invalid_variation_notice( $product_object ) {
	global $wpdb;

	// Give ability for extensions to hide this notice.
	/* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
	if ( ! apply_filters( 'woocommerce_show_invalid_variations_notice', true, $product_object ) ) {
		return;
	}
	/* phpcs: enable */

	$variation_ids = $product_object ? $product_object->get_children() : array();

	if ( empty( $variation_ids ) ) {
		return;
	}

	$variation_count = count( $variation_ids );

	// Check if a variation exists without pricing data.
	// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
	$valid_variation_count = $wpdb->get_var(
		"
		SELECT count(post_id) FROM {$wpdb->postmeta}
		WHERE post_id in (" . implode( ',', array_map( 'absint', $variation_ids ) ) . ")
		AND ( meta_key='_subscription_sign_up_fee' OR meta_key='_price' )
		AND meta_value >= 0
		AND meta_value != ''
		"
	);
	// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

	$invalid_variation_count = $variation_count - $valid_variation_count;

	if ( 0 < $invalid_variation_count ) {
		?>
		<div id="message" class="inline notice notice-warning woocommerce-message woocommerce-notice-invalid-variation">
			<p>
			<?php
			echo wp_kses_post(
				sprintf(
					/* Translators: %d variation count. */
					_n( '%d variation does not have a price.', '%d variations do not have prices.', $invalid_variation_count, 'woocommerce' ),
					$invalid_variation_count
				) . '&nbsp;' .
				__( 'Variations (and their attributes) that do not have prices will not be shown in your store.', 'woocommerce' )
			);
			?>
			</p>
			<div class="woocommerce-add-variation-price-container">
				<button type="button" class="button add_price_for_variations"><?php esc_html_e( 'Add price', 'woocommerce' ); ?></button>
			</div>
		</div>
		<?php
	}
}

/**
 * Get current admin page URL.
 *
 * Returns an empty string if it cannot generate a URL.
 *
 * @internal
 * @since 4.4.0
 * @return string
 */
function wc_get_current_admin_url() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );

	if ( ! $uri ) {
		return '';
	}

	return remove_query_arg( array( '_wpnonce', '_wc_notice_nonce', 'wc_db_update', 'wc_db_update_nonce', 'wc-hide-notice' ), admin_url( $uri ) );
}

/**
 * Get default product type options.
 *
 * @internal
 * @since 7.9.0
 * @return array
 */
function wc_get_default_product_type_options() {
	return array(
		'virtual'      => array(
			'id'            => '_virtual',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Virtual', 'woocommerce' ),
			'description'   => __( 'Virtual products are intangible and are not shipped.', 'woocommerce' ),
			'default'       => 'no',
		),
		'downloadable' => array(
			'id'            => '_downloadable',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Downloadable', 'woocommerce' ),
			'description'   => __( 'Downloadable products give access to a file upon purchase.', 'woocommerce' ),
			'default'       => 'no',
		),
	);
}
