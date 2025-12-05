<?php
/**
** A base module for [checkbox], [checkbox*], and [radio]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_checkbox', 10, 0 );

function wpcf7_add_form_tag_checkbox() {
	wpcf7_add_form_tag( array( 'checkbox', 'checkbox*', 'radio' ),
		'wpcf7_checkbox_form_tag_handler',
		array(
			'name-attr' => true,
			'selectable-values' => true,
			'multiple-controls-container' => true,
		)
	);
}

function wpcf7_checkbox_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$label_first = $tag->has_option( 'label_first' );
	$use_label_element = $tag->has_option( 'use_label_element' );
	$exclusive = $tag->has_option( 'exclusive' );
	$free_text = $tag->has_option( 'free_text' );
	$multiple = false;

	if ( 'checkbox' === $tag->basetype ) {
		$multiple = ! $exclusive;
	} else { // radio
		$exclusive = false;
	}

	if ( $exclusive ) {
		$class .= ' wpcf7-exclusive-checkbox';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	if ( $validation_error ) {
		$atts['aria-describedby'] = wpcf7_get_validation_error_reference(
			$tag->name
		);
	}

	$tabindex = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( false !== $tabindex ) {
		$tabindex = (int) $tabindex;
	}

	$html = '';
	$count = 0;

	if ( $data = (array) $tag->get_data_option() ) {
		if ( $free_text ) {
			$tag->values = array_merge(
				array_slice( $tag->values, 0, -1 ),
				array_values( $data ),
				array_slice( $tag->values, -1 )
			);

			$tag->labels = array_merge(
				array_slice( $tag->labels, 0, -1 ),
				array_values( $data ),
				array_slice( $tag->labels, -1 )
			);
		} else {
			$tag->values = array_merge( $tag->values, array_values( $data ) );
			$tag->labels = array_merge( $tag->labels, array_values( $data ) );
		}
	}

	$values = $tag->values;
	$labels = $tag->labels;

	$default_choice = $tag->get_default_option( null, array(
		'multiple' => $multiple,
	) );

	$hangover = wpcf7_get_hangover( $tag->name, $multiple ? array() : '' );

	foreach ( $values as $key => $value ) {
		if ( $hangover ) {
			$checked = in_array( $value, (array) $hangover, true );
		} else {
			$checked = in_array( $value, (array) $default_choice, true );
		}

		if ( isset( $labels[$key] ) ) {
			$label = $labels[$key];
		} else {
			$label = $value;
		}

		$item_atts = array(
			'type' => $tag->basetype,
			'name' => $tag->name . ( $multiple ? '[]' : '' ),
			'value' => $value,
			'checked' => $checked,
			'tabindex' => $tabindex,
		);

		$item_atts = wpcf7_format_atts( $item_atts );

		if ( $label_first ) { // put label first, input last
			$item = sprintf(
				'<span class="wpcf7-list-item-label">%1$s</span><input %2$s />',
				esc_html( $label ),
				$item_atts
			);
		} else {
			$item = sprintf(
				'<input %2$s /><span class="wpcf7-list-item-label">%1$s</span>',
				esc_html( $label ),
				$item_atts
			);
		}

		if ( $use_label_element ) {
			$item = '<label>' . $item . '</label>';
		}

		if ( false !== $tabindex and 0 < $tabindex ) {
			$tabindex += 1;
		}

		$class = 'wpcf7-list-item';
		$count += 1;

		if ( 1 === $count ) {
			$class .= ' first';
		}

		if ( count( $values ) === $count ) { // last round
			$class .= ' last';

			if ( $free_text ) {
				$free_text_name = sprintf( '_wpcf7_free_text_%s', $tag->name );

				$free_text_atts = array(
					'type' => 'text',
					'name' => $free_text_name,
					'class' => 'wpcf7-free-text',
					'tabindex' => $tabindex,
				);

				if ( wpcf7_is_posted() ) {
					$free_text_atts['value'] = wpcf7_superglobal_post( $free_text_name );
				}

				$item .= sprintf(
					' <input %s />',
					wpcf7_format_atts( $free_text_atts )
				);

				$class .= ' has-free-text';
			}
		}

		$item = '<span class="' . esc_attr( $class ) . '">' . $item . '</span>';
		$html .= $item;
	}

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap" data-name="%1$s"><span %2$s>%3$s</span>%4$s</span>',
		esc_attr( $tag->name ),
		wpcf7_format_atts( $atts ),
		$html,
		$validation_error
	);

	return $html;
}


add_action(
	'wpcf7_swv_create_schema',
	'wpcf7_swv_add_checkbox_rules',
	10, 2
);

function wpcf7_swv_add_checkbox_rules( $schema, $contact_form ) {
	$tags = $contact_form->scan_form_tags( array(
		'basetype' => array( 'checkbox', 'radio' ),
	) );

	foreach ( $tags as $tag ) {
		if ( $tag->is_required() or 'radio' === $tag->type ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'required', array(
					'field' => $tag->name,
					'error' => wpcf7_get_message( 'invalid_required' ),
				) )
			);
		}

		if ( 'radio' === $tag->type or $tag->has_option( 'exclusive' ) ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'maxitems', array(
					'field' => $tag->name,
					'threshold' => 1,
					'error' => $contact_form->filter_message(
						__( 'Too many items are selected.', 'contact-form-7' )
					),
				) )
			);
		}
	}
}


add_action(
	'wpcf7_swv_create_schema',
	'wpcf7_swv_add_checkbox_enum_rules',
	20, 2
);

function wpcf7_swv_add_checkbox_enum_rules( $schema, $contact_form ) {
	$tags = $contact_form->scan_form_tags( array(
		'basetype' => array( 'checkbox', 'radio' ),
	) );

	$values = array_reduce(
		$tags,
		function ( $values, $tag ) {
			if ( $tag->has_option( 'free_text' ) ) {
				$values[$tag->name] = 'free_text';
			}

			if (
				isset( $values[$tag->name] ) and
				! is_array( $values[$tag->name] ) // Maybe 'free_text'
			) {
				return $values;
			}

			if ( ! isset( $values[$tag->name] ) ) {
				$values[$tag->name] = array();
			}

			$tag_values = array_merge(
				(array) $tag->values,
				(array) $tag->get_data_option()
			);

			$values[$tag->name] = array_merge(
				$values[$tag->name],
				$tag_values
			);

			return $values;
		},
		array()
	);

	foreach ( $values as $field => $field_values ) {
		if ( ! is_array( $field_values ) ) { // Maybe 'free_text'
			continue;
		}

		$field_values = array_map(
			static function ( $value ) {
				return html_entity_decode(
					(string) $value,
					ENT_QUOTES | ENT_HTML5,
					'UTF-8'
				);
			},
			$field_values
		);

		$field_values = array_filter(
			array_unique( $field_values ),
			static function ( $value ) {
				return '' !== $value;
			}
		);

		$schema->add_rule(
			wpcf7_swv_create_rule( 'enum', array(
				'field' => $field,
				'accept' => array_values( $field_values ),
				'error' => $contact_form->filter_message(
					__( 'Undefined value was submitted through this field.', 'contact-form-7' )
				),
			) )
		);
	}
}


add_filter( 'wpcf7_posted_data_checkbox',
	'wpcf7_posted_data_checkbox',
	10, 3
);

add_filter( 'wpcf7_posted_data_checkbox*',
	'wpcf7_posted_data_checkbox',
	10, 3
);

add_filter( 'wpcf7_posted_data_radio',
	'wpcf7_posted_data_checkbox',
	10, 3
);

function wpcf7_posted_data_checkbox( $value, $value_orig, $form_tag ) {
	if ( $form_tag->has_option( 'free_text' ) ) {
		$value = (array) $value;

		$free_text_name = sprintf( '_wpcf7_free_text_%s', $form_tag->name );
		$free_text = wpcf7_superglobal_post( $free_text_name );

		$last_val = array_pop( $value );

		if ( isset( $last_val ) ) {
			$last_val = sprintf( '%s %s', $last_val, $free_text );
			$value[] = trim( $last_val );
		}
	}

	return $value;
}


/* Tag generator */

