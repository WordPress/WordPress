<?php get_header(); ?>

		<div id="container">
			<div id="content">

    <?php if ( have_posts() ) : 
		include 'loop.php';
    else : ?>
		<h2><?php _e( 'Not Found', 'twentyten' ); ?></h2>
		<div class="entry-content">
			<p><?php _e( 'Apologies, but we were unable to find what you were looking for. Perhaps searching will help.', 'twentyten' ); ?></p>
			<?php get_search_form(); ?>
		</div>
    <?php endif; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
