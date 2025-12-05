<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Class WPSEO_Taxonomy_Presenter.
 */
class WPSEO_Taxonomy_Fields_Presenter {

	/**
	 * The taxonomy meta data for the current term.
	 *
	 * @var array
	 */
	private $tax_meta;

	/**
	 * Constructs the WPSEO_Taxonomy_Fields_Presenter class.
	 *
	 * @param stdClass $term The current term.
	 */
	public function __construct( $term ) {
		$this->tax_meta = WPSEO_Taxonomy_Meta::get_term_meta( (int) $term->term_id, $term->taxonomy );
	}

	/**
	 * Displaying the form fields.
	 *
	 * @param array $fields Array with the fields that will be displayed.
	 *
	 * @return string
	 */
	public function html( array $fields ) {
		$content = '';
		foreach ( $fields as $field_name => $field_configuration ) {
			$content .= $this->form_row( 'wpseo_' . $field_name, $field_configuration );
		}
		return $content;
	}

	/**
	 * Create a row in the form table.
	 *
	 * @param string $field_name          Variable the row controls.
	 * @param array  $field_configuration Array with the field configuration.
	 *
	 * @return string
	 */
	private function form_row( $field_name, array $field_configuration ) {
		$esc_field_name = esc_attr( $field_name );

		$options = (array) $field_configuration['options'];

		if ( ! empty( $field_configuration['description'] ) ) {
			$options['description'] = $field_configuration['description'];
		}

		$label            = $this->get_label( $field_configuration['label'], $esc_field_name );
		$field            = $this->get_field( $field_configuration['type'], $esc_field_name, $this->get_field_value( $field_name ), $options );
		$help_content     = ( $field_configuration['options']['help'] ?? '' );
		$help_button_text = ( $field_configuration['options']['help-button'] ?? '' );
		$help             = new WPSEO_Admin_Help_Panel( $field_name, $help_button_text, $help_content );

		return $this->parse_row( $label, $help, $field );
	}

	/**
	 * Generates the html for the given field config.
	 *
	 * @param string $field_type  The fieldtype, e.g: text, checkbox, etc.
	 * @param string $field_name  The name of the field.
	 * @param string $field_value The value of the field.
	 * @param array  $options     Array with additional options.
	 *
	 * @return string
	 */
	private function get_field( $field_type, $field_name, $field_value, array $options ) {

		$class            = $this->get_class( $options );
		$field            = '';
		$description      = '';
		$aria_describedby = '';

		if ( ! empty( $options['description'] ) ) {
			$aria_describedby = ' aria-describedby="' . $field_name . '-desc"';
			$description      = '<p id="' . $field_name . '-desc" class="yoast-metabox__description">' . $options['description'] . '</p>';
		}

		switch ( $field_type ) {
			case 'div':
				$field .= '<div id="' . $field_name . '"></div>';
				break;
			case 'url':
				$field .= '<input name="' . $field_name . '" id="' . $field_name . '" ' . $class . ' type="url" value="' . esc_attr( urldecode( $field_value ) ) . '" size="40"' . $aria_describedby . '/>';
				break;
			case 'text':
				$field .= '<input name="' . $field_name . '" id="' . $field_name . '" ' . $class . ' type="text" value="' . esc_attr( $field_value ) . '" size="40"' . $aria_describedby . '/>';
				break;
			case 'checkbox':
				$field .= '<input name="' . $field_name . '" id="' . $field_name . '" type="checkbox" ' . checked( $field_value ) . $aria_describedby . '/>';
				break;
			case 'textarea':
				$rows = 3;
				if ( ! empty( $options['rows'] ) ) {
					$rows = $options['rows'];
				}
				$field .= '<textarea class="large-text" rows="' . esc_attr( $rows ) . '" id="' . $field_name . '" name="' . $field_name . '"' . $aria_describedby . '>' . esc_textarea( $field_value ) . '</textarea>';
				break;
			case 'select':
				if ( is_array( $options ) && $options !== [] ) {
					$field .= '<select name="' . $field_name . '" id="' . $field_name . '"' . $aria_describedby . '>';

					$select_options = ( array_key_exists( 'options', $options ) ) ? $options['options'] : $options;

					foreach ( $select_options as $option => $option_label ) {
						$selected = selected( $option, $field_value, false );
						$field   .= '<option ' . $selected . ' value="' . esc_attr( $option ) . '">' . esc_html( $option_label ) . '</option>';
					}
					unset( $option, $option_label, $selected );

					$field .= '</select>';
				}
				break;
			case 'hidden':
				$field .= '<input name="' . $field_name . '" id="hidden_' . $field_name . '" type="hidden" value="' . esc_attr( $field_value ) . '" />';
				break;
		}

		return $field . $description;
	}

	/**
	 * Getting the value for given field_name.
	 *
	 * @param string $field_name The fieldname to get the value for.
	 *
	 * @return string
	 */
	private function get_field_value( $field_name ) {
		if ( isset( $this->tax_meta[ $field_name ] ) && $this->tax_meta[ $field_name ] !== '' ) {
			return $this->tax_meta[ $field_name ];
		}

		return '';
	}

	/**
	 * Getting the class attributes if $options contains a class key.
	 *
	 * @param array $options The array with field options.
	 *
	 * @return string
	 */
	private function get_class( array $options ) {
		if ( ! empty( $options['class'] ) ) {
			return ' class="' . esc_attr( $options['class'] ) . '"';
		}

		return '';
	}

	/**
	 * Getting the label HTML.
	 *
	 * @param string $label      The label value.
	 * @param string $field_name The target field.
	 *
	 * @return string
	 */
	private function get_label( $label, $field_name ) {
		if ( $label !== '' ) {
			return '<label for="' . $field_name . '">' . esc_html( $label ) . '</label>';
		}

		return '';
	}

	/**
	 * Returns the HTML for the row which contains label, help and the field.
	 *
	 * @param string                 $label The html for the label if there was a label set.
	 * @param WPSEO_Admin_Help_Panel $help  The help panel to render in this row.
	 * @param string                 $field The html for the field.
	 *
	 * @return string
	 */
	private function parse_row( $label, WPSEO_Admin_Help_Panel $help, $field ) {
		if ( $label !== '' || $help !== '' ) {
			return $label . $help->get_button_html() . $help->get_panel_html() . $field;
		}

		return $field;
	}
}
