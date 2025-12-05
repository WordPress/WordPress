<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor font control.
 *
 * A base control for creating font control. Displays font select box. The
 * control allows you to set a list of fonts.
 *
 * @since 1.0.0
 */
class Control_Font extends Base_Data_Control {

	/**
	 * Get font control type.
	 *
	 * Retrieve the control type, in this case `font`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'font';
	}

	/**
	 * Get font control default settings.
	 *
	 * Retrieve the default settings of the font control. Used to return the default
	 * settings while initializing the font control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'groups' => Fonts::get_font_groups(),
			'options' => Fonts::get_fonts(),
		];
	}

	/**
	 * Render font control output in the editor.
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
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<select id="<?php $this->print_control_uid(); ?>" class="elementor-control-font-family" data-setting="{{ data.name }}">
					<option value=""><?php echo esc_html__( 'Default', 'elementor' ); ?></option>
					<# _.each( data.groups, function( group_label, group_name ) {
						var groupFonts = getFontsByGroups( group_name );
						if ( ! _.isEmpty( groupFonts ) ) { #>
						<optgroup label="{{ group_label }}">
							<# _.each( groupFonts, function( fontType, fontName ) { #>
								<option value="{{ fontName }}">{{{ fontName }}}</option>
							<# } ); #>
						</optgroup>
						<# }
					}); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
