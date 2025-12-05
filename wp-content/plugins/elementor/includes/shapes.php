<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor shapes.
 *
 * Elementor shapes handler class is responsible for setting up the supported
 * shape dividers.
 *
 * @since 1.3.0
 */
class Shapes {

	/**
	 * The exclude filter.
	 */
	const FILTER_EXCLUDE = 'exclude';

	/**
	 * The include filter.
	 */
	const FILTER_INCLUDE = 'include';

	/**
	 * Shapes.
	 *
	 * Holds the list of supported shapes.
	 *
	 * @since 1.3.0
	 * @access private
	 * @static
	 *
	 * @var array A list of supported shapes.
	 */
	private static $shapes;

	/**
	 * Get shapes.
	 *
	 * Retrieve a shape from the lists of supported shapes. If no shape specified
	 * it will return all the supported shapes.
	 *
	 * @since 1.3.0
	 * @access public
	 * @static
	 *
	 * @param array $shape Optional. Specific shape. Default is `null`.
	 *
	 * @return array The specified shape or a list of all the supported shapes.
	 */
	public static function get_shapes( $shape = null ) {
		if ( null === self::$shapes ) {
			self::init_shapes();
		}

		if ( $shape ) {
			return isset( self::$shapes[ $shape ] ) ? self::$shapes[ $shape ] : null;
		}

		return self::$shapes;
	}

	/**
	 * Filter shapes.
	 *
	 * Retrieve shapes filtered by a specific condition, from the list of
	 * supported shapes.
	 *
	 * @since 1.3.0
	 * @access public
	 * @static
	 *
	 * @param string $by     Specific condition to filter by.
	 * @param string $filter Optional. Comparison condition to filter by.
	 *                       Default is `include`.
	 *
	 * @return array A list of filtered shapes.
	 */
	public static function filter_shapes( $by, $filter = self::FILTER_INCLUDE ) {
		return array_filter(
			self::get_shapes(), function( $shape ) use ( $by, $filter ) {
				return self::FILTER_INCLUDE === $filter xor empty( $shape[ $by ] );
			}
		);
	}

	/**
	 * Get shape path.
	 *
	 * For a given shape, retrieve the file path.
	 *
	 * @since 1.3.0
	 * @access public
	 * @static
	 *
	 * @param string $shape       The shape.
	 * @param bool   $is_negative Optional. Whether the file name is negative or
	 *                            not. Default is `false`.
	 *
	 * @return string Shape file path.
	 */
	public static function get_shape_path( $shape, $is_negative = false ) {
		if ( ! isset( self::$shapes[ $shape ] ) ) {
			return '';
		}

		if ( isset( self::$shapes[ $shape ]['path'] ) ) {
			$path = self::$shapes[ $shape ]['path'];
			return ( $is_negative ) ? str_replace( '.svg', '-negative.svg', $path ) : $path;
		}

		$file_name = $shape;

		if ( $is_negative ) {
			$file_name .= '-negative';
		}

		return ELEMENTOR_PATH . 'assets/shapes/' . $file_name . '.svg';
	}

