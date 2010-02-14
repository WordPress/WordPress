<?php get_header(); ?>

		<div id="container">
			<div id="content">

<?php the_post(); ?>

<?php if ( is_day() ) : ?>
				<h1 class="page-title"><?php printf( __( 'Daily Archives: <span>%s</span>', 'twentyten' ), get_the_date() ); ?></h1>
<?php elseif ( is_month() ) : ?>
				<h1 class="page-title"><?php printf( __( 'Monthly Archives: <span>%s</span>', 'twentyten' ), get_the_date('F Y') ); ?></h1>
<?php elseif ( is_year() ) : ?>
				<h1 class="page-title"><?php printf( __( 'Yearly Archives: <span>%s</span>', 'twentyten' ), get_the_date('Y') ); ?></h1>
<?php else : ?>
				<h1 class="page-title"><?php _e( 'Blog Archives', 'twentyten' ); ?></h1>
<?php endif; ?>

<?php rewind_posts(); ?>

<?php include 'loop.php'; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>