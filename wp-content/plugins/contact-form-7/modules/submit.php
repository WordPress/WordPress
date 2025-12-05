<?php
/**
** A base module for [submit]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_submit', 10, 0 );

function wpcf7_add_form_tag_submit() {
	wpcf7_add_form_tag( 'submit', 'wpcf7_submit_form_tag_handler' );
}

function wpcf7_submit_form_tag_handler( $tag ) {
	$class = wpcf7_form_controls_class( $tag->type, 'has-spinner' );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value = __( 'Send', 'contact-form-7' );
	}

	$atts['type'] = 'submit';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<input %1$s />', $atts );

	return $html;
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_submit', 55, 0 );

function wpcf7_add_tag_generator_submit() {
	$tag_generator = WPCF7_TagGenerator::get_instance();

	$tag_generator->add( 'submit', __( 'submit', 'contact-form-7' ),
		'wpcf7_tag_generator_submit',
		array( 'version' => '2' )
	);
}

function wpcf7_tag_generator_submit( $contact_form, $options ) {
	$field_types = array(
		'submit' => array(
			'display_name' => __( 'Submit button', 'contact-form-7' ),
			'heading' => __( 'Submit button form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a <a href="https://contactform7.com/submit-button/">submit button</a>.', 'contact-form-7' ),
		),
	);

	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

	$formatter = new WPCF7_HTMLFormatter();

	$formatter->append_start_tag( 'header', array(
		'class' => 'description-box',
	) );

	$formatter->append_start_tag( 'h3' );

	$formatter->append_preformatted(
		esc_html( $field_types['submit']['heading'] )
	);

	$formatter->end_tag( 'h3' );

	$formatter->append_start_tag( 'p' );

	$formatter->append_preformatted(
		wp_kses_data( $field_types['submit']['description'] )
	);

	$formatter->end_tag( 'header' );

	$formatter->append_start_tag( 'div', array(
		'class' => 'control-box',
	) );

	$formatter->call_user_func( static function () use ( $tgg, $field_types ) {
		$tgg->print( 'field_type', array(
			'select_options' => array(
				'submit' => $field_types['submit']['display_name'],
			),
		) );

		$tgg->print( 'class_attr' );

		$tgg->print( 'default_value', array(
			'title' => __( 'Label', 'contact-form-7' ),
		) );
	} );

	$formatter->end_tag( 'div' );

	$formatter->append_start_tag( 'footer', array(
		'class' => 'insert-box',
	) );

	$formatter->call_user_func( static function () use ( $tgg, $field_types ) {
		$tgg->print( 'insert_box_content' );
	} );

	$formatter->print();
}
