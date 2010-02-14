<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php the_post(); ?>

				<h1 class="page-title"><?php _e( 'Category Archives:', 'twentyten' ); ?> <span><?php single_cat_title(); ?></span></span></h1>
				<?php $categorydesc = category_description(); if ( !empty($categorydesc) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . $categorydesc . '</div>' ); ?>

<?php rewind_posts(); ?>

<?php get_generic_template( 'loop', 'category' ); ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>