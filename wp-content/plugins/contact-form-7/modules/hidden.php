<?php

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_hidden', 10, 0 );

function wpcf7_add_form_tag_hidden() {
	wpcf7_add_form_tag( 'hidden',
		'wpcf7_hidden_form_tag_handler',
		array(
			'name-attr' => true,
			'display-hidden' => true,
		)
	);
}

function wpcf7_hidden_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$atts = array();

	$class = wpcf7_form_controls_class( $tag->type );
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	$value = (string) reset( $tag->values );
	$value = $tag->get_default_option( $value );
	$atts['value'] = $value;

	$atts['type'] = 'hidden';
	$atts['name'] = $tag->name;
	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<input %s />', $atts );
	return $html;
}
