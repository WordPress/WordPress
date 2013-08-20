<?php
/**
 * The Sidebar containing the main widget area.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>
<?php
if (   ! is_active_sidebar( 'sidebar-3' ) )
	return;
?>
<div id="supplementary">

	<?php if ( is_active_sidebar( 'sidebar-3' ) ) : ?>
	<div id="footer-sidebar" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-3' ); ?>
	</div><!-- #footer-sidebar -->
	<?php endif; ?>

</div><!-- #supplementary -->
