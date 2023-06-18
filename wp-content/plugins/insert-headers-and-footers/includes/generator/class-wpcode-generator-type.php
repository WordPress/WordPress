<?php
/**
 * Base for different types of snippet generators.
 *
 * @package WPCode
 */

abstract class WPCode_Generator_Type {
	/**
	 * The name (slug) for this generator.
	 *
	 * @var string
	 */
	public $name;
	/**
	 * The title of the generator (translatable field).
	 *
	 * @var string
	 */
	public $title;
	/**
	 * The description of the generator.
	 * This will be displayed in the list of available generators.
	 *
	 * @var string
	 */
	public $description;
	/**
	 * Array of categories for this generator.
	 *
	 * @var array
	 */
	public $categories;
	/**
	 * Array of tabs for the generator fields.
	 *
	 * @var array;
	 */
	public $tabs;
	/**
	 * Store the form data object in an array so we pick values from it.
	 *
	 * @var array
	 */
	public $form_data;
	/**
	 * Catch here all the fields not using the default value.
	 *
	 * @var array
	 */
	public $fields_from_form = array();
	/**
	 * Location where the snippet will run after being saved.
	 *
	 * @var string
	 */
	public $location = 'everywhere';
	/**
	 * Array of tags to add to the saved snippet.
	 *
	 * @var string[]
	 */
	public $tags = array(
		'generated',
	);
	/**
	 * Snippet code type for when it will be saved.
	 *
	 * @var string
	 */
	public $code_type = 'php';
	/**
	 * Should the generated snippet be auto-inserted?
	 *
	 * @var bool
	 */
	public $auto_insert = true;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_strings();
		$this->load_tabs();
	}

	/**
	 * Replace this in the type to add translatable fields on init.
	 *
	 * @return void
	 */
	abstract protected function set_strings();

	/**
	 * Load the data for the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array();
	}

	/**
	 * Let's use a method.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Let's use a method.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Let's use a method.
	 *
	 * @return string
	 */
	public function get_location() {
		return $this->location;
	}

	/**
	 * Let's use a method.
	 *
	 * @return array
	 */
	public function get_tags() {
		return $this->tags;
	}

	/**
	 * Let's use a method.
	 *
	 * @return string
	 */
	public function get_code_type() {
		return $this->code_type;
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the categories of this generator.
	 *
	 * @return array
	 */
	public function get_categories() {
		return $this->categories;
	}

	/**
	 * Takes a tab id and renders the form items.
	 *
	 * @param string $tab_id The tab id.
	 * @param array  $snippet_data Data to prefill the form with from a generated snippet.
	 *
	 * @return void
	 */
	public function render_tab( $tab_id, $snippet_data ) {
		$tab_info = $this->tabs[ $tab_id ];

		foreach ( $tab_info['columns'] as $column_fields ) {
			$repeater_groups_fields = array();
			$repeater_groups_values = array();
			?>
			<div class="wpcode-generator-column">
				<?php
				foreach ( $column_fields as $field ) {
					if ( isset( $field['id'] ) && isset( $snippet_data[ $field['id'] ] ) ) {
						if ( isset( $field['repeater'] ) ) {
							$repeater_groups_fields[ $field['repeater'] ][ $field['id'] ] = $field;
							$repeater_groups_values[ $field['repeater'] ][ $field['id'] ] = $snippet_data[ $field['id'] ];
							$field['value']                                               = $snippet_data[ $field['id'] ][0];
						} else {
							$field['value'] = $snippet_data[ $field['id'] ];
						}
					}
					$this->render_field( $field );
				}
				// Render repeater fields values.
				if ( ! empty( $repeater_groups_values ) ) {
					$i = 0;
					foreach ( $repeater_groups_values as $repeater_id => $repeater_values ) {
						foreach ( $repeater_values as $field_id => $field_values ) {
							foreach ( $field_values as $key => $field_value ) {
								if ( 0 === $key ) {
									// This one was already rendered.
									continue;
								}
								?>
								<div class="wpcode-repeater-group" data-id="<?php echo absint( $i ); ?>">
									<?php foreach ( $repeater_groups_fields[ $repeater_id ] as $repeater_field_id => $repeater_groups_field ) {
										$repeater_groups_field['value'] = $repeater_values[ $repeater_field_id ][ $key ];
										$this->render_field( $repeater_groups_field );
									}
									?>
									<button type="button" class="wpcode-button wpcode-button-secondary wpcode-remove-row" data-target="<?php echo absint( $i ); ?>"><?php esc_html_e( 'Remove Row', 'insert-headers-and-footers' ); ?></button>
								</div>
								<?php
								$i ++;
							}
							continue 2;
						}
					}
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * Takes a field config from the tabs object and renders it's input.
	 *
	 * @param array $field The field config.
	 *
	 * @return void
	 */
	public function render_field( $field ) {
		// Check if the field type is set.
		if ( ! isset( $field['type'] ) ) {
			return;
		}
		$type = $field['type'];
		// Check if we have a method of rendering the field.
		if ( ! method_exists( $this, 'render_field_' . $type ) ) {
			return;
		}

		$this->add_field_wrap( $field );
		call_user_func_array( array( $this, 'render_field_' . $type ), array( $field ) );
		$this->add_field_wrap( $field, true );
	}

	/**
	 * Add field wrap.
	 *
	 * @param array $field The field config.
	 * @param bool  $end Whether to output the closing tag.
	 *
	 * @return void
	 */
	public function add_field_wrap( $field, $end = false ) {
		if ( $end ) {
			echo '</div>';

			return;
		}
		$type     = $field['type'];
		$repeater = empty( $field['repeater'] ) ? '' : 'data-repeater="' . esc_attr( $field['repeater'] ) . '"';
		$classes  = array(
			'wpcode-generator-field',
			'wpcode-generator-field-' . $type,
		);
		if ( ! empty( $field['autocomplete'] ) ) {
			$classes[] = 'wpcode-generator-field-autocomplete';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" ' . $repeater . '>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render the description field.
	 *
	 * @param array $field The field array as defined in the tabs array.
	 *
	 * @return void
	 */
	public function render_field_description( $field ) {
		$field = wp_parse_args(
			$field,
			array(
				'label'   => '',
				'content' => '',
			)
		);

		$this->text_field_label( $field['label'] );
		if ( ! empty( $field['content'] ) ) {
			?>
			<p><?php echo wp_kses_post( $field['content'] ); ?></p>
			<?php
		}
	}

	/**
	 * Render a label for text-type fields (description, list, etc).
	 *
	 * @param string $label The label to render.
	 *
	 * @return void
	 */
	public function text_field_label( $label ) {
		if ( empty( $label ) ) {
			return;
		}
		?>
		<label><?php echo wp_kses_post( $label ); ?></label>
		<?php
	}

	/**
	 * Render a list from an array.
	 *
	 * @param array $field The field array.
	 *
	 * @return void
	 */
	public function render_field_list( $field ) {
		$field = wp_parse_args(
			$field,
			array(
				'label'   => '',
				'content' => array(),
			)
		);

		$this->text_field_label( $field['label'] );
		if ( ! empty( $field['content'] ) && is_array( $field['content'] ) ) {
			?>
			<ul>
				<?php foreach ( $field['content'] as $li ) { ?>
					<li><?php echo wp_kses_post( $li ); ?></li>
				<?php } ?>
			</ul>
			<?php
		}
	}

	/**
	 * Render a text input field.
	 *
	 * @param array $field The field array.
	 *
	 * @return void
	 */
	public function render_field_text( $field ) {
		if ( empty( $field['id'] ) ) {
			return;
		}
		if ( ! empty( $field['label'] ) ) {
			$this->input_field_label( $field['label'], $field['id'] );
		}
		$id          = $field['id'];
		$placeholder = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
		$name        = empty( $field['name'] ) ? $id : $field['name'];
		$value       = isset( $field['value'] ) ? $field['value'] : $this->get_default_value( $field['id'] );
		?>
		<input type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" value="<?php echo esc_attr( $value ); ?>" class="wpcode-input-text"/>
		<?php
		if ( ! empty( $field['description'] ) ) {
			$this->input_field_description( $field['description'] );
		}
		if ( ! empty( $field['autocomplete'] ) ) {
			?>
			<script type="application/json" class="wpcode-field-autocomplete"><?php echo wp_json_encode( $field['autocomplete'] ); ?></script>
			<?php
		}
	}

	/**
	 * HTML field just extends the text one for now.
	 *
	 * @param array $field The field array.
	 *
	 * @return void
	 */
	public function render_field_html( $field ) {
		$this->render_field_text( $field );
	}

	/**
	 * Render a label for an input.
	 *
	 * @param string $label The label text.
	 * @param string $id The id to use for the "for" attribute of the label.
	 *
	 * @return void
	 */
	public function input_field_label( $label, $id ) {
		if ( empty( $label ) ) {
			return;
		}
		?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<?php echo wp_kses_post( $label ); ?>
		</label>
		<?php
	}

	/**
	 * Render a field's description.
	 *
	 * @param string $description The field description.
	 *
	 * @return void
	 */
	public function input_field_description( $description ) {
		?>
		<p class="wpcode-field-description"><?php echo wp_kses_post( $description ); ?></p>
		<?php
	}

	/**
	 * Render a select dropdown.
	 *
	 * @param array $field The field options.
	 *
	 * @return void
	 */
	public function render_field_select( $field ) {
		if ( empty( $field['id'] ) ) {
			return;
		}
		if ( ! empty( $field['label'] ) ) {
			$this->input_field_label( $field['label'], $field['id'] );
		}
		$id   = $field['id'];
		$name = empty( $field['name'] ) ? $id : $field['name'];
		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
			reset( $field['options'] );
			if ( isset( $field['value'] ) ) {
				$selected = $field['value'];
			} else {
				$selected = isset( $field['default'] ) ? $field['default'] : key( $field['options'] );
			}
			?>
			<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>">
				<?php
				foreach ( $field['options'] as $value => $label ) {
					?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selected, $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
			<?php
		}
		if ( ! empty( $field['description'] ) ) {
			$this->input_field_description( $field['description'] );
		}
	}

	/**
	 * Render a list of checkboxes from a field list of options.
	 *
	 * @param array $field The field settings array.
	 *
	 * @return void
	 */
	public function render_field_checkbox_list( $field ) {
		if ( empty( $field['options'] ) ) {
			return;
		}
		$checked = empty( $field['default'] ) ? array() : $field['default'];
		if ( isset( $field['value'] ) ) {
			$checked = $field['value'];
		}

		foreach ( $field['options'] as $value => $label ) {
			?>
			<div class="wpcode-checkbox-line">
				<label class="wpcode-checkbox-toggle">
					<input type="checkbox" name="<?php echo esc_attr( $field['id'] ); ?>[]" value="<?php echo esc_attr( $value ); ?>" id="<?php echo esc_attr( $field['id'] . '_' . $value ); ?>" <?php checked( in_array( $value, $checked, true ) ); ?>/>
					<span class="wpcode-checkbox-toggle-slider"></span>
				</label>
				<label for="<?php echo esc_attr( $field['id'] . '_' . $value ); ?>"><?php echo esc_html( $label ); ?></label>
			</div>
			<?php
		}
	}

	/**
	 * The repeater button used to add new repeater rows.
	 *
	 * @param array $field The field array.
	 *
	 * @return void
	 */
	public function render_field_repeater_button( $field ) {
		if ( empty( $field['id'] ) ) {
			return;
		}

		?>
		<button type="button" class="wpcode-button wpcode-button-secondary wpcode-repeater-button" data-target="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['button_text'] ); ?></button>
		<?php

	}

	/**
	 * Set the form data object from an array - usually the $_POST object.
	 *
	 * @param array $form_data The form data object.
	 *
	 * @return string
	 */
	public function process_form_data( $form_data ) {
		$this->form_data = $form_data;

		return $this->get_snippet_code();
	}

	/**
	 * Get the snippet code with the values added to it.
	 *
	 * @return string
	 */
	abstract public function get_snippet_code();

	/**
	 * Get a value for output in the snippet code, if the form data is set
	 * we sanitise that and return it otherwise return the default value.
	 *
	 * @param string $field_id The field id.
	 *
	 * @return string
	 */
	public function get_value( $field_id ) {
		return ! empty( $this->form_data[ $field_id ] ) ? $this->sanitize_form_data( $field_id ) : $this->get_default_value( $field_id );
	}

	/**
	 * Sanitize form value based on the field type.
	 *
	 * @param string $field_id The id of the field.
	 *
	 * @return string
	 */
	protected function sanitize_form_data( $field_id ) {
		$field_type = $this->find_field_prop( $field_id, 'type' );
		$value      = $this->form_data[ $field_id ];
		if ( 'text' === $field_type && is_array( $value ) ) {
			$field_type = 'text_array';
		}
		if ( 'textarea' === $field_type && is_array( $value ) ) {
			$field_type = 'textarea_array';
		}
		if ( 'select' === $field_type && is_array( $value ) ) {
			$field_type = 'text_array';
		}
		if ( 'html' === $field_type && is_array( $value ) ) {
			$field_type = 'html_array';
		}

		switch ( $field_type ) {
			case 'text_array':
			case 'checkbox_list':
				$sanitized = array_map( 'sanitize_text_field', wp_unslash( $value ) );
				foreach ( $sanitized as $item => $value ) {
					$sanitized[ $item ] = '' === $value ? $this->get_default_value( $field_id ) : $value;
				}
				break;
			case 'textarea_array':
				$sanitized = array_map( 'sanitize_textarea_field', wp_unslash( $value ) );
				foreach ( $sanitized as $item => $value ) {
					$sanitized[ $item ] = '' === $value ? $this->get_default_value( $field_id ) : $value;
				}
				break;
			case 'html_array':
				$sanitized = array_map( 'wp_kses_post', wp_unslash( $value ) );
				foreach ( $sanitized as $item => $value ) {
					$sanitized[ $item ] = '' === $value ? $this->get_default_value( $field_id ) : $value;
				}
				break;
			case 'textarea':
				$sanitized = sanitize_textarea_field( wp_unslash( $value ) );
				break;
			case 'html':
				$sanitized = wp_kses_post( wp_unslash( $value ) );
				break;
			case 'text':
			default:
				$sanitized = sanitize_text_field( wp_unslash( $value ) );
		}

		if ( ! isset( $this->fields_from_form[ $field_id ] ) ) {
			$this->fields_from_form[ $field_id ] = $sanitized;
		}

		return $sanitized;
	}

	/**
	 * Go through the tabs config and find a field value by its id.
	 *
	 * @param string $field_id The id of the field.
	 * @param string $field_value The key of the value (e.g. 'content').
	 *
	 * @return string
	 */
	public function find_field_prop( $field_id, $field_value ) {
		$tabs = $this->get_tabs();
		foreach ( $tabs as $tab ) {
			foreach ( $tab['columns'] as $column_fields ) {
				foreach ( $column_fields as $field ) {
					if ( ! empty( $field['id'] ) && $field_id === $field['id'] ) {
						return ! isset( $field[ $field_value ] ) ? '' : $field[ $field_value ];
					}
				}
			}
		}

		return '';
	}

	/**
	 * Get the tabs for rendering.
	 *
	 * @return array
	 */
	public function get_tabs() {
		return $this->tabs;
	}

	/**
	 * Go through the tabs config and find the default value for a field.
	 *
	 * @param string $field_id The id of the field for which we want the default value.
	 *
	 * @return string
	 */
	public function get_default_value( $field_id ) {
		return $this->find_field_prop( $field_id, 'default' );
	}

	/**
	 * Get a string with values comma-separated and convert it to PHP array.
	 *
	 * @param string $field_id The id of the field to grab the data for.
	 *
	 * @return string
	 */
	public function get_value_comma_separated( $field_id ) {
		return $this->get_value_comma_separated_code( $this->get_value( $field_id ) );
	}

	/**
	 * Get a comma separated string and return an array.
	 *
	 * @param string $value The value to explode.
	 * @param bool   $quotes Whether to add quotes to the values or not.
	 *
	 * @return string
	 */
	public function get_value_comma_separated_code( $value, $quotes = true ) {
		$items = explode( ',', $value );

		return $this->array_to_code_string( $items, $quotes );
	}

	/**
	 * Takes an array of strings and returns php code for an array of strings.
	 *
	 * @param string[] $items The array to convert.
	 * @param bool     $quotes Whether to add quotes to the values or not.
	 *
	 * @return string
	 */
	public function array_to_code_string( $items, $quotes = true ) {
		if ( empty( $items ) || empty( $items[0] ) ) {
			return 'array()';
		}
		$items = array_map( 'trim', $items );
		if ( $quotes ) {
			$items = array_map( array( $this, 'add_quotes' ), $items );
		}

		return 'array( ' . implode( ', ', $items ) . ' )';
	}

	/**
	 * Callback to add quotes because we can't use closures in PHP 5.2.
	 *
	 * @param string $item String to add quotes to.
	 *
	 * @return string
	 */
	private function add_quotes( $item ) {
		return "'$item'";
	}

	/**
	 * Get value of array fields like checkboxes or select multiple.
	 *
	 * @param string $field_id The field id.
	 *
	 * @return string
	 */
	public function get_array_value( $field_id ) {
		$value = $this->get_value( $field_id );

		return $this->array_to_code_string( $value );
	}

	/**
	 * Get the fields that were updated using the form (not using the default value).
	 *
	 * @return array
	 */
	public function get_generator_data() {
		return $this->fields_from_form;
	}

	/**
	 * If the generated snippet should be auto-inserted or not (used as a shortcode).
	 *
	 * @return bool
	 */
	public function get_auto_insert() {
		return $this->auto_insert;
	}

	/**
	 * Get PHP array code for an optional parameter by field ID.
	 *
	 * @param string $field_id The field id to grab the value for.
	 * @param bool   $quotes Wrap the output value in quotes?.
	 * @param string $array_key The array key if different from the field id, otherwise the field id is used.
	 *
	 * @return string
	 * @see get_optional_value_code
	 */
	public function get_optional_value( $field_id, $quotes = false, $array_key = '' ) {
		if ( empty( $array_key ) ) {
			$array_key = $field_id;
		}
		$value           = $this->get_value( $field_id );
		$default         = $this->get_default_value( $field_id );
		$comma_separated = $this->find_field_prop( $field_id, 'comma-separated' );

		return $this->get_optional_value_code( $value, $default, $array_key, $quotes, $comma_separated );
	}

	/**
	 * Get PHP array code for an optional parameter.
	 * If the default value is used nothing will be output.
	 * It will also attempt to align all the values properly.
	 *
	 * @param string $value The current value to compare to the default.
	 * @param string $default The default value.
	 * @param string $array_key The array key to use if the value will be output.
	 * @param bool   $quotes Whether to use quotes for the value output.
	 * @param bool   $comma_separated If the value is actually a comma-separated list.
	 *
	 * @return string
	 */
	public function get_optional_value_code( $value, $default, $array_key, $quotes = false, $comma_separated = false ) {
		if ( $value === $default ) {
			return '';
		}
		if ( $comma_separated ) {
			$value = $this->get_value_comma_separated_code( $value, $quotes );
		} elseif ( $quotes ) {
			$value = "'$value'";
		}
		$indent = 22 - strlen( $array_key );
		$indent = str_repeat( ' ', $indent );

		return "\t\t'$array_key'$indent=> $value,\n";
	}

	/**
	 * Output a simple spacer used to align repeater rows.
	 *
	 * @return void
	 */
	public function render_field_spacer() {
		?>
		<div class="wpcode-column-spacer"></div>
		<?php
	}

	/**
	 * Render a textarea field, optionally a code editor.
	 *
	 * @param array $field The field settings.
	 *
	 * @return void
	 */
	public function render_field_textarea( $field ) {
		if ( empty( $field['id'] ) ) {
			return;
		}
		if ( ! empty( $field['label'] ) ) {
			$this->input_field_label( $field['label'], $field['id'] );
		}
		$id      = $field['id'];
		$name    = empty( $field['name'] ) ? $id : $field['name'];
		$classes = array(
			'wpcode-input-textarea',
		);
		if ( ! empty( $field['code'] ) ) {
			$classes[] = 'wpcode-generator-code';
		}
		$value = isset( $field['value'] ) ? $field['value'] : '';
		?>
		<textarea name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"><?php echo esc_html( $value ); ?></textarea>
		<?php
		if ( ! empty( $field['description'] ) ) {
			$this->input_field_description( $field['description'] );
		}
	}

	/**
	 * Sanitize a value to be used as a PHP function name.
	 *
	 * @param string $value The name you want sanitized.
	 *
	 * @return string
	 */
	public function sanitize_function_name( $value ) {
		return str_replace( '-', '_', sanitize_title_with_dashes( $value ) );
	}
}
