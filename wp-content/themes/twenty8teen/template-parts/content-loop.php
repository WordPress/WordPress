<?php
/**
 * Template part for displaying the posts Loop
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Twenty8teen
 */

if ( have_posts() ) {
	$classes = twenty8teen_widget_get_classes();
	twenty8teen_widget_set_classes( wp_basename( __FILE__, '.php' ), null );

	/* Start the Loop */
	while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
		<?php

		$post_format = twenty8teen_get_type_or_format();
	/*
	 * Include the Post-Format-specific template for the entry.
	 * If you want to override this in a child theme, then include a file
	 * called entry-___.php (where ___ is the Post Format name) and that will be 
	 * used instead. Or same for custom post type, or both.
	 */
		get_template_part( 'loop-parts/entry', $post_format );

		get_template_part( 'loop-parts/post-navigation', $post_format );
		get_template_part( 'loop-parts/comments', $post_format ); ?>

			<div class="clear"></div>
		</article><!-- #post-<?php the_ID(); ?> -->
	<?php

	endwhile;

}
