<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor textarea control.
 *
 * A base control for creating textarea control. Displays a classic textarea.
 *
 * @since 1.0.0
 */
class Control_Textarea extends Base_Data_Control {

	/**
	 * Get textarea control type.
	 *
	 * Retrieve the control type, in this case `textarea`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'textarea';
	}

	/**
	 * Get textarea control default settings.
	 *
	 * Retrieve the default settings of the textarea control. Used to return the
	 * default settings while initializing the textarea control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
			'rows' => 5,
			'placeholder' => '',
			'ai' => [
				'active' => true,
				'type' => 'textarea',
			],
			'dynamic' => [
				'categories' => [ TagsModule::TEXT_CATEGORY ],
			],
		];
	}

	/**
	 * Render textarea control output in the editor.
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
		<div class="elementor-control-field">
			<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper">
				<textarea id="<?php $this->print_control_uid(); ?>" class="elementor-control-tag-area" rows="{{ data.rows }}" data-setting="{{ data.name }}" placeholder="{{ view.getControlPlaceholder() }}"></textarea>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
