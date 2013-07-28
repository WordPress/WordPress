<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
if ( isset( $GLOBALS['content_width'] ) )
	$GLOBALS['content_width'] = 306;

$format = get_post_format();
if ( false === $format )
	$format = 'standard';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
	<div class="entry-content clearfix">
		<?php
			if ( 'gallery' == $format ) :
				$featured_image = get_the_post_thumbnail( get_the_ID(), 'featured-thumbnail-formatted' );
				$images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
				if ( $images ) :
					$total_images = count( $images );
					if ( empty( $featured_image ) ) {
						$image = array_shift( $images );
						$featured_image = wp_get_attachment_image( $image->ID, 'featured-thumbnail-formatted' );
					}
		?>
					<a href="<?php the_permalink(); ?>"><?php echo $featured_image; ?></a>
					<p class="wp-caption-text"><?php printf( _n( 'This gallery contains <a %1$s>%2$s photo</a>.', 'This gallery contains <a %1$s>%2$s photos</a>.', $total_images, 'twentyfourteen' ),
					'href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'twentyfourteen' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark"',
					number_format_i18n( $total_images )
				); ?></p><?php
				else :
					the_excerpt();
				endif;
			else :
				the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
			endif;
		?>
	</div><!-- .entry-content -->

	<header class="entry-header">
		<div class="entry-meta">
			<?php
				if ( 'link' != $format ) :
					the_title( '<h1 class="entry-title"><a href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'twentyfourteen' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark">', '</a></h1>' );
				endif;
			?>
			<?php twentyfourteen_posted_on(); ?>
			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php endif; ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
</article>