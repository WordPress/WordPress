<?php
/**
 * Bagberry functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Bagberry
 * @since Bagberry 1.0
 */


add_action( 'after_setup_theme', 'bagberry_support' );
// add_action( 'wp', 'bagberry_remove_theme_support' );
add_action( 'woocommerce_init', 'bagberry_woocommerce_setup' );

// add_action( 'woocommerce_shop_loop_item_title', 'bagberry_shop_loop_item_description', 15 );
add_action( 'woocommerce_checkout_before_order_review_heading', 'bagberry_woocommerce_order_review_open_tag' );
add_action( 'woocommerce_checkout_after_order_review', 'bagberry_woocommerce_close_tag' );
add_action( 'woocommerce_checkout_before_customer_details', 'bagberry_woocommerce_customer_details_open_tag' );
add_action( 'woocommerce_checkout_after_customer_details', 'bagberry_woocommerce_close_tag' );

add_action( 'wp_enqueue_scripts', 'bagberry_scripts' );
add_action( 'enqueue_block_editor_assets', 'bagberry_block_editor_assets' );
add_action( 'wp_footer', 'bagberry_woocommerce_product_gallery_choice', 99 );

// removing coupon on checkout page 
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// removing & adding checkout payment form
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_payment', 9 );

// adding coupon through ajax on checkout page
add_action( 'wc_ajax_checkout_coupon', 'bagberry_woocommerce_checkout_coupon', 10 );

// adding quantity label text 
add_action( 'woocommerce_before_quantity_input_field', 'bagberry_woocommerce_before_quantity_input_field' );

// Remove link close
// remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_details_wrap_open', 8 );
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_details_wrap_close', 20 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 25 );

// Summary Open & Close
add_action( 'woocommerce_before_single_product_summary', 'bagberry_woocommerce_product_summary_open', 7 );
add_action( 'woocommerce_after_single_product_summary', 'bagberry_woocommerce_product_summary_close', 7 );

// Removing & adding sale flash
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_show_product_sale_flash', 3 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_rating', 8 );


add_filter( 'woocommerce_gallery_thumbnail_size', 'bagberry_woocommerce_gallery_thumbnail_size' );

add_filter( 'use_block_editor_for_post_type', 'bagberry_enable_block_editor_product', 10, 2 );
add_filter( 'woocommerce_cart_item_name', 'bagberry_woocommerce_cart_item_name', 10, 3 );
// add_filter( 'woocommerce_order_item_name', 'bagberry_woocommerce_order_item_name', 10, 3 );

// Customizing product grid block outputs
add_filter( 'woocommerce_blocks_product_grid_item_html', 'bagberry_woocommerce_blocks_product_grid_item_html', 10, 3 );
add_filter( 'woocommerce_blocks_product_grid_item_html', 'bagberry_woocommerce_blocks_product_grid_item_html', 10, 3 );


if ( ! function_exists( 'bagberry_support' ) ) {

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Bagberry 1.0
	 *
	 * @return void
	 */
	function bagberry_support() {

		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on bagberry, use a find and replace
		 * to change 'bagberry' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'bagberry', get_template_directory() . '/languages' );


		$defaults = array(
			'height'               => 18,
			'width'                => 136,
			'flex-height'          => true,
			'flex-width'           => true
		);
	 
		add_theme_support( 'custom-logo', $defaults );

		// Declaring WooCommerce support
		add_theme_support( 'woocommerce' );

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

		// Enqueue editor styles.
		// add_editor_style( 'style-editor.css' );

	}

}

if ( !function_exists( 'bagberry_remove_theme_support' ) ) {
	function bagberry_remove_theme_support() {

		// Remove support for product gallery slider
		remove_theme_support( 'wc-product-gallery-slider' );

	}
}

if ( !function_exists( 'bagberry_woocommerce_setup' ) ) {
	function bagberry_woocommerce_setup() {

		// Removing Wocommerce CSS
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


	}
}

