<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor control base units.
 *
 * An abstract class for creating new unit controls in the panel.
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Control_Base_Units extends Control_Base_Multiple {

	/**
	 * Get units control default value.
	 *
	 * Retrieve the default value of the units control. Used to return the default
	 * values while initializing the units control.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Control default value.
	 */
	public function get_default_value() {
		return [
			'unit' => 'px',
		];
	}

	/**
	 * Get units control default settings.
	 *
	 * Retrieve the default settings of the units control. Used to return the default
	 * settings while initializing the units control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'em' => [
					'min' => 0.1,
					'max' => 10,
					'step' => 0.1,
				],
				'rem' => [
					'min' => 0.1,
					'max' => 10,
					'step' => 0.1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'deg' => [
					'min' => 0,
					'max' => 360,
					'step' => 1,
				],
				'grad' => [
					'min' => 0,
					'max' => 400,
					'step' => 1,
				],
				'rad' => [
					'min' => 0,
					'max' => 6.2832,
					'step' => 0.0001,
				],
				'turn' => [
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
				],
				'vh' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'vw' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				's' => [
					'min' => 0,
					'max' => 3,
					'step' => 0.1,
				],
				'ms' => [
					'min' => 0,
					'max' => 3000,
					'step' => 100,
				],
			],
		];
	}

	/**
	 * Print units control settings.
	 *
	 * Used to generate the units control template in the editor.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function print_units_template() {
		?>
		<# if ( data.size_units && data.size_units.length > 1 ) { #>
		<div class="e-units-wrapper">
			<div class="e-units-switcher">
				<span></span>
				<i class="eicon-edit" aria-hidden="true"></i>
				<i class="eicon-angle-right" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Switch units', 'elementor' ); ?></span>
			</div>
			<div class="e-units-choices">
				<# _.each( data.size_units, function( unit ) { #>
				<input id="elementor-choose-{{ data._cid + data.name + unit }}" type="radio" name="elementor-choose-{{ data.name + data._cid }}" data-setting="unit" value="{{ unit }}">
				<label class="elementor-units-choices-label" for="elementor-choose-{{ data._cid + data.name + unit }}" data-choose="{{{ unit }}}">
					<# if ( 'custom' === unit ) { #>
						<i class="eicon-edit" aria-hidden="true"></i>
						<span class="elementor-screen-only"><?php echo esc_html__( 'Custom unit', 'elementor' ); ?></span>
					<# } else { #>
						<span>{{{ unit }}}</span>
					<# } #>
				</label>
				<# } ); #>
			</div>
		</div>
		<# } #>
		<?php
	}

	public function get_style_value( $css_property, $control_value, array $control_data ) {
		$return_value = parent::get_style_value( $css_property, $control_value, $control_data );

		if ( 'UNIT' === $css_property && 'custom' === $return_value ) {
			$return_value = '__EMPTY__';
		}

		return $return_value;
	}
}
