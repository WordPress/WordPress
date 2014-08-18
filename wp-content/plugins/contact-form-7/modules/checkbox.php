<?php
/**
** A base module for [checkbox], [checkbox*], and [radio]
**/

/* Shortcode handler */

add_action( 'wpcf7_init', 'wpcf7_add_shortcode_checkbox' );

function wpcf7_add_shortcode_checkbox() {
	wpcf7_add_shortcode( array( 'checkbox', 'checkbox*', 'radio' ), 
		'wpcf7_checkbox_shortcode_handler', true );
}

function wpcf7_checkbox_shortcode_handler( $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( $validation_error )
		$class .= ' wpcf7-not-valid';

	$label_first = $tag->has_option( 'label_first' );
	$use_label_element = $tag->has_option( 'use_label_element' );
	$exclusive = $tag->has_option( 'exclusive' );
	$free_text = $tag->has_option( 'free_text' );
	$multiple = false;

	if ( 'checkbox' == $tag->basetype )
		$multiple = ! $exclusive;
	else // radio
		$exclusive = false;

	if ( $exclusive )
		$class .= ' wpcf7-exclusive-checkbox';

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();

	$tabindex = $tag->get_option( 'tabindex', 'int', true );

	if ( false !== $tabindex )
		$tabindex = absint( $tabindex );

	$defaults = array();

	if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) )
		$defaults = explode( '_', $matches[1] );

	$html = '';
	$count = 0;

	$values = (array) $tag->values;
	$labels = (array) $tag->labels;

	if ( $data = (array) $tag->get_data_option() ) {
		if ( $free_text ) {
			$values = array_merge(
				array_slice( $values, 0, -1 ),
				array_values( $data ),
				array_slice( $values, -1 ) );
			$labels = array_merge(
				array_slice( $labels, 0, -1 ),
				array_values( $data ),
				array_slice( $labels, -1 ) );
		} else {
			$values = array_merge( $values, array_values( $data ) );
			$labels = array_merge( $labels, array_values( $data ) );
		}
	}

	$hangover = wpcf7_get_hangover( $tag->name, $multiple ? array() : '' );

	foreach ( $values as $key => $value ) {
		$class = 'wpcf7-list-item';

		$checked = false;

		if ( $hangover ) {
			if ( $multiple ) {
				$checked = in_array( esc_sql( $value ), (array) $hangover );
			} else {
				$checked = ( $hangover == esc_sql( $value ) );
			}
		} else {
			$checked = in_array( $key + 1, (array) $defaults );
		}

		if ( isset( $labels[$key] ) )
			$label = $labels[$key];
		else
			$label = $value;

		$item_atts = array(
			'type' => $tag->basetype,
			'name' => $tag->name . ( $multiple ? '[]' : '' ),
			'value' => $value,
			'checked' => $checked ? 'checked' : '',
			'tabindex' => $tabindex ? $tabindex : '' );

		$item_atts = wpcf7_format_atts( $item_atts );

		if ( $label_first ) { // put label first, input last
			$item = sprintf(
				'<span class="wpcf7-list-item-label">%1$s</span>&nbsp;<input %2$s />',
				esc_html( $label ), $item_atts );
		} else {
			$item = sprintf(
				'<input %2$s />&nbsp;<span class="wpcf7-list-item-label">%1$s</span>',
				esc_html( $label ), $item_atts );
		}

		if ( $use_label_element )
			$item = '<label>' . $item . '</label>';

		if ( false !== $tabindex )
			$tabindex += 1;

		$count += 1;

		if ( 1 == $count ) {
			$class .= ' first';
		}

		if ( count( $values ) == $count ) { // last round
			$class .= ' last';

			if ( $free_text ) {
				$free_text_name = sprintf(
					'_wpcf7_%1$s_free_text_%2$s', $tag->basetype, $tag->name );

				$free_text_atts = array(
					'name' => $free_text_name,
					'class' => 'wpcf7-free-text',
					'tabindex' => $tabindex ? $tabindex : '' );

				if ( wpcf7_is_posted() && isset( $_POST[$free_text_name] ) ) {
					$free_text_atts['value'] = wp_unslash(
						$_POST[$free_text_name] );
				}

				$free_text_atts = wpcf7_format_atts( $free_text_atts );

				$item .= sprintf( ' <input type="text" %s />', $free_text_atts );

				$class .= ' has-free-text';
			}
		}

		$item = '<span class="' . esc_attr( $class ) . '">' . $item . '</span>';
		$html .= $item;
	}

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><span %2$s>%3$s</span>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'wpcf7_validate_checkbox', 'wpcf7_checkbox_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_checkbox*', 'wpcf7_checkbox_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_radio', 'wpcf7_checkbox_validation_filter', 10, 2 );

