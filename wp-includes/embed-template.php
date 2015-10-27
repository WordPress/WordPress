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

wp_enqueue_style( 'open-sans' );

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
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
			if ( is_array( $meta ) ) {
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
					printf(
						'<a href="%s" target="_top"><img src="%s" srcset="%s 2x" width="32" height="32" alt="" class="wp-embed-site-icon"/><span>%s</span></a>',
						esc_url( home_url() ),
						esc_url( get_site_icon_url( 32, admin_url( 'images/w-logo-blue.png' ) ) ),
						esc_url( get_site_icon_url( 64, admin_url( 'images/w-logo-blue.png' ) ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
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
					<?php if ( get_comments_number() || comments_open() ) : ?>
						<div class="wp-embed-comments">
							<a href="<?php comments_link(); ?>" target="_top">
								<span class="dashicons dashicons-admin-comments"></span>
								<?php
								printf(
									_n(
										'%s <span class="screen-reader-text">Comment</span>',
										'%s <span class="screen-reader-text">Comments</span>',
										get_comments_number()
									),
									number_format_i18n( get_comments_number() )
								);
								?>
							</a>
						</div>
					<?php endif; ?>
					<div class="wp-embed-share">
						<button type="button" class="wp-embed-share-dialog-open"
						        aria-label="<?php esc_attr_e( 'Open sharing dialog' ); ?>">
							<span class="dashicons dashicons-share"></span>
						</button>
					</div>
				</div>
			</div>
			<div class="wp-embed-share-dialog hidden">
				<div class="wp-embed-share-dialog-content">
					<div class="wp-embed-share-dialog-text">
						<ul class="wp-embed-share-tabs" role="tablist">
							<li id="wp-embed-share-tab-button-wordpress" class="wp-embed-share-tab-button" role="presentation">
								<button role="tab" aria-controls="wp-embed-share-tab-wordpress" aria-selected="true" tabindex="0"><?php esc_html_e( 'WordPress Embed' ); ?></button>
							</li>
							<li id="wp-embed-share-tab-button-embed" class="wp-embed-share-tab-button" role="presentation">
								<button role="tab" aria-controls="wp-embed-share-tab-html" aria-selected="false" tabindex="-1"><?php esc_html_e( 'HTML Embed' ); ?></button>
							</li>
						</ul>
						<div id="wp-embed-share-tab-wordpress" class="wp-embed-share-tab" role="tabpanel" aria-labelledby="wp-embed-share-tab-button-wordpress" aria-hidden="false">
							<input type="text" value="<?php the_permalink(); ?>" class="wp-embed-share-input" tabindex="0" readonly/>

							<p class="wp-embed-share-description">
								<?php _e( 'Copy and paste this URL into your WordPress site to embed' ); ?>
							</p>
						</div>
						<div id="wp-embed-share-tab-html" class="wp-embed-share-tab" role="tabpanel" aria-labelledby="wp-embed-share-tab-button-html" aria-hidden="true">
							<textarea class="wp-embed-share-input" tabindex="0" readonly><?php echo esc_textarea( get_post_embed_html( null, 600, 400 ) ); ?></textarea>

							<p class="wp-embed-share-description">
								<?php _e( 'Copy and paste this code into your site to embed' ); ?>
							</p>
						</div>
					</div>

					<button type="button" class="wp-embed-share-dialog-close" aria-label="<?php esc_attr_e( 'Close sharing dialog' ); ?>">
						<span class="dashicons dashicons-no"></span>
					</button>
				</div>
			</div>
		</div>
		<?php
	endwhile;
else :
	?>
	<div class="wp-embed">
		<p class="wp-embed-heading"><?php _e( 'Page not found' ); ?></p>

		<div class="wp-embed-excerpt">
			<p><?php _e( 'Error 404! The requested content was not found.' ) ?></p>
		</div>

		<div class="wp-embed-footer">
			<div class="wp-embed-site-title">
				<?php
				printf(
					'<a href="%s" target="_top"><img src="%s" srcset="%s 2x" width="32" height="32" alt="" class="wp-embed-site-icon"/><span>%s</span></a>',
					esc_url( home_url() ),
					esc_url( get_site_icon_url( 32, admin_url( 'images/w-logo-blue.png' ) ) ),
					esc_url( get_site_icon_url( 64, admin_url( 'images/w-logo-blue.png' ) ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
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
