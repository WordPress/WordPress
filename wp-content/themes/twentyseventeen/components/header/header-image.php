<?php
/**
 * Displays header image
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

?>
<div class="custom-header">
	<?php
	$header_image = get_header_image();

	// Check if Custom Header image has been added.
	if ( ! empty( $header_image ) ) : ?>

		<div class="custom-header-image" style="background-image: url(<?php echo esc_url( $header_image ); ?>)"></div>
		<?php get_template_part( 'components/header/site', 'branding' ); ?>

	<?php elseif ( twentyseventeen_is_frontpage() && has_post_thumbnail() ) :
		// If not, fall back to front page's featured image, only on the front page.
		$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
		$thumbnail_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'twentyseventeen-featured-image' );
		?>

		<div class="custom-header-image" style="background-image: url(<?php echo esc_url( $thumbnail_attributes[0] ); ?>)"></div>
		<?php get_template_part( 'components/header/site', 'branding' ); ?>

	<?php else : ?>
		<?php // Otherwise, show a blank header. ?>
		<div class="custom-header-simple">
			<?php get_template_part( 'components/header/site', 'branding' ); ?>
		</div><!-- .custom-header-simple -->

	<?php endif; ?>

</div><!-- .custom-header -->
