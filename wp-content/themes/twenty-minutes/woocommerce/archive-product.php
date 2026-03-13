<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

 defined( 'ABSPATH' ) || exit;

 get_header( 'shop' ); ?>
 
 <?php
  $twenty_minutes_shop_page_sidebar = get_theme_mod( 'twenty_minutes_shop_page_sidebar',false );
  if ( false == $twenty_minutes_shop_page_sidebar ) {
	$colmd = 'col-lg-12 col-md-12';
  } else { 
	$colmd = 'col-lg-8 col-md-8';
  } 
 ?>
 
 <div class="container">
	 <main role="main" id="maincontent">
		 <div class="row m-0">
			 <?php if(get_theme_mod( 'twenty_minutes_shop_page_sidebar_position','Right Sidebar') == 'Right Sidebar'){ ?>
				 <div class="<?php echo esc_attr( $colmd ); ?>">
					 <?php
					 /**
					  * Hook: woocommerce_before_main_content.
					  *
					  * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
					  * @hooked woocommerce_breadcrumb - 20
					  * @hooked WC_Structured_Data::generate_website_data() - 30
					  */
					 do_action( 'woocommerce_before_main_content' );
 
					 /**
					  * Hook: woocommerce_shop_loop_header.
					  *
					  * @since 8.6.0
					  *
					  * @hooked woocommerce_product_taxonomy_archive_header - 10
					  */
					 do_action( 'woocommerce_shop_loop_header' );
 
					 if ( woocommerce_product_loop() ) {
 
						 /**
						  * Hook: woocommerce_before_shop_loop.
						  *
						  * @hooked woocommerce_output_all_notices - 10
						  * @hooked woocommerce_result_count - 20
						  * @hooked woocommerce_catalog_ordering - 30
						  */
						 do_action( 'woocommerce_before_shop_loop' );
 
						 woocommerce_product_loop_start();
 
						 if ( wc_get_loop_prop( 'total' ) ) {
							 while ( have_posts() ) {
								 the_post();
 
								 /**
								  * Hook: woocommerce_shop_loop.
								  */
								 do_action( 'woocommerce_shop_loop' );
 
								 wc_get_template_part( 'content', 'product' );
							 }
						 }
 
						 woocommerce_product_loop_end();
 
						 /**
						  * Hook: woocommerce_after_shop_loop.
						  *
						  * @hooked woocommerce_pagination - 10
						  */
						 do_action( 'woocommerce_after_shop_loop' );
					 } else {
						 /**
						  * Hook: woocommerce_no_products_found.
						  *
						  * @hooked wc_no_products_found - 10
						  */
						 do_action( 'woocommerce_no_products_found' );
					 }
 
					 /**
					  * Hook: woocommerce_after_main_content.
					  *
					  * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					  */
					 do_action( 'woocommerce_after_main_content' ); ?>
				 </div>
				 <?php if ( false != $twenty_minutes_shop_page_sidebar ) {?>
					 <div class="col-lg-4 col-md-4 mt-3" id="sidebar">
						 <?php dynamic_sidebar('woocommerce_sidebar'); ?>
					 </div>
				 <?php } ?>
			 <?php } elseif (get_theme_mod( 'twenty_minutes_shop_page_sidebar_position','Right Sidebar') == 'Left Sidebar') {?>
				 <?php if ( false != $twenty_minutes_shop_page_sidebar ) {?>
					 <div class="col-lg-4 col-md-4 mt-3" id="sidebar">
						 <?php dynamic_sidebar('woocommerce_sidebar'); ?>
					 </div>
				 <?php } ?>
				 <div class="<?php echo esc_attr( $colmd ); ?>">
					 <?php
					 /**
					  * Hook: woocommerce_before_main_content.
					  *
					  * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
					  * @hooked woocommerce_breadcrumb - 20
					  * @hooked WC_Structured_Data::generate_website_data() - 30
					  */
					 do_action( 'woocommerce_before_main_content' );
 
					 /**
					  * Hook: woocommerce_shop_loop_header.
					  *
					  * @since 8.6.0
					  *
					  * @hooked woocommerce_product_taxonomy_archive_header - 10
					  */
					 do_action( 'woocommerce_shop_loop_header' );
 
					 if ( woocommerce_product_loop() ) {
 
						 /**
						  * Hook: woocommerce_before_shop_loop.
						  *
						  * @hooked woocommerce_output_all_notices - 10
						  * @hooked woocommerce_result_count - 20
						  * @hooked woocommerce_catalog_ordering - 30
						  */
						 do_action( 'woocommerce_before_shop_loop' );
 
						 woocommerce_product_loop_start();
 
						 if ( wc_get_loop_prop( 'total' ) ) {
							 while ( have_posts() ) {
								 the_post();
 
								 /**
								  * Hook: woocommerce_shop_loop.
								  */
								 do_action( 'woocommerce_shop_loop' );
 
								 wc_get_template_part( 'content', 'product' );
							 }
						 }
 
						 woocommerce_product_loop_end();
 
						 /**
						  * Hook: woocommerce_after_shop_loop.
						  *
						  * @hooked woocommerce_pagination - 10
						  */
						 do_action( 'woocommerce_after_shop_loop' );
					 } else {
						 /**
						  * Hook: woocommerce_no_products_found.
						  *
						  * @hooked wc_no_products_found - 10
						  */
						 do_action( 'woocommerce_no_products_found' );
					 }
 
					 /**
					  * Hook: woocommerce_after_main_content.
					  *
					  * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					  */
					 do_action( 'woocommerce_after_main_content' ); ?>
				 </div>
			 <?php }?>
		 </div>
	 </main>
 </div>
 <?php get_footer( 'shop' );  ?>  