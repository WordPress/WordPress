<?php
/**
 * This is the template that displays the custom logo.
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Twenty8teen
 */

add_filter( 'wp_get_attachment_image_attributes', 'twenty8teen_logo_add_widget_classes', 10, 3 );
the_custom_logo();
remove_filter( 'wp_get_attachment_image_attributes', 'twenty8teen_logo_add_widget_classes', 10 );
