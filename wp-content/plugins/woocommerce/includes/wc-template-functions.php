<?php
/**
 * WooCommerce Template
 *
 * Functions for the templating system.
 *
 * @package  WooCommerce\Functions
 * @version  2.5.0
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
function wc_template_redirect() {
	global $wp_query, $wp;

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	// When default permalinks are enabled, redirect shop page to post type archive url.
	if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && wc_get_page_id( 'shop' ) === absint( $_GET['page_id'] ) && get_post_type_archive_link( 'product' ) ) {
		wp_safe_redirect( get_post_type_archive_link( 'product' ) );
		exit;
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	// When on the checkout with an empty cart, redirect to cart page.
	if ( is_page( wc_get_page_id( 'checkout' ) ) && wc_get_page_id( 'checkout' ) !== wc_get_page_id( 'cart' ) && WC()->cart->is_empty() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
		wc_add_notice( __( 'Checkout is not available whilst your cart is empty.', 'woocommerce' ), 'notice' );
		wp_safe_redirect( wc_get_cart_url() );
		exit;

	}

	// Logout.
	if ( isset( $wp->query_vars['customer-logout'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'customer-logout' ) ) {
		wp_safe_redirect( str_replace( '&amp;', '&', wp_logout_url( apply_filters( 'woocommerce_logout_default_redirect_url', wc_get_page_permalink( 'myaccount' ) ) ) ) );
		exit;
	}

	// Redirect to the correct logout endpoint.
	if ( isset( $wp->query_vars['customer-logout'] ) && 'true' === $wp->query_vars['customer-logout'] ) {
		wp_safe_redirect( esc_url_raw( wc_get_account_endpoint_url( 'customer-logout' ) ) );
		exit;
	}

	// Trigger 404 if trying to access an endpoint on wrong page.
	if ( is_wc_endpoint_url() && ! is_account_page() && ! is_checkout() && apply_filters( 'woocommerce_account_endpoint_page_not_found', true ) ) {
		$wp_query->set_404();
		status_header( 404 );
		include get_query_template( '404' );
		exit;
	}

	// Redirect to the product page if we have a single product.
	if ( is_search() && is_post_type_archive( 'product' ) && apply_filters( 'woocommerce_redirect_single_search_result', true ) && 1 === absint( $wp_query->found_posts ) ) {
		$product = wc_get_product( $wp_query->post );

		if ( $product && $product->is_visible() ) {
			wp_safe_redirect( get_permalink( $product->get_id() ), 302 );
			exit;
		}
	}

	// Ensure gateways and shipping methods are loaded early.
	if ( is_add_payment_method_page() || is_checkout() ) {
		// Buffer the checkout page.
		ob_start();

		// Ensure gateways and shipping methods are loaded early.
		WC()->payment_gateways();
		WC()->shipping();
	}
}
add_action( 'template_redirect', 'wc_template_redirect' );

/**
 * When loading sensitive checkout or account pages, send a HTTP header to limit rendering of pages to same origin iframes for security reasons.
 *
 * Can be disabled with: remove_action( 'template_redirect', 'wc_send_frame_options_header' );
 *
 * @since  2.3.10
 */
function wc_send_frame_options_header() {

	if ( ( is_checkout() || is_account_page() ) && ! is_customize_preview() ) {
		send_frame_options_header();
	}
}
add_action( 'template_redirect', 'wc_send_frame_options_header' );

/**
 * No index our endpoints.
 * Prevent indexing pages like order-received.
 *
 * @since 2.5.3
 */
function wc_prevent_endpoint_indexing() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.PHP.NoSilencedErrors.Discouraged
	if ( is_wc_endpoint_url() || isset( $_GET['download_file'] ) ) {
		@header( 'X-Robots-Tag: noindex' );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.PHP.NoSilencedErrors.Discouraged
}
add_action( 'template_redirect', 'wc_prevent_endpoint_indexing' );

/**
 * Remove adjacent_posts_rel_link_wp_head - pointless for products.
 *
 * @since 3.0.0
 */
function wc_prevent_adjacent_posts_rel_link_wp_head() {
	if ( is_singular( 'product' ) ) {
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	}
}
add_action( 'template_redirect', 'wc_prevent_adjacent_posts_rel_link_wp_head' );

/**
 * Show the gallery if JS is disabled.
 *
 * @since 3.0.6
 */
function wc_gallery_noscript() {
	?>
	<noscript><style>.woocommerce-product-gallery{ opacity: 1 !important; }</style></noscript>
	<?php
}
add_action( 'wp_head', 'wc_gallery_noscript' );

/**
 * When the_post is called, put product data into a global.
 *
 * @param mixed $post Post Object.
 * @return WC_Product
 */
function wc_setup_product_data( $post ) {
	unset( $GLOBALS['product'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'product', 'product_variation' ), true ) ) {
		return;
	}

	$GLOBALS['product'] = wc_get_product( $post );

	return $GLOBALS['product'];
}
add_action( 'the_post', 'wc_setup_product_data' );

/**
 * Sets up the woocommerce_loop global from the passed args or from the main query.
 *
 * @since 3.3.0
 * @param array $args Args to pass into the global.
 */
function wc_setup_loop( $args = array() ) {
	$default_args = array(
		'loop'         => 0,
		'columns'      => wc_get_default_products_per_row(),
		'name'         => '',
		'is_shortcode' => false,
		'is_paginated' => true,
		'is_search'    => false,
		'is_filtered'  => false,
		'total'        => 0,
		'total_pages'  => 0,
		'per_page'     => 0,
		'current_page' => 1,
	);

	// If this is a main WC query, use global args as defaults.
	if ( $GLOBALS['wp_query']->get( 'wc_query' ) ) {
		$default_args = array_merge(
			$default_args,
			array(
				'is_search'    => $GLOBALS['wp_query']->is_search(),
				'is_filtered'  => is_filtered(),
				'total'        => $GLOBALS['wp_query']->found_posts,
				'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
				'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
				'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
			)
		);
	}

	// Merge any existing values.
	if ( isset( $GLOBALS['woocommerce_loop'] ) ) {
		$default_args = array_merge( $default_args, $GLOBALS['woocommerce_loop'] );
	}

	$GLOBALS['woocommerce_loop'] = wp_parse_args( $args, $default_args );
}
add_action( 'woocommerce_before_shop_loop', 'wc_setup_loop' );

/**
 * Resets the woocommerce_loop global.
 *
 * @since 3.3.0
 */
function wc_reset_loop() {
	unset( $GLOBALS['woocommerce_loop'] );
}
add_action( 'woocommerce_after_shop_loop', 'woocommerce_reset_loop', 999 );

/**
 * Gets a property from the woocommerce_loop global.
 *
 * @since 3.3.0
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 * @return mixed
 */
function wc_get_loop_prop( $prop, $default = '' ) {
	wc_setup_loop(); // Ensure shop loop is setup.

	return isset( $GLOBALS['woocommerce_loop'], $GLOBALS['woocommerce_loop'][ $prop ] ) ? $GLOBALS['woocommerce_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the woocommerce_loop global.
 *
 * @since 3.3.0
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function wc_set_loop_prop( $prop, $value = '' ) {
	if ( ! isset( $GLOBALS['woocommerce_loop'] ) ) {
		wc_setup_loop();
	}
	$GLOBALS['woocommerce_loop'][ $prop ] = $value;
}

/**
 * Set the current visbility for a product in the woocommerce_loop global.
 *
 * @since 4.4.0
 * @param int  $product_id Product it to cache visbiility for.
 * @param bool $value The poduct visibility value to cache.
 */
function wc_set_loop_product_visibility( $product_id, $value ) {
	wc_set_loop_prop( "product_visibility_$product_id", $value );
}

/**
 * Gets the cached current visibility for a product from the woocommerce_loop global.
 *
 * @since 4.4.0
 * @param int $product_id Product id to get the cached visibility for.
 *
 * @return bool|null The cached product visibility, or null if on visibility has been cached for that product.
 */
function wc_get_loop_product_visibility( $product_id ) {
	return wc_get_loop_prop( "product_visibility_$product_id", null );
}

/**
 * Should the WooCommerce loop be displayed?
 *
 * This will return true if we have posts (products) or if we have subcats to display.
 *
 * @since 3.4.0
 * @return bool
 */
function woocommerce_product_loop() {
	return have_posts() || 'products' !== woocommerce_get_loop_display_mode();
}

/**
 * Output generator tag to aid debugging.
 *
 * @param string $gen Generator.
 * @param string $type Type.
 * @return string
 */
function wc_generator_tag( $gen, $type ) {
	$version = Constants::get_constant( 'WC_VERSION' );

	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="WooCommerce ' . esc_attr( $version ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="WooCommerce ' . esc_attr( $version ) . '" />';
			break;
	}
	return $gen;
}

/**
 * Add body classes for WC pages.
 *
 * @param  array $classes Body Classes.
 * @return array
 */
function wc_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_shop() ) {

		$classes[] = 'woocommerce-shop';

	}

	if ( is_woocommerce() ) {

		$classes[] = 'woocommerce';
		$classes[] = 'woocommerce-page';

	} elseif ( is_checkout() ) {

		$classes[] = 'woocommerce-checkout';
		$classes[] = 'woocommerce-page';

	} elseif ( is_cart() ) {

		$classes[] = 'woocommerce-cart';
		$classes[] = 'woocommerce-page';

	} elseif ( is_account_page() ) {

		$classes[] = 'woocommerce-account';
		$classes[] = 'woocommerce-page';

	}

	if ( is_store_notice_showing() ) {
		$classes[] = 'woocommerce-demo-store';
	}

	foreach ( WC()->query->get_query_vars() as $key => $value ) {
		if ( is_wc_endpoint_url( $key ) ) {
			$classes[] = 'woocommerce-' . sanitize_html_class( $key );
		}
	}

	if ( wc_block_theme_has_styles_for_element( 'button' ) ) {

		$classes[] = 'woocommerce-block-theme-has-button-styles';

	}

	$classes[] = 'woocommerce-no-js';

	add_action( 'wp_footer', 'wc_no_js' );

	return array_unique( $classes );
}

/**
 * NO JS handling.
 *
 * @since 3.4.0
 */
function wc_no_js() {
	?>
	<script type="text/javascript">
		(function () {
			var c = document.body.className;
			c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
			document.body.className = c;
		})();
	</script>
	<?php
}

/**
 * Display the classes for the product cat div.
 *
 * @since 2.4.0
 * @param string|array $class One or more classes to add to the class list.
 * @param object       $category object Optional.
 */
function wc_product_cat_class( $class = '', $category = null ) {
	// Separates classes with a single space, collates classes for post DIV.
	echo 'class="' . esc_attr( join( ' ', wc_get_product_cat_class( $class, $category ) ) ) . '"';
}

/**
 * Get the default columns setting - this is how many products will be shown per row in loops.
 *
 * @since 3.3.0
 * @return int
 */
function wc_get_default_products_per_row() {
	$columns      = get_option( 'woocommerce_catalog_columns', 4 );
	$product_grid = wc_get_theme_support( 'product_grid' );
	$min_columns  = isset( $product_grid['min_columns'] ) ? absint( $product_grid['min_columns'] ) : 0;
	$max_columns  = isset( $product_grid['max_columns'] ) ? absint( $product_grid['max_columns'] ) : 0;

	if ( $min_columns && $columns < $min_columns ) {
		$columns = $min_columns;
		update_option( 'woocommerce_catalog_columns', $columns );
	} elseif ( $max_columns && $columns > $max_columns ) {
		$columns = $max_columns;
		update_option( 'woocommerce_catalog_columns', $columns );
	}

	if ( has_filter( 'loop_shop_columns' ) ) { // Legacy filter handling.
		$columns = apply_filters( 'loop_shop_columns', $columns );
	}

	$columns = absint( $columns );

	return max( 1, $columns );
}

/**
 * Get the default rows setting - this is how many product rows will be shown in loops.
 *
 * @since 3.3.0
 * @return int
 */
function wc_get_default_product_rows_per_page() {
	$rows         = absint( get_option( 'woocommerce_catalog_rows', 4 ) );
	$product_grid = wc_get_theme_support( 'product_grid' );
	$min_rows     = isset( $product_grid['min_rows'] ) ? absint( $product_grid['min_rows'] ) : 0;
	$max_rows     = isset( $product_grid['max_rows'] ) ? absint( $product_grid['max_rows'] ) : 0;

	if ( $min_rows && $rows < $min_rows ) {
		$rows = $min_rows;
		update_option( 'woocommerce_catalog_rows', $rows );
	} elseif ( $max_rows && $rows > $max_rows ) {
		$rows = $max_rows;
		update_option( 'woocommerce_catalog_rows', $rows );
	}

	return $rows;
}

/**
 * Reset the product grid settings when a new theme is activated.
 *
 * @since 3.3.0
 */
