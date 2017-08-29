<?php
/**
 * The sidebar containing the main widget area.
 *
 * Please browse readme.txt for credits and forking information
 * @package noteblog
 */
?>

<div id="secondary" class="col-md-3 sidebar widget-area" role="complementary">
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
<div class="secondary-inner">
		<?php do_action( 'noteblog_before_sidebar' ); ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div><!-- #secondary .widget-area -->
<?php endif; ?>
</div>


