<?php
/**
 * Connect existing WooCommerce pages to WooCommerce Admin.
 *
 * @package WooCommerce\Admin
 */

use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Admin\Features\Features;
use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Returns core WC pages to connect to WC-Admin.
 *
 * @return array
 */
function wc_admin_get_core_pages_to_connect() {
	$all_reports = WC_Admin_Reports::get_reports();
	$report_tabs = array();

	foreach ( $all_reports as $report_id => $report_data ) {
		$report_tabs[ $report_id ] = $report_data['title'];
	}

	return array(
		'wc-addons'   => array(
			'title' => __( 'Extensions', 'woocommerce' ),
			'tabs'  => array(),
		),
		'wc-reports'  => array(
			'title' => __( 'Reports', 'woocommerce' ),
			'tabs'  => $report_tabs,
		),
		'wc-settings' => array(
			'title' => __( 'Settings', 'woocommerce' ),
			'tabs'  => apply_filters( 'woocommerce_settings_tabs_array', array() ), // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
		),
		'wc-status'   => array(
			'title' => __( 'Status', 'woocommerce' ),
			// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
			'tabs'  => apply_filters(
				'woocommerce_admin_status_tabs',
				array(
					'status' => __( 'System status', 'woocommerce' ),
					'tools'  => __( 'Tools', 'woocommerce' ),
					'logs'   => __( 'Logs', 'woocommerce' ),
				)
			),
		),
	);
}

/**
 * Filter breadcrumbs for core pages that aren't explicitly connected.
 *
 * @param array $breadcrumbs Breadcrumb pieces.
 * @return array Filtered breadcrumb pieces.
 */
function wc_admin_filter_core_page_breadcrumbs( $breadcrumbs ) {
	$screen_id              = PageController::get_instance()->get_current_screen_id();
	$pages_to_connect       = wc_admin_get_core_pages_to_connect();
	$woocommerce_breadcrumb = array(
		'admin.php?page=wc-admin',
		__( 'WooCommerce', 'woocommerce' ),
	);

	foreach ( $pages_to_connect as $page_id => $page_data ) {
		if ( preg_match( "/^woocommerce_page_{$page_id}\-/", $screen_id ) ) {
			if ( empty( $page_data['tabs'] ) ) {
				$new_breadcrumbs = array(
					$woocommerce_breadcrumb,
					$page_data['title'],
				);
			} else {
				$new_breadcrumbs = array(
					$woocommerce_breadcrumb,
					array(
						add_query_arg( 'page', $page_id, 'admin.php' ),
						$page_data['title'],
					),
				);

				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET['tab'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$current_tab = wc_clean( wp_unslash( $_GET['tab'] ) );
				} else {
					$current_tab = key( $page_data['tabs'] );
				}

				$new_breadcrumbs[] = $page_data['tabs'][ $current_tab ];
			}

			return $new_breadcrumbs;
		}
	}

	return $breadcrumbs;
}

/**
 * Render the WC-Admin header bar on all WooCommerce core pages.
 *
 * @param bool $is_connected Whether the current page is connected.
 * @param bool $current_page The current page, if connected.
 * @return bool Whether to connect the page.
 */
function wc_admin_connect_core_pages( $is_connected, $current_page ) {
	if ( false === $is_connected && false === $current_page ) {
		$screen_id        = PageController::get_instance()->get_current_screen_id();
		$pages_to_connect = wc_admin_get_core_pages_to_connect();

		foreach ( $pages_to_connect as $page_id => $page_data ) {
			if ( preg_match( "/^woocommerce_page_{$page_id}\-/", $screen_id ) ) {
				add_filter( 'woocommerce_navigation_get_breadcrumbs', 'wc_admin_filter_core_page_breadcrumbs' );

				return true;
			}
		}
	}

	return $is_connected;
}

add_filter( 'woocommerce_navigation_is_connected_page', 'wc_admin_connect_core_pages', 10, 2 );

$posttype_list_base = 'edit.php';

