<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php the_post(); ?>

				<h1 class="page-title"><?php _e( 'Tag Archives: ', 'twentyten' ); ?><span><?php single_tag_title(); ?></span></h1>

<?php rewind_posts(); ?>

<?php get_generic_template( 'loop', 'tag' ); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>