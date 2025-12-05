<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor text shadow control.
 *
 * A base control for creating text shadows control. Displays input fields for
 * horizontal shadow, vertical shadow, shadow blur and shadow color.
 *
 * @since 1.6.0
 */
class Control_Text_Shadow extends Control_Base_Multiple {

	/**
	 * Get text shadow control type.
	 *
	 * Retrieve the control type, in this case `text_shadow`.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'text_shadow';
	}

	/**
	 * Get text shadow control default values.
	 *
	 * Retrieve the default value of the text shadow control. Used to return the
	 * default values while initializing the text shadow control.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Control default value.
	 */
	public function get_default_value() {
		return [
			'horizontal' => 0,
			'vertical' => 0,
			'blur' => 10,
			'color' => 'rgba(0,0,0,0.3)',
		];
	}

	/**
	 * Get text shadow control sliders.
	 *
	 * Retrieve the sliders of the text shadow control. Sliders are used while
	 * rendering the control output in the editor.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Control sliders.
	 */
	public function get_sliders() {
		return [
			'blur' => [
				'label' => esc_html__( 'Blur', 'elementor' ),
				'min' => 0,
				'max' => 100,
			],
			'horizontal' => [
				'label' => esc_html__( 'Horizontal', 'elementor' ),
				'min' => -100,
				'max' => 100,
			],
			'vertical' => [
				'label' => esc_html__( 'Vertical', 'elementor' ),
				'min' => -100,
				'max' => 100,
			],
		];
	}

	/**
	 * Render text shadow control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.6.0
	 * @access public
	 */
	public function content_template() {
		?>
		<div class="elementor-shadow-box">
			<div class="elementor-control-field elementor-color-picker-wrapper">
				<label class="elementor-control-title"><?php echo esc_html__( 'Color', 'elementor' ); ?></label>
				<div class="elementor-control-input-wrapper elementor-control-unit-1">
					<div class="elementor-color-picker-placeholder"></div>
				</div>
			</div>
			<?php
			foreach ( $this->get_sliders() as $slider_name => $slider ) :
				?>
				<div class="elementor-shadow-slider elementor-control-type-slider">
					<label for="<?php $this->print_control_uid( $slider_name ); ?>" class="elementor-control-title"><?php echo esc_html( $slider['label'] ); ?></label>
					<div class="elementor-control-input-wrapper">
						<div class="elementor-slider" data-input="<?php echo esc_attr( $slider_name ); ?>"></div>
						<div class="elementor-slider-input elementor-control-unit-2">
							<input id="<?php $this->print_control_uid( $slider_name ); ?>" type="number" min="<?php echo esc_attr( $slider['min'] ); ?>" max="<?php echo esc_attr( $slider['max'] ); ?>" data-setting="<?php echo esc_attr( $slider_name ); ?>"/>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
