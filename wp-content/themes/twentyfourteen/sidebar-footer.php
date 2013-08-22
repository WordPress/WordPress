<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

if ( ! is_active_sidebar( 'sidebar-4' ) )
	return;
?>

<div id="supplementary">
	<div id="footer-sidebar" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-4' ); ?>
	</div><!-- #footer-sidebar -->
</div><!-- #supplementary -->
