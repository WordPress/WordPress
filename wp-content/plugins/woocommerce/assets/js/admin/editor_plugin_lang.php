<?php

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
    woocommerce:{
        insert: "' . esc_js( __( 'Insert Shortcode', 'woocommerce' ) ) . '",
        price_button: "' . esc_js( __( 'Product price/cart button', 'woocommerce' ) ) . '",
        product_by_sku: "' . esc_js( __( 'Product by SKU/ID', 'woocommerce' ) ) . '",
        products_by_sku: "' . esc_js( __( 'Products by SKU/ID', 'woocommerce' ) ) . '",
        product_categories: "' . esc_js( __( 'Product categories', 'woocommerce' ) ) . '",
        products_by_cat_slug: "' . esc_js( __( 'Products by category slug', 'woocommerce' ) ) . '",
        recent_products: "' . esc_js( __( 'Recent products', 'woocommerce' ) ) . '",
        featured_products: "' . esc_js( __( 'Featured products', 'woocommerce' ) ) . '",
        shop_messages: "' . esc_js( __( 'Shop Messages', 'woocommerce' ) ) . '",
        order_tracking: "' . esc_js( __( 'Order tracking', 'woocommerce' ) ) . '",
        my_account: "' . esc_js( __( 'My Account', 'woocommerce' ) ) . '",
        shop_messages_shortcode: "' . esc_js( apply_filters( "shop_messages_shortcode_tag", 'woocommerce_shop_messages' ) ) . '",
        order_tracking_shortcode: "' . esc_js( apply_filters( "woocommerce_order_tracking_shortcode_tag", 'woocommerce_order_tracking' ) ) . '"
    }
}})';