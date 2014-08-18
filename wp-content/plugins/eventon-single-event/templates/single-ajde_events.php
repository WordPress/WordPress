<?php	
/*
 *	The template for displaying single event
 *
 *	Override this tempalate by coping it to wp-content/*yourtheme/eventon/single-ajde_events.php
 *	This template is built based on wordpress twentythirteen theme standards and may not fit your custom
 *	theme correctly
 *
 *	@Author: AJDE
 *	@EventON
 *	@version: 0.3
 */
	
		
// load header through eventon single events class
$eventon_sin_event->eventon_header();

	do_action('eventon_before_main_content');
?>	
	<div class='evo_page_body'>
		<div class='evo_page_content <?php echo ($eventon_sin_event->has_evo_se_sidebar())? 'evo_se_sidarbar':null;?>'>
			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">

					<?php	


						eventon_se_page_content();
						
						/* use this if you move the content-single-event.php else where along this file*/
						//require_once('content-single-event.php');



					?>		
					</div><!-- .entry-content -->

					

					<footer class="entry-meta">
						<?php edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->
				</article><!-- #post -->
			<?php endwhile; ?>

		</div><!-- #content -->
	
		<?php
			
			eventon_se_sidebar();

		?>
	</div><!-- #primary -->
	<div class="clear"></div>

	
<?php 	do_action('eventon_after_main_content'); ?>
	
	
<?php get_footer(); ?>