function wc_reset_product_grid_settings() {
	$product_grid = wc_get_theme_support( 'product_grid' );

	if ( ! empty( $product_grid['default_rows'] ) ) {
		update_option( 'woocommerce_catalog_rows', absint( $product_grid['default_rows'] ) );
	}

	if ( ! empty( $product_grid['default_columns'] ) ) {
		update_option( 'woocommerce_catalog_columns', absint( $product_grid['default_columns'] ) );
	}

	wp_cache_flush(); // Flush any caches which could impact settings or templates.
}
add_action( 'after_switch_theme', 'wc_reset_product_grid_settings' );

/**
 * Get classname for woocommerce loops.
 *
 * @since 2.6.0
 * @return string
 */
function wc_get_loop_class() {
	$loop_index = wc_get_loop_prop( 'loop', 0 );
	$columns    = absint( max( 1, wc_get_loop_prop( 'columns', wc_get_default_products_per_row() ) ) );

	$loop_index ++;
	wc_set_loop_prop( 'loop', $loop_index );

	if ( 0 === ( $loop_index - 1 ) % $columns || 1 === $columns ) {
		return 'first';
	}

	if ( 0 === $loop_index % $columns ) {
		return 'last';
	}

	return '';
}


/**
 * Get the classes for the product cat div.
 *
 * @since 2.4.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param object       $category object Optional.
 *
 * @return array
 */
function wc_get_product_cat_class( $class = '', $category = null ) {
	$classes   = is_array( $class ) ? $class : array_map( 'trim', explode( ' ', $class ) );
	$classes[] = 'product-category';
	$classes[] = 'product';
	$classes[] = wc_get_loop_class();
	$classes   = apply_filters( 'product_cat_class', $classes, $class, $category );

	return array_unique( array_filter( $classes ) );
}

/**
 * Adds extra post classes for products via the WordPress post_class hook, if used.
 *
 * Note: For performance reasons we instead recommend using wc_product_class/wc_get_product_class instead.
 *
 * @since 2.1.0
 * @param array        $classes Current classes.
 * @param string|array $class Additional class.
 * @param int          $post_id Post ID.
 * @return array
 */
function wc_product_post_class( $classes, $class = '', $post_id = 0 ) {
	if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
		return $classes;
	}

	$product = wc_get_product( $post_id );

	if ( ! $product ) {
		return $classes;
	}

	$classes[] = 'product';
	$classes[] = wc_get_loop_class();
	$classes[] = $product->get_stock_status();

	if ( $product->is_on_sale() ) {
		$classes[] = 'sale';
	}
	if ( $product->is_featured() ) {
		$classes[] = 'featured';
	}
	if ( $product->is_downloadable() ) {
		$classes[] = 'downloadable';
	}
	if ( $product->is_virtual() ) {
		$classes[] = 'virtual';
	}
	if ( $product->is_sold_individually() ) {
		$classes[] = 'sold-individually';
	}
	if ( $product->is_taxable() ) {
		$classes[] = 'taxable';
	}
	if ( $product->is_shipping_taxable() ) {
		$classes[] = 'shipping-taxable';
	}
	if ( $product->is_purchasable() ) {
		$classes[] = 'purchasable';
	}
	if ( $product->get_type() ) {
		$classes[] = 'product-type-' . $product->get_type();
	}
	if ( $product->is_type( 'variable' ) && $product->get_default_attributes() ) {
		$classes[] = 'has-default-attributes';
	}

	$key = array_search( 'hentry', $classes, true );
	if ( false !== $key ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}

/**
 * Get product taxonomy HTML classes.
 *
 * @since 3.4.0
 * @param array  $term_ids Array of terms IDs or objects.
 * @param string $taxonomy Taxonomy.
 * @return array
 */
function wc_get_product_taxonomy_class( $term_ids, $taxonomy ) {
	$classes = array();

	foreach ( $term_ids as $term_id ) {
		$term = get_term( $term_id, $taxonomy );

		if ( empty( $term->slug ) ) {
			continue;
		}

		$term_class = sanitize_html_class( $term->slug, $term->term_id );
		if ( is_numeric( $term_class ) || ! trim( $term_class, '-' ) ) {
			$term_class = $term->term_id;
		}

		// 'post_tag' uses the 'tag' prefix for backward compatibility.
		if ( 'post_tag' === $taxonomy ) {
			$classes[] = 'tag-' . $term_class;
		} else {
			$classes[] = sanitize_html_class( $taxonomy . '-' . $term_class, $taxonomy . '-' . $term->term_id );
		}
	}

	return $classes;
}

/**
 * Retrieves the classes for the post div as an array.
 *
 * This method was modified from WordPress's get_post_class() to allow the removal of taxonomies
 * (for performance reasons). Previously wc_product_post_class was hooked into post_class. @since 3.6.0
 *
 * @since 3.4.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|WC_Product $product Product ID or product object.
 * @return array
 */
function wc_get_product_class( $class = '', $product = null ) {
	if ( is_null( $product ) && ! empty( $GLOBALS['product'] ) ) {
		// Product was null so pull from global.
		$product = $GLOBALS['product'];
	}

	if ( $product && ! is_a( $product, 'WC_Product' ) ) {
		// Make sure we have a valid product, or set to false.
		$product = wc_get_product( $product );
	}

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
	} else {
		$class = array();
	}

	$post_classes = array_map( 'esc_attr', $class );

	if ( ! $product ) {
		return $post_classes;
	}

	// Run through the post_class hook so 3rd parties using this previously can still append classes.
	// Note, to change classes you will need to use the newer woocommerce_post_class filter.
	// @internal This removes the wc_product_post_class filter so classes are not duplicated.
	$filtered = has_filter( 'post_class', 'wc_product_post_class' );

	if ( $filtered ) {
		remove_filter( 'post_class', 'wc_product_post_class', 20 );
	}

	$post_classes = apply_filters( 'post_class', $post_classes, $class, $product->get_id() );

	if ( $filtered ) {
		add_filter( 'post_class', 'wc_product_post_class', 20, 3 );
	}

	$classes = array_merge(
		$post_classes,
		array(
			'product',
			'type-product',
			'post-' . $product->get_id(),
			'status-' . $product->get_status(),
			wc_get_loop_class(),
			$product->get_stock_status(),
		),
		wc_get_product_taxonomy_class( $product->get_category_ids(), 'product_cat' ),
		wc_get_product_taxonomy_class( $product->get_tag_ids(), 'product_tag' )
	);

	if ( $product->get_image_id() ) {
		$classes[] = 'has-post-thumbnail';
	}
	if ( $product->get_post_password() ) {
		$classes[] = post_password_required( $product->get_id() ) ? 'post-password-required' : 'post-password-protected';
	}
	if ( $product->is_on_sale() ) {
		$classes[] = 'sale';
	}
	if ( $product->is_featured() ) {
		$classes[] = 'featured';
	}
	if ( $product->is_downloadable() ) {
		$classes[] = 'downloadable';
	}
	if ( $product->is_virtual() ) {
		$classes[] = 'virtual';
	}
	if ( $product->is_sold_individually() ) {
		$classes[] = 'sold-individually';
	}
	if ( $product->is_taxable() ) {
		$classes[] = 'taxable';
	}
	if ( $product->is_shipping_taxable() ) {
		$classes[] = 'shipping-taxable';
	}
	if ( $product->is_purchasable() ) {
		$classes[] = 'purchasable';
	}
	if ( $product->get_type() ) {
		$classes[] = 'product-type-' . $product->get_type();
	}
	if ( $product->is_type( 'variable' ) && $product->get_default_attributes() ) {
		$classes[] = 'has-default-attributes';
	}

	// Include attributes and any extra taxonomies only if enabled via the hook - this is a performance issue.
	if ( apply_filters( 'woocommerce_get_product_class_include_taxonomies', false ) ) {
		$taxonomies = get_taxonomies( array( 'public' => true ) );
		$type       = 'variation' === $product->get_type() ? 'product_variation' : 'product';
		foreach ( (array) $taxonomies as $taxonomy ) {
			if ( is_object_in_taxonomy( $type, $taxonomy ) && ! in_array( $taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
				$classes = array_merge( $classes, wc_get_product_taxonomy_class( (array) get_the_terms( $product->get_id(), $taxonomy ), $taxonomy ) );
			}
		}
	}

	/**
	 * WooCommerce Post Class filter.
	 *
	 * @since 3.6.2
	 * @param array      $classes Array of CSS classes.
	 * @param WC_Product $product Product object.
	 */
	$classes = apply_filters( 'woocommerce_post_class', $classes, $product );

	return array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
}

/**
 * Display the classes for the product div.
 *
 * @since 3.4.0
 * @param string|array           $class      One or more classes to add to the class list.
 * @param int|WP_Post|WC_Product $product_id Product ID or product object.
 */
function wc_product_class( $class = '', $product_id = null ) {
	echo 'class="' . esc_attr( implode( ' ', wc_get_product_class( $class, $product_id ) ) ) . '"';
}

/**
 * Outputs hidden form inputs for each query string variable.
 *
 * @since 3.0.0
 * @param string|array $values Name value pairs, or a URL to parse.
 * @param array        $exclude Keys to exclude.
 * @param string       $current_key Current key we are outputting.
 * @param bool         $return Whether to return.
 * @return string
 */
