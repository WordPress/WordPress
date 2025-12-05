<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor hidden control.
 *
 * A base control for creating hidden control. Used to save additional data in
 * the database without a visual presentation in the panel.
 *
 * @since 1.0.0
 */
class Control_Hidden extends Base_Data_Control {

	/**
	 * Get hidden control type.
	 *
	 * Retrieve the control type, in this case `hidden`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'hidden';
	}

	/**
	 * Render hidden control output in the editor.
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
		<input type="hidden" data-setting="{{{ data.name }}}" />
		<?php
	}
}