if ( !function_exists( 'bagberry_woocommerce_checkout_coupon' ) ) {

	function bagberry_woocommerce_checkout_coupon() {
		if ( !wc_coupons_enabled() ) {
			return;
		}
		
		?>
		<div class="agni_checkout_coupon coupon">
			<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php echo esc_attr_x( 'Coupon code', 'Checkout coupon code', 'bagberry' ); ?>" />
			<a class="coupon_submit" href="#"><?php echo esc_html_e( 'Apply coupon', 'bagberry' ); ?></a>
		</div>
		<?php 
	}
}


if ( !function_exists( 'bagberry_woocommerce_order_review_open_tag' ) ) {
	function bagberry_woocommerce_order_review_open_tag() {
		?>
		<div class="agni-order-review">
		<?php
	}
}

if ( !function_exists( 'bagberry_woocommerce_customer_details_open_tag' ) ) {
	function bagberry_woocommerce_customer_details_open_tag() {
		?>
		<div class="agni-customer-details">
		<?php
	}
}

if ( !function_exists( 'bagberry_woocommerce_close_tag' ) ) {
	function bagberry_woocommerce_close_tag() {
		?>
		</div>
		<?php
	}
}


if ( !function_exists( 'bagberry_scripts' ) ) {

	/**
	 * Enqueue styles.
	 *
	 * @since Bagberry 1.0
	 *
	 * @return void
	 */
	function bagberry_scripts() {

		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_enqueue_style(
			'bagberry-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);
		wp_style_add_data( 'bagberry-style', 'rtl', 'replace' );

		wp_dequeue_style( 'wc-blocks-style' );

		// wp_dequeue_style( 'wp-block-gallery' );


		// Enqueueing JS
		wp_enqueue_script( 'bagberry-script', get_template_directory_uri() . '/assets/js/script.js', array( 'jquery' ), wp_get_theme()->get('Version'), true );
		wp_localize_script( 'bagberry-script', 'bagberry_script', array(
			'imgdir' => get_template_directory_uri() . '/assets/img',
		));

		wp_register_script( 'bagberry-single-product-zoom', get_template_directory_uri() . '/assets/js/single-product-zoom.js', array( 'jquery', 'wc-single-product' ), wp_get_theme()->get('Version'), true );

		if ( class_exists('WooCommerce') ) {
			if ( is_product() ) {
				wp_enqueue_script( 'bagberry-single-product-zoom' );
			}
		}

	}

}

if ( !function_exists('bagberry_block_editor_assets') ) {
	
	function bagberry_block_editor_assets() {

		// Register block editor stylesheet.
		wp_enqueue_style('bagberry-block-editor-style', get_template_directory_uri() . '/assets/css/block-editor.css', array(), wp_get_theme()->get('Version'));
		wp_style_add_data( 'bagberry-block-editor-style', 'rtl', 'replace' );

		// Enqueue Styles

		// wp_deregister_style( 'wc-blocks-editor-style' );
		wp_deregister_style( 'wc-blocks-style' );
		
		wp_dequeue_style( 'wc-blocks-editor-style' );
		wp_dequeue_style( 'wc-blocks-style' );

	}
}

if ( !function_exists( 'bagberry_woocommerce_product_gallery_choice' ) ) {
	function bagberry_woocommerce_product_gallery_choice() {

		?>
		<script>
			
			var has_slider = document.querySelector('.has-woocommerce-product-gallery-slider');
			if( !has_slider ){
				if ( typeof wc_single_product_params != 'undefined' ) { 
					wc_single_product_params.flexslider_enabled = 0
				}
			}
			
		</script>
		<?php
	}
}


if ( !function_exists( 'bagberry_enable_block_editor_product' ) ) {
	// enable gutenberg for woocommerce
	function bagberry_enable_block_editor_product( $can_edit, $post_type ) {
		if ( 'product' == $post_type ) {
			$can_edit = true;
		}

		return $can_edit;
	}
}

if ( !function_exists( 'bagberry_shop_loop_item_description' ) ) {
	function bagberry_shop_loop_item_description() {

		if ( !class_exists( 'WooCommerce' ) ) {
			return;
		}

		$product = wc_get_product( get_the_id() );

		/**
		 * Hook: woocommerce_prepare_short_description.
		 *
		 * @since bagberry 1.0
		 */
		$description = apply_filters( 'woocommerce_prepare_short_description', $product->get_short_description() );


		?>
			<div class="woocommerce-loop-product__description"><?php echo wp_kses_post( $description ); ?></div>
		<?php

	}
}


if ( !function_exists( 'bagberry_woocommerce_cart_item_name' ) ) {
	function bagberry_woocommerce_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
		

		if ( is_checkout() ) {
			$product = $cart_item['data'];
			?>
			<?php 
			echo wp_kses( $product->get_image(), array( 
				'img' => array(
					'src' => array(),
					'srcset' => array(),
					'class' => array(),
					'width' => array(),
					'height' => array(),
					'loading' => array(),
					'alt' => array()
				)
			) ); 
			?>
			<span><?php echo wp_kses_post( $product_name ); ?></span>
			<?php
		} else {
			?>
			<?php echo wp_kses_post( $product_name ); ?>
			<?php
		}
	}
}

