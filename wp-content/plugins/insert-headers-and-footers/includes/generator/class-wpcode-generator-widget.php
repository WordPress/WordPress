<?php
/**
 * Generate a snippet to add a custom Widget.
 *
 * @package WPCode
 */

/**
 * WPCode_Generator_Script class.
 */
class WPCode_Generator_Widget extends WPCode_Generator_Type {

	/**
	 * The generator slug.
	 *
	 * @var string
	 */
	public $name = 'widget';

	/**
	 * The categories for this generator.
	 *
	 * @var string[]
	 */
	public $categories = array(
		'design',
	);

	/**
	 * Set the translatable strings.
	 *
	 * @return void
	 */
	protected function set_strings() {
		$this->title       = __( 'Widget', 'insert-headers-and-footers' );
		$this->description = __( 'Generate a snippet to register a custom sidebar widget for your website.', 'insert-headers-and-footers' );
	}

	/**
	 * Load the generator tabs.
	 *
	 * @return void
	 */
	protected function load_tabs() {
		$this->tabs = array(
			'info'    => array(
				'label'   => __( 'Info', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						// Column 1 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Overview', 'insert-headers-and-footers' ),
							'content' => __( 'Using this generator you can easily add a custom sidebar widget with settings.', 'insert-headers-and-footers' ),
						),
					),
					// Column 2.
					array(
						// Column 2 fields.
						array(
							'type'    => 'list',
							'label'   => __( 'Usage', 'insert-headers-and-footers' ),
							'content' => array(
								__( 'Fill in the forms using the menu on the left.', 'insert-headers-and-footers' ),
								__( 'Click the "Update Code" button.', 'insert-headers-and-footers' ),
								__( 'Click on "Use Snippet" to create a new snippet with the generated code.', 'insert-headers-and-footers' ),
								__( 'Activate and save the snippet and you\'re ready to go', 'insert-headers-and-footers' ),
							),
						),
					),
					// Column 3.
					array(
						// Column 3 fields.
						array(
							'type'    => 'description',
							'label'   => __( 'Examples', 'insert-headers-and-footers' ),
							'content' => __( 'Sidebar widgets are very useful when you want to display the same content on multiple pages, you can create a widget with contact methods, for example and fields to set a phone number, email, etc.', 'insert-headers-and-footers' ),
						),
					),
				),
			),
			'general' => array(
				'label'   => __( 'General', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Class name', 'insert-headers-and-footers' ),
							'description' => __( 'Make this unique to avoid conflicts with other similar snippets.', 'insert-headers-and-footers' ),
							'id'          => 'class_name',
							'placeholder' => 'Custom_Generated_Widget',
							'default'     => 'Custom_Generated_Widget' . time(),
							// This makes it unique for people who don't want to customize.
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Prefix', 'insert-headers-and-footers' ),
							'description' => __( 'Used to prefix all the field names.', 'insert-headers-and-footers' ),
							'id'          => 'prefix',
							'placeholder' => 'custom_',
							'default'     => 'custom' . time() . '_',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Text Domain', 'insert-headers-and-footers' ),
							'description' => __( 'Optional textdomain for translations.', 'insert-headers-and-footers' ),
							'id'          => 'text_domain',
							'placeholder' => 'text_domain',
							'default'     => 'text_domain',
						),
					),
				),
			),
			'widget'  => array(
				'label'   => __( 'Widget', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Widget ID', 'insert-headers-and-footers' ),
							'description' => __( 'Unique id for the widget, used in the code.', 'insert-headers-and-footers' ),
							'id'          => 'widget_id',
							'name'        => 'widget_id',
							'default'     => 'custom_widget_id',
							'placeholder' => '',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Widget Title', 'insert-headers-and-footers' ),
							'description' => __( 'The title of the widget (displayed in the admin).', 'insert-headers-and-footers' ),
							'id'          => 'widget_title',
							'name'        => 'widget_title',
							'default'     => 'Custom Widget',
							'placeholder' => '',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Description', 'insert-headers-and-footers' ),
							'description' => __( 'Description used in the admin to explain what the widget is used for.', 'insert-headers-and-footers' ),
							'id'          => 'description',
							'name'        => 'description',
							'default'     => 'This is a custom widget generated with WPCode',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'CSS Class', 'insert-headers-and-footers' ),
							'description' => __( 'Widget-specific CSS class name.', 'insert-headers-and-footers' ),
							'id'          => 'css_class',
							'default'     => 'custom-generated-widget',
						),
					),
					// Column 3.
					array(
						array(
							'type'        => 'textarea',
							'label'       => __( 'Widget Output Code', 'insert-headers-and-footers' ),
							'description' => __( 'PHP Code used for outputting the fields in the frontend. If left empty it will output the fields values in a simple list.', 'insert-headers-and-footers' ),
							'id'          => 'code',
							'code'        => true,
						),
					),
				),
			),
			'fields'  => array(
				'label'   => __( 'Fields', 'insert-headers-and-footers' ),
				'columns' => array(
					// Column 1.
					array(
						array(
							'type'        => 'select',
							'label'       => __( 'Field Type', 'insert-headers-and-footers' ),
							'description' => __( 'Pick the type of field you want to use for this setting.', 'insert-headers-and-footers' ),
							'id'          => 'field_type',
							'name'        => 'field_type[]',
							'options'     => array(
								'text'     => __( 'Text', 'insert-headers-and-footers' ),
								'email'    => __( 'Email', 'insert-headers-and-footers' ),
								'url'      => __( 'URL', 'insert-headers-and-footers' ),
								'number'   => __( 'Number', 'insert-headers-and-footers' ),
								'textarea' => __( 'Textarea', 'insert-headers-and-footers' ),
								'select'   => __( 'Select', 'insert-headers-and-footers' ),
								'checkbox' => __( 'Checkboxes', 'insert-headers-and-footers' ),
								'radio'    => __( 'Radio', 'insert-headers-and-footers' ),
							),
							'repeater'    => 'fields',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Field ID', 'insert-headers-and-footers' ),
							'description' => __( 'Unique id for this field, used in the code.', 'insert-headers-and-footers' ),
							'id'          => 'field_id',
							'name'        => 'field_id[]',
							'repeater'    => 'fields',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Field Label', 'insert-headers-and-footers' ),
							'description' => __( 'The label displayed next to this field in the admin form.', 'insert-headers-and-footers' ),
							'id'          => 'field_label',
							'name'        => 'field_label[]',
							'repeater'    => 'fields',
						),
						array(
							'type' => 'spacer',
						),
					),
					// Column 2.
					array(
						array(
							'type'        => 'text',
							'label'       => __( 'Description', 'insert-headers-and-footers' ),
							'description' => __( 'Display a short descriptive text below this field.', 'insert-headers-and-footers' ),
							'id'          => 'field_description',
							'name'        => 'field_description[]',
							'repeater'    => 'fields',
						),
						array(
							'type'        => 'text',
							'label'       => __( 'Default', 'insert-headers-and-footers' ),
							'description' => __( 'Set the default value for this field.', 'insert-headers-and-footers' ),
							'id'          => 'field_default',
							'name'        => 'field_default[]',
							'repeater'    => 'fields',
						),
						array(
							'type'        => 'textarea',
							'label'       => __( 'Options', 'insert-headers-and-footers' ),
							'description' => __( 'Use value|label for each line to add options for select, checkbox or radio.', 'insert-headers-and-footers' ),
							'id'          => 'field_options',
							'name'        => 'field_options[]',
							'repeater'    => 'fields',
						),
					),
					// Column 3.
					array(
						array(
							'type'    => 'description',
							'label'   => __( 'Add another field', 'insert-headers-and-footers' ),
							'content' => __( 'Use the "Add field" button below to add as many fields as you need.', 'insert-headers-and-footers' ),
						),
						array(
							'type'        => 'repeater_button',
							'button_text' => __( 'Add field', 'insert-headers-and-footers' ),
							'id'          => 'fields', // Repeater to repeat when clicked.
						),
					),
				),
			),
		);
	}

	/**
	 * Dynamically get the code for a widget field by type.
	 *
	 * @param string $type The type of field.
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param string $options The options for fields that have options.
	 *
	 * @return mixed|string
	 */
	public function get_widget_field_code( $type, $id, $label, $description, $options ) {
		if ( ! method_exists( $this, 'widget_field_' . $type ) ) {
			return '';
		}
		$options           = preg_split( "/\r\n|[\r\n]/", $options );
		$processed_options = array();
		foreach ( $options as $option ) {
			$split_option                          = explode( '|', $option );
			$processed_options[ $split_option[0] ] = $split_option[0];
			if ( isset( $split_option[1] ) ) {
				$processed_options[ $split_option[0] ] = $split_option[1];
			}
		}

		return call_user_func_array(
			array( $this, 'widget_field_' . $type ),
			array(
				$id,
				$label,
				$description,
				$processed_options,
			)
		);
	}

	/**
	 * Get the code for a text field.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param array  $options The field options (unused here).
	 * @param string $type The input type, so we can reuse this for similar fields.
	 *
	 * @return string
	 */
	public function widget_field_text( $id, $label, $description, $options = array(), $type = 'text' ) {
		return "
		echo '<p>';
		echo '<label for=\"'. \$this->get_field_id( '$id' ) .'\">'. __( '$label', '{$this->get_value( 'text_domain' ) }' ) .'</label>';
		echo '<input type=\"$type\" id=\"'. \$this->get_field_id( '$id' ) .'\" name=\"'. \$this->get_field_name( '$id' ) .'\" class=\"widefat\" value=\"'. esc_attr(\$instance['$id']) .'\" />';
		{$this->widget_field_description( $description )}
		echo '</p>';
		";
	}

	/**
	 * Email field, uses the text field with a different input type.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param array  $options The field options (unused here).
	 *
	 * @return string
	 */
	public function widget_field_email( $id, $label, $description, $options = array() ) {
		return $this->widget_field_text( $id, $label, $description, $options, 'email' );
	}

	/**
	 * URL field, uses the text field with a different input type.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param array  $options The field options (unused here).
	 *
	 * @return string
	 */
	public function widget_field_url( $id, $label, $description, $options = array() ) {
		return $this->widget_field_text( $id, $label, $description, $options, 'url' );
	}

	/**
	 * Number field, uses the text field with a different input type.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param array  $options The field options (unused here).
	 *
	 * @return string
	 */
	public function widget_field_number( $id, $label, $description, $options = array() ) {
		return $this->widget_field_text( $id, $label, $description, $options, 'number' );
	}

	/**
	 * Textarea field.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param array  $options The field options (unused here).
	 *
	 * @return string
	 */
	public function widget_field_textarea( $id, $label, $description, $options = array() ) {
		return "
		echo '<p>';
		echo '<label for=\"'. \$this->get_field_id( '$id' ) .'\">'. __( '$label', '{$this->get_value( 'text_domain' ) }' ) .'</label>';
		echo '<textarea id=\"'. \$this->get_field_id( '$id' ) .'\" name=\"'. \$this->get_field_name( '$id' ) .'\" class=\"widefat\">'. esc_html(\$instance['$id']) .'</textarea>';
		{$this->widget_field_description( $description )}
		echo '</p>';
		";
	}

	/**
	 * Get the code for a text field.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param string $options The field options.
	 *
	 * @return string
	 */
	public function widget_field_select( $id, $label, $description, $options = array() ) {
		$field_code = "
		echo '<p>';
		echo '<label for=\"'. \$this->get_field_id( '$id' ) .'\">'. __( '$label', '{$this->get_value( 'text_domain' ) }' ) .'</label>';
		echo '<select id=\"'. \$this->get_field_id( '$id' ) .'\" name=\"'. \$this->get_field_name( '$id' ) .'\" class=\"widefat\">';";

		foreach ( $options as $value => $label ) {
			if ( empty( $value ) ) {
				continue;
			}
			$field_code .= "\n\t\t echo'<option value=\"$value\" '. selected('$value', \$instance['$id'], false) .'>$label</option>';";
		}
		$field_code .= "\n\t\techo '</select>';
		{$this->widget_field_description( $description )}
		echo '</p>';";

		return $field_code;
	}

	/**
	 * Get the code for a checkbox field.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param string $options The field options.
	 *
	 * @return string
	 */
	public function widget_field_checkbox( $id, $label, $description, $options = array() ) {
		$field_code = "
		echo '<p>';
		echo '<label for=\"'. \$this->get_field_id( '$id' ) .'\">'. __( '$label', '{$this->get_value( 'text_domain' ) }' ) .'</label>';";
		foreach ( $options as $value => $label ) {
			if ( empty( $value ) ) {
				continue;
			}
			$field_code .= "\n\t\t echo'<div><label><input type=\"checkbox\" value=\"$value\" '. checked( in_array( '$value', \$instance['$id'], true ), true, false) .' name=\"'. \$this->get_field_name( '$id' ) .'[]\"> $label</label></div>';";
		}
		$field_code .= "{$this->widget_field_description( $description )}
		echo '</p>';";

		return $field_code;
	}

	/**
	 * Get the code for a radio field.
	 *
	 * @param string $id The field id.
	 * @param string $label The field label.
	 * @param string $description The field description.
	 * @param string $options The field options.
	 *
	 * @return string
	 */
	public function widget_field_radio( $id, $label, $description, $options = array() ) {
		$field_code = "
		echo '<p>';
		echo '<label for=\"'. \$this->get_field_id( '$id' ) .'\">'. __( '$label', '{$this->get_value( 'text_domain' ) }' ) .'</label>';";
		foreach ( $options as $value => $label ) {
			if ( empty( $value ) ) {
				continue;
			}
			$field_code .= "\n\t\t echo'<div><label><input type=\"radio\" value=\"$value\" '. checked( '$value', \$instance['$id'], false) .' name=\"'. \$this->get_field_name( '$id' ) .'\"> $label</label></div>';";
		}
		$field_code .= "\n{$this->widget_field_description( $description )}
		echo '</p>';";

		return $field_code;
	}

	/**
	 * Get standard markup for the description of a field.
	 *
	 * @param string $description The field description.
	 *
	 * @return string
	 */
	public function widget_field_description( $description ) {
		if ( empty( $description ) ) {
			return '';
		}

		return "echo '<span class=\"description\">' . __( '$description', '{$this->get_value( 'text_domain' ) }' ) . '</span>';";
	}

	/**
	 * Get the snippet code with dynamic values applied.
	 *
	 * @return string
	 */
	public function get_snippet_code() {

		$instance_defaults = '';
		$fields_markup     = '';

		$fields       = $this->get_value( 'field_id' );
		$labels       = $this->get_value( 'field_label' );
		$values       = $this->get_value( 'field_default' );
		$type         = $this->get_value( 'field_type' );
		$options      = $this->get_value( 'field_options' );
		$descriptions = $this->get_value( 'field_description' );

		if ( ! empty( $fields ) && is_array( $fields ) ) {
			foreach ( $fields as $key => $field_id ) {
				if ( empty( $field_id ) ) {
					continue;
				}
				$value = "'$values[$key]'";
				if ( 'checkbox' === $type[ $key ] ) {
					$value = "array('$values[$key]')";
				}
				$instance_defaults .= "\t\t\t'$field_id' => $value,\n";

				$fields_markup .= $this->get_widget_field_code( $type[ $key ], $field_id, $labels[ $key ], $descriptions[ $key ], $options[ $key ] );
			}
		}

		$widget_output = $this->get_value( 'code' );
		if ( empty( $widget_output ) && ! empty( $fields ) && is_array( $fields ) ) {
			// If there's no custom PHP code for the output build a simple list output.
			$widget_output = "\n\t\techo '<ul>';\n";
			foreach ( $fields as $key => $field_id ) {
				if ( empty( $field_id ) ) {
					continue;
				}
				$value = "\$instance['$field_id']";
				if ( 'checkbox' === $type[ $key ] ) {
					$value = "implode( ', ', $value )";
				}
				$widget_output .= "\t\t\techo '<li>{$labels[ $key ]}: ' . $value . '</li>';\n";

			}

			$widget_output .= "\t\techo '</ul>';\n";
		}

		return <<<EOD
class {$this->get_value( 'class_name' )} extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'{$this->get_value( 'widget_id' )}',
			__( '{$this->get_value( 'widget_title' )}', '{$this->get_value( 'text_domain' )}' ),
			array(
				'description' => __( '{$this->get_value( 'description' )}', '{$this->get_value( 'text_domain' )}' ),
				'classname' => '{$this->get_value( 'css_class' )}',
			)
		);
	}
	
	public function widget( \$args, \$instance ) {
			\$instance = wp_parse_args( (array) \$instance, array(
$instance_defaults\t\t) );
		// Before widget tag
		echo \$args['before_widget'];
{$widget_output}
		// After widget tag
		echo \$args['after_widget'];
	}
	
	public function form( \$instance ) {
			// Set default values
		\$instance = wp_parse_args( (array) \$instance, array(
$instance_defaults\t\t) );
		
		$fields_markup
	}
}


function {$this->get_value( 'prefix' )}register_widgets() {
	register_widget( '{$this->get_value( 'class_name' )}' );
}
add_action( 'widgets_init', '{$this->get_value( 'prefix' )}register_widgets' );

EOD;
	}

}
