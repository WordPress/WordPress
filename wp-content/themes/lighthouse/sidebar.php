<?php
/**
 * The sidebar containing the main widget area.
 *
 * Please browse readme.txt for credits and forking information
 * @package Lighthouse
 */
?>
<div id="secondary" class="col-md-3 sidebar widget-area" role="complementary">
    <?php do_action( 'before_sidebar' ); ?>
   <?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary .widget-area -->


