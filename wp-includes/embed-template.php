<?php
/**
 * Contains the post embed template.
 *
 * When a post is embedded in an iframe, this file is used to
 * create the output.
 *
 * @package WordPress
 * @subpackage oEmbed
 * @since 4.4.0
 */

if ( ! headers_sent() ) {
	header( 'X-WP-embed: true' );
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php echo wp_get_document_title(); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php
	/**
	 * Print scripts or data in the embed template <head> tag.
	 *
	 * @since 4.4.0
	 */
	do_action( 'embed_head' );
	?>
</head>
<body <?php body_class(); ?>>
<?php
if ( have_posts() ) :
	while ( have_posts() ) : the_post();
		// Add post thumbnail to response if available.
		$thumbnail_id = false;

		if ( has_post_thumbnail() ) {
			$thumbnail_id = get_post_thumbnail_id();
		}

		if ( 'attachment' === get_post_type() && wp_attachment_is_image() ) {
			$thumbnail_id = get_the_ID();
		}

		if ( $thumbnail_id ) {
			$aspect_ratio = 1;
			$measurements = array( 1, 1 );
			$image_size   = 'full'; // Fallback.

			$meta = wp_get_attachment_metadata( $thumbnail_id );
			if ( ! empty( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size => $data ) {
					if ( $data['width'] / $data['height'] > $aspect_ratio ) {
						$aspect_ratio = $data['width'] / $data['height'];
						$measurements = array( $data['width'], $data['height'] );
						$image_size   = $size;
					}
				}
			}

			/**
			 * Filter the thumbnail image size for use in the embed template.
			 *
			 * @since 4.4.0
			 *
			 * @param string $image_size Thumbnail image size.
			 */
			$image_size = apply_filters( 'embed_thumbnail_image_size', $image_size );

			$shape = $measurements[0] / $measurements[1] >= 1.75 ? 'rectangular' : 'square';

			/**
			 * Filter the thumbnail shape for use in the embed template.
			 *
			 * Rectangular images are shown above the title
			 * while square images are shown next to the content.
			 *
			 * @since 4.4.0
			 *
			 * @param string $shape Thumbnail image shape. Either 'rectangular' or 'square'.
			 */
			$shape = apply_filters( 'embed_thumbnail_image_shape', $shape );
		}
		?>
		<div <?php post_class( 'wp-embed' ); ?>>
			<?php if ( $thumbnail_id && 'rectangular' === $shape ) : ?>
				<div class="wp-embed-featured-image rectangular">
					<a href="<?php the_permalink(); ?>" target="_top">
						<?php echo wp_get_attachment_image( $thumbnail_id, $image_size ); ?>
					</a>
				</div>
			<?php endif; ?>

			<p class="wp-embed-heading">
				<a href="<?php the_permalink(); ?>" target="_top">
					<?php the_title(); ?>
				</a>
			</p>

			<?php if ( $thumbnail_id && 'square' === $shape ) : ?>
				<div class="wp-embed-featured-image square">
					<a href="<?php the_permalink(); ?>" target="_top">
						<?php echo wp_get_attachment_image( $thumbnail_id, $image_size ); ?>
					</a>
				</div>
			<?php endif; ?>

			<div class="wp-embed-excerpt"><?php the_excerpt_embed(); ?></div>

			<?php
			/**
			 * Print additional content after the embed excerpt.
			 *
			 * @since 4.4.0
			 */
			do_action( 'embed_content' );
			?>

			<div class="wp-embed-footer">
				<div class="wp-embed-site-title">
					<?php
					$site_title = sprintf(
						'<a href="%s" target="_top"><img src="%s" srcset="%s 2x" width="32" height="32" alt="" class="wp-embed-site-icon"/><span>%s</span></a>',
						esc_url( home_url() ),
						esc_url( get_site_icon_url( 32, includes_url( 'images/w-logo-blue.png' ) ) ),
						esc_url( get_site_icon_url( 64, includes_url( 'images/w-logo-blue.png' ) ) ),
						esc_html( get_bloginfo( 'name' ) )
					);

					/**
					 * Filter the site title HTML in the embed footer.
					 *
					 * @since 4.4.0
					 *
					 * @param string $site_title The site title HTML.
					 */
					echo apply_filters( 'embed_site_title_html', $site_title );
					?>
				</div>

				<div class="wp-embed-meta">
					<?php
					/**
					 * Print additional meta content in the embed template.
					 *
					 * @since 4.4.0
					 */
					do_action( 'embed_content_meta');
					?>
				</div>
			</div>
		</div>
		<?php
	endwhile;
else :
	?>
	<div class="wp-embed">
		<p class="wp-embed-heading"><?php _e( 'Oops! That embed can&#8217;t be found.' ); ?></p>

		<div class="wp-embed-excerpt">
			<p>
				<?php
				printf(
					/* translators: %s: a link to the embedded site */
					__( 'It looks like nothing was found at this location. Maybe try visiting %s directly?' ),
					'<strong><a href="' . esc_url( home_url() ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a></strong>'
				);
				?>
			</p>
		</div>

		<div class="wp-embed-footer">
			<div class="wp-embed-site-title">
				<?php
				$site_title = sprintf(
					'<a href="%s" target="_top"><img src="%s" srcset="%s 2x" width="32" height="32" alt="" class="wp-embed-site-icon"/><span>%s</span></a>',
					esc_url( home_url() ),
					esc_url( get_site_icon_url( 32, includes_url( 'images/w-logo-blue.png' ) ) ),
					esc_url( get_site_icon_url( 64, includes_url( 'images/w-logo-blue.png' ) ) ),
					esc_html( get_bloginfo( 'name' ) )
				);

				/** This filter is documented in wp-includes/embed-template.php */
				echo apply_filters( 'embed_site_title_html', $site_title );
				?>
			</div>
		</div>
	</div>
	<?php
endif;

/**
 * Print scripts or data before the closing body tag in the embed template.
 *
 * @since 4.4.0
 */
do_action( 'embed_footer' );
?>
</body>
</html>
