<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
if ( isset( $GLOBALS['content_width'] ) )
	$GLOBALS['content_width'] = 306;

$images = get_posts( array(
	'post_parent'    => get_post()->post_parent,
	'fields'         => 'ids',
	'numberposts'    => -1,
	'post_status'    => 'inherit',
	'post_type'      => 'attachment',
	'post_mime_type' => 'image',
	'order'          => 'ASC',
	'orderby'        => 'menu_order ID'
) );
$total_images = count( $images );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php
			if ( has_post_format( 'gallery' ) ) :
				if ( has_post_thumbnail() ) :
					$featured_image = get_the_post_thumbnail( get_the_ID(), 'featured-thumbnail-formatted' );
				elseif ( $total_images > 0 ) :
					$image = array_shift( $images );
					$featured_image = wp_get_attachment_image( $image, 'featured-thumbnail-formatted' );
		?>
		<a href="<?php the_permalink(); ?>"><?php echo $featured_image; ?></a>
		<p class="wp-caption-text">
			<?php
				printf( _n( 'This gallery contains <a href="%1$s" rel="bookmark">%2$s photo</a>.', 'This gallery contains <a href="%1$s" rel="bookmark">%2$s photos</a>.', $total_images, 'twentyfourteen' ),
					esc_url( get_permalink() ),
					number_format_i18n( $total_images )
				);
			?>
		</p>
		<?php
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
				if ( ! has_post_format( 'link' ) ) :
					the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
				endif;

				twentyfourteen_posted_on();

				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
			?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php endif; ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
</article><!-- #post-## -->