	/**
	 * Init shapes.
	 *
	 * Set the supported shapes.
	 *
	 * @since 1.3.0
	 * @access private
	 * @static
	 */
	private static function init_shapes() {
		$native_shapes = [
			'mountains' => [
				'title' => esc_html_x( 'Mountains', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/mountains.svg',
				'has_flip' => true,
			],
			'drops' => [
				'title' => esc_html_x( 'Drops', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/drops.svg',
				'has_negative' => true,
				'has_flip' => true,
				'height_only' => true,
			],
			'clouds' => [
				'title' => esc_html_x( 'Clouds', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/clouds.svg',
				'has_negative' => true,
				'has_flip' => true,
				'height_only' => true,
			],
			'zigzag' => [
				'title' => esc_html_x( 'Zigzag', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/zigzag.svg',
			],
			'pyramids' => [
				'title' => esc_html_x( 'Pyramids', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/pyramids.svg',
				'has_negative' => true,
				'has_flip' => true,
			],
			'triangle' => [
				'title' => esc_html_x( 'Triangle', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/triangle.svg',
				'has_negative' => true,
			],
			'triangle-asymmetrical' => [
				'title' => esc_html_x( 'Triangle Asymmetrical', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/triangle-asymmetrical.svg',
				'has_negative' => true,
				'has_flip' => true,
			],
			'tilt' => [
				'title' => esc_html_x( 'Tilt', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/tilt.svg',
				'has_flip' => true,
				'height_only' => true,
			],
			'opacity-tilt' => [
				'title' => esc_html_x( 'Tilt Opacity', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/opacity-tilt.svg',
				'has_flip' => true,
			],
			'opacity-fan' => [
				'title' => esc_html_x( 'Fan Opacity', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/opacity-fan.svg',
			],
			'curve' => [
				'title' => esc_html_x( 'Curve', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/curve.svg',
				'has_negative' => true,
			],
			'curve-asymmetrical' => [
				'title' => esc_html_x( 'Curve Asymmetrical', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/curve-asymmetrical.svg',
				'has_negative' => true,
				'has_flip' => true,
			],
			'waves' => [
				'title' => esc_html_x( 'Waves', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/waves.svg',
				'has_negative' => true,
				'has_flip' => true,
			],
			'wave-brush' => [
				'title' => esc_html_x( 'Waves Brush', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/wave-brush.svg',
				'has_flip' => true,
			],
			'waves-pattern' => [
				'title' => esc_html_x( 'Waves Pattern', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/waves-pattern.svg',
				'has_flip' => true,
			],
			'book' => [
				'title' => esc_html_x( 'Book', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/book.svg',
				'has_negative' => true,
			],
			'split' => [
				'title' => esc_html_x( 'Split', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/split.svg',
				'has_negative' => true,
			],
			'arrow' => [
				'title' => esc_html_x( 'Arrow', 'Shapes', 'elementor' ),
				'image' => ELEMENTOR_ASSETS_URL . 'shapes/arrow.svg',
				'has_negative' => true,
			],
		];

		self::$shapes = array_merge( $native_shapes, self::get_additional_shapes() );
	}

	/**
	 * Get Additional Shapes
	 *
	 * Used to add custom shapes to elementor.
	 *
	 * @since 2.5.0
	 *
	 * @return array
	 */
	private static function get_additional_shapes() {
		static $additional_shapes = null;

		if ( null !== $additional_shapes ) {
			return $additional_shapes;
		}

		$additional_shapes = [];

		/**
		 * Additional shapes.
		 *
		 * Filters the shapes used by Elementor to add additional shapes.
		 *
		 * @since 2.0.1
		 *
		 * @param array $additional_shapes Additional Elementor shapes.
		 */
		$additional_shapes = apply_filters( 'elementor/shapes/additional_shapes', $additional_shapes );

		// BC for addons that add additional shapes the old way using `url` instead of `image`.
		foreach ( $additional_shapes as $shape_name => $shape_settings ) {
			if ( ! isset( $shape_settings['image'] ) && isset( $shape_settings['url'] ) ) {
				$additional_shapes[ $shape_name ]['image'] = $shape_settings['url'];
			}
		}

		return $additional_shapes;
	}

	/**
	 * Get Additional Shapes For Config
	 *
	 * Used to set additional shape paths for editor
	 *
	 * @since 2.5.0
	 *
	 * @return array|bool
	 */
	public static function get_additional_shapes_for_config() {
		$additional_shapes = self::get_additional_shapes();
		if ( empty( $additional_shapes ) ) {
			return false;
		}

		$additional_shapes_config = [];
		foreach ( $additional_shapes as $shape_name => $shape_settings ) {
			if ( ! isset( $shape_settings['url'] ) ) {
				continue;
			}
			$additional_shapes_config[ $shape_name ] = $shape_settings['url'];
		}

		if ( empty( $additional_shapes_config ) ) {
			return false;
		}

		return $additional_shapes_config;
	}
}
