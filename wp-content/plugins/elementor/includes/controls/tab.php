<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor tab control.
 *
 * A base control for creating tab control. Displays a tab header for a set of
 * controls.
 *
 * Note: Do not use it directly, instead use: `$widget->start_controls_tab()`
 * and in the end `$widget->end_controls_tab()`.
 *
 * @since 1.0.0
 */
class Control_Tab extends Base_UI_Control {

	/**
	 * Get tab control type.
	 *
	 * Retrieve the control type, in this case `tab`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'tab';
	}

	/**
	 * Render tab control output in the editor.
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
			<div class="elementor-panel-tab-heading">
				{{{ data.label }}}
			</div>
		<?php
	}
}
