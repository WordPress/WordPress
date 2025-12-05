<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor animation control.
 *
 * A base control for creating entrance animation control. Displays a select box
 * with the available entrance animation effects @see Control_Animation::get_animations() .
 *
 * @since 1.0.0
 */
class Control_Animation extends Base_Data_Control {

	/**
	 * Get control type.
	 *
	 * Retrieve the animation control type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'animation';
	}

	/**
	 * Retrieve default control settings.
	 *
	 * Get the default settings of the control. Used to return the default
	 * settings while initializing the control.
	 *
	 * @since 2.5.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		$default_settings['label_block'] = true;
		$default_settings['render_type'] = 'none';

		return $default_settings;
	}

	/**
	 * Get animations list.
	 *
	 * Retrieve the list of all the available animations.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return array Control type.
	 */
	public static function get_animations() {
		$additional_animations = [];

		/**
		 * Entrance animations.
		 *
		 * Filters the animations list displayed in the animations control.
		 *
		 * This hook can be used to register animations in addition to the
		 * basic Elementor animations.
		 *
		 * @since 2.4.0
		 *
		 * @param array $additional_animations Additional animations array.
		 */
		$additional_animations = apply_filters( 'elementor/controls/animations/additional_animations', $additional_animations );

		return array_merge( static::get_default_animations(), $additional_animations );
	}

	public static function get_default_animations() {
		return [
			'Fading' => [
				'fadeIn' => 'Fade In',
				'fadeInDown' => 'Fade In Down',
				'fadeInLeft' => 'Fade In Left',
				'fadeInRight' => 'Fade In Right',
				'fadeInUp' => 'Fade In Up',
			],
			'Zooming' => [
				'zoomIn' => 'Zoom In',
				'zoomInDown' => 'Zoom In Down',
				'zoomInLeft' => 'Zoom In Left',
				'zoomInRight' => 'Zoom In Right',
				'zoomInUp' => 'Zoom In Up',
			],
			'Bouncing' => [
				'bounceIn' => 'Bounce In',
				'bounceInDown' => 'Bounce In Down',
				'bounceInLeft' => 'Bounce In Left',
				'bounceInRight' => 'Bounce In Right',
				'bounceInUp' => 'Bounce In Up',
			],
			'Sliding' => [
				'slideInDown' => 'Slide In Down',
				'slideInLeft' => 'Slide In Left',
				'slideInRight' => 'Slide In Right',
				'slideInUp' => 'Slide In Up',
			],
			'Rotating' => [
				'rotateIn' => 'Rotate In',
				'rotateInDownLeft' => 'Rotate In Down Left',
				'rotateInDownRight' => 'Rotate In Down Right',
				'rotateInUpLeft' => 'Rotate In Up Left',
				'rotateInUpRight' => 'Rotate In Up Right',
			],
			'Attention Seekers' => [
				'bounce' => 'Bounce',
				'flash' => 'Flash',
				'pulse' => 'Pulse',
				'rubberBand' => 'Rubber Band',
				'shake' => 'Shake',
				'headShake' => 'Head Shake',
				'swing' => 'Swing',
				'tada' => 'Tada',
				'wobble' => 'Wobble',
				'jello' => 'Jello',
			],
			'Light Speed' => [
				'lightSpeedIn' => 'Light Speed In',
			],
			'Specials' => [
				'rollIn' => 'Roll In',
			],
		];
	}

	/**
	 * Render animations control template.
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
			<div class="elementor-control-input-wrapper">
				<select id="<?php $this->print_control_uid(); ?>" data-setting="{{ data.name }}">
					<option value=""><?php echo esc_html__( 'Default', 'elementor' ); ?></option>
					<option value="none"><?php echo esc_html__( 'None', 'elementor' ); ?></option>
					<?php foreach ( static::get_animations() as $animations_group_name => $animations_group ) : ?>
						<optgroup label="<?php echo esc_attr( $animations_group_name ); ?>">
							<?php foreach ( $animations_group as $animation_name => $animation_title ) : ?>
								<option value="<?php echo esc_attr( $animation_name ); ?>"><?php echo esc_html( $animation_title ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	public static function get_assets( $setting ) {
		if ( ! $setting || 'none' === $setting ) {
			return [];
		}

		return [
			'styles' => [ 'e-animation-' . $setting ],
		];
	}
}
