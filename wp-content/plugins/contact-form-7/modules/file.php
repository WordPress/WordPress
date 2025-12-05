<?php
/**
** A base module for [file] and [file*]
**/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_file', 10, 0 );

function wpcf7_add_form_tag_file() {
	wpcf7_add_form_tag( array( 'file', 'file*' ),
		'wpcf7_file_form_tag_handler',
		array(
			'name-attr' => true,
			'file-uploading' => true,
		)
	);
}

function wpcf7_file_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['capture'] = $tag->get_option( 'capture', '(user|environment)', true );
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$atts['accept'] = wpcf7_acceptable_filetypes(
		$tag->get_option( 'filetypes' ), 'attr'
	);

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

	$atts['type'] = 'file';
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
	'wpcf7_swv_add_file_rules',
	10, 2
);

function wpcf7_swv_add_file_rules( $schema, $contact_form ) {
	$tags = $contact_form->scan_form_tags( array(
		'basetype' => array( 'file' ),
	) );

	foreach ( $tags as $tag ) {
		if ( $tag->is_required() ) {
			$schema->add_rule(
				wpcf7_swv_create_rule( 'requiredfile', array(
					'field' => $tag->name,
					'error' => wpcf7_get_message( 'invalid_required' ),
				) )
			);
		}

		$schema->add_rule(
			wpcf7_swv_create_rule( 'file', array(
				'field' => $tag->name,
				'accept' => explode( ',', wpcf7_acceptable_filetypes(
					$tag->get_option( 'filetypes' ), 'attr'
				) ),
				'error' => wpcf7_get_message( 'upload_file_type_invalid' ),
			) )
		);

		$schema->add_rule(
			wpcf7_swv_create_rule( 'maxfilesize', array(
				'field' => $tag->name,
				'threshold' => $tag->get_limit_option(),
				'error' => wpcf7_get_message( 'upload_file_too_large' ),
			) )
		);
	}
}


add_filter( 'wpcf7_mail_tag_replaced_file', 'wpcf7_file_mail_tag', 10, 4 );
add_filter( 'wpcf7_mail_tag_replaced_file*', 'wpcf7_file_mail_tag', 10, 4 );

function wpcf7_file_mail_tag( $replaced, $submitted, $html, $mail_tag ) {
	$submission = WPCF7_Submission::get_instance();
	$uploaded_files = $submission->uploaded_files();
	$name = $mail_tag->field_name();

	if ( ! empty( $uploaded_files[$name] ) ) {
		$paths = (array) $uploaded_files[$name];
		$paths = array_map( 'wp_basename', $paths );

		$replaced = wpcf7_flat_join( $paths, array(
			'separator' => wp_get_list_item_separator(),
		) );
	}

	return $replaced;
}


/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_file', 50, 0 );

function wpcf7_add_tag_generator_file() {
	$tag_generator = WPCF7_TagGenerator::get_instance();

	$tag_generator->add( 'file', __( 'file', 'contact-form-7' ),
		'wpcf7_tag_generator_file',
		array( 'version' => '2' )
	);
}

function wpcf7_tag_generator_file( $contact_form, $options ) {
	$field_types = array(
		'file' => array(
			'display_name' => __( 'File uploading field', 'contact-form-7' ),
			'heading' => __( 'File uploading field form-tag generator', 'contact-form-7' ),
			'description' => __( 'Generates a form-tag for a <a href="https://contactform7.com/file-uploading-and-attachment/">file uploading field</a>.', 'contact-form-7' ),
		),
	);

	$tgg = new WPCF7_TagGeneratorGenerator( $options['content'] );

	$formatter = new WPCF7_HTMLFormatter();

	$formatter->append_start_tag( 'header', array(
		'class' => 'description-box',
	) );

	$formatter->append_start_tag( 'h3' );

	$formatter->append_preformatted(
		esc_html( $field_types['file']['heading'] )
	);

	$formatter->end_tag( 'h3' );

	$formatter->append_start_tag( 'p' );

	$formatter->append_preformatted(
		wp_kses_data( $field_types['file']['description'] )
	);

	$formatter->end_tag( 'header' );

	$formatter->append_start_tag( 'div', array(
		'class' => 'control-box',
	) );

	$formatter->call_user_func( static function () use ( $tgg, $field_types ) {
		$tgg->print( 'field_type', array(
			'with_required' => true,
			'select_options' => array(
				'file' => $field_types['file']['display_name'],
			),
		) );

		$tgg->print( 'field_name' );

		$tgg->print( 'class_attr' );
	} );

	$formatter->append_start_tag( 'fieldset' );

	$formatter->append_start_tag( 'legend', array(
		'id' => $tgg->ref( 'filetypes-option-legend' ),
	) );

	$formatter->append_preformatted(
		esc_html( __( 'Acceptable file types', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'legend' );

	$formatter->append_start_tag( 'label' );

	$formatter->append_start_tag( 'span', array(
		'id' => $tgg->ref( 'filetypes-option-description' ),
	) );

	$formatter->append_preformatted(
		esc_html( __( 'Pipe-separated file types list. You can use file extensions and MIME types.', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'span' );

	$formatter->append_start_tag( 'br' );

	$formatter->append_start_tag( 'input', array(
		'type' => 'text',
		'pattern' => '[0-9a-z*\/\|]*',
		'value' => 'audio/*|video/*|image/*',
		'aria-labelledby' => $tgg->ref( 'filetypes-option-legend' ),
		'aria-describedby' => $tgg->ref( 'filetypes-option-description' ),
		'data-tag-part' => 'option',
		'data-tag-option' => 'filetypes:',
	) );

	$formatter->end_tag( 'fieldset' );

	$formatter->append_start_tag( 'fieldset' );

	$formatter->append_start_tag( 'legend', array(
		'id' => $tgg->ref( 'limit-option-legend' ),
	) );

	$formatter->append_preformatted(
		esc_html( __( 'File size limit', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'legend' );

	$formatter->append_start_tag( 'label' );

	$formatter->append_start_tag( 'span', array(
		'id' => $tgg->ref( 'limit-option-description' ),
	) );

	$formatter->append_preformatted(
		esc_html( __( 'In bytes. You can use kb and mb suffixes.', 'contact-form-7' ) )
	);

	$formatter->end_tag( 'span' );

	$formatter->append_start_tag( 'br' );

	$formatter->append_start_tag( 'input', array(
		'type' => 'text',
		'pattern' => '[1-9][0-9]*([kKmM]?[bB])?',
		'value' => '1mb',
		'aria-labelledby' => $tgg->ref( 'limit-option-legend' ),
		'aria-describedby' => $tgg->ref( 'limit-option-description' ),
		'data-tag-part' => 'option',
		'data-tag-option' => 'limit:',
	) );

	$formatter->end_tag( 'fieldset' );

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
