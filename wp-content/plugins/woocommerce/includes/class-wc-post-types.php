<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		WC_Post_types
 * @version		2.1.0
 * @package		WooCommerce/Classes/Products
 * @category	Class
 * @author 		WooThemes
 */
class WC_Post_types {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
	}

	/**
	 * Register WooCommerce taxonomies.
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'product_type' ) )
			return;

		do_action( 'woocommerce_register_taxonomy' );

		$permalinks = get_option( 'woocommerce_permalinks' );

		register_taxonomy( 'product_type',
	        apply_filters( 'woocommerce_taxonomy_objects_product_type', array( 'product' ) ),
	        apply_filters( 'woocommerce_taxonomy_args_product_type', array(
	            'hierarchical' 			=> false,
	            'show_ui' 				=> false,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> is_admin(),
	            'rewrite'				=> false,
	            'public'                => false
	        ) )
	    );

		register_taxonomy( 'product_cat',
	        apply_filters( 'woocommerce_taxonomy_objects_product_cat', array( 'product' ) ),
	        apply_filters( 'woocommerce_taxonomy_args_product_cat', array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_wc_term_recount',
	            'label' 				=> __( 'Product Categories', 'woocommerce' ),
	            'labels' => array(
	                    'name' 				=> __( 'Product Categories', 'woocommerce' ),
	                    'singular_name' 	=> __( 'Product Category', 'woocommerce' ),
						'menu_name'			=> _x( 'Categories', 'Admin menu name', 'woocommerce' ),
	                    'search_items' 		=> __( 'Search Product Categories', 'woocommerce' ),
	                    'all_items' 		=> __( 'All Product Categories', 'woocommerce' ),
	                    'parent_item' 		=> __( 'Parent Product Category', 'woocommerce' ),
	                    'parent_item_colon' => __( 'Parent Product Category:', 'woocommerce' ),
	                    'edit_item' 		=> __( 'Edit Product Category', 'woocommerce' ),
	                    'update_item' 		=> __( 'Update Product Category', 'woocommerce' ),
	                    'add_new_item' 		=> __( 'Add New Product Category', 'woocommerce' ),
	                    'new_item_name' 	=> __( 'New Product Category Name', 'woocommerce' )
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
	            'capabilities'			=> array(
	            	'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
	            ),
	            'rewrite' 				=> array(
					'slug'         => empty( $permalinks['category_base'] ) ? _x( 'product-category', 'slug', 'woocommerce' ) : $permalinks['category_base'],
					'with_front'   => false,
					'hierarchical' => true,
	            ),
	        ) )
	    );

	    register_taxonomy( 'product_tag',
	        apply_filters( 'woocommerce_taxonomy_objects_product_tag', array( 'product' ) ),
	        apply_filters( 'woocommerce_taxonomy_args_product_tag', array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_wc_term_recount',
	            'label' 				=> __( 'Product Tags', 'woocommerce' ),
	            'labels' => array(
	                    'name' 				=> __( 'Product Tags', 'woocommerce' ),
	                    'singular_name' 	=> __( 'Product Tag', 'woocommerce' ),
						'menu_name'			=> _x( 'Tags', 'Admin menu name', 'woocommerce' ),
	                    'search_items' 		=> __( 'Search Product Tags', 'woocommerce' ),
	                    'all_items' 		=> __( 'All Product Tags', 'woocommerce' ),
	                    'parent_item' 		=> __( 'Parent Product Tag', 'woocommerce' ),
	                    'parent_item_colon' => __( 'Parent Product Tag:', 'woocommerce' ),
	                    'edit_item' 		=> __( 'Edit Product Tag', 'woocommerce' ),
	                    'update_item' 		=> __( 'Update Product Tag', 'woocommerce' ),
	                    'add_new_item' 		=> __( 'Add New Product Tag', 'woocommerce' ),
	                    'new_item_name' 	=> __( 'New Product Tag Name', 'woocommerce' )
	            	),
	            'show_ui' 				=> true,
	            'query_var' 			=> true,
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
				),
	            'rewrite' 				=> array(
					'slug'       => empty( $permalinks['tag_base'] ) ? _x( 'product-tag', 'slug', 'woocommerce' ) : $permalinks['tag_base'],
					'with_front' => false
	            ),
	        ) )
	    );

		register_taxonomy( 'product_shipping_class',
	        apply_filters( 'woocommerce_taxonomy_objects_product_shipping_class', array('product', 'product_variation') ),
	        apply_filters( 'woocommerce_taxonomy_args_product_shipping_class', array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> __( 'Shipping Classes', 'woocommerce' ),
	            'labels' => array(
	                    'name' 				=> __( 'Shipping Classes', 'woocommerce' ),
	                    'singular_name' 	=> __( 'Shipping Class', 'woocommerce' ),
						'menu_name'			=> _x( 'Shipping Classes', 'Admin menu name', 'woocommerce' ),
	                    'search_items' 		=> __( 'Search Shipping Classes', 'woocommerce' ),
	                    'all_items' 		=> __( 'All Shipping Classes', 'woocommerce' ),
	                    'parent_item' 		=> __( 'Parent Shipping Class', 'woocommerce' ),
	                    'parent_item_colon' => __( 'Parent Shipping Class:', 'woocommerce' ),
	                    'edit_item' 		=> __( 'Edit Shipping Class', 'woocommerce' ),
	                    'update_item' 		=> __( 'Update Shipping Class', 'woocommerce' ),
	                    'add_new_item' 		=> __( 'Add New Shipping Class', 'woocommerce' ),
	                    'new_item_name' 	=> __( 'New Shipping Class Name', 'woocommerce' )
	            	),
	            'show_ui' 				=> true,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> is_admin(),
				'capabilities'			=> array(
					'manage_terms' 		=> 'manage_product_terms',
					'edit_terms' 		=> 'edit_product_terms',
					'delete_terms' 		=> 'delete_product_terms',
					'assign_terms' 		=> 'assign_product_terms',
				),
	            'rewrite' 				=> false,
	        ) )
	    );

	    register_taxonomy( 'shop_order_status',
	        apply_filters( 'woocommerce_taxonomy_objects_shop_order_status', array('shop_order') ),
	        apply_filters( 'woocommerce_taxonomy_args_shop_order_status', array(
	            'hierarchical' 			=> false,
	            'update_count_callback' => '_update_post_term_count',
	            'show_ui' 				=> false,
	            'show_in_nav_menus' 	=> false,
	            'query_var' 			=> is_admin(),
	            'rewrite' 				=> false,
	            'public'                => false
	        ) )
	    );

	    global $wc_product_attributes, $woocommerce;

	    $wc_product_attributes = array();

		if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
			foreach ( $attribute_taxonomies as $tax ) {
		    	if ( $name = wc_attribute_taxonomy_name( $tax->attribute_name ) ) {

		    		$label = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;

		    		$wc_product_attributes[ $name ] = $tax;

		    		register_taxonomy( $name,
				        apply_filters( 'woocommerce_taxonomy_objects_' . $name, array( 'product' ) ),
				        apply_filters( 'woocommerce_taxonomy_args_' . $name, array(
				            'hierarchical' 				=> true,
	            			'update_count_callback' 	=> '_update_post_term_count',
				            'labels' => array(
				                    'name' 						=> $label,
				                    'singular_name' 			=> $label,
				                    'search_items' 				=> sprintf( __( 'Search %s', 'woocommerce' ), $label ),
				                    'all_items' 				=> sprintf( __( 'All %s', 'woocommerce' ), $label ),
				                    'parent_item' 				=> sprintf( __( 'Parent %s', 'woocommerce' ), $label ),
				                    'parent_item_colon' 		=> sprintf( __( 'Parent %s:', 'woocommerce' ), $label ),
				                    'edit_item' 				=> sprintf( __( 'Edit %s', 'woocommerce' ), $label ),
				                    'update_item' 				=> sprintf( __( 'Update %s', 'woocommerce' ), $label ),
				                    'add_new_item' 				=> sprintf( __( 'Add New %s', 'woocommerce' ), $label ),
				                    'new_item_name' 			=> sprintf( __( 'New %s', 'woocommerce' ), $label )
				            	),
				            'show_ui' 					=> false,
				            'query_var' 				=> true,
				            'capabilities'			=> array(
				            	'manage_terms' 		=> 'manage_product_terms',
								'edit_terms' 		=> 'edit_product_terms',
								'delete_terms' 		=> 'delete_product_terms',
								'assign_terms' 		=> 'assign_product_terms',
				            ),
				            'show_in_nav_menus' 		=> apply_filters( 'woocommerce_attribute_show_in_nav_menus', false, $name ),
				            'rewrite' 					=> array(
								'slug'         => ( empty( $permalinks['attribute_base'] ) ? '' : trailingslashit( $permalinks['attribute_base'] ) ) . sanitize_title( $tax->attribute_name ),
								'with_front'   => false,
								'hierarchical' => true
				            ),
				        ) )
				    );
		    	}
		    }
			
			do_action( 'woocommerce_after_register_taxonomy' );
		}
	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {
		if ( post_type_exists('product') )
			return;

		do_action( 'woocommerce_register_post_type' );

		$permalinks        = get_option( 'woocommerce_permalinks' );
		$product_permalink = empty( $permalinks['product_base'] ) ? _x( 'product', 'slug', 'woocommerce' ) : $permalinks['product_base'];

		register_post_type( "product",
			apply_filters( 'woocommerce_register_post_type_product',
				array(
					'labels' => array(
							'name' 					=> __( 'Products', 'woocommerce' ),
							'singular_name' 		=> __( 'Product', 'woocommerce' ),
							'menu_name'				=> _x( 'Products', 'Admin menu name', 'woocommerce' ),
							'add_new' 				=> __( 'Add Product', 'woocommerce' ),
							'add_new_item' 			=> __( 'Add New Product', 'woocommerce' ),
							'edit' 					=> __( 'Edit', 'woocommerce' ),
							'edit_item' 			=> __( 'Edit Product', 'woocommerce' ),
							'new_item' 				=> __( 'New Product', 'woocommerce' ),
							'view' 					=> __( 'View Product', 'woocommerce' ),
							'view_item' 			=> __( 'View Product', 'woocommerce' ),
							'search_items' 			=> __( 'Search Products', 'woocommerce' ),
							'not_found' 			=> __( 'No Products found', 'woocommerce' ),
							'not_found_in_trash' 	=> __( 'No Products found in trash', 'woocommerce' ),
							'parent' 				=> __( 'Parent Product', 'woocommerce' )
						),
					'description' 			=> __( 'This is where you can add new products to your store.', 'woocommerce' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'product',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite' 				=> $product_permalink ? array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true ) : false,
					'query_var' 			=> true,
					'supports' 				=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes' ),
					'has_archive' 			=> ( $shop_page_id = wc_get_page_id( 'shop' ) ) && get_page( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop',
					'show_in_nav_menus' 	=> true
				)
			)
		);

		register_post_type( "product_variation",
			apply_filters( 'woocommerce_register_post_type_product_variation',
				array(
					'label'        => __( 'Variations', 'woocommerce' ),
					'public'       => false,
					'hierarchical' => false,
					'supports'     => false
				)
			)
		);

		$menu_name = _x('Orders', 'Admin menu name', 'woocommerce' );

		if ( $order_count = wc_processing_order_count() ) {
			$menu_name .= " <span class='awaiting-mod update-plugins count-$order_count'><span class='processing-count'>" . number_format_i18n( $order_count ) . "</span></span>" ;
		}

	    register_post_type( "shop_order",
		    apply_filters( 'woocommerce_register_post_type_shop_order',
				array(
					'labels' => array(
							'name' 					=> __( 'Orders', 'woocommerce' ),
							'singular_name' 		=> __( 'Order', 'woocommerce' ),
							'add_new' 				=> __( 'Add Order', 'woocommerce' ),
							'add_new_item' 			=> __( 'Add New Order', 'woocommerce' ),
							'edit' 					=> __( 'Edit', 'woocommerce' ),
							'edit_item' 			=> __( 'Edit Order', 'woocommerce' ),
							'new_item' 				=> __( 'New Order', 'woocommerce' ),
							'view' 					=> __( 'View Order', 'woocommerce' ),
							'view_item' 			=> __( 'View Order', 'woocommerce' ),
							'search_items' 			=> __( 'Search Orders', 'woocommerce' ),
							'not_found' 			=> __( 'No Orders found', 'woocommerce' ),
							'not_found_in_trash' 	=> __( 'No Orders found in trash', 'woocommerce' ),
							'parent' 				=> __( 'Parent Orders', 'woocommerce' ),
							'menu_name'				=> $menu_name
						),
					'description' 			=> __( 'This is where store orders are stored.', 'woocommerce' ),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'shop_order',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'show_in_menu' 			=> current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
					'hierarchical' 			=> false,
					'show_in_nav_menus' 	=> false,
					'rewrite' 				=> false,
					'query_var' 			=> false,
					'supports' 				=> array( 'title', 'comments', 'custom-fields' ),
					'has_archive' 			=> false,
				)
			)
		);

		if ( get_option( 'woocommerce_enable_coupons' ) == 'yes' ) {
		    register_post_type( "shop_coupon",
			    apply_filters( 'woocommerce_register_post_type_shop_coupon',
					array(
						'labels' => array(
								'name' 					=> __( 'Coupons', 'woocommerce' ),
								'singular_name' 		=> __( 'Coupon', 'woocommerce' ),
								'menu_name'				=> _x( 'Coupons', 'Admin menu name', 'woocommerce' ),
								'add_new' 				=> __( 'Add Coupon', 'woocommerce' ),
								'add_new_item' 			=> __( 'Add New Coupon', 'woocommerce' ),
								'edit' 					=> __( 'Edit', 'woocommerce' ),
								'edit_item' 			=> __( 'Edit Coupon', 'woocommerce' ),
								'new_item' 				=> __( 'New Coupon', 'woocommerce' ),
								'view' 					=> __( 'View Coupons', 'woocommerce' ),
								'view_item' 			=> __( 'View Coupon', 'woocommerce' ),
								'search_items' 			=> __( 'Search Coupons', 'woocommerce' ),
								'not_found' 			=> __( 'No Coupons found', 'woocommerce' ),
								'not_found_in_trash' 	=> __( 'No Coupons found in trash', 'woocommerce' ),
								'parent' 				=> __( 'Parent Coupon', 'woocommerce' )
							),
						'description' 			=> __( 'This is where you can add new coupons that customers can use in your store.', 'woocommerce' ),
						'public' 				=> false,
						'show_ui' 				=> true,
						'capability_type' 		=> 'shop_coupon',
						'map_meta_cap'			=> true,
						'publicly_queryable' 	=> false,
						'exclude_from_search' 	=> true,
						'show_in_menu' 			=> current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
						'hierarchical' 			=> false,
						'rewrite' 				=> false,
						'query_var' 			=> false,
						'supports' 				=> array( 'title' ),
						'show_in_nav_menus'		=> false,
						'show_in_admin_bar'     => true
					)
				)
			);
		}
	}
}

new WC_Post_types();
