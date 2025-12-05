<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor color control.
 *
 * A base control for creating color control. Displays a color picker field with
 * an alpha slider. Includes a customizable color palette that can be preset by
 * the user. Accepts a `scheme` argument that allows you to set a value from the
 * active color scheme as the default value returned by the control.
 *
 * @since 1.0.0
 */
class Control_Color extends Base_Data_Control {

	/**
	 * Get color control type.
	 *
	 * Retrieve the control type, in this case `color`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'color';
	}

	/**
	 * Render color control output in the editor.
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
			<label class="elementor-control-title">{{{ data.label || '' }}}</label>
			<div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper elementor-control-unit-5">
				<div class="elementor-color-picker-placeholder"></div>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * Get color control default settings.
	 *
	 * Retrieve the default settings of the color control. Used to return the default
	 * settings while initializing the color control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'alpha' => true,
			'scheme' => '',
			'dynamic' => [
				'categories' => [
					TagsModule::COLOR_CATEGORY,
				],
				'active' => true,
			],
			'global' => [
				'active' => true,
			],
		];
	}
}