add_action( 'wpcf7_admin_init',
	'wpcf7_add_tag_generator_checkbox_and_radio', 30, 0 );

function wpcf7_add_tag_generator_checkbox_and_radio() {
	$tag_generator = WPCF7_TagGenerator::get_instance();

	$basetypes = array(
		'checkbox' => __( 'checkboxes', 'contact-form-7' ),
		'radio' => __( 'radio buttons', 'contact-form-7' ),
	);

	foreach ( $basetypes as $id => $title ) {
		$tag_generator->add( $id, $title,
			'wpcf7_tag_generator_checkbox',
			array( 'version' => '2' )
		);
	}
}

function wpcf7_tag_generator_checkbox( $contact_form, $options ) {
	$field_types = array(
		'checkbox' => array(
			'display_name' => __( 'Checkboxes', 'contact-form-7' ),
			'heading' => __( 'Checkboxes form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a group of <a href="https://contactform7.com/checkboxes-radio-buttons-and-menus/">checkboxes</a>.', 'contact-form-7' ),
		),
		'radio' => array(
			'display_name' => __( 'Radio buttons', 'contact-form-7' ),
			'heading' => __( 'Radio buttons form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a group of <a href="https://contactform7.com/checkboxes-radio-buttons-and-menus/">radio buttons</a>.', 'contact-form-7' ),
		),
	);

	$basetype = $options['id'];

	if ( ! in_array( $basetype, array_keys( $field_types ), true ) ) {
		$basetype = 'checkbox';
	}

	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

	$formatter = new WPCF7_HTMLFormatter();

	$formatter->append_start_tag( 'header', array(
		'class' => 'description-box',
	) );

	$formatter->append_start_tag( 'h3' );

	$formatter->append_preformatted(
		esc_html( $field_types[$basetype]['heading'] )
	);

	$formatter->end_tag( 'h3' );

	$formatter->append_start_tag( 'p' );

	$formatter->append_preformatted(
		wp_kses_data( $field_types[$basetype]['description'] )
	);

	$formatter->end_tag( 'header' );

	$formatter->append_start_tag( 'div', array(
		'class' => 'control-box',
	) );

	$formatter->call_user_func( static function () use ( $tgg, $field_types, $basetype ) {
		$tgg->print( 'field_type', array(
			'with_required' => 'checkbox' === $basetype,
			'select_options' => array(
				$basetype => $field_types[$basetype]['display_name'],
			),
		) );

		$tgg->print( 'field_name' );

		$tgg->print( 'class_attr' );

		$tgg->print( 'selectable_values', array(
			'use_label_element' => 'checked',
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