// WooCommerce > Orders.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-orders',
		'screen_id' => 'edit-shop_order',
		'title'     => __( 'Orders', 'woocommerce' ),
		'path'      => add_query_arg( 'post_type', 'shop_order', $posttype_list_base ),
	)
);

// WooCommerce > Orders > Add New.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-add-order',
		'parent'    => 'woocommerce-orders',
		'screen_id' => 'shop_order-add',
		'title'     => __( 'Add New', 'woocommerce' ),
	)
);

// WooCommerce > Orders > Edit Order.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-edit-order',
		'parent'    => 'woocommerce-orders',
		'screen_id' => 'shop_order',
		'title'     => __( 'Edit Order', 'woocommerce' ),
	)
);

if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
	// WooCommerce > Orders (COT).
	wc_admin_connect_page(
		array(
			'id'        => 'woocommerce-custom-orders',
			'screen_id' => wc_get_page_screen_id( 'shop-order' ),
			'title'     => __( 'Orders', 'woocommerce' ),
			'path'      => 'admin.php?page=wc-orders',
		)
	);
}

// WooCommerce > Coupons.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-coupons',
		'parent'    => Features::is_enabled( 'coupons' ) ? 'woocommerce-marketing' : null,
		'screen_id' => 'edit-shop_coupon',
		'title'     => __( 'Coupons', 'woocommerce' ),
		'path'      => add_query_arg( 'post_type', 'shop_coupon', $posttype_list_base ),
	)
);

// WooCommerce > Coupons > Add New.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-add-coupon',
		'parent'    => 'woocommerce-coupons',
		'screen_id' => 'shop_coupon-add',
		'title'     => __( 'Add New', 'woocommerce' ),
	)
);

// WooCommerce > Coupons > Edit Coupon.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-edit-coupon',
		'parent'    => 'woocommerce-coupons',
		'screen_id' => 'shop_coupon',
		'title'     => __( 'Edit Coupon', 'woocommerce' ),
	)
);

// WooCommerce > Products.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-products',
		'screen_id' => 'edit-product',
		'title'     => __( 'Products', 'woocommerce' ),
		'path'      => add_query_arg( 'post_type', 'product', $posttype_list_base ),
	)
);

// WooCommerce > Products > Add New.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-add-product',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product-add',
		'title'     => __( 'Add New', 'woocommerce' ),
	)
);

// WooCommerce > Products > Edit Order.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-edit-product',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product',
		'title'     => __( 'Edit Product', 'woocommerce' ),
	)
);

// WooCommerce > Products > Import Products.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-import-products',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product_page_product_importer',
		'title'     => __( 'Import Products', 'woocommerce' ),
	)
);

// WooCommerce > Products > Export Products.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-export-products',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product_page_product_exporter',
		'title'     => __( 'Export Products', 'woocommerce' ),
	)
);

// WooCommerce > Products > Product categories.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-product-categories',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'edit-product_cat',
		'title'     => __( 'Product categories', 'woocommerce' ),
	)
);

// WooCommerce > Products > Edit category.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-product-edit-category',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product_cat',
		'title'     => __( 'Edit category', 'woocommerce' ),
	)
);

// WooCommerce > Products > Product tags.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-product-tags',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'edit-product_tag',
		'title'     => __( 'Product tags', 'woocommerce' ),
	)
);

// WooCommerce > Products > Edit tag.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-product-edit-tag',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product_tag',
		'title'     => __( 'Edit tag', 'woocommerce' ),
	)
);

// WooCommerce > Products > Attributes.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-product-attributes',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product_page_product_attributes',
		'title'     => __( 'Attributes', 'woocommerce' ),
	)
);

// WooCommerce > Products > Edit attribute.
wc_admin_connect_page(
	array(
		'id'        => 'woocommerce-product-edit-attribute',
		'parent'    => 'woocommerce-products',
		'screen_id' => 'product_page_product_attribute-edit',
		'title'     => __( 'Edit attribute', 'woocommerce' ),
	)
);
