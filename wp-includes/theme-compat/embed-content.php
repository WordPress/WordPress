<?php
/**
 * Contains the post embed content template part
 *
 * When a post is embedded in an iframe, this file is used to create the content template part
 * output if the active theme does not include an embed-content.php template.
 *
 * @package WordPress
 * @subpackage Theme_Compat
 * @since 4.5.0
 */
?>
	<div <?php post_class( 'wp-embed' ); ?>>
		<?php
		$thumbnail_id = 0;

		if ( has_post_thumbnail() ) {
			$thumbnail_id = get_post_thumbnail_id();
		}

		if ( 'attachment' === get_post_type() && wp_attachment_is_image() ) {
			$thumbnail_id = get_the_ID();
		}

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
		 * Filters the thumbnail image size for use in the embed template.
		 *
		 * @since 4.4.0
		 * @since 4.5.0 Added `$thumbnail_id` parameter.
		 *
		 * @param string $image_size   Thumbnail image size.
		 * @param int    $thumbnail_id Attachment ID.
		 */
		$image_size = apply_filters( 'embed_thumbnail_image_size', $image_size, $thumbnail_id );

		$shape = $measurements[0] / $measurements[1] >= 1.75 ? 'rectangular' : 'square';

		/**
		 * Filters the thumbnail shape for use in the embed template.
		 *
		 * Rectangular images are shown above the title while square images
		 * are shown next to the content.
		 *
		 * @since 4.4.0
		 * @since 4.5.0 Added `$thumbnail_id` parameter.
		 *
		 * @param string $shape        Thumbnail image shape. Either 'rectangular' or 'square'.
		 * @param int    $thumbnail_id Attachment ID.
		 */
		$shape = apply_filters( 'embed_thumbnail_image_shape', $shape, $thumbnail_id );

		if ( 'rectangular' === $shape ) : ?>
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

		<?php if ( 'square' === $shape ) : ?>
			<div class="wp-embed-featured-image square">
				<a href="<?php the_permalink(); ?>" target="_top">
					<?php echo wp_get_attachment_image( $thumbnail_id, $image_size ); ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="wp-embed-excerpt"><?php the_excerpt_embed(); ?></div>

		<?php
		/**
		 * Prints additional content after the embed excerpt.
		 *
		 * @since 4.4.0
		 */
		do_action( 'embed_content' );
		?>

		<div class="wp-embed-footer">
			<?php the_embed_site_title() ?>

			<div class="wp-embed-meta">
				<?php
				/**
				 * Prints additional meta content in the embed template.
				 *
				 * @since 4.4.0
				 */
				do_action( 'embed_content_meta');
				?>
			</div>
		</div>
	</div>
<?php
