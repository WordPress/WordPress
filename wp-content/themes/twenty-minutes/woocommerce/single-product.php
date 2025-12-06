<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>

<?php
 $twenty_minutes_single_page_sidebar = get_theme_mod( 'twenty_minutes_single_page_sidebar',false );
 if ( false == $twenty_minutes_single_page_sidebar ) {
   $colmd = 'col-lg-12 col-md-12';
 } else { 
   $colmd = 'col-lg-8 col-md-8';
 } 
?>

<div class="container">
	<main role="main" id="maincontent">
		<div class="row m-0 mt-5">
			<?php if(get_theme_mod( 'twenty_minutes_single_product_page_layout','Right Sidebar') == 'Right Sidebar'){ ?>
				<div class="<?php echo esc_attr( $colmd ); ?> background-img-skin">
					<?php
						/**
						 * woocommerce_before_main_content hook.
						 *
						 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
						 * @hooked woocommerce_breadcrumb - 20
						 */
						do_action( 'woocommerce_before_main_content' );
					?>

						<?php while ( have_posts() ) : the_post(); ?>

							<?php wc_get_template_part( 'content', 'single-product' ); ?>

						<?php endwhile; // end of the loop. ?>

					<?php
						/**
						 * woocommerce_after_main_content hook.
						 *
						 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
						 */
						do_action( 'woocommerce_after_main_content' );
					?>
				</div>
				<?php if ( false != $twenty_minutes_single_page_sidebar ) {?>
					<div class="col-lg-4 col-md-4" id="sidebar">
						<?php dynamic_sidebar( 'woocommerce-single-sidebar' ); ?>
					</div>
				<?php } ?>
			<?php } elseif (get_theme_mod( 'twenty_minutes_single_product_page_layout','Right Sidebar') == 'Left Sidebar') {?>	
				<?php if ( false != $twenty_minutes_single_page_sidebar ) {?>
					<div class="col-lg-4 col-md-4" id="sidebar">
						<?php dynamic_sidebar( 'woocommerce-single-sidebar' ); ?>
					</div>
				<?php } ?>
				<div class="<?php echo esc_attr( $colmd ); ?> background-img-skin">
					<?php
						/**
						 * woocommerce_before_main_content hook.
						 *
						 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
						 * @hooked woocommerce_breadcrumb - 20
						 */
						do_action( 'woocommerce_before_main_content' );
					?>

						<?php while ( have_posts() ) : the_post(); ?>

							<?php wc_get_template_part( 'content', 'single-product' ); ?>

						<?php endwhile; // end of the loop. ?>

					<?php
						/**
						 * woocommerce_after_main_content hook.
						 *
						 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
						 */
						do_action( 'woocommerce_after_main_content' );
					?>
				</div>
			<?php }?>		
		</div>
	</main>
</div>

<?php get_footer( 'shop' );


