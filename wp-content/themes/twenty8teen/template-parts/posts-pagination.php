<?php
/**
 * Template part for displaying the pagination links
 * @package Twenty8teen
 */

 add_filter( 'navigation_markup_template', 'twenty8teen_nav_add_widget_classes', 10, 2 );
 the_posts_pagination( 'mid_size=3' );
 remove_filter( 'navigation_markup_template', 'twenty8teen_nav_add_widget_classes', 10 );
