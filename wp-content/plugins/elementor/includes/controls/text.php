<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor text control.
 *
 * A base control for creating text control. Displays a simple text input.
 *
 * @since 1.0.0
 */
class Control_Text extends Base_Data_Control {

	/**
	 * Get text control type.
	 *
	 * Retrieve the control type, in this case `text`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'text';
	}

	/**
	 * Render text control output in the editor.
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
			<# if ( data.label ) {#>
				<label for="<?php $this->print_control_uid(); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5 elementor-control-dynamic-switcher-wrapper">
				<input id="<?php $this->print_control_uid(); ?>" type="{{ data.input_type }}" class="tooltip-target elementor-control-tag-area" data-tooltip="{{ data.title }}" data-setting="{{ data.name }}" placeholder="{{ view.getControlPlaceholder() }}" />
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * Get text control default settings.
	 *
	 * Retrieve the default settings of the text control. Used to return the
	 * default settings while initializing the text control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'input_type' => 'text',
			'placeholder' => '',
			'title' => '',
			'ai' => [
				'active' => true,
				'type' => 'text',
			],
			'dynamic' => [
				'categories' => [
					TagsModule::TEXT_CATEGORY,
				],
			],
		];
	}
}
