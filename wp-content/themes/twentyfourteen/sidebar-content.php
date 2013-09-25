<?php
/**
 * The Content Sidebar.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
if ( ! is_active_sidebar( 'sidebar-3' ) )
	return;
?>
<div id="content-sidebar" class="content-sidebar widget-area" role="complementary">
	<?php do_action( 'before_sidebar' ); ?>

	<?php dynamic_sidebar( 'sidebar-3' ); ?>
</div><!-- #content-sidebar -->