function wc_query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
	if ( is_null( $values ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$values = $_GET;
	} elseif ( is_string( $values ) ) {
		$url_parts = wp_parse_url( $values );
		$values    = array();

		if ( ! empty( $url_parts['query'] ) ) {
			// This is to preserve full-stops, pluses and spaces in the query string when ran through parse_str.
			$replace_chars = array(
				'.' => '{dot}',
				'+' => '{plus}',
			);

			$query_string = str_replace( array_keys( $replace_chars ), array_values( $replace_chars ), $url_parts['query'] );

			// Parse the string.
			parse_str( $query_string, $parsed_query_string );

			// Convert the full-stops, pluses and spaces back and add to values array.
			foreach ( $parsed_query_string as $key => $value ) {
				$new_key            = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $key );
				$new_value          = str_replace( array_values( $replace_chars ), array_keys( $replace_chars ), $value );
				$values[ $new_key ] = $new_value;
			}
		}
	}
	$html = '';

	foreach ( $values as $key => $value ) {
		if ( in_array( $key, $exclude, true ) ) {
			continue;
		}
		if ( $current_key ) {
			$key = $current_key . '[' . $key . ']';
		}
		if ( is_array( $value ) ) {
			$html .= wc_query_string_form_fields( $value, $exclude, $key, true );
		} else {
			$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
		}
	}

	if ( $return ) {
		return $html;
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get the terms and conditions page ID.
 *
 * @since 3.4.0
 * @return int
 */
function wc_terms_and_conditions_page_id() {
	$page_id = wc_get_page_id( 'terms' );
	return apply_filters( 'woocommerce_terms_and_conditions_page_id', 0 < $page_id ? absint( $page_id ) : 0 );
}

/**
 * Get the privacy policy page ID.
 *
 * @since 3.4.0
 * @return int
 */
function wc_privacy_policy_page_id() {
	$page_id = get_option( 'wp_page_for_privacy_policy', 0 );
	return apply_filters( 'woocommerce_privacy_policy_page_id', 0 < $page_id ? absint( $page_id ) : 0 );
}

/**
 * See if the checkbox is enabled or not based on the existance of the terms page and checkbox text.
 *
 * @since 3.4.0
 * @return bool
 */
function wc_terms_and_conditions_checkbox_enabled() {
	$page_id = wc_terms_and_conditions_page_id();
	$page    = $page_id ? get_post( $page_id ) : false;
	return $page && wc_get_terms_and_conditions_checkbox_text();
}

/**
 * Get the terms and conditions checkbox text, if set.
 *
 * @since 3.4.0
 * @return string
 */
function wc_get_terms_and_conditions_checkbox_text() {
	/* translators: %s terms and conditions page name and link */
	return trim( apply_filters( 'woocommerce_get_terms_and_conditions_checkbox_text', get_option( 'woocommerce_checkout_terms_and_conditions_checkbox_text', sprintf( __( 'I have read and agree to the website %s', 'woocommerce' ), '[terms]' ) ) ) );
}

/**
 * Get the privacy policy text, if set.
 *
 * @since 3.4.0
 * @param string $type Type of policy to load. Valid values include registration and checkout.
 * @return string
 */
function wc_get_privacy_policy_text( $type = '' ) {
	$text = '';

	switch ( $type ) {
		case 'checkout':
			/* translators: %s privacy policy page name and link */
			$text = get_option( 'woocommerce_checkout_privacy_policy_text', sprintf( __( 'Your personal data will be used to process your order, support your experience throughout this website, and for other purposes described in our %s.', 'woocommerce' ), '[privacy_policy]' ) );
			break;
		case 'registration':
			/* translators: %s privacy policy page name and link */
			$text = get_option( 'woocommerce_registration_privacy_policy_text', sprintf( __( 'Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our %s.', 'woocommerce' ), '[privacy_policy]' ) );
			break;
	}

	return trim( apply_filters( 'woocommerce_get_privacy_policy_text', $text, $type ) );
}

/**
 * Output t&c checkbox text.
 *
 * @since 3.4.0
 */
function wc_terms_and_conditions_checkbox_text() {
	$text = wc_get_terms_and_conditions_checkbox_text();

	if ( ! $text ) {
		return;
	}

	echo wp_kses_post( wc_replace_policy_page_link_placeholders( $text ) );
}

/**
 * Output t&c page's content (if set). The page can be set from checkout settings.
 *
 * @since 3.4.0
 */
function wc_terms_and_conditions_page_content() {
	$terms_page_id = wc_terms_and_conditions_page_id();

	if ( ! $terms_page_id ) {
		return;
	}

	$page = get_post( $terms_page_id );

	if ( $page && 'publish' === $page->post_status && $page->post_content && ! has_shortcode( $page->post_content, 'woocommerce_checkout' ) ) {
		echo '<div class="woocommerce-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">' . wc_format_content( wp_kses_post( $page->post_content ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Render privacy policy text on the checkout.
 *
 * @since 3.4.0
 */
function wc_checkout_privacy_policy_text() {
	echo '<div class="woocommerce-privacy-policy-text">';
	wc_privacy_policy_text( 'checkout' );
	echo '</div>';
}

/**
 * Render privacy policy text on the register forms.
 *
 * @since 3.4.0
 */
function wc_registration_privacy_policy_text() {
	echo '<div class="woocommerce-privacy-policy-text">';
	wc_privacy_policy_text( 'registration' );
	echo '</div>';
}

/**
 * Output privacy policy text. This is custom text which can be added via the customizer/privacy settings section.
 *
 * Loads the relevant policy for the current page unless a specific policy text is required.
 *
 * @since 3.4.0
 * @param string $type Type of policy to load. Valid values include registration and checkout.
 */
function wc_privacy_policy_text( $type = 'checkout' ) {
	if ( ! wc_privacy_policy_page_id() ) {
		return;
	}
	echo wp_kses_post( wpautop( wc_replace_policy_page_link_placeholders( wc_get_privacy_policy_text( $type ) ) ) );
}

/**
 * Replaces placeholders with links to WooCommerce policy pages.
 *
 * @since 3.4.0
 * @param string $text Text to find/replace within.
 * @return string
 */
function wc_replace_policy_page_link_placeholders( $text ) {
	$privacy_page_id = wc_privacy_policy_page_id();
	$terms_page_id   = wc_terms_and_conditions_page_id();
	$privacy_link    = $privacy_page_id ? '<a href="' . esc_url( get_permalink( $privacy_page_id ) ) . '" class="woocommerce-privacy-policy-link" target="_blank">' . __( 'privacy policy', 'woocommerce' ) . '</a>' : __( 'privacy policy', 'woocommerce' );
	$terms_link      = $terms_page_id ? '<a href="' . esc_url( get_permalink( $terms_page_id ) ) . '" class="woocommerce-terms-and-conditions-link" target="_blank">' . __( 'terms and conditions', 'woocommerce' ) . '</a>' : __( 'terms and conditions', 'woocommerce' );

	$find_replace = array(
		'[terms]'          => $terms_link,
		'[privacy_policy]' => $privacy_link,
	);

	return str_replace( array_keys( $find_replace ), array_values( $find_replace ), $text );
}

/**
 * Template pages
 */

if ( ! function_exists( 'woocommerce_content' ) ) {

	/**
	 * Output WooCommerce content.
	 *
	 * This function is only used in the optional 'woocommerce.php' template.
	 * which people can add to their themes to add basic woocommerce support.
	 * without hooks or modifying core templates.
	 */
	function woocommerce_content() {

		if ( is_singular( 'product' ) ) {

			while ( have_posts() ) :
				the_post();
				wc_get_template_part( 'content', 'single-product' );
			endwhile;

		} else {
			?>

			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

				<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

			<?php endif; ?>

			<?php do_action( 'woocommerce_archive_description' ); ?>

			<?php if ( woocommerce_product_loop() ) : ?>

				<?php do_action( 'woocommerce_before_shop_loop' ); ?>

				<?php woocommerce_product_loop_start(); ?>

				<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				<?php endif; ?>

				<?php woocommerce_product_loop_end(); ?>

				<?php do_action( 'woocommerce_after_shop_loop' ); ?>

				<?php
			else :
				do_action( 'woocommerce_no_products_found' );
			endif;
		}
	}
}

/**
 * Global
 */

if ( ! function_exists( 'woocommerce_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 */
	function woocommerce_output_content_wrapper() {
		wc_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'woocommerce_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 */
	function woocommerce_output_content_wrapper_end() {
		wc_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'woocommerce_get_sidebar' ) ) {

	/**
	 * Get the shop sidebar template.
	 */
	function woocommerce_get_sidebar() {
		wc_get_template( 'global/sidebar.php' );
	}
}

if ( ! function_exists( 'woocommerce_demo_store' ) ) {

	/**
	 * Adds a demo store banner to the site if enabled.
	 */
	function woocommerce_demo_store() {
		if ( ! is_store_notice_showing() ) {
			return;
		}

		$notice = get_option( 'woocommerce_demo_store_notice' );

		if ( empty( $notice ) ) {
			$notice = __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'woocommerce' );
		}

		$notice_id = md5( $notice );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'woocommerce_demo_store', '<p class="woocommerce-store-notice demo_store" data-notice-id="' . esc_attr( $notice_id ) . '" style="display:none;">' . wp_kses_post( $notice ) . ' <a href="#" class="woocommerce-store-notice__dismiss-link">' . esc_html__( 'Dismiss', 'woocommerce' ) . '</a></p>', $notice );
	}
}

/**
 * Loop
 */

if ( ! function_exists( 'woocommerce_page_title' ) ) {

	/**
	 * Page Title function.
	 *
	 * @param  bool $echo Should echo title.
	 * @return string
	 */
	function woocommerce_page_title( $echo = true ) {

		if ( is_search() ) {
			/* translators: %s: search query */
			$page_title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'woocommerce' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				/* translators: %s: page number */
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'woocommerce' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_tax() ) {

			$page_title = single_term_title( '', false );

		} else {

			$shop_page_id = wc_get_page_id( 'shop' );
			$page_title   = get_the_title( $shop_page_id );

		}

		$page_title = apply_filters( 'woocommerce_page_title', $page_title );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $page_title;
		} else {
			return $page_title;
		}
	}
}

if ( ! function_exists( 'woocommerce_product_loop_start' ) ) {

	/**
	 * Output the start of a product loop. By default this is a UL.
	 *
	 * @param bool $echo Should echo?.
	 * @return string
	 */
	function woocommerce_product_loop_start( $echo = true ) {
		ob_start();

		wc_set_loop_prop( 'loop', 0 );

		wc_get_template( 'loop/loop-start.php' );

		$loop_start = apply_filters( 'woocommerce_product_loop_start', ob_get_clean() );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $loop_start;
		} else {
			return $loop_start;
		}
	}
}

if ( ! function_exists( 'woocommerce_product_loop_end' ) ) {

	/**
	 * Output the end of a product loop. By default this is a UL.
	 *
	 * @param bool $echo Should echo?.
	 * @return string
	 */
	function woocommerce_product_loop_end( $echo = true ) {
		ob_start();

		wc_get_template( 'loop/loop-end.php' );

		$loop_end = apply_filters( 'woocommerce_product_loop_end', ob_get_clean() );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $loop_end;
		} else {
			return $loop_end;
		}
	}
}
if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	function woocommerce_template_loop_product_title() {
		echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
if ( ! function_exists( 'woocommerce_template_loop_category_title' ) ) {

	/**
	 * Show the subcategory title in the product loop.
	 *
	 * @param object $category Category object.
	 */
	function woocommerce_template_loop_category_title( $category ) {
		?>
		<h2 class="woocommerce-loop-category__title">
			<?php
			echo esc_html( $category->name );

			if ( $category->count > 0 ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category );
			}
			?>
		</h2>
		<?php
	}
}

if ( ! function_exists( 'woocommerce_template_loop_product_link_open' ) ) {
	/**
	 * Insert the opening anchor tag for products in the loop.
	 */
	function woocommerce_template_loop_product_link_open() {
		global $product;

		$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );

		echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
	}
}

if ( ! function_exists( 'woocommerce_template_loop_product_link_close' ) ) {
	/**
	 * Insert the closing anchor tag for products in the loop.
	 */
	function woocommerce_template_loop_product_link_close() {
		echo '</a>';
	}
}

if ( ! function_exists( 'woocommerce_template_loop_category_link_open' ) ) {
	/**
	 * Insert the opening anchor tag for categories in the loop.
	 *
	 * @param int|object|string $category Category ID, Object or String.
	 */
	function woocommerce_template_loop_category_link_open( $category ) {
		$category_term = get_term( $category, 'product_cat' );
		$category_name = ( ! $category_term || is_wp_error( $category_term ) ) ? '' : $category_term->name;
		/* translators: %s: Category name */
		echo '<a aria-label="' . sprintf( esc_attr__( 'Visit product category %1$s', 'woocommerce' ), esc_attr( $category_name ) ) . '" href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">';
	}
}

if ( ! function_exists( 'woocommerce_template_loop_category_link_close' ) ) {
	/**
	 * Insert the closing anchor tag for categories in the loop.
	 */
	function woocommerce_template_loop_category_link_close() {
		echo '</a>';
	}
}

if ( ! function_exists( 'woocommerce_taxonomy_archive_description' ) ) {
	/**
	 * Show an archive description on taxonomy archives.
	 */
	function woocommerce_taxonomy_archive_description() {
		if ( is_product_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
			$term = get_queried_object();

			if ( $term ) {
				/**
				 * Filters the archive's raw description on taxonomy archives.
				 *
				 * @since 6.7.0
				 *
				 * @param string  $term_description Raw description text.
				 * @param WP_Term $term             Term object for this taxonomy archive.
				 */
				$term_description = apply_filters( 'woocommerce_taxonomy_archive_description_raw', $term->description, $term );

				if ( ! empty( $term_description ) ) {
					echo '<div class="term-description">' . wc_format_content( wp_kses_post( $term_description ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}
}

if ( ! function_exists( 'woocommerce_product_archive_description' ) ) {

	/**
	 * Show a shop page description on product archives.
	 */
	function woocommerce_product_archive_description() {
		// Don't display the description on search results page.
		if ( is_search() ) {
			return;
		}

		if ( is_post_type_archive( 'product' ) && in_array( absint( get_query_var( 'paged' ) ), array( 0, 1 ), true ) ) {
			$shop_page = get_post( wc_get_page_id( 'shop' ) );
			if ( $shop_page ) {

				$allowed_html = wp_kses_allowed_html( 'post' );

				// This is needed for the search product block to work.
				$allowed_html = array_merge(
					$allowed_html,
					array(
						'form'   => array(
							'action'         => true,
							'accept'         => true,
							'accept-charset' => true,
							'enctype'        => true,
							'method'         => true,
							'name'           => true,
							'target'         => true,
						),

						'input'  => array(
							'type'        => true,
							'id'          => true,
							'class'       => true,
							'placeholder' => true,
							'name'        => true,
							'value'       => true,
						),

						'button' => array(
							'type'  => true,
							'class' => true,
							'label' => true,
						),

						'svg'    => array(
							'hidden'    => true,
							'role'      => true,
							'focusable' => true,
							'xmlns'     => true,
							'width'     => true,
							'height'    => true,
							'viewbox'   => true,
						),
						'path'   => array(
							'd' => true,
						),
					)
				);

				$description = wc_format_content( wp_kses( $shop_page->post_content, $allowed_html ) );
				if ( $description ) {
					echo '<div class="page-description">' . $description . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}
}

if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {

	/**
	 * Get the add to cart template for the loop.
	 *
	 * @param array $args Arguments.
	 */
	function woocommerce_template_loop_add_to_cart( $args = array() ) {
		global $product;

		if ( $product ) {
			$defaults = array(
				'quantity'   => 1,
				'class'      => implode(
					' ',
					array_filter(
						array(
							'button',
							wc_wp_theme_get_element_class_name( 'button' ), // escaped in the template.
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
						)
					)
				),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => $product->add_to_cart_description(),
					'rel'              => 'nofollow',
				),
			);

			$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

			if ( isset( $args['attributes']['aria-label'] ) ) {
				$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
			}

			wc_get_template( 'loop/add-to-cart.php', $args );
		}
	}
}

if ( ! function_exists( 'woocommerce_template_loop_product_thumbnail' ) ) {

	/**
	 * Get the product thumbnail for the loop.
	 */
	function woocommerce_template_loop_product_thumbnail() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo woocommerce_get_product_thumbnail();
	}
}
if ( ! function_exists( 'woocommerce_template_loop_price' ) ) {

	/**
	 * Get the product price for the loop.
	 */
	function woocommerce_template_loop_price() {
		wc_get_template( 'loop/price.php' );
	}
}
if ( ! function_exists( 'woocommerce_template_loop_rating' ) ) {

	/**
	 * Display the average rating in the loop.
	 */
	function woocommerce_template_loop_rating() {
		wc_get_template( 'loop/rating.php' );
	}
}
if ( ! function_exists( 'woocommerce_show_product_loop_sale_flash' ) ) {

	/**
	 * Get the sale flash for the loop.
	 */
	function woocommerce_show_product_loop_sale_flash() {
		wc_get_template( 'loop/sale-flash.php' );
	}
}

if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {

	/**
	 * Get the product thumbnail, or the placeholder if not set.
	 *
	 * @param string $size (default: 'woocommerce_thumbnail').
	 * @param  array $attr Image attributes.
	 * @param  bool  $placeholder True to return $placeholder if no image is found, or false to return an empty string.
	 * @return string
	 */
	function woocommerce_get_product_thumbnail( $size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true ) {
		global $product;

		if ( ! is_array( $attr ) ) {
			$attr = array();
		}

		if ( ! is_bool( $placeholder ) ) {
			$placeholder = true;
		}

		$image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

		return $product ? $product->get_image( $image_size, $attr, $placeholder ) : '';
	}
}

if ( ! function_exists( 'woocommerce_result_count' ) ) {

	/**
	 * Output the result count text (Showing x - x of x results).
	 */
	function woocommerce_result_count() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}
		$args = array(
			'total'    => wc_get_loop_prop( 'total' ),
			'per_page' => wc_get_loop_prop( 'per_page' ),
			'current'  => wc_get_loop_prop( 'current_page' ),
		);

		wc_get_template( 'loop/result-count.php', $args );
	}
}

if ( ! function_exists( 'woocommerce_catalog_ordering' ) ) {

	/**
	 * Output the product sorting options.
	 */
	function woocommerce_catalog_ordering() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}
		$show_default_orderby    = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$catalog_orderby_options = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => __( 'Default sorting', 'woocommerce' ),
				'popularity' => __( 'Sort by popularity', 'woocommerce' ),
				'rating'     => __( 'Sort by average rating', 'woocommerce' ),
				'date'       => __( 'Sort by latest', 'woocommerce' ),
				'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
				'price-desc' => __( 'Sort by price: high to low', 'woocommerce' ),
			)
		);

		$default_orderby = wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', '' ) );
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( wc_get_loop_prop( 'is_search' ) ) {
			$catalog_orderby_options = array_merge( array( 'relevance' => __( 'Relevance', 'woocommerce' ) ), $catalog_orderby_options );

			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! wc_review_ratings_enabled() ) {
			unset( $catalog_orderby_options['rating'] );
		}

		if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
			$orderby = current( array_keys( $catalog_orderby_options ) );
		}

		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby,
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_pagination' ) ) {

	/**
	 * Output the pagination.
	 */
	function woocommerce_pagination() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
			return;
		}

		$args = array(
			'total'   => wc_get_loop_prop( 'total_pages' ),
			'current' => wc_get_loop_prop( 'current_page' ),
			'base'    => esc_url_raw( add_query_arg( 'product-page', '%#%', false ) ),
			'format'  => '?product-page=%#%',
		);

		if ( ! wc_get_loop_prop( 'is_shortcode' ) ) {
			$args['format'] = '';
			$args['base']   = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
		}

		wc_get_template( 'loop/pagination.php', $args );
	}
}

