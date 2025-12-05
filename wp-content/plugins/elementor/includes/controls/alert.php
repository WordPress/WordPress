<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor alert control.
 *
 * A base control for creating alerts in the Editor panels.
 *
 * @since 3.19.0
 */
class Control_Alert extends Base_UI_Control {

	/**
	 * Get alert control type.
	 *
	 * Retrieve the control type, in this case `alert`.
	 *
	 * @since 3.19.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'alert';
	}

	/**
	 * Render alert control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 3.19.0
	 * @access public
	 */
	public function content_template() {
		?>
		<#
		const validAlertTypes = [ 'info', 'success', 'warning', 'danger' ];
		if ( ! validAlertTypes.includes( data.alert_type ) ) {
			data.alert_type = 'info';
		}
		data.content = elementor.compileTemplate( data.content, { view } );
		#>
		<div class="elementor-control-alert elementor-panel-alert elementor-panel-alert-{{ data.alert_type }}">
			<# if ( data.heading ) { #>
			<div class="elementor-control-alert-heading">{{{ data.heading }}}</div>
			<# } #>
			<# if ( data.content ) { #>
			<div class="elementor-control-alert-content ">{{{ data.content }}}</div>
			<# } #>
		</div>
		<?php
	}

	/**
	 * Get alert control default settings.
	 *
	 * Retrieve the default settings of the alert control. Used to return the
	 * default settings while initializing the alert control.
	 *
	 * @since 3.19.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'alert_type' => '', // info, success, warning, danger.
			'heading' => '',
			'content' => '',
		];
	}
}
