<?php
/**
 * This is the template part that displays the site header image.
 * @package Twenty8teen
 */

$default = twenty8teen_default_booleans();
$class = 'header-image'
	. ( get_theme_mod( 'show_header_imagebehind', $default['show_header_imagebehind'] )
	? ' image-behind' : '' );

if ( has_header_image() ) {
	the_header_image_tag( array(
		'class' => twenty8teen_widget_get_classes( $class ) )
	);
}
else {
	$default = twenty8teen_default_identimages();
	$opt = get_theme_mod( 'show_header_identimage', $default['show_header_identimage'] );
	if ( 'none' !== $opt ) {
		$this_id = is_singular() ? get_permalink() : wp_unslash( $_SERVER['REQUEST_URI'] );
		$attr = twenty8teen_add_gradient( $this_id, array(
			'class' => twenty8teen_widget_get_classes( $class ),
			'src' => get_template_directory_uri() . '/images/clear.png',
			'alt' => '',
		), $opt );
		?>
		<img <?php twenty8teen_attributes( 'img', $attr ); ?> />
	<?php
	}
}