if ( !function_exists( 'bagberry_woocommerce_order_item_name' ) ) {
	function bagberry_woocommerce_order_item_name( $product_name, $item, $is_visible) {

		$item_data = $item->get_data();
		$item_id =  ( 0 != $item_data['variation_id'] ) ? $item_data['variation_id'] :  $item_data['product_id'];

		$product = wc_get_product($item_id);

		?>
		<?php 
		echo wp_kses( $product->get_image(), array( 
			'img' => array(
				'src' => array(),
				'srcset' => array(),
				'class' => array(),
				'width' => array(),
				'height' => array(),
				'loading' => array(),
				'alt' => array()
			)
		) ); 
		?>
		<span><?php echo wp_kses_post( $product_name ); ?></span>
		<?php
	}
}

if ( !function_exists( 'bagberry_woocommerce_gallery_thumbnail_size' ) ) {
	function bagberry_woocommerce_gallery_thumbnail_size( $size ) {
		return 'woocommerce_single';
	}
}

if ( !function_exists( 'bagberry_woocommerce_blocks_product_grid_item_html' ) ) {
	function bagberry_woocommerce_blocks_product_grid_item_html( $html, $data, $product ) {


		return "
			<li class=\"wc-block-grid__product\">
				<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
					{$data->image}
					{$data->badge}
				</a>
				<div class=\"wc-block-grid__product-details\">
					<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
						{$data->title}
					</a>
					{$data->price}
					{$data->rating}
					{$data->button}
				</div>
			</li>
		";
	
	}
}


// Product details wrapper
function woocommerce_template_loop_product_details_wrap_open() { 
	?>
	<div class="product-details">
<?php 
}

function woocommerce_template_loop_product_details_wrap_close() { 
	?>
	</div>
<?php 
}

// Product summary wrapper
function bagberry_woocommerce_product_summary_open() {
	?>
	<div class="product-summary-top">
	<?php
}
function bagberry_woocommerce_product_summary_close() {
	?>
	</div>
	<?php
}


// Quantity label field
if ( !function_exists( 'bagberry_woocommerce_before_quantity_input_field' ) ) {
	function bagberry_woocommerce_before_quantity_input_field() {
		if ( is_product() ) {
			?>
		<span><?php echo esc_html__( 'Quantity', 'bagberry' ); ?></span>
		<?php
		}
	}
}



// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';

require get_template_directory() . '/inc/agni-importer-exporter/agni-importer-exporter.php';
