<?php
/**
 * Template part for displaying the post thumbnail as header image
 * @package Twenty8teen
 */
if ( ! is_singular() ) {
 	return;
}
$default = twenty8teen_default_booleans();
$class = 'header-image'
	. ( get_theme_mod( 'show_header_imagebehind', $default['show_header_imagebehind'] )
	? ' image-behind' : '' );

the_post_thumbnail( 'large',
	array( 'class' => twenty8teen_widget_get_classes( $class ) ) ); 
