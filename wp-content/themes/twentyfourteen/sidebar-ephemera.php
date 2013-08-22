<?php
/**
 * A template to display recent post formatted posts.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>

<div id="ephemera" class="ephemera" role="complementary">
	<?php
		if ( ! dynamic_sidebar( 'sidebar-2' ) ) :
			foreach ( array( 'video', 'image', 'gallery', 'aside', 'link', 'quote' ) as $format ) :
				the_widget(
					'Twenty_Fourteen_Ephemera_Widget',
					array(
						'format' => $format,
					),
					array(
						'before_widget' => '<aside class="widget widget_twentyfourteen_ephemera">',
						'after_widget'  => '</aside>',
					)
				);
			endforeach;
		endif;
	?>
</div>