/**
 * Single Product
 */

if ( ! function_exists( 'woocommerce_show_product_images' ) ) {

	/**
	 * Output the product image before the single product summary.
	 */
	function woocommerce_show_product_images() {
		wc_get_template( 'single-product/product-image.php' );
	}
}
if ( ! function_exists( 'woocommerce_show_product_thumbnails' ) ) {

	/**
	 * Output the product thumbnails.
	 */
	function woocommerce_show_product_thumbnails() {
		wc_get_template( 'single-product/product-thumbnails.php' );
	}
}

/**
 * Get HTML for a gallery image.
 *
 * Hooks: woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
 *
 * @since 3.3.2
 * @param int  $attachment_id Attachment ID.
 * @param bool $main_image Is this the main image or a thumbnail?.
 * @return string
 */
function wc_get_gallery_image_html( $attachment_id, $main_image = false ) {
	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	$alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
	$image             = wp_get_attachment_image(
		$attachment_id,
		$image_size,
		false,
		apply_filters(
			'woocommerce_gallery_image_html_attachment_image_params',
			array(
				'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
				'data-src'                => esc_url( $full_src[0] ),
				'data-large_image'        => esc_url( $full_src[0] ),
				'data-large_image_width'  => esc_attr( $full_src[1] ),
				'data-large_image_height' => esc_attr( $full_src[2] ),
				'class'                   => esc_attr( $main_image ? 'wp-post-image' : '' ),
			),
			$attachment_id,
			$image_size,
			$main_image
		)
	);

	return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" data-thumb-alt="' . esc_attr( $alt_text ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
}

if ( ! function_exists( 'woocommerce_output_product_data_tabs' ) ) {

	/**
	 * Output the product tabs.
	 */
	function woocommerce_output_product_data_tabs() {
		wc_get_template( 'single-product/tabs/tabs.php' );
	}
}
if ( ! function_exists( 'woocommerce_template_single_title' ) ) {

	/**
	 * Output the product title.
	 */
	function woocommerce_template_single_title() {
		wc_get_template( 'single-product/title.php' );
	}
}
if ( ! function_exists( 'woocommerce_template_single_rating' ) ) {

	/**
	 * Output the product rating.
	 */
	function woocommerce_template_single_rating() {
		if ( post_type_supports( 'product', 'comments' ) ) {
			wc_get_template( 'single-product/rating.php' );
		}
	}
}
if ( ! function_exists( 'woocommerce_template_single_price' ) ) {

	/**
	 * Output the product price.
	 */
	function woocommerce_template_single_price() {
		wc_get_template( 'single-product/price.php' );
	}
}
if ( ! function_exists( 'woocommerce_template_single_excerpt' ) ) {

	/**
	 * Output the product short description (excerpt).
	 */
	function woocommerce_template_single_excerpt() {
		wc_get_template( 'single-product/short-description.php' );
	}
}
if ( ! function_exists( 'woocommerce_template_single_meta' ) ) {

	/**
	 * Output the product meta.
	 */
	function woocommerce_template_single_meta() {
		wc_get_template( 'single-product/meta.php' );
	}
}
if ( ! function_exists( 'woocommerce_template_single_sharing' ) ) {

	/**
	 * Output the product sharing.
	 */
	function woocommerce_template_single_sharing() {
		wc_get_template( 'single-product/share.php' );
	}
}
if ( ! function_exists( 'woocommerce_show_product_sale_flash' ) ) {

	/**
	 * Output the product sale flash.
	 */
	function woocommerce_show_product_sale_flash() {
		wc_get_template( 'single-product/sale-flash.php' );
	}
}

