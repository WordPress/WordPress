<?php
/**
 * Template Name: Homepage
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary">
		<div id="content" role="main">

			<div class="home-top">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php if ( has_post_thumbnail() ) { ?>
						<div class="entry-page-image">
							<?php the_post_thumbnail(); ?>
						</div>
					<?php } ?>
					<section class="home-content<?php echo ( has_post_thumbnail() ) ? ' thumbnail' : ''; ?>">
						<div class="entry-content">
							<header class="entry-header">
								<h1 class="entry-title"><?php the_title(); ?></h1>
							</header><!-- .entry-header -->
							<?php the_content(); ?>
						</div><!-- .entry-content -->
					</section>
				<?php endwhile; // end of the loop. ?>
			</div><!-- .home-top -->

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar( 'home' ); ?>
<?php get_footer(); ?>