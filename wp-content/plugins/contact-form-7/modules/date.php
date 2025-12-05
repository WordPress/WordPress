<?php
/**
** A base module for the following types of tags:
** 	[date] and [date*]		# Date
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_date', 10, 0 );

function wpcf7_add_form_tag_date() {
	wpcf7_add_form_tag( array( 'date', 'date*' ),
		'wpcf7_date_form_tag_handler',
		array(
			'name-attr' => true,
		)
	);
}

function wpcf7_date_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	$class .= ' wpcf7-validates-as-date';

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
	$atts['min'] = $tag->get_date_option( 'min' );
	$atts['max'] = $tag->get_date_option( 'max' );
	$atts['step'] = $tag->get_option( 'step', 'int', true );
	$atts['readonly'] = $tag->has_option( 'readonly' );
	$atts['autocomplete'] = $tag->get_autocomplete_option();

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	if ( $validation_error ) {
		$atts['aria-invalid'] = 'true';
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	} else {
		$atts['aria-invalid'] = 'false';
	}

	$value = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' ) or $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option( $value );

	if ( $value ) {
		$datetime_obj = date_create_immutable(
			preg_replace( '/[_]+/', ' ', $value ),
			wp_timezone()
		);

		if ( $datetime_obj ) {
			$value = $datetime_obj->format( 'Y-m-d' );
		}
	}

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['value'] = $value;
	$atts['type'] = $tag->basetype;
	$atts['name'] = $tag->name;

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><input %2$s />%3$s</span>',
		esc_attr( $tag->name ),
		wpcf7_format_atts( $atts ),
		$validation_error
	);

	return $html;
}


add_action(
	'wpcf7_swv_create_schema',
	'wpcf7_swv_add_date_rules',
	10, 2
);

function wpcf7_swv_add_date_rules( $schema, $contact_form ) {
	$tags = $contact_form->scan_form_tags( array(
		'basetype' => array( 'date' ),
	) );

	foreach ( $tags as $tag ) {
		if ( $tag->is_required() ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'required', array(
					'field' => $tag->name,
					'error' => wpcf7_get_message( 'invalid_required' ),
				) )
			);
		}

		$schema->add_rule(
			wpcf7_swv_create_rule( 'date', array(
				'field' => $tag->name,
				'error' => wpcf7_get_message( 'invalid_date' ),
			) )
		);

		$min = $tag->get_date_option( 'min' );
		$max = $tag->get_date_option( 'max' );

		if ( false !== $min ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'mindate', array(
					'field' => $tag->name,
					'threshold' => $min,
					'error' => wpcf7_get_message( 'date_too_early' ),
				) )
			);
		}

		if ( false !== $max ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'maxdate', array(
					'field' => $tag->name,
					'threshold' => $max,
					'error' => wpcf7_get_message( 'date_too_late' ),
				) )
			);
		}
	}
}


/* Messages */

add_filter( 'wpcf7_messages', 'wpcf7_date_messages', 10, 1 );

function wpcf7_date_messages( $messages ) {
	return array_merge( $messages, array(
		'invalid_date' => array(
			'description' => __( 'Date format that the sender entered is invalid', 'contact-form-7' ),
			'default' => __( 'Please enter a date in YYYY-MM-DD format.', 'contact-form-7' ),
		),

		'date_too_early' => array(
			'description' => __( 'Date is earlier than minimum limit', 'contact-form-7' ),
			'default' => __( 'This field has a too early date.', 'contact-form-7' ),
		),

		'date_too_late' => array(
			'description' => __( 'Date is later than maximum limit', 'contact-form-7' ),
			'default' => __( 'This field has a too late date.', 'contact-form-7' ),
		),
	) );
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_date', 19, 0 );

function wpcf7_add_tag_generator_date() {
	$tag_generator = WPCF7_TagGenerator::get_instance();

	$tag_generator->add( 'date', __( 'date', 'contact-form-7' ),
		'wpcf7_tag_generator_date',
		array( 'version' => '2' )
	);
}

function wpcf7_tag_generator_date( $contact_form, $options ) {
	$field_types = array(
		'date' => array(
			'display_name' => __( 'Date field', 'contact-form-7' ),
			'heading' => __( 'Date field form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a <a href="https://contactform7.com/date-field/">date input field</a>.', 'contact-form-7' ),
		),
	);

	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

	$formatter = new WPCF7_HTMLFormatter();

	$formatter->append_start_tag( 'header', array(
		'class' => 'description-box',
	) );

	$formatter->append_start_tag( 'h3' );

	$formatter->append_preformatted(
		esc_html( $field_types['date']['heading'] )
	);

	$formatter->end_tag( 'h3' );

	$formatter->append_start_tag( 'p' );

	$formatter->append_preformatted(
		wp_kses_data( $field_types['date']['description'] )
	);

	$formatter->end_tag( 'header' );

	$formatter->append_start_tag( 'div', array(
		'class' => 'control-box',
	) );

	$formatter->call_user_func( static function () use ( $tgg, $field_types ) {
		$tgg->print( 'field_type', array(
			'with_required' => true,
			'select_options' => array(
				'date' => $field_types['date']['display_name'],
			),
		) );

		$tgg->print( 'field_name' );

		$tgg->print( 'class_attr' );

		$tgg->print( 'min_max', array(
			'type' => 'date',
			'title' => __( 'Range', 'contact-form-7' ),
			'min_option' => 'min:',
			'max_option' => 'max:',
		) );

		$tgg->print( 'default_value', array(
			'type' => 'date',
			'with_placeholder' => false,
		) );
	} );

	$formatter->end_tag( 'div' );

	$formatter->append_start_tag( 'footer', array(
		'class' => 'insert-box',
	) );

	$formatter->call_user_func( static function () use ( $tgg, $field_types ) {
		$tgg->print( 'insert_box_content' );

		$tgg->print( 'mail_tag_tip' );
	} );

	$formatter->print();
}
