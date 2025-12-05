<?php
namespace Elementor\Core\Kits\Controls;

use Elementor\Control_Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Repeater extends Control_Repeater {

	const CONTROL_TYPE = 'global-style-repeater';

	/**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `global-style-repeater`.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return self::CONTROL_TYPE;
	}

	/**
	 * Get repeater control default settings.
	 *
	 * Retrieve the default settings of the repeater control. Used to return the
	 * default settings while initializing the repeater control.
	 *
	 * @since 3.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		$settings = parent::get_default_settings();

		$settings['item_actions']['duplicate'] = false;

		return $settings;
	}

	/**
	 * Render repeater control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function content_template() {
		?>
		<div class="elementor-repeater-fields-wrapper" role="list"></div>
		<# if ( itemActions.add ) { #>
			<div class="elementor-button-wrapper">
				<button class="elementor-button elementor-repeater-add" type="button">
					<i class="eicon-plus" aria-hidden="true"></i>
					<span class="elementor-repeater__add-button__text">{{{ addButtonText }}}</span>
				</button>
			</div>
		<# } #>
		<?php
	}
}