if ( ! function_exists( 'woocommerce_template_single_add_to_cart' ) ) {

	/**
	 * Trigger the single product add to cart action.
	 */
	function woocommerce_template_single_add_to_cart() {
		global $product;
		do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
	}
}
if ( ! function_exists( 'woocommerce_simple_add_to_cart' ) ) {

	/**
	 * Output the simple product add to cart area.
	 */
	function woocommerce_simple_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/simple.php' );
	}
}
if ( ! function_exists( 'woocommerce_grouped_add_to_cart' ) ) {

	/**
	 * Output the grouped product add to cart area.
	 */
	function woocommerce_grouped_add_to_cart() {
		global $product;

		$products = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

		if ( $products ) {
			wc_get_template(
				'single-product/add-to-cart/grouped.php',
				array(
					'grouped_product'    => $product,
					'grouped_products'   => $products,
					'quantites_required' => false,
				)
			);
		}
	}
}
if ( ! function_exists( 'woocommerce_variable_add_to_cart' ) ) {

	/**
	 * Output the variable product add to cart area.
	 */
	function woocommerce_variable_add_to_cart() {
		global $product;

		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		// Get Available variations?
		$get_variations = count( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );

		// Load the template.
		wc_get_template(
			'single-product/add-to-cart/variable.php',
			array(
				'available_variations' => $get_variations ? $product->get_available_variations() : false,
				'attributes'           => $product->get_variation_attributes(),
				'selected_attributes'  => $product->get_default_attributes(),
			)
		);
	}
}
if ( ! function_exists( 'woocommerce_external_add_to_cart' ) ) {

	/**
	 * Output the external product add to cart area.
	 */
	function woocommerce_external_add_to_cart() {
		global $product;

		if ( ! $product->add_to_cart_url() ) {
			return;
		}

		wc_get_template(
			'single-product/add-to-cart/external.php',
			array(
				'product_url' => $product->add_to_cart_url(),
				'button_text' => $product->single_add_to_cart_text(),
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_quantity_input' ) ) {

	/**
	 * Output the quantity input for add to cart forms.
	 *
	 * @param  array           $args Args for the input.
	 * @param  WC_Product|null $product Product.
	 * @param  boolean         $echo Whether to return or echo|string.
	 *
	 * @return string
	 */
	function woocommerce_quantity_input( $args = array(), $product = null, $echo = true ) {
		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'input_id'     => uniqid( 'quantity_' ),
			'input_name'   => 'quantity',
			'input_value'  => '1',
			'classes'      => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
			'max_value'    => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
			'min_value'    => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
			'step'         => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
			'pattern'      => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
			'inputmode'    => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
			'product_name' => $product ? $product->get_title() : '',
			'placeholder'  => apply_filters( 'woocommerce_quantity_input_placeholder', '', $product ),
			// When autocomplete is enabled in firefox, it will overwrite actual value with what user entered last. So we default to off.
			// See @link https://github.com/woocommerce/woocommerce/issues/30733.
			'autocomplete' => apply_filters( 'woocommerce_quantity_input_autocomplete', 'off', $product ),
			'readonly'     => false,
		);

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Apply sanity to min/max args - min cannot be lower than 0.
		$args['min_value'] = max( $args['min_value'], 0 );
		$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

		// Max cannot be lower than min if defined.
		if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
			$args['max_value'] = $args['min_value'];
		}

		/**
		 * The input type attribute will generally be 'number' unless the quantity cannot be changed, in which case
		 * it will be set to 'hidden'. An exception is made for non-hidden readonly inputs: in this case we set the
		 * type to 'text' (this prevents most browsers from rendering increment/decrement arrows, which are useless
		 * and/or confusing in this context).
		 */
		$type = $args['min_value'] > 0 && $args['min_value'] === $args['max_value'] ? 'hidden' : 'number';
		$type = $args['readonly'] && 'hidden' !== $type ? 'text' : $type;

		/**
		 * Controls the quantity input's type attribute.
		 *
		 * @since 7.4.0
		 *
		 * @param string $type A valid input type attribute value, usually 'number' or 'hidden'.
		 */
		$args['type'] = apply_filters( 'woocommerce_quantity_input_type', $type );

		ob_start();
		wc_get_template( 'global/quantity-input.php', $args );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'woocommerce_product_description_tab' ) ) {

	/**
	 * Output the description tab content.
	 */
	function woocommerce_product_description_tab() {
		wc_get_template( 'single-product/tabs/description.php' );
	}
}
if ( ! function_exists( 'woocommerce_product_additional_information_tab' ) ) {

	/**
	 * Output the attributes tab content.
	 */
	function woocommerce_product_additional_information_tab() {
		wc_get_template( 'single-product/tabs/additional-information.php' );
	}
}
if ( ! function_exists( 'woocommerce_default_product_tabs' ) ) {

	/**
	 * Add default product tabs to product pages.
	 *
	 * @param array $tabs Array of tabs.
	 * @return array
	 */
	function woocommerce_default_product_tabs( $tabs = array() ) {
		global $product, $post;

		// Description tab - shows product content.
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'woocommerce' ),
				'priority' => 10,
				'callback' => 'woocommerce_product_description_tab',
			);
		}

		// Additional information tab - shows attributes.
		if ( $product && ( $product->has_attributes() || apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() ) ) ) {
			$tabs['additional_information'] = array(
				'title'    => __( 'Additional information', 'woocommerce' ),
				'priority' => 20,
				'callback' => 'woocommerce_product_additional_information_tab',
			);
		}

		// Reviews tab - shows comments.
		if ( comments_open() ) {
			$tabs['reviews'] = array(
				/* translators: %s: reviews count */
				'title'    => sprintf( __( 'Reviews (%d)', 'woocommerce' ), $product->get_review_count() ),
				'priority' => 30,
				'callback' => 'comments_template',
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'woocommerce_sort_product_tabs' ) ) {

	/**
	 * Sort tabs by priority.
	 *
	 * @param array $tabs Array of tabs.
	 * @return array
	 */
	function woocommerce_sort_product_tabs( $tabs = array() ) {

		// Make sure the $tabs parameter is an array.
		if ( ! is_array( $tabs ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Function woocommerce_sort_product_tabs() expects an array as the first parameter. Defaulting to empty array.' );
			$tabs = array();
		}

		// Re-order tabs by priority.
		if ( ! function_exists( '_sort_priority_callback' ) ) {
			/**
			 * Sort Priority Callback Function
			 *
			 * @param array $a Comparison A.
			 * @param array $b Comparison B.
			 * @return bool
			 */
			function _sort_priority_callback( $a, $b ) {
				if ( ! isset( $a['priority'], $b['priority'] ) || $a['priority'] === $b['priority'] ) {
					return 0;
				}
				return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
			}
		}

		uasort( $tabs, '_sort_priority_callback' );

		return $tabs;
	}
}

if ( ! function_exists( 'woocommerce_comments' ) ) {

	/**
	 * Output the Review comments template.
	 *
	 * @param WP_Comment $comment Comment object.
	 * @param array      $args Arguments.
	 * @param int        $depth Depth.
	 */
	function woocommerce_comments( $comment, $args, $depth ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['comment'] = $comment;
		wc_get_template(
			'single-product/review.php',
			array(
				'comment' => $comment,
				'args'    => $args,
				'depth'   => $depth,
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_review_display_gravatar' ) ) {
	/**
	 * Display the review authors gravatar
	 *
	 * @param array $comment WP_Comment.
	 * @return void
	 */
	function woocommerce_review_display_gravatar( $comment ) {
		echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '' );
	}
}

if ( ! function_exists( 'woocommerce_review_display_rating' ) ) {
	/**
	 * Display the reviewers star rating
	 *
	 * @return void
	 */
	function woocommerce_review_display_rating() {
		if ( post_type_supports( 'product', 'comments' ) ) {
			wc_get_template( 'single-product/review-rating.php' );
		}
	}
}

if ( ! function_exists( 'woocommerce_review_display_meta' ) ) {
	/**
	 * Display the review authors meta (name, verified owner, review date)
	 *
	 * @return void
	 */
	function woocommerce_review_display_meta() {
		wc_get_template( 'single-product/review-meta.php' );
	}
}

if ( ! function_exists( 'woocommerce_review_display_comment_text' ) ) {

	/**
	 * Display the review content.
	 */
	function woocommerce_review_display_comment_text() {
		echo '<div class="description">';
		comment_text();
		echo '</div>';
	}
}

if ( ! function_exists( 'woocommerce_output_related_products' ) ) {

	/**
	 * Output the related products.
	 */
	function woocommerce_output_related_products() {

		$args = array(
			'posts_per_page' => 4,
			'columns'        => 4,
			'orderby'        => 'rand', // @codingStandardsIgnoreLine.
		);

		woocommerce_related_products( apply_filters( 'woocommerce_output_related_products_args', $args ) );
	}
}

if ( ! function_exists( 'woocommerce_related_products' ) ) {

	/**
	 * Output the related products.
	 *
	 * @param array $args Provided arguments.
	 */
	function woocommerce_related_products( $args = array() ) {
		global $product;

		if ( ! $product ) {
			return;
		}

		$defaults = array(
			'posts_per_page' => 2,
			'columns'        => 2,
			'orderby'        => 'rand', // @codingStandardsIgnoreLine.
			'order'          => 'desc',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get visible related products then sort them at random.
		$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

		// Handle orderby.
		$args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

		// Set global loop values.
		wc_set_loop_prop( 'name', 'related' );
		wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_related_products_columns', $args['columns'] ) );

		wc_get_template( 'single-product/related.php', $args );
	}
}

if ( ! function_exists( 'woocommerce_upsell_display' ) ) {

	/**
	 * Output product up sells.
	 *
	 * @param int    $limit (default: -1).
	 * @param int    $columns (default: 4).
	 * @param string $orderby Supported values - rand, title, ID, date, modified, menu_order, price.
	 * @param string $order Sort direction.
	 */
	function woocommerce_upsell_display( $limit = '-1', $columns = 4, $orderby = 'rand', $order = 'desc' ) {
		global $product;

		if ( ! $product ) {
			return;
		}

		// Handle the legacy filter which controlled posts per page etc.
		$args = apply_filters(
			'woocommerce_upsell_display_args',
			array(
				'posts_per_page' => $limit,
				'orderby'        => $orderby,
				'order'          => $order,
				'columns'        => $columns,
			)
		);
		wc_set_loop_prop( 'name', 'up-sells' );
		wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_upsells_columns', isset( $args['columns'] ) ? $args['columns'] : $columns ) );

		$orderby = apply_filters( 'woocommerce_upsells_orderby', isset( $args['orderby'] ) ? $args['orderby'] : $orderby );
		$order   = apply_filters( 'woocommerce_upsells_order', isset( $args['order'] ) ? $args['order'] : $order );
		$limit   = apply_filters( 'woocommerce_upsells_total', isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : $limit );

		// Get visible upsells then sort them at random, then limit result set.
		$upsells = wc_products_array_orderby( array_filter( array_map( 'wc_get_product', $product->get_upsell_ids() ), 'wc_products_array_filter_visible' ), $orderby, $order );
		$upsells = $limit > 0 ? array_slice( $upsells, 0, $limit ) : $upsells;

		wc_get_template(
			'single-product/up-sells.php',
			array(
				'upsells'        => $upsells,

				// Not used now, but used in previous version of up-sells.php.
				'posts_per_page' => $limit,
				'orderby'        => $orderby,
				'columns'        => $columns,
			)
		);
	}
}

/** Cart */

if ( ! function_exists( 'woocommerce_shipping_calculator' ) ) {

	/**
	 * Output the cart shipping calculator.
	 *
	 * @param string $button_text Text for the shipping calculation toggle.
	 */
	function woocommerce_shipping_calculator( $button_text = '' ) {
		if ( 'no' === get_option( 'woocommerce_enable_shipping_calc' ) || ! WC()->cart->needs_shipping() ) {
			return;
		}
		wp_enqueue_script( 'wc-country-select' );
		wc_get_template(
			'cart/shipping-calculator.php',
			array(
				'button_text' => $button_text,
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_cart_totals' ) ) {

	/**
	 * Output the cart totals.
	 */
	function woocommerce_cart_totals() {
		if ( is_checkout() ) {
			return;
		}
		wc_get_template( 'cart/cart-totals.php' );
	}
}

if ( ! function_exists( 'woocommerce_cross_sell_display' ) ) {

	/**
	 * Output the cart cross-sells.
	 *
	 * @param  int    $limit (default: 2).
	 * @param  int    $columns (default: 2).
	 * @param  string $orderby (default: 'rand').
	 * @param  string $order (default: 'desc').
	 */
	function woocommerce_cross_sell_display( $limit = 2, $columns = 2, $orderby = 'rand', $order = 'desc' ) {
		if ( is_checkout() ) {
			return;
		}
		// Get visible cross sells then sort them at random.
		$cross_sells = array_filter( array_map( 'wc_get_product', WC()->cart->get_cross_sells() ), 'wc_products_array_filter_visible' );

		wc_set_loop_prop( 'name', 'cross-sells' );
		wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_cross_sells_columns', $columns ) );

		// Handle orderby and limit results.
		$orderby     = apply_filters( 'woocommerce_cross_sells_orderby', $orderby );
		$order       = apply_filters( 'woocommerce_cross_sells_order', $order );
		$cross_sells = wc_products_array_orderby( $cross_sells, $orderby, $order );
		$limit       = apply_filters( 'woocommerce_cross_sells_total', $limit );
		$cross_sells = $limit > 0 ? array_slice( $cross_sells, 0, $limit ) : $cross_sells;

		wc_get_template(
			'cart/cross-sells.php',
			array(
				'cross_sells'    => $cross_sells,

				// Not used now, but used in previous version of up-sells.php.
				'posts_per_page' => $limit,
				'orderby'        => $orderby,
				'columns'        => $columns,
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_button_proceed_to_checkout' ) ) {

	/**
	 * Output the proceed to checkout button.
	 */
	function woocommerce_button_proceed_to_checkout() {
		wc_get_template( 'cart/proceed-to-checkout-button.php' );
	}
}

if ( ! function_exists( 'woocommerce_widget_shopping_cart_button_view_cart' ) ) {

	/**
	 * Output the view cart button.
	 */
	function woocommerce_widget_shopping_cart_button_view_cart() {
		$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
		echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="button wc-forward' . esc_attr( $wp_button_class ) . '">' . esc_html__( 'View cart', 'woocommerce' ) . '</a>';
	}
}

if ( ! function_exists( 'woocommerce_widget_shopping_cart_proceed_to_checkout' ) ) {

	/**
	 * Output the proceed to checkout button.
	 */
	function woocommerce_widget_shopping_cart_proceed_to_checkout() {
		$wp_button_class = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
		echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="button checkout wc-forward' . esc_attr( $wp_button_class ) . '">' . esc_html__( 'Checkout', 'woocommerce' ) . '</a>';
	}
}

if ( ! function_exists( 'woocommerce_widget_shopping_cart_subtotal' ) ) {
	/**
	 * Output to view cart subtotal.
	 *
	 * @since 3.7.0
	 */
	function woocommerce_widget_shopping_cart_subtotal() {
		echo '<strong>' . esc_html__( 'Subtotal:', 'woocommerce' ) . '</strong> ' . WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/** Mini-Cart */

if ( ! function_exists( 'woocommerce_mini_cart' ) ) {

	/**
	 * Output the Mini-cart - used by cart widget.
	 *
	 * @param array $args Arguments.
	 */
	function woocommerce_mini_cart( $args = array() ) {

		$defaults = array(
			'list_class' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		wc_get_template( 'cart/mini-cart.php', $args );
	}
}

/** Login */

if ( ! function_exists( 'woocommerce_login_form' ) ) {

	/**
	 * Output the WooCommerce Login Form.
	 *
	 * @param array $args Arguments.
	 */
	function woocommerce_login_form( $args = array() ) {

		$defaults = array(
			'message'  => '',
			'redirect' => '',
			'hidden'   => false,
		);

		$args = wp_parse_args( $args, $defaults );

		wc_get_template( 'global/form-login.php', $args );
	}
}

if ( ! function_exists( 'woocommerce_checkout_login_form' ) ) {

	/**
	 * Output the WooCommerce Checkout Login Form.
	 */
	function woocommerce_checkout_login_form() {
		wc_get_template(
			'checkout/form-login.php',
			array(
				'checkout' => WC()->checkout(),
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_breadcrumb' ) ) {

	/**
	 * Output the WooCommerce Breadcrumb.
	 *
	 * @param array $args Arguments.
	 */
	function woocommerce_breadcrumb( $args = array() ) {
		$args = wp_parse_args(
			$args,
			apply_filters(
				'woocommerce_breadcrumb_defaults',
				array(
					'delimiter'   => '&nbsp;&#47;&nbsp;',
					'wrap_before' => '<nav class="woocommerce-breadcrumb">',
					'wrap_after'  => '</nav>',
					'before'      => '',
					'after'       => '',
					'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
				)
			)
		);

		$breadcrumbs = new WC_Breadcrumb();

		if ( ! empty( $args['home'] ) ) {
			$breadcrumbs->add_crumb( $args['home'], apply_filters( 'woocommerce_breadcrumb_home_url', home_url() ) );
		}

		$args['breadcrumb'] = $breadcrumbs->generate();

		/**
		 * WooCommerce Breadcrumb hook
		 *
		 * @hooked WC_Structured_Data::generate_breadcrumblist_data() - 10
		 */
		do_action( 'woocommerce_breadcrumb', $breadcrumbs, $args );

		wc_get_template( 'global/breadcrumb.php', $args );
	}
}

if ( ! function_exists( 'woocommerce_order_review' ) ) {

	/**
	 * Output the Order review table for the checkout.
	 *
	 * @param bool $deprecated Deprecated param.
	 */
	function woocommerce_order_review( $deprecated = false ) {
		wc_get_template(
			'checkout/review-order.php',
			array(
				'checkout' => WC()->checkout(),
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_checkout_payment' ) ) {

	/**
	 * Output the Payment Methods on the checkout.
	 */
	function woocommerce_checkout_payment() {
		if ( WC()->cart->needs_payment() ) {
			$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
			WC()->payment_gateways()->set_current_gateway( $available_gateways );
		} else {
			$available_gateways = array();
		}

		wc_get_template(
			'checkout/payment.php',
			array(
				'checkout'           => WC()->checkout(),
				'available_gateways' => $available_gateways,
				'order_button_text'  => apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) ),
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_checkout_coupon_form' ) ) {

	/**
	 * Output the Coupon form for the checkout.
	 */
	function woocommerce_checkout_coupon_form() {
		if ( is_user_logged_in() || WC()->checkout()->is_registration_enabled() || ! WC()->checkout()->is_registration_required() ) {
			wc_get_template(
				'checkout/form-coupon.php',
				array(
					'checkout' => WC()->checkout(),
				)
			);
		}
	}
}

if ( ! function_exists( 'woocommerce_products_will_display' ) ) {

	/**
	 * Check if we will be showing products or not (and not sub-categories only).
	 *
	 * @return bool
	 */
	function woocommerce_products_will_display() {
		$display_type = woocommerce_get_loop_display_mode();

		return 0 < wc_get_loop_prop( 'total', 0 ) && 'subcategories' !== $display_type;
	}
}

if ( ! function_exists( 'woocommerce_get_loop_display_mode' ) ) {

	/**
	 * See what is going to display in the loop.
	 *
	 * @since 3.3.0
	 * @return string Either products, subcategories, or both, based on current page.
	 */
	function woocommerce_get_loop_display_mode() {
		// Only return products when filtering things.
		if ( wc_get_loop_prop( 'is_search' ) || wc_get_loop_prop( 'is_filtered' ) ) {
			return 'products';
		}

		$parent_id    = 0;
		$display_type = '';

		if ( is_shop() ) {
			$display_type = get_option( 'woocommerce_shop_page_display', '' );
		} elseif ( is_product_category() ) {
			$parent_id    = get_queried_object_id();
			$display_type = get_term_meta( $parent_id, 'display_type', true );
			$display_type = '' === $display_type ? get_option( 'woocommerce_category_archive_display', '' ) : $display_type;
		}

		if ( ( ! is_shop() || 'subcategories' !== $display_type ) && 1 < wc_get_loop_prop( 'current_page' ) ) {
			return 'products';
		}

		// Ensure valid value.
		if ( '' === $display_type || ! in_array( $display_type, array( 'products', 'subcategories', 'both' ), true ) ) {
			$display_type = 'products';
		}

		// If we're showing categories, ensure we actually have something to show.
		if ( in_array( $display_type, array( 'subcategories', 'both' ), true ) ) {
			$subcategories = woocommerce_get_product_subcategories( $parent_id );

			if ( empty( $subcategories ) ) {
				$display_type = 'products';
			}
		}

		return $display_type;
	}
}

if ( ! function_exists( 'woocommerce_maybe_show_product_subcategories' ) ) {

	/**
	 * Maybe display categories before, or instead of, a product loop.
	 *
	 * @since 3.3.0
	 * @param string $loop_html HTML.
	 * @return string
	 */
	function woocommerce_maybe_show_product_subcategories( $loop_html = '' ) {
		if ( wc_get_loop_prop( 'is_shortcode' ) && ! WC_Template_Loader::in_content_filter() ) {
			return $loop_html;
		}

		$display_type = woocommerce_get_loop_display_mode();

		// If displaying categories, append to the loop.
		if ( 'subcategories' === $display_type || 'both' === $display_type ) {
			ob_start();
			woocommerce_output_product_categories(
				array(
					'parent_id' => is_product_category() ? get_queried_object_id() : 0,
				)
			);
			$loop_html .= ob_get_clean();

			if ( 'subcategories' === $display_type ) {
				wc_set_loop_prop( 'total', 0 );

				// This removes pagination and products from display for themes not using wc_get_loop_prop in their product loops.  @todo Remove in future major version.
				global $wp_query;

				if ( $wp_query->is_main_query() ) {
					$wp_query->post_count    = 0;
					$wp_query->max_num_pages = 0;
				}
			}
		}

		return $loop_html;
	}
}

if ( ! function_exists( 'woocommerce_product_subcategories' ) ) {
	/**
	 * This is a legacy function which used to check if we needed to display subcats and then output them. It was called by templates.
	 *
	 * From 3.3 onwards this is all handled via hooks and the woocommerce_maybe_show_product_subcategories function.
	 *
	 * Since some templates have not updated compatibility, to avoid showing incorrect categories this function has been deprecated and will
	 * return nothing. Replace usage with woocommerce_output_product_categories to render the category list manually.
	 *
	 * This is a legacy function which also checks if things should display.
	 * Themes no longer need to call these functions. It's all done via hooks.
	 *
	 * @deprecated 3.3.1 @todo Add a notice in a future version.
	 * @param array $args Arguments.
	 * @return null|boolean
	 */
	function woocommerce_product_subcategories( $args = array() ) {
		$defaults = array(
			'before'        => '',
			'after'         => '',
			'force_display' => false,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['force_display'] ) {
			// We can still render if display is forced.
			woocommerce_output_product_categories(
				array(
					'before'    => $args['before'],
					'after'     => $args['after'],
					'parent_id' => is_product_category() ? get_queried_object_id() : 0,
				)
			);
			return true;
		} else {
			// Output nothing. woocommerce_maybe_show_product_subcategories will handle the output of cats.
			$display_type = woocommerce_get_loop_display_mode();

			if ( 'subcategories' === $display_type ) {
				// This removes pagination and products from display for themes not using wc_get_loop_prop in their product loops. @todo Remove in future major version.
				global $wp_query;

				if ( $wp_query->is_main_query() ) {
					$wp_query->post_count    = 0;
					$wp_query->max_num_pages = 0;
				}
			}

			return 'subcategories' === $display_type || 'both' === $display_type;
		}
	}
}

if ( ! function_exists( 'woocommerce_output_product_categories' ) ) {
	/**
	 * Display product sub categories as thumbnails.
	 *
	 * This is a replacement for woocommerce_product_subcategories which also does some logic
	 * based on the loop. This function however just outputs when called.
	 *
	 * @since 3.3.1
	 * @param array $args Arguments.
	 * @return boolean
	 */
	function woocommerce_output_product_categories( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'before'    => apply_filters( 'woocommerce_before_output_product_categories', '' ),
				'after'     => apply_filters( 'woocommerce_after_output_product_categories', '' ),
				'parent_id' => 0,
			)
		);

		$product_categories = woocommerce_get_product_subcategories( $args['parent_id'] );

		if ( ! $product_categories ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before'];

		foreach ( $product_categories as $category ) {
			wc_get_template(
				'content-product_cat.php',
				array(
					'category' => $category,
				)
			);
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['after'];

		return true;
	}
}

if ( ! function_exists( 'woocommerce_get_product_subcategories' ) ) {
	/**
	 * Get (and cache) product subcategories.
	 *
	 * @param int $parent_id Get subcategories of this ID.
	 * @return array
	 */
	function woocommerce_get_product_subcategories( $parent_id = 0 ) {
		$parent_id          = absint( $parent_id );
		$cache_key          = apply_filters( 'woocommerce_get_product_subcategories_cache_key', 'product-category-hierarchy-' . $parent_id, $parent_id );
		$product_categories = $cache_key ? wp_cache_get( $cache_key, 'product_cat' ) : false;

		if ( false === $product_categories ) {
			// NOTE: using child_of instead of parent - this is not ideal but due to a WP bug ( https://core.trac.wordpress.org/ticket/15626 ) pad_counts won't work.
			$product_categories = get_categories(
				apply_filters(
					'woocommerce_product_subcategories_args',
					array(
						'parent'       => $parent_id,
						'hide_empty'   => 0,
						'hierarchical' => 1,
						'taxonomy'     => 'product_cat',
						'pad_counts'   => 1,
					)
				)
			);

			if ( $cache_key ) {
				wp_cache_set( $cache_key, $product_categories, 'product_cat' );
			}
		}

		if ( apply_filters( 'woocommerce_product_subcategories_hide_empty', true ) ) {
			$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );
		}

		return $product_categories;
	}
}

if ( ! function_exists( 'woocommerce_subcategory_thumbnail' ) ) {

	/**
	 * Show subcategory thumbnails.
	 *
	 * @param mixed $category Category.
	 */
	function woocommerce_subcategory_thumbnail( $category ) {
		$small_thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' );
		$dimensions           = wc_get_image_size( $small_thumbnail_size );
		$thumbnail_id         = get_term_meta( $category->term_id, 'thumbnail_id', true );

		if ( $thumbnail_id ) {
			$image        = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
			$image        = $image[0];
			$image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $small_thumbnail_size ) : false;
			$image_sizes  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $small_thumbnail_size ) : false;
		} else {
			$image        = wc_placeholder_img_src();
			$image_srcset = false;
			$image_sizes  = false;
		}

		if ( $image ) {
			// Prevent esc_url from breaking spaces in urls for image embeds.
			// Ref: https://core.trac.wordpress.org/ticket/23605.
			$image = str_replace( ' ', '%20', $image );

			// Add responsive image markup if available.
			if ( $image_srcset && $image_sizes ) {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" />';
			} else {
				echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
			}
		}
	}
}

if ( ! function_exists( 'woocommerce_order_details_table' ) ) {

	/**
	 * Displays order details in a table.
	 *
	 * @param mixed $order_id Order ID.
	 */
	function woocommerce_order_details_table( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		wc_get_template(
			'order/order-details.php',
			array(
				'order_id' => $order_id,
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_order_downloads_table' ) ) {

	/**
	 * Displays order downloads in a table.
	 *
	 * @since 3.2.0
	 * @param array $downloads Downloads.
	 */
	function woocommerce_order_downloads_table( $downloads ) {
		if ( ! $downloads ) {
			return;
		}
		wc_get_template(
			'order/order-downloads.php',
			array(
				'downloads' => $downloads,
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_order_again_button' ) ) {

	/**
	 * Display an 'order again' button on the view order page.
	 *
	 * @param object $order Order.
	 */
	function woocommerce_order_again_button( $order ) {
		if ( ! $order || ! $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) ) ) || ! is_user_logged_in() ) {
			return;
		}

		wc_get_template(
			'order/order-again.php',
			array(
				'order'           => $order,
				'order_again_url' => wp_nonce_url( add_query_arg( 'order_again', $order->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' ),
			)
		);
	}
}

/** Forms */

if ( ! function_exists( 'woocommerce_form_field' ) ) {

	/**
	 * Outputs a checkout/address form field.
	 *
	 * @param string $key Key.
	 * @param mixed  $args Arguments.
	 * @param string $value (default: null).
	 * @return string
	 */
	function woocommerce_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
			'maxlength'         => false,
			'required'          => false,
			'autocomplete'      => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
			'autofocus'         => '',
			'priority'          => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );

		if ( is_string( $args['class'] ) ) {
			$args['class'] = array( $args['class'] );
		}

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
		} else {
			$required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
		}

		if ( is_string( $args['label_class'] ) ) {
			$args['label_class'] = array( $args['label_class'] );
		}

		if ( is_null( $value ) ) {
			$value = $args['default'];
		}

		// Custom attribute handling.
		$custom_attributes         = array();
		$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

		if ( $args['maxlength'] ) {
			$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
		}

		if ( ! empty( $args['autocomplete'] ) ) {
			$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
		}

		if ( true === $args['autofocus'] ) {
			$args['custom_attributes']['autofocus'] = 'autofocus';
		}

		if ( $args['description'] ) {
			$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
		}

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( ! empty( $args['validate'] ) ) {
			foreach ( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$field           = '';
		$label_id        = $args['id'];
		$sort            = $args['priority'] ? $args['priority'] : '';
		$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

		switch ( $args['type'] ) {
			case 'country':
				$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

				if ( 1 === count( $countries ) ) {

					$field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

					$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';

				} else {
					$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

					$field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_attr__( 'Select a country / region&hellip;', 'woocommerce' ) ) . '" ' . $data_label . '><option value="">' . esc_html__( 'Select a country / region&hellip;', 'woocommerce' ) . '</option>';

					foreach ( $countries as $ckey => $cvalue ) {
						$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
					}

					$field .= '</select>';

					$field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country / region', 'woocommerce' ) . '">' . esc_html__( 'Update country / region', 'woocommerce' ) . '</button></noscript>';

				}

				break;
			case 'state':
				/* Get country this state field is representing */
				$for_country = isset( $args['country'] ) ? $args['country'] : WC()->checkout->get_value( 'billing_state' === $key ? 'billing_country' : 'shipping_country' );
				$states      = WC()->countries->get_states( $for_country );

				if ( is_array( $states ) && empty( $states ) ) {

					$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

					$field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" readonly="readonly" data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '"/>';

				} elseif ( ! is_null( $for_country ) && is_array( $states ) ) {
					$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_html__( 'Select an option&hellip;', 'woocommerce' ) ) . '"  data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . $data_label . '>
						<option value="">' . esc_html__( 'Select an option&hellip;', 'woocommerce' ) . '</option>';

					foreach ( $states as $ckey => $cvalue ) {
						$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
					}

					$field .= '</select>';

				} else {

					$field .= '<input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' data-input-classes="' . esc_attr( implode( ' ', $args['input_class'] ) ) . '"/>';

				}

				break;
			case 'textarea':
				$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

				break;
			case 'checkbox':
				$field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
						<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';

				break;
			case 'text':
			case 'password':
			case 'datetime':
			case 'datetime-local':
			case 'date':
			case 'month':
			case 'time':
			case 'week':
			case 'number':
			case 'email':
			case 'url':
			case 'tel':
				$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				break;
			case 'hidden':
				$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="input-hidden ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				break;
			case 'select':
				$field   = '';
				$options = '';

				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						if ( '' === $option_key ) {
							// If we have a blank option, select2 needs a placeholder.
							if ( empty( $args['placeholder'] ) ) {
								$args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
							}
							$custom_attributes[] = 'data-allow_clear="true"';
						}
						$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_html( $option_text ) . '</option>';
					}

					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
							' . $options . '
						</select>';
				}

				break;
			case 'radio':
				$label_id .= '_' . current( array_keys( $args['options'] ) );

				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						$field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
						$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . esc_html( $option_text ) . '</label>';
					}
				}

				break;
		}

		if ( ! empty( $field ) ) {
			$field_html = '';

			if ( $args['label'] && 'checkbox' !== $args['type'] ) {
				$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
			}

			$field_html .= '<span class="woocommerce-input-wrapper">' . $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
			}

			$field_html .= '</span>';

			$container_class = esc_attr( implode( ' ', $args['class'] ) );
			$container_id    = esc_attr( $args['id'] ) . '_field';
			$field           = sprintf( $field_container, $container_class, $container_id, $field_html );
		}

		/**
		 * Filter by type.
		 */
		$field = apply_filters( 'woocommerce_form_field_' . $args['type'], $field, $key, $args, $value );

		/**
		 * General filter on form fields.
		 *
		 * @since 3.4.0
		 */
		$field = apply_filters( 'woocommerce_form_field', $field, $key, $args, $value );

		if ( $args['return'] ) {
			return $field;
		} else {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $field;
		}
	}
}

if ( ! function_exists( 'get_product_search_form' ) ) {

	/**
	 * Display product search form.
	 *
	 * Will first attempt to locate the product-searchform.php file in either the child or.
	 * the parent, then load it. If it doesn't exist, then the default search form.
	 * will be displayed.
	 *
	 * The default searchform uses html5.
	 *
	 * @param bool $echo (default: true).
	 * @return string
	 */
	function get_product_search_form( $echo = true ) {
		global $product_search_form_index;

		ob_start();

		if ( empty( $product_search_form_index ) ) {
			$product_search_form_index = 0;
		}

		do_action( 'pre_get_product_search_form' );

		wc_get_template(
			'product-searchform.php',
			array(
				'index' => $product_search_form_index++,
			)
		);

		$form = apply_filters( 'get_product_search_form', ob_get_clean() );

		if ( ! $echo ) {
			return $form;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $form;
	}
}

if ( ! function_exists( 'woocommerce_output_auth_header' ) ) {

	/**
	 * Output the Auth header.
	 */
	function woocommerce_output_auth_header() {
		wc_get_template( 'auth/header.php' );
	}
}

if ( ! function_exists( 'woocommerce_output_auth_footer' ) ) {

	/**
	 * Output the Auth footer.
	 */
	function woocommerce_output_auth_footer() {
		wc_get_template( 'auth/footer.php' );
	}
}

if ( ! function_exists( 'woocommerce_single_variation' ) ) {

	/**
	 * Output placeholders for the single variation.
	 */
	function woocommerce_single_variation() {
		echo '<div class="woocommerce-variation single_variation"></div>';
	}
}

if ( ! function_exists( 'woocommerce_single_variation_add_to_cart_button' ) ) {

	/**
	 * Output the add to cart button for variations.
	 */
	function woocommerce_single_variation_add_to_cart_button() {
		wc_get_template( 'single-product/add-to-cart/variation-add-to-cart-button.php' );
	}
}

if ( ! function_exists( 'wc_dropdown_variation_attribute_options' ) ) {

	/**
	 * Output a list of variation attributes for use in the cart forms.
	 *
	 * @param array $args Arguments.
	 * @since 2.4.0
	 */
	function wc_dropdown_variation_attribute_options( $args = array() ) {
		$args = wp_parse_args(
			apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
			array(
				'options'          => false,
				'attribute'        => false,
				'product'          => false,
				'selected'         => false,
				'required'         => false,
				'name'             => '',
				'id'               => '',
				'class'            => '',
				'show_option_none' => __( 'Choose an option', 'woocommerce' ),
			)
		);

		// Get selected value.
		if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
			$selected_key = 'attribute_' . sanitize_title( $args['attribute'] );
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] );
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$required              = (bool) $args['required'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		$html  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '"' . ( $required ? ' required' : '' ) . '>';
		$html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms(
					$product->get_id(),
					$attribute,
					array(
						'fields' => 'all',
					)
				);

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options, true ) ) {
						$html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
					}
				}
			} else {
				foreach ( $options as $option ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
				}
			}
		}

		$html .= '</select>';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo apply_filters( 'woocommerce_dropdown_variation_attribute_options_html', $html, $args );
	}
}

if ( ! function_exists( 'woocommerce_account_content' ) ) {

	/**
	 * My Account content output.
	 */
	function woocommerce_account_content() {
		global $wp;

		if ( ! empty( $wp->query_vars ) ) {
			foreach ( $wp->query_vars as $key => $value ) {
				// Ignore pagename param.
				if ( 'pagename' === $key ) {
					continue;
				}

				if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) {
					do_action( 'woocommerce_account_' . $key . '_endpoint', $value );
					return;
				}
			}
		}

		// No endpoint found? Default to dashboard.
		wc_get_template(
			'myaccount/dashboard.php',
			array(
				'current_user' => get_user_by( 'id', get_current_user_id() ),
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_account_navigation' ) ) {

	/**
	 * My Account navigation template.
	 */
	function woocommerce_account_navigation() {
		wc_get_template( 'myaccount/navigation.php' );
	}
}

if ( ! function_exists( 'woocommerce_account_orders' ) ) {

	/**
	 * My Account > Orders template.
	 *
	 * @param int $current_page Current page number.
	 */
	function woocommerce_account_orders( $current_page ) {
		$current_page    = empty( $current_page ) ? 1 : absint( $current_page );
		$customer_orders = wc_get_orders(
			apply_filters(
				'woocommerce_my_account_my_orders_query',
				array(
					'customer' => get_current_user_id(),
					'page'     => $current_page,
					'paginate' => true,
				)
			)
		);

		wc_get_template(
			'myaccount/orders.php',
			array(
				'current_page'    => absint( $current_page ),
				'customer_orders' => $customer_orders,
				'has_orders'      => 0 < $customer_orders->total,
				'wp_button_class' => wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '',
			)
		);
	}
}

if ( ! function_exists( 'woocommerce_account_view_order' ) ) {

	/**
	 * My Account > View order template.
	 *
	 * @param int $order_id Order ID.
	 */
	function woocommerce_account_view_order( $order_id ) {
		WC_Shortcode_My_Account::view_order( absint( $order_id ) );
	}
}

if ( ! function_exists( 'woocommerce_account_downloads' ) ) {

	/**
	 * My Account > Downloads template.
	 */
	function woocommerce_account_downloads() {
		wc_get_template( 'myaccount/downloads.php' );
	}
}

if ( ! function_exists( 'woocommerce_account_edit_address' ) ) {

	/**
	 * My Account > Edit address template.
	 *
	 * @param string $type Address type.
	 */
	function woocommerce_account_edit_address( $type ) {
		$type = wc_edit_address_i18n( sanitize_title( $type ), true );

		WC_Shortcode_My_Account::edit_address( $type );
	}
}

if ( ! function_exists( 'woocommerce_account_payment_methods' ) ) {

	/**
	 * My Account > Downloads template.
	 */
	function woocommerce_account_payment_methods() {
		wc_get_template( 'myaccount/payment-methods.php' );
	}
}

if ( ! function_exists( 'woocommerce_account_add_payment_method' ) ) {

	/**
	 * My Account > Add payment method template.
	 */
	function woocommerce_account_add_payment_method() {
		WC_Shortcode_My_Account::add_payment_method();
	}
}

if ( ! function_exists( 'woocommerce_account_edit_account' ) ) {

	/**
	 * My Account > Edit account template.
	 */
	function woocommerce_account_edit_account() {
		WC_Shortcode_My_Account::edit_account();
	}
}

if ( ! function_exists( 'wc_no_products_found' ) ) {

	/**
	 * Handles the loop when no products were found/no product exist.
	 */
	function wc_no_products_found() {
		wc_get_template( 'loop/no-products-found.php' );
	}
}


if ( ! function_exists( 'wc_get_email_order_items' ) ) {
	/**
	 * Get HTML for the order items to be shown in emails.
	 *
	 * @param WC_Order $order Order object.
	 * @param array    $args Arguments.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	function wc_get_email_order_items( $order, $args = array() ) {
		ob_start();

		$defaults = array(
			'show_sku'      => false,
			'show_image'    => false,
			'image_size'    => array( 32, 32 ),
			'plain_text'    => false,
			'sent_to_admin' => false,
		);

		$args     = wp_parse_args( $args, $defaults );
		$template = $args['plain_text'] ? 'emails/plain/email-order-items.php' : 'emails/email-order-items.php';

		wc_get_template(
			$template,
			apply_filters(
				'woocommerce_email_order_items_args',
				array(
					'order'               => $order,
					'items'               => $order->get_items(),
					'show_download_links' => $order->is_download_permitted() && ! $args['sent_to_admin'],
					'show_sku'            => $args['show_sku'],
					'show_purchase_note'  => $order->is_paid() && ! $args['sent_to_admin'],
					'show_image'          => $args['show_image'],
					'image_size'          => $args['image_size'],
					'plain_text'          => $args['plain_text'],
					'sent_to_admin'       => $args['sent_to_admin'],
				)
			)
		);

		return apply_filters( 'woocommerce_email_order_items_table', ob_get_clean(), $order );
	}
}

if ( ! function_exists( 'wc_display_item_meta' ) ) {
	/**
	 * Display item meta data.
	 *
	 * @since  3.0.0
	 * @param  WC_Order_Item $item Order Item.
	 * @param  array         $args Arguments.
	 * @return string|void
	 */
	function wc_display_item_meta( $item, $args = array() ) {
		$strings = array();
		$html    = '';
		$args    = wp_parse_args(
			$args,
			array(
				'before'       => '<ul class="wc-item-meta"><li>',
				'after'        => '</li></ul>',
				'separator'    => '</li><li>',
				'echo'         => true,
				'autop'        => false,
				'label_before' => '<strong class="wc-item-meta-label">',
				'label_after'  => ':</strong> ',
			)
		);

		foreach ( $item->get_all_formatted_meta_data() as $meta_id => $meta ) {
			$value     = $args['autop'] ? wp_kses_post( $meta->display_value ) : wp_kses_post( make_clickable( trim( $meta->display_value ) ) );
			$strings[] = $args['label_before'] . wp_kses_post( $meta->display_key ) . $args['label_after'] . $value;
		}

		if ( $strings ) {
			$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}

		$html = apply_filters( 'woocommerce_display_item_meta', $html, $item, $args );

		if ( $args['echo'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'wc_display_item_downloads' ) ) {
	/**
	 * Display item download links.
	 *
	 * @since  3.0.0
	 * @param  WC_Order_Item $item Order Item.
	 * @param  array         $args Arguments.
	 * @return string|void
	 */
	function wc_display_item_downloads( $item, $args = array() ) {
		$strings = array();
		$html    = '';
		$args    = wp_parse_args(
			$args,
			array(
				'before'    => '<ul class ="wc-item-downloads"><li>',
				'after'     => '</li></ul>',
				'separator' => '</li><li>',
				'echo'      => true,
				'show_url'  => false,
			)
		);

		$downloads = is_object( $item ) && $item->is_type( 'line_item' ) ? $item->get_item_downloads() : array();

		if ( $downloads ) {
			$i = 0;
			foreach ( $downloads as $file ) {
				$i ++;

				if ( $args['show_url'] ) {
					$strings[] = '<strong class="wc-item-download-label">' . esc_html( $file['name'] ) . ':</strong> ' . esc_html( $file['download_url'] );
				} else {
					/* translators: %d: downloads count */
					$prefix    = count( $downloads ) > 1 ? sprintf( __( 'Download %d', 'woocommerce' ), $i ) : __( 'Download', 'woocommerce' );
					$strings[] = '<strong class="wc-item-download-label">' . $prefix . ':</strong> <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a>';
				}
			}
		}

		if ( $strings ) {
			$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
		}

		$html = apply_filters( 'woocommerce_display_item_downloads', $html, $item, $args );

		if ( $args['echo'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'woocommerce_photoswipe' ) ) {

	/**
	 * Get the shop sidebar template.
	 */
	function woocommerce_photoswipe() {
		if ( current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
			wc_get_template( 'single-product/photoswipe.php' );
		}
	}
}

/**
 * Outputs a list of product attributes for a product.
 *
 * @since  3.0.0
 * @param  WC_Product $product Product Object.
 */
function wc_display_product_attributes( $product ) {
	$product_attributes = array();

	// Display weight and dimensions before attribute list.
	$display_dimensions = apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() );

	if ( $display_dimensions && $product->has_weight() ) {
		$product_attributes['weight'] = array(
			'label' => __( 'Weight', 'woocommerce' ),
			'value' => wc_format_weight( $product->get_weight() ),
		);
	}

	if ( $display_dimensions && $product->has_dimensions() ) {
		$product_attributes['dimensions'] = array(
			'label' => __( 'Dimensions', 'woocommerce' ),
			'value' => wc_format_dimensions( $product->get_dimensions( false ) ),
		);
	}

	// Add product attributes to list.
	$attributes = array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' );

	foreach ( $attributes as $attribute ) {
		$values = array();

		if ( $attribute->is_taxonomy() ) {
			$attribute_taxonomy = $attribute->get_taxonomy_object();
			$attribute_values   = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

			foreach ( $attribute_values as $attribute_value ) {
				$value_name = esc_html( $attribute_value->name );

				if ( $attribute_taxonomy->attribute_public ) {
					$values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
				} else {
					$values[] = $value_name;
				}
			}
		} else {
			$values = $attribute->get_options();

			foreach ( $values as &$value ) {
				$value = make_clickable( esc_html( $value ) );
			}
		}

		$product_attributes[ 'attribute_' . sanitize_title_with_dashes( $attribute->get_name() ) ] = array(
			'label' => wc_attribute_label( $attribute->get_name() ),
			'value' => apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values ),
		);
	}

	/**
	 * Hook: woocommerce_display_product_attributes.
	 *
	 * @since 3.6.0.
	 * @param array $product_attributes Array of atributes to display; label, value.
	 * @param WC_Product $product Showing attributes for this product.
	 */
	$product_attributes = apply_filters( 'woocommerce_display_product_attributes', $product_attributes, $product );

	wc_get_template(
		'single-product/product-attributes.php',
		array(
			'product_attributes' => $product_attributes,
			// Legacy params.
			'product'            => $product,
			'attributes'         => $attributes,
			'display_dimensions' => $display_dimensions,
		)
	);
}

/**
 * Get HTML to show product stock.
 *
 * @since  3.0.0
 * @param  WC_Product $product Product Object.
 * @return string
 */
function wc_get_stock_html( $product ) {
	$html         = '';
	$availability = $product->get_availability();

	if ( ! empty( $availability['availability'] ) ) {
		ob_start();

		wc_get_template(
			'single-product/stock.php',
			array(
				'product'      => $product,
				'class'        => $availability['class'],
				'availability' => $availability['availability'],
			)
		);

		$html = ob_get_clean();
	}

	if ( has_filter( 'woocommerce_stock_html' ) ) {
		wc_deprecated_function( 'The woocommerce_stock_html filter', '', 'woocommerce_get_stock_html' );
		$html = apply_filters( 'woocommerce_stock_html', $html, $availability['availability'], $product );
	}

	return apply_filters( 'woocommerce_get_stock_html', $html, $product );
}

/**
 * Get HTML for ratings.
 *
 * @since  3.0.0
 * @param  float $rating Rating being shown.
 * @param  int   $count  Total number of ratings.
 * @return string
 */
function wc_get_rating_html( $rating, $count = 0 ) {
	$html = '';

	if ( 0 < $rating ) {
		/* translators: %s: rating */
		$label = sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $rating );
		$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">' . wc_get_star_rating_html( $rating, $count ) . '</div>';
	}

	return apply_filters( 'woocommerce_product_get_rating_html', $html, $rating, $count );
}

/**
 * Get HTML for star rating.
 *
 * @since  3.1.0
 * @param  float $rating Rating being shown.
 * @param  int   $count  Total number of ratings.
 * @return string
 */
function wc_get_star_rating_html( $rating, $count = 0 ) {
	$html = '<span style="width:' . ( ( $rating / 5 ) * 100 ) . '%">';

	if ( 0 < $count ) {
		/* translators: 1: rating 2: rating count */
		$html .= sprintf( _n( 'Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'woocommerce' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>', '<span class="rating">' . esc_html( $count ) . '</span>' );
	} else {
		/* translators: %s: rating */
		$html .= sprintf( esc_html__( 'Rated %s out of 5', 'woocommerce' ), '<strong class="rating">' . esc_html( $rating ) . '</strong>' );
	}

	$html .= '</span>';

	return apply_filters( 'woocommerce_get_star_rating_html', $html, $rating, $count );
}

/**
 * Returns a 'from' prefix if you want to show where prices start at.
 *
 * @since  3.0.0
 * @return string
 */
function wc_get_price_html_from_text() {
	return apply_filters( 'woocommerce_get_price_html_from_text', '<span class="from">' . _x( 'From:', 'min_price', 'woocommerce' ) . ' </span>' );
}

/**
 * Get logout endpoint.
 *
 * @since  2.6.9
 *
 * @param string $redirect Redirect URL.
 *
 * @return string
 */
function wc_logout_url( $redirect = '' ) {
	$redirect = $redirect ? $redirect : apply_filters( 'woocommerce_logout_default_redirect_url', wc_get_page_permalink( 'myaccount' ) );

	if ( get_option( 'woocommerce_logout_endpoint' ) ) {
		return wp_nonce_url( wc_get_endpoint_url( 'customer-logout', '', $redirect ), 'customer-logout' );
	}

	return wp_logout_url( $redirect );
}

/**
 * Show notice if cart is empty.
 *
 * @since 3.1.0
 */
function wc_empty_cart_message() {
	echo '<p class="cart-empty woocommerce-info">' . wp_kses_post( apply_filters( 'wc_empty_cart_message', __( 'Your cart is currently empty.', 'woocommerce' ) ) ) . '</p>';
}

/**
 * Disable search engines indexing core, dynamic, cart/checkout pages.
 *
 * @todo Deprecated this function after dropping support for WP 5.6.
 * @since 3.2.0
 */
function wc_page_noindex() {
	// wp_no_robots is deprecated since WP 5.7.
	if ( function_exists( 'wp_robots_no_robots' ) ) {
		return;
	}

	if ( is_page( wc_get_page_id( 'cart' ) ) || is_page( wc_get_page_id( 'checkout' ) ) || is_page( wc_get_page_id( 'myaccount' ) ) ) {
		wp_no_robots();
	}
}
add_action( 'wp_head', 'wc_page_noindex' );

/**
 * Disable search engines indexing core, dynamic, cart/checkout pages.
 * Uses "wp_robots" filter introduced in WP 5.7.
 *
 * @since 5.0.0
 * @param array $robots Associative array of robots directives.
 * @return array Filtered robots directives.
 */
function wc_page_no_robots( $robots ) {
	if ( is_page( wc_get_page_id( 'cart' ) ) || is_page( wc_get_page_id( 'checkout' ) ) || is_page( wc_get_page_id( 'myaccount' ) ) ) {
		return wp_robots_no_robots( $robots );
	}

	return $robots;
}
add_filter( 'wp_robots', 'wc_page_no_robots' );

/**
 * Get a slug identifying the current theme.
 *
 * @since 3.3.0
 * @return string
 */
function wc_get_theme_slug_for_templates() {
	return apply_filters( 'woocommerce_theme_slug_for_templates', get_option( 'template' ) );
}

/**
 * Gets and formats a list of cart item data + variations for display on the frontend.
 *
 * @since 3.3.0
 * @param array $cart_item Cart item object.
 * @param bool  $flat Should the data be returned flat or in a list.
 * @return string
 */
function wc_get_formatted_cart_item_data( $cart_item, $flat = false ) {
	$item_data = array();

	// Variation values are shown only if they are not found in the title as of 3.0.
	// This is because variation titles display the attributes.
	if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
		foreach ( $cart_item['variation'] as $name => $value ) {
			$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

			if ( taxonomy_exists( $taxonomy ) ) {
				// If this is a term slug, get the term's nice name.
				$term = get_term_by( 'slug', $value, $taxonomy );
				if ( ! is_wp_error( $term ) && $term && $term->name ) {
					$value = $term->name;
				}
				$label = wc_attribute_label( $taxonomy );
			} else {
				// If this is a custom option slug, get the options name.
				$value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data'] );
				$label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
			}

			// Check the nicename against the title.
			if ( '' === $value || wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
				continue;
			}

			$item_data[] = array(
				'key'   => $label,
				'value' => $value,
			);
		}
	}

	// Filter item data to allow 3rd parties to add more to the array.
	$item_data = apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );

	// Format item data ready to display.
	foreach ( $item_data as $key => $data ) {
		// Set hidden to true to not display meta on cart.
		if ( ! empty( $data['hidden'] ) ) {
			unset( $item_data[ $key ] );
			continue;
		}
		$item_data[ $key ]['key']     = ! empty( $data['key'] ) ? $data['key'] : $data['name'];
		$item_data[ $key ]['display'] = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
	}

	// Output flat or in list format.
	if ( count( $item_data ) > 0 ) {
		ob_start();

		if ( $flat ) {
			foreach ( $item_data as $data ) {
				echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['display'] ) . "\n";
			}
		} else {
			wc_get_template( 'cart/cart-item-data.php', array( 'item_data' => $item_data ) );
		}

		return ob_get_clean();
	}

	return '';
}

/**
 * Gets the url to remove an item from the cart.
 *
 * @since 3.3.0
 * @param string $cart_item_key contains the id of the cart item.
 * @return string url to page
 */
function wc_get_cart_remove_url( $cart_item_key ) {
	$cart_page_url = wc_get_cart_url();
	return apply_filters( 'woocommerce_get_remove_url', $cart_page_url ? wp_nonce_url( add_query_arg( 'remove_item', $cart_item_key, $cart_page_url ), 'woocommerce-cart' ) : '' );
}

/**
 * Gets the url to re-add an item into the cart.
 *
 * @since 3.3.0
 * @param  string $cart_item_key Cart item key to undo.
 * @return string url to page
 */
function wc_get_cart_undo_url( $cart_item_key ) {
	$cart_page_url = wc_get_cart_url();

	$query_args = array(
		'undo_item' => $cart_item_key,
	);

	return apply_filters( 'woocommerce_get_undo_url', $cart_page_url ? wp_nonce_url( add_query_arg( $query_args, $cart_page_url ), 'woocommerce-cart' ) : '', $cart_item_key );
}

/**
 * Outputs all queued notices on WC pages.
 *
 * @since 3.5.0
 */
function woocommerce_output_all_notices() {
	echo '<div class="woocommerce-notices-wrapper">';
	wc_print_notices();
	echo '</div>';
}

/**
 * Products RSS Feed.
 *
 * @deprecated 2.6
 */
function wc_products_rss_feed() {
	wc_deprecated_function( 'wc_products_rss_feed', '2.6' );
}

if ( ! function_exists( 'woocommerce_reset_loop' ) ) {

	/**
	 * Reset the loop's index and columns when we're done outputting a product loop.
	 *
	 * @deprecated 3.3
	 */
	function woocommerce_reset_loop() {
		wc_reset_loop();
	}
}

if ( ! function_exists( 'woocommerce_product_reviews_tab' ) ) {
	/**
	 * Output the reviews tab content.
	 *
	 * @deprecated 2.4.0 Unused.
	 */
	function woocommerce_product_reviews_tab() {
		wc_deprecated_function( 'woocommerce_product_reviews_tab', '2.4' );
	}
}

/**
 * Display pay buttons HTML.
 *
 * @since 3.9.0
 */
function wc_get_pay_buttons() {
	$supported_gateways = array();
	$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

	foreach ( $available_gateways as $gateway ) {
		if ( $gateway->supports( 'pay_button' ) ) {
			$supported_gateways[] = $gateway->get_pay_button_id();
		}
	}

	if ( ! $supported_gateways ) {
		return;
	}

	echo '<div class="woocommerce-pay-buttons">';
	foreach ( $supported_gateways as $pay_button_id ) {
		echo sprintf( '<div class="woocommerce-pay-button__%1$s %1$s" id="%1$s"></div>', esc_attr( $pay_button_id ) );
	}
	echo '</div>';
}

// phpcs:enable Generic.Commenting.Todo.TaskFound
