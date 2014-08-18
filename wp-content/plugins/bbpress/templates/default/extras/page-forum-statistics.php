<?php

/**
 * Template Name: bbPress - Statistics
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); ?>

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div id="bbp-statistics" class="bbp-statistics">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content">

				<?php get_the_content() ? the_content() : _e( '<p>Here are the statistics and popular topics of our forums.</p>', 'bbpress' ); ?>

				<div id="bbpress-forums">

					<?php bbp_get_template_part( 'content', 'statistics' ); ?>

					<?php do_action( 'bbp_before_popular_topics' ); ?>

					<?php bbp_set_query_name( 'bbp_popular_topics' ); ?>

					<?php if ( bbp_view_query( 'popular' ) ) : ?>

						<h2 class="entry-title"><?php _e( 'Popular Topics', 'bbpress' ); ?></h2>

						<?php bbp_get_template_part( 'pagination', 'topics' ); ?>

						<?php bbp_get_template_part( 'loop',       'topics' ); ?>

						<?php bbp_get_template_part( 'pagination', 'topics' ); ?>

					<?php endif; ?>

					<?php bbp_reset_query_name(); ?>

					<?php do_action( 'bbp_after_popular_topics' ); ?>

				</div>
			</div>
		</div><!-- #bbp-statistics -->

	<?php endwhile; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

<?php get_sidebar(); ?>

<?php get_footer();