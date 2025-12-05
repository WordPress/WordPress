<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor section control.
 *
 * A base control for creating section control. Displays a header that
 * functions as a toggle to show or hide a set of controls.
 *
 * Note: Do not use it directly, instead use `$widget->start_controls_section()`
 * and `$widget->end_controls_section()` to wrap a set of controls.
 *
 * @since 1.0.0
 */
class Control_Section extends Base_UI_Control {

	/**
	 * Get section control type.
	 *
	 * Retrieve the control type, in this case `section`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'section';
	}

	/**
	 * Render section control output in the editor.
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
		<button class="elementor-panel-heading">
			<div class="elementor-panel-heading-toggle elementor-section-toggle" data-collapse_id="{{ data.name }}">
				<i class="eicon-caret-right" aria-hidden="true"></i>
			</div>
			<div class="elementor-panel-heading-title elementor-section-title">{{{ data.label }}}</div>
		</button>
		<?php
	}
}
