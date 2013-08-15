<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a class="attachment-featured-featured" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentyfourteen' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="<?php the_ID(); ?>">
		<?php
			if ( has_post_thumbnail() ) :
				the_post_thumbnail( 'featured-thumbnail-featured' );

			else :
				$images = get_children( array(
					'post_parent'    => get_the_ID(),
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'numberposts'    => 1,
				) );

				if ( $images ) :
					$image = array_shift( $images );
					echo wp_get_attachment_image( $image->ID, 'featured-thumbnail-featured' );

				else : ?>
					<img class="featured-thumbnail-featured" src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="" /><?php

				endif;
			endif;
		?>
	</a>

	<div class="entry-wrap">
		<header class="entry-header">
			<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
			<div class="entry-meta">
				<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
			</div><!-- .entry-meta -->
			<?php endif; ?>

			<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	</div>
</article><!-- #post-## -->
