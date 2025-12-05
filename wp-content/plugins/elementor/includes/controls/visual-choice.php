<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Visual Choice control.
 *
 * This control extends the base Choose control allowing the user to choose between options represented by SVG or Image.
 *
 * @since 3.28.0
 */
class Control_Visual_Choice extends Base_Data_Control {

	public function get_type() {
		return 'visual_choice';
	}

	public function content_template() {
		$control_uid_input_type = '{{value}}';
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="elementor-visual-choice-choices" style="--elementor-visual-choice-columns: {{ data.columns }};">
					<#
					_.each( data.options, function( options, value ) {
						choiceType = options.type || 'image';
					#>
					<div class="elementor-visual-choice-element elementor-visual-choice-element-{{ choiceType }}" style="--elementor-visual-choice-span: {{ data.toggle ? '1' : '0' }};">
						<input id="<?php $this->print_control_uid( $control_uid_input_type ); ?>" type="radio" name="elementor-visual-choice-{{ data.name }}-{{ data._cid }}" value="{{ value }}" class="elementor-screen-only">
						<label class="elementor-visual-choice-label tooltip-target" for="<?php $this->print_control_uid( $control_uid_input_type ); ?>" data-tooltip="{{ options.title }}">
							<#
							switch ( choiceType ) {
								case 'button':
									#>
									<div class="elementor-button">{{{ options.title }}}</div>
									<#
									break;
								case 'image':
								default:
									#>
									<img src="{{ options.image }}" aria-hidden="true" alt="{{ options.title }}" data-hover="{{ value }}" />
									<span class="elementor-screen-only">{{{ options.title }}}</span>
									<#
							};
							#>
						</label>
					</div>
					<# } ); #>
				</div>
			</div>
		</div>

		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	protected function get_default_settings() {
		return [
			'options' => [],
			'toggle' => true,
			'columns' => 1,
		];
	}
}