function wpcf7_checkbox_validation_filter( $result, $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	$type = $tag->type;
	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? (array) $_POST[$name] : array();

	if ( 'checkbox*' == $type ) {
		if ( empty( $value ) ) {
			$result['valid'] = false;
			$result['reason'][$name] = wpcf7_get_message( 'invalid_required' );
		}
	}

	if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
		$result['idref'][$name] = $id;
	}

	return $result;
}


/* Adding free text field */

add_filter( 'wpcf7_posted_data', 'wpcf7_checkbox_posted_data' );

function wpcf7_checkbox_posted_data( $posted_data ) {
	$tags = wpcf7_scan_shortcode(
		array( 'type' => array( 'checkbox', 'checkbox*', 'radio' ) ) );

	if ( empty( $tags ) ) {
		return $posted_data;
	}

	foreach ( $tags as $tag ) {
		$tag = new WPCF7_Shortcode( $tag );

		if ( ! isset( $posted_data[$tag->name] ) ) {
			continue;
		}

		$posted_items = (array) $posted_data[$tag->name];

		if ( $tag->has_option( 'free_text' ) ) {
			if ( WPCF7_USE_PIPE ) {
				$values = $tag->pipes->collect_afters();
			} else {
				$values = $tag->values;
			}

			$last = array_pop( $values );
			$last = html_entity_decode( $last, ENT_QUOTES, 'UTF-8' );

			if ( in_array( $last, $posted_items ) ) {
				$posted_items = array_diff( $posted_items, array( $last ) );

				$free_text_name = sprintf(
					'_wpcf7_%1$s_free_text_%2$s', $tag->basetype, $tag->name );

				$free_text = $posted_data[$free_text_name];

				if ( ! empty( $free_text ) ) {
					$posted_items[] = trim( $last . ' ' . $free_text );
				} else {
					$posted_items[] = $last;
				}
			}
		}

		$posted_data[$tag->name] = $posted_items;
	}

	return $posted_data;
}


/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_checkbox_and_radio', 30 );

function wpcf7_add_tag_generator_checkbox_and_radio() {
	if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
		return;

	wpcf7_add_tag_generator( 'checkbox', __( 'Checkboxes', 'contact-form-7' ),
		'wpcf7-tg-pane-checkbox', 'wpcf7_tg_pane_checkbox' );

	wpcf7_add_tag_generator( 'radio', __( 'Radio buttons', 'contact-form-7' ),
		'wpcf7-tg-pane-radio', 'wpcf7_tg_pane_radio' );
}

function wpcf7_tg_pane_checkbox( $contact_form ) {
	wpcf7_tg_pane_checkbox_and_radio( 'checkbox' );
}

function wpcf7_tg_pane_radio( $contact_form ) {
	wpcf7_tg_pane_checkbox_and_radio( 'radio' );
}

function wpcf7_tg_pane_checkbox_and_radio( $type = 'checkbox' ) {
	if ( 'radio' != $type )
		$type = 'checkbox';

?>
<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="hidden">
<form action="">
<table>
<?php if ( 'checkbox' == $type ) : ?>
<tr><td><input type="checkbox" name="required" />&nbsp;<?php echo esc_html( __( 'Required field?', 'contact-form-7' ) ); ?></td></tr>
<?php endif; ?>

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
<td><?php echo esc_html( __( 'Choices', 'contact-form-7' ) ); ?><br />
<textarea name="values"></textarea><br />
<span style="font-size: smaller"><?php echo esc_html( __( "* One choice per line.", 'contact-form-7' ) ); ?></span>
</td>

<td>
<br /><input type="checkbox" name="label_first" class="option" />&nbsp;<?php echo esc_html( __( 'Put a label first, a checkbox last?', 'contact-form-7' ) ); ?>
<br /><input type="checkbox" name="use_label_element" class="option" />&nbsp;<?php echo esc_html( __( 'Wrap each item with <label> tag?', 'contact-form-7' ) ); ?>
<?php if ( 'checkbox' == $type ) : ?>
<br /><input type="checkbox" name="exclusive" class="option" />&nbsp;<?php echo esc_html( __( 'Make checkboxes exclusive?', 'contact-form-7' ) ); ?>
<?php endif; ?>
</td>
</tr>
</table>

<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?><br /><input type="text" name="<?php echo $type; ?>" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>

<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'contact-form-7' ) ); ?><br /><input type="text" class="mail-tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" /></div>
</form>
</div>
<?php
}

?>