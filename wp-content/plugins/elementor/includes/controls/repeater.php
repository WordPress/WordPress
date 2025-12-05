<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor repeater control.
 *
 * A base control for creating repeater control. Repeater control allows you to
 * build repeatable blocks of fields. You can create, for example, a set of
 * fields that will contain a title and a WYSIWYG text - the user will then be
 * able to add "rows", and each row will contain a title and a text. The data
 * can be wrapper in custom HTML tags, designed using CSS, and interact using JS
 * or external libraries.
 *
 * @since 1.0.0
 */
class Control_Repeater extends Base_Data_Control implements Has_Validation {

	/**
	 * Get repeater control type.
	 *
	 * Retrieve the control type, in this case `repeater`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'repeater';
	}

	/**
	 * Get repeater control default value.
	 *
	 * Retrieve the default value of the data control. Used to return the default
	 * values while initializing the repeater control.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Control default value.
	 */
	public function get_default_value() {
		return [];
	}

	/**
	 * Get repeater control default settings.
	 *
	 * Retrieve the default settings of the repeater control. Used to return the
	 * default settings while initializing the repeater control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'fields' => [],
			'title_field' => '',
			'prevent_empty' => true,
			'is_repeater' => true,
			'max_items' => 0,
			'min_items' => 0,
			'item_actions' => [
				'add' => true,
				'duplicate' => true,
				'remove' => true,
				'sort' => true,
			],
		];
	}

	/**
	 * Get repeater control value.
	 *
	 * Retrieve the value of the repeater control from a specific Controls_Stack.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $control  Control.
	 * @param array $settings Controls_Stack settings.
	 *
	 * @return mixed Control values.
	 */
	public function get_value( $control, $settings ) {
		$value = parent::get_value( $control, $settings );

		if ( ! empty( $value ) ) {
			foreach ( $value as &$item ) {
				foreach ( $control['fields'] as $field ) {
					$control_obj = Plugin::$instance->controls_manager->get_control( $field['type'] );

					// Prior to 1.5.0 the fields may contains non-data controls.
					if ( ! $control_obj instanceof Base_Data_Control ) {
						continue;
					}

					$item[ $field['name'] ] = $control_obj->get_value( $field, $item );
				}
			}
		}

		return $value;
	}

	/**
	 * Import repeater.
	 *
	 * Used as a wrapper method for inner controls while importing Elementor
	 * template JSON file, and replacing the old data.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @param array $settings     Control settings.
	 * @param array $control_data Optional. Control data. Default is an empty array.
	 *
	 * @return array Control settings.
	 */
	public function on_import( $settings, $control_data = [] ) {
		if ( empty( $settings ) || empty( $control_data['fields'] ) ) {
			return $settings;
		}

		$method = 'on_import';

		foreach ( $settings as &$item ) {
			foreach ( $control_data['fields'] as $field ) {
				if ( empty( $field['name'] ) || empty( $item[ $field['name'] ] ) ) {
					continue;
				}

				$control_obj = Plugin::$instance->controls_manager->get_control( $field['type'] );

				if ( ! $control_obj ) {
					continue;
				}

				if ( method_exists( $control_obj, $method ) ) {
					$item[ $field['name'] ] = $control_obj->{$method}( $item[ $field['name'] ], $field );
				}
			}
		}

		return $settings;
	}

	/**
	 * Render repeater control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		?>
		<label>
			<span class="elementor-control-title">{{{ data.label }}}</span>
		</label>
		<div class="elementor-repeater-fields-wrapper" role="list"></div>
		<# if ( itemActions.add ) { #>
			<div class="elementor-button-wrapper">
				<button class="elementor-button elementor-repeater-add" type="button">
					<i class="eicon-plus" aria-hidden="true"></i>
					<# if ( data.button_text ) { #>
						{{{ data.button_text }}}
					<# } else { #>
						<?php echo esc_html__( 'Add Item', 'elementor' ); ?>
					<# } #>
				</button>
			</div>
		<# } #>
		<?php
	}

	public function validate( array $control_data ): bool {
		if ( isset( $control_data['min_items'] ) ) {

			if (
				! isset( $control_data['default'] ) ||
				count( $control_data['default'] ) < $control_data['min_items']
			) {
				throw new \Exception(
					esc_html__( 'In a Repeater control, if you specify a minimum number of items, you must also specify a default value that contains at least that number of items.', 'elementor' )
				);
			}
		}

		return true;
	}
}
