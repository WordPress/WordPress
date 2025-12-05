<?php
/**
 * Reflection module
 *
 * @link https://contactform7.com/reflection/
 */


add_action( 'wpcf7_init', 'wpcf7_add_form_tag_reflection', 10, 0 );

/**
 * Registers reflection-related form-tag types.
 */
function wpcf7_add_form_tag_reflection() {
	wpcf7_add_form_tag( 'reflection',
		'wpcf7_reflection_form_tag_handler',
		array(
			'name-attr' => true,
			'display-block' => true,
			'not-for-mail' => true,
		)
	);

	wpcf7_add_form_tag( 'output',
		'wpcf7_output_form_tag_handler',
		array(
			'name-attr' => true,
			'not-for-mail' => true,
		)
	);
}


/**
 * The form-tag handler for the reflection type.
 */
function wpcf7_reflection_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$values = $tag->values ? $tag->values : array( '' );

	if ( ! wpcf7_get_validation_error( $tag->name ) ) {
		$hangover = array_filter( (array) wpcf7_get_hangover( $tag->name ) );

		if ( $hangover ) {
			$values = $hangover;
		}
	}

	$content = array_reduce(
		$values,
		static function ( $carry, $item ) use ( $tag ) {
			$output_tag = sprintf(
				'<output %1$s>%2$s</output>',
				wpcf7_format_atts( array(
					'name' => $tag->name,
					'data-default' => $item,
				) ),
				( '' !== $item ) ? esc_html( $item ) : '&nbsp;'
			);

			return $carry . $output_tag;
		},
		''
	);

	$html = sprintf(
		'<fieldset %1$s>%2$s</fieldset>',
		wpcf7_format_atts( array(
			'data-reflection-of' => $tag->name,
			'class' => $tag->get_class_option(
				wpcf7_form_controls_class( $tag->type )
			),
			'id' => $tag->get_id_option(),
		) ),
		$content
	);

	return $html;
}


/**
 * The form-tag handler for the output type.
 */
function wpcf7_output_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$value = (string) reset( $tag->values );

	if ( ! wpcf7_get_validation_error( $tag->name ) ) {
		$hangover = array_filter( (array) wpcf7_get_hangover( $tag->name ) );

		if ( $hangover ) {
			$value = (string) reset( $hangover );
		}
	}

	$html = sprintf(
		'<output %1$s>%2$s</output>',
		wpcf7_format_atts( array(
			'data-reflection-of' => $tag->name,
			'data-default' => $value,
			'name' => $tag->name,
			'class' => $tag->get_class_option(
				wpcf7_form_controls_class( $tag->type )
			),
			'id' => $tag->get_id_option(),
		) ),
		esc_html( $value )
	);

	return $html;
}
