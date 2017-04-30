<?php
/**
** A base module for [textarea] and [textarea*]
**/

/* Shortcode handler */

add_action( 'wpcf7_init', 'wpcf7_add_shortcode_textarea' );

function wpcf7_add_shortcode_textarea() {
	wpcf7_add_shortcode( array( 'textarea', 'textarea*' ),
		'wpcf7_textarea_shortcode_handler', true );
}

function wpcf7_textarea_shortcode_handler( $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error )
		$class .= ' wpcf7-not-valid';

	$atts = array();

	$atts['cols'] = $tag->get_cols_option( '40' );
	$atts['rows'] = $tag->get_rows_option( '10' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	if ( $tag->has_option( 'readonly' ) )
		$atts['readonly'] = 'readonly';

	if ( $tag->is_required() )
		$atts['aria-required'] = 'true';

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset( $tag->values );

	if ( '' !== $tag->content )
		$value = $tag->content;

	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['name'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><textarea %2$s>%3$s</textarea>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts,
		esc_textarea( $value ), $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_textarea', 'wpcf7_textarea_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_textarea*', 'wpcf7_textarea_validation_filter', 10, 2 );

function wpcf7_textarea_validation_filter( $result, $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	$type = $tag->type;
	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? (string) $_POST[$name] : '';

	if ( 'textarea*' == $type ) {
		if ( '' == $value ) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
		}
	}

	if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
		$result['idref'][$name] = $id;
	}

	return $result;
}


/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_textarea', 20 );

function wpcf7_add_tag_generator_textarea() {
	if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
		return;

	wpcf7_add_tag_generator( 'textarea', __( 'Text area', 'contact-form-7' ),
		'wpcf7-tg-pane-textarea', 'wpcf7_tg_pane_textarea' );
}

function wpcf7_tg_pane_textarea( $contact_form ) {
?>
<div id="wpcf7-tg-pane-textarea" class="hidden">
<form action="">
<table>
<tr><td><input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'contact-form-7' ) ); ?></td></tr>
<tr><td><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>
</table>

<table>
<tr>
<td><code>id</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="id" class="idvalue oneline option" /></td>

<td><code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="class" class="classvalue oneline option" /></td>
</tr>

<tr>
<td><code>cols</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="cols" class="numeric oneline option" min="1" /></td>

<td><code>rows</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="rows" class="numeric oneline option" min="1" /></td>
</tr>

<tr>
<td><code>maxlength</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="maxlength" class="numeric oneline option" min="1" /></td>
</tr>

<tr>
<td><?php echo esc_html( __( 'Default value', 'contact-form-7' ) ); ?> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br /><input type="text" name="values" class="oneline" /></td>

<td>
<br /><input type="checkbox" name="placeholder" class="option" />&nbsp;<?php echo esc_html( __( 'Use this text as placeholder?', 'contact-form-7' ) ); ?>
</td>
</tr>
</table>

<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br /><input type="text" name="textarea" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>

<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'contact-form-7' ) ); ?><br /><input type="text" class="mail-tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>
</form>
</div>
<?php
}

?>