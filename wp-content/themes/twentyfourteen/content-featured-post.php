<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
	<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'twentyfourteen' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="<?php the_ID(); ?>" class="attachment-featured-featured">
		<?php
			$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC', 'numberposts' => 1 ) );

			if ( '' != get_the_post_thumbnail() ) :
				the_post_thumbnail( 'featured-thumbnail-featured' );
			elseif ( $images ) :
				$image = array_shift( $images );
				echo wp_get_attachment_image( $image->ID, 'featured-thumbnail-featured' );
			else : ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/placeholder.png" alt="" class="featured-thumbnail-featured" /><?php
			endif;
		?>
	</a>

	<div class="entry-wrap">
		<header class="entry-header">
			<div class="entry-meta">
				<?php
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list( __( ', ', 'twentyfourteen' ) );
					if ( $categories_list && twentyfourteen_categorized_blog() ) :
				?>
				<span class="cat-links">
					<?php echo $categories_list; ?>
				</span>
				<?php endif; // End if categories ?>
			</div><!-- .entry-meta -->
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		</header><!-- .entry-header -->

		<div class="entry-summary clearfix">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	</div>

</article><!-- #post-<?php the_ID(); ?> -->