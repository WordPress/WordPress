<?php
/**
 * The template containing the sidebar widget area
 * @package Twenty8teen
 */

if ( ! is_active_sidebar( 'side-widget-area' ) ) {
	return;
}
?>

<aside id="sidebar" <?php twenty8teen_area_classes( 'sidebar', 'widget-area' ); ?>>
	<?php dynamic_sidebar( 'side-widget-area' ); ?>
</aside><!-- #sidebar -->
