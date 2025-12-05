<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor structure control.
 *
 * A base control for creating structure control. A private control for section
 * columns structure.
 *
 * @since 1.0.0
 */
class Control_Structure extends Base_Data_Control {

	/**
	 * Get structure control type.
	 *
	 * Retrieve the control type, in this case `structure`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'structure';
	}

	/**
	 * Render structure control output in the editor.
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
			<div class="elementor-control-input-wrapper">
				<#
				var morePresets = getMorePresets();

				if ( morePresets.length ) { #>
					<div class="elementor-control-structure-presets">
						<# _.each( morePresets, function( preset ) { #>
							<div class="elementor-control-structure-preset-wrapper">
								<input id="<?php $this->print_control_uid( '{{ preset.key }}' ); ?>" type="radio" name="elementor-control-structure-preset-{{ data._cid }}" data-setting="structure" value="{{ preset.key }}">
								<label for="<?php $this->print_control_uid( '{{ preset.key }}' ); ?>" class="elementor-control-structure-preset">
									{{{ elementor.presetsFactory.getPresetSVG( preset.preset, 102, 42 ).outerHTML }}}
								</label>
								<div class="elementor-control-structure-preset-title">{{{ preset.preset.join( ', ' ) }}}</div>
							</div>
						<# } ); #>
					</div>
				<# } #>
			</div>
			<div class="elementor-control-structure-reset">
				<i class="eicon-undo" aria-hidden="true"></i>
				<?php echo esc_html__( 'Reset', 'elementor' ); ?>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * Get structure control default settings.
	 *
	 * Retrieve the default settings of the structure control. Used to return the
	 * default settings while initializing the structure control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
			'show_label' => false,
		];
	}
}
