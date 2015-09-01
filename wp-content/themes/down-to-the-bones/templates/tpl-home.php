<?php 

/*
Template Name: Homepage
 */

get_header(); ?>

	<?php get_sidebar(); ?>

	<?php if (have_posts()) : while (have_posts()) : the_post();  ?>

			<h1><?php the_title(); ?></h1>
			
			<?php the_content(); ?>

			<?php edit_post_link('Edit page', '<p>', '</p>'); ?>

	<?php endwhile; endif; wp_reset_postdata(); ?>

<?php get_footer(); ?>