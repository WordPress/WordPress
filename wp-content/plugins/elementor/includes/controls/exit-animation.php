<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor exit animation control.
 *
 * A control for creating exit animation. Displays a select box
 * with the available exit animation effects @see Control_Exit_Animation::get_animations() .
 *
 * @since 2.5.0
 */
class Control_Exit_Animation extends Control_Animation {

	/**
	 * Get control type.
	 *
	 * Retrieve the animation control type.
	 *
	 * @since 2.5.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'exit_animation';
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
		 * Exit animations.
		 *
		 * Filters the animations list displayed in the exit animations control.
		 *
		 * This hook can be used to register new animations in addition to the
		 * basic Elementor exit animations.
		 *
		 * @since 2.5.0
		 *
		 * @param array $additional_animations Additional animations array.
		 */
		$additional_animations = apply_filters( 'elementor/controls/exit-animations/additional_animations', $additional_animations );

		return array_merge( static::get_default_animations(), $additional_animations );
	}

	public static function get_default_animations(): array {
		return [
			'Fading' => [
				'fadeIn' => 'Fade Out',
				'fadeInDown' => 'Fade Out Up',
				'fadeInLeft' => 'Fade Out Left',
				'fadeInRight' => 'Fade Out Right',
				'fadeInUp' => 'Fade Out Down',
			],
			'Zooming' => [
				'zoomIn' => 'Zoom Out',
				'zoomInDown' => 'Zoom Out Up',
				'zoomInLeft' => 'Zoom Out Left',
				'zoomInRight' => 'Zoom Out Right',
				'zoomInUp' => 'Zoom Out Down',
			],
			'Sliding' => [
				'slideInDown' => 'Slide Out Up',
				'slideInLeft' => 'Slide Out Left',
				'slideInRight' => 'Slide Out Right',
				'slideInUp' => 'Slide Out Down',
			],
			'Rotating' => [
				'rotateIn' => 'Rotate Out',
				'rotateInDownLeft' => 'Rotate Out Up Left',
				'rotateInDownRight' => 'Rotate Out Up Right',
				'rotateInUpRight' => 'Rotate Out Down Left',
				'rotateInUpLeft' => 'Rotate Out Down Right',
			],
			'Light Speed' => [
				'lightSpeedIn' => 'Light Speed Out',
			],
			'Specials' => [
				'rollIn' => 'Roll Out',
			],
		];
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
