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
	if ( has_custom_header() ) :
	?>

		<?php // Output the full custom header - video and/or image fallback. ?>
		<div class="custom-header-image">
			<?php the_custom_header_markup(); ?>
		</div>
		<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>

	<?php else : ?>

		<?php // Otherwise, show a blank header. ?>
		<div class="custom-header-simple">
			<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
		</div><!-- .custom-header-simple -->

	<?php endif; ?>

</div><!-- .custom-header -->
