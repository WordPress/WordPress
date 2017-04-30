<?php

/**
 * Template Name: bbPress - Topics (No Replies)
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); ?>

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<div id="topics-front" class="bbp-topics-front">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content">

				<?php the_content(); ?>

				<div id="bbpress-forums">

					<?php bbp_breadcrumb(); ?>

					<?php bbp_set_query_name( 'bbp_no_replies' ); ?>

					<?php if ( bbp_has_topics( array( 'meta_key' => '_bbp_reply_count', 'meta_value' => '1', 'meta_compare' => '<', 'orderby' => 'date', 'show_stickies' => false ) ) ) : ?>

						<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

						<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

						<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

					<?php else : ?>

						<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

					<?php endif; ?>

					<?php bbp_reset_query_name(); ?>

				</div>
			</div>
		</div><!-- #topics-front -->

	<?php endwhile; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
