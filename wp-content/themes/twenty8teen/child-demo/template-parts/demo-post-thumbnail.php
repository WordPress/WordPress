<?php
/**
 * Template part for displaying the post thumbnail
 * @package Twenty8teen
 */

if ( is_singular() ) {
	the_post_thumbnail( 'thumbnail',
		array( 'class' => twenty8teen_widget_get_classes( 'democlass' ) ) );
}
