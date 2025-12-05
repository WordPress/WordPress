<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

/**
 * Elementor divider widget.
 *
 * Elementor widget that displays a line that divides different elements in the
 * page.
 *
 * @since 1.0.0
 */
class Widget_Divider extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve divider widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'divider';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve divider widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Divider', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve divider widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-divider';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the divider widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'divider', 'hr', 'line', 'border' ];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 3.24.0
	 * @access public
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'widget-divider' ];
	}

	private static function get_additional_styles() {
		static $additional_styles = null;

		if ( null !== $additional_styles ) {
			return $additional_styles;
		}
		$additional_styles = [];
		/**
		 * Additional Styles.
		 *
		 * Filters the styles used by Elementor to add additional divider styles.
		 *
		 * @since 2.7.0
		 *
		 * @param array $additional_styles Additional Elementor divider styles.
		 */
		$additional_styles = apply_filters( 'elementor/divider/styles/additional_styles', $additional_styles );
		return $additional_styles;
	}

	private function get_separator_styles() {
		return array_merge(
			self::get_additional_styles(),
			[
				'curly'   => [
					'label' => esc_html_x( 'Curly', 'Shapes', 'elementor' ),
					'shape' => '<path d="M0,21c3.3,0,8.3-0.9,15.7-7.1c6.6-5.4,4.4-9.3,2.4-10.3c-3.4-1.8-7.7,1.3-7.3,8.8C11.2,20,17.1,21,24,21"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'line',
				],
				'curved'   => [
					'label' => esc_html_x( 'Curved', 'Shapes', 'elementor' ),
					'shape' => '<path d="M0,6c6,0,6,13,12,13S18,6,24,6"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'line',
				],
				'multiple'   => [
					'label' => esc_html_x( 'Multiple', 'Shapes', 'elementor' ),
					'shape' => '<path d="M24,8v12H0V8H24z M24,4v1H0V4H24z"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => false,
					'round' => false,
					'group' => 'pattern',
				],
				'slashes' => [
					'label' => esc_html_x( 'Slashes', 'Shapes', 'elementor' ),
					'shape' => '<g transform="translate(-12.000000, 0)"><path d="M28,0L10,18"/><path d="M18,0L0,18"/><path d="M48,0L30,18"/><path d="M38,0L20,18"/></g>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'view_box' => '0 0 20 16',
					'group' => 'line',
				],
				'squared' => [
					'label' => esc_html_x( 'Squared', 'Shapes', 'elementor' ),
					'shape' => '<polyline points="0,6 6,6 6,18 18,18 18,6 24,6 	"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'line',
				],
				'wavy'   => [
					'label' => esc_html_x( 'Wavy', 'Shapes', 'elementor' ),
					'shape' => '<path d="M0,6c6,0,0.9,11.1,6.9,11.1S18,6,24,6"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'line',
				],
				'zigzag'  => [
					'label' => esc_html_x( 'Zigzag', 'Shapes', 'elementor' ),
					'shape' => '<polyline points="0,18 12,6 24,18 "/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'line',
				],
				'arrows'   => [
					'label' => esc_html_x( 'Arrows', 'Shapes', 'elementor' ),
					'shape' => '<path d="M14.2,4c0.3,0,0.5,0.1,0.7,0.3l7.9,7.2c0.2,0.2,0.3,0.4,0.3,0.7s-0.1,0.5-0.3,0.7l-7.9,7.2c-0.2,0.2-0.4,0.3-0.7,0.3s-0.5-0.1-0.7-0.3s-0.3-0.4-0.3-0.7l0-2.9l-11.5,0c-0.4,0-0.7-0.3-0.7-0.7V9.4C1,9,1.3,8.7,1.7,8.7l11.5,0l0-3.6c0-0.3,0.1-0.5,0.3-0.7S13.9,4,14.2,4z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => true,
					'round' => true,
					'group' => 'pattern',
				],
				'pluses'   => [
					'label' => esc_html_x( 'Pluses', 'Shapes', 'elementor' ),
					'shape' => '<path d="M21.4,9.6h-7.1V2.6c0-0.9-0.7-1.6-1.6-1.6h-1.6c-0.9,0-1.6,0.7-1.6,1.6v7.1H2.6C1.7,9.6,1,10.3,1,11.2v1.6c0,0.9,0.7,1.6,1.6,1.6h7.1v7.1c0,0.9,0.7,1.6,1.6,1.6h1.6c0.9,0,1.6-0.7,1.6-1.6v-7.1h7.1c0.9,0,1.6-0.7,1.6-1.6v-1.6C23,10.3,22.3,9.6,21.4,9.6z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => true,
					'round' => false,
					'group' => 'pattern',
				],
				'rhombus'   => [
					'label' => esc_html_x( 'Rhombus', 'Shapes', 'elementor' ),
					'shape' => '<path d="M12.7,2.3c-0.4-0.4-1.1-0.4-1.5,0l-8,9.1c-0.3,0.4-0.3,0.9,0,1.2l8,9.1c0.4,0.4,1.1,0.4,1.5,0l8-9.1c0.3-0.4,0.3-0.9,0-1.2L12.7,2.3z"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'pattern',
				],
				'parallelogram'   => [
					'label' => esc_html_x( 'Parallelogram', 'Shapes', 'elementor' ),
					'shape' => '<polygon points="9.4,2 24,2 14.6,21.6 0,21.6"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => false,
					'group' => 'pattern',
				],
				'rectangles'   => [
					'label' => esc_html_x( 'Rectangles', 'Shapes', 'elementor' ),
					'shape' => '<rect x="15" y="0" width="30" height="30"/>',
					'preserve_aspect_ratio' => false,
					'supports_amount' => true,
					'round' => true,
					'group' => 'pattern',
					'view_box' => '0 0 60 30',
				],
				'dots_tribal'   => [
					'label' => esc_html_x( 'Dots', 'Shapes', 'elementor' ),
					'shape' => '<path d="M3,10.2c2.6,0,2.6,2,2.6,3.2S4.4,16.5,3,16.5s-3-1.4-3-3.2S0.4,10.2,3,10.2z M18.8,10.2c1.7,0,3.2,1.4,3.2,3.2s-1.4,3.2-3.2,3.2c-1.7,0-3.2-1.4-3.2-3.2S17,10.2,18.8,10.2z M34.6,10.2c1.5,0,2.6,1.4,2.6,3.2s-0.5,3.2-1.9,3.2c-1.5,0-3.4-1.4-3.4-3.2S33.1,10.2,34.6,10.2z M50.5,10.2c1.7,0,3.2,1.4,3.2,3.2s-1.4,3.2-3.2,3.2c-1.7,0-3.3-0.9-3.3-2.6S48.7,10.2,50.5,10.2z M66.2,10.2c1.5,0,3.4,1.4,3.4,3.2s-1.9,3.2-3.4,3.2c-1.5,0-2.6-0.4-2.6-2.1S64.8,10.2,66.2,10.2z M82.2,10.2c1.7,0.8,2.6,1.4,2.6,3.2s-0.1,3.2-1.6,3.2c-1.5,0-3.7-1.4-3.7-3.2S80.5,9.4,82.2,10.2zM98.6,10.2c1.5,0,2.6,0.4,2.6,2.1s-1.2,4.2-2.6,4.2c-1.5,0-3.7-0.4-3.7-2.1S97.1,10.2,98.6,10.2z M113.4,10.2c1.2,0,2.2,0.9,2.2,3.2s-0.1,3.2-1.3,3.2s-3.1-1.4-3.1-3.2S112.2,10.2,113.4,10.2z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 126 26',
				],
				'trees_2_tribal'   => [
					'label' => esc_html_x( 'Fir Tree', 'Shapes', 'elementor' ),
					'shape' => '<path d="M111.9,18.3v3.4H109v-3.4H111.9z M90.8,18.3v3.4H88v-3.4H90.8z M69.8,18.3v3.4h-2.9v-3.4H69.8z M48.8,18.3v3.4h-2.9v-3.4H48.8z M27.7,18.3v3.4h-2.9v-3.4H27.7z M6.7,18.3v3.4H3.8v-3.4H6.7z M46.4,4l4.3,4.8l-1.8,0l3.5,4.4l-2.2-0.1l3,3.3l-11,0.4l3.6-3.8l-2.9-0.1l3.1-4.2l-1.9,0L46.4,4z M111.4,4l2.4,4.8l-1.8,0l3.5,4.4l-2.5-0.1l3.3,3.3h-11l3.1-3.4l-2.5-0.1l3.1-4.2l-1.9,0L111.4,4z M89.9,4l2.9,4.8l-1.9,0l3.2,4.2l-2.5,0l3.5,3.5l-11-0.4l3-3.1l-2.4,0L88,8.8l-1.9,0L89.9,4z M68.6,4l3,4.4l-1.9,0.1l3.4,4.1l-2.7,0.1l3.8,3.7H63.8l2.9-3.6l-2.9,0.1L67,8.7l-2,0.1L68.6,4z M26.5,4l3,4.4l-1.9,0.1l3.7,4.7l-2.5-0.1l3.3,3.3H21l3.1-3.4l-2.5-0.1l3.2-4.3l-2,0.1L26.5,4z M4.9,4l3.7,4.8l-1.5,0l3.1,4.2L7.6,13l3.4,3.4H0l3-3.3l-2.3,0.1l3.5-4.4l-2.3,0L4.9,4z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 126 26',
				],
				'rounds_tribal'   => [
					'label' => esc_html_x( 'Half Rounds', 'Shapes', 'elementor' ),
					'shape' => '<path d="M11.9,15.9L11.9,15.9L0,16c-0.2-3.7,1.5-5.7,4.9-6C10,9.6,12.4,14.2,11.9,15.9zM26.9,15.9L26.9,15.9L15,16c0.5-3.7,2.5-5.7,5.9-6C26,9.6,27.4,14.2,26.9,15.9z M37.1,10c3.4,0.3,5.1,2.3,4.9,6H30.1C29.5,14.4,31.9,9.6,37.1,10z M57,15.9L57,15.9L45,16c0-3.4,1.6-5.4,4.9-5.9C54.8,9.3,57.4,14.2,57,15.9z M71.9,15.9L71.9,15.9L60,16c-0.2-3.7,1.5-5.7,4.9-6C70,9.6,72.4,14.2,71.9,15.9z M82.2,10c3.4,0.3,5,2.3,4.8,6H75.3C74,13,77.1,9.6,82.2,10zM101.9,15.9L101.9,15.9L90,16c-0.2-3.7,1.5-5.7,4.9-6C100,9.6,102.4,14.2,101.9,15.9z M112.1,10.1c2.7,0.5,4.3,2.5,4.9,5.9h-11.9l0,0C104.5,14.4,108,9.3,112.1,10.1z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 120 26',
				],
				'leaves_tribal'   => [
					'label' => esc_html_x( 'Leaves', 'Shapes', 'elementor' ),
					'shape' => '<path d="M3,1.5C5,4.9,6,8.8,6,13s-1.7,8.1-5,11.5C0.3,21.1,0,17.2,0,13S1,4.9,3,1.5z M16,1.5c2,3.4,3,7.3,3,11.5s-1,8.1-3,11.5c-2-4.1-3-8.3-3-12.5S14,4.3,16,1.5z M29,1.5c2,4.8,3,9.3,3,13.5s-1,7.4-3,9.5c-2-3.4-3-7.3-3-11.5S27,4.9,29,1.5z M41.1,1.5C43.7,4.9,45,8.8,45,13s-1,8.1-3,11.5c-2-3.4-3-7.3-3-11.5S39.7,4.9,41.1,1.5zM55,1.5c2,2.8,3,6.3,3,10.5s-1.3,8.4-4,12.5c-1.3-3.4-2-7.3-2-11.5S53,4.9,55,1.5z M68,1.5c2,3.4,3,7.3,3,11.5s-0.7,8.1-2,11.5c-2.7-4.8-4-9.3-4-13.5S66,3.6,68,1.5z M82,1.5c1.3,4.8,2,9.3,2,13.5s-1,7.4-3,9.5c-2-3.4-3-7.3-3-11.5S79.3,4.9,82,1.5z M94,1.5c2,3.4,3,7.3,3,11.5s-1.3,8.1-4,11.5c-1.3-1.4-2-4.3-2-8.5S92,6.9,94,1.5z M107,1.5c2,2.1,3,5.3,3,9.5s-0.7,8.7-2,13.5c-2.7-3.4-4-7.3-4-11.5S105,4.9,107,1.5z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 117 26',
				],
				'stripes_tribal'   => [
					'label' => esc_html_x( 'Stripes', 'Shapes', 'elementor' ),
					'shape' => '<path d="M54,1.6V26h-9V2.5L54,1.6z M69,1.6v23.3L60,26V1.6H69z M24,1.6v23.5l-9-0.6V1.6H24z M30,0l9,0.7v24.5h-9V0z M9,2.5v22H0V3.7L9,2.5z M75,1.6l9,0.9v22h-9V1.6z M99,2.7v21.7h-9V3.8L99,2.7z M114,3.8v20.7l-9-0.5V3.8L114,3.8z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 120 26',
				],
				'squares_tribal'   => [
					'label' => esc_html_x( 'Squares', 'Shapes', 'elementor' ),
					'shape' => '<path d="M46.8,7.8v11.5L36,18.6V7.8H46.8z M82.4,7.8L84,18.6l-12,0.7L70.4,7.8H82.4z M0,7.8l12,0.9v9.9H1.3L0,7.8z M30,7.8v10.8H19L18,7.8H30z M63.7,7.8L66,18.6H54V9.5L63.7,7.8z M89.8,7L102,7.8v10.8H91.2L89.8,7zM108,7.8l12,0.9v8.9l-12,1V7.8z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 126 26',
				],
				'trees_tribal'   => [
					'label' => esc_html_x( 'Trees', 'Shapes', 'elementor' ),
					'shape' => '<path d="M6.4,2l4.2,5.7H7.7v2.7l3.8,5.2l-3.8,0v7.8H4.8v-7.8H0l4.8-5.2V7.7H1.1L6.4,2z M25.6,2L31,7.7h-3.7v2.7l4.8,5.2h-4.8v7.8h-2.8v-7.8l-3.8,0l3.8-5.2V7.7h-2.9L25.6,2z M47.5,2l4.2,5.7h-3.3v2.7l3.8,5.2l-3.8,0l0.4,7.8h-2.8v-7.8H41l4.8-5.2V7.7h-3.7L47.5,2z M66.2,2l5.4,5.7h-3.7v2.7l4.8,5.2h-4.8v7.8H65v-7.8l-3.8,0l3.8-5.2V7.7h-2.9L66.2,2zM87.4,2l4.8,5.7h-2.9v3.1l3.8,4.8l-3.8,0v7.8h-2.8v-7.8h-4.8l4.8-4.8V7.7h-3.7L87.4,2z M107.3,2l5.4,5.7h-3.7v2.7l4.8,5.2h-4.8v7.8H106v-7.8l-3.8,0l3.8-5.2V7.7h-2.9L107.3,2z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 123 26',
				],
				'planes_tribal'   => [
					'label' => esc_html_x( 'Tribal', 'Shapes', 'elementor' ),
					'shape' => '<path d="M29.6,10.3l2.1,2.2l-3.6,3.3h7v2.9h-7l3.6,3.5l-2.1,1.7l-5.2-5.2h-5.8v-2.9h5.8L29.6,10.3z M70.9,9.6l2.1,1.7l-3.6,3.5h7v2.9h-7l3.6,3.3l-2.1,2.2l-5.2-5.5h-5.8v-2.9h5.8L70.9,9.6z M111.5,9.6l2.1,1.7l-3.6,3.5h7v2.9h-7l3.6,3.3l-2.1,2.2l-5.2-5.5h-5.8v-2.9h5.8L111.5,9.6z M50.2,2.7l2.1,1.7l-3.6,3.5h7v2.9h-7l3.6,3.3l-2.1,2.2L45,10.7h-5.8V7.9H45L50.2,2.7z M11,2l2.1,1.7L9.6,7.2h7V10h-7l3.6,3.3L11,15.5L5.8,10H0V7.2h5.8L11,2z M91.5,2l2.1,2.2l-3.6,3.3h7v2.9h-7l3.6,3.5l-2.1,1.7l-5.2-5.2h-5.8V7.5h5.8L91.5,2z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 121 26',
				],
				'x_tribal'   => [
					'label' => esc_html_x( 'X', 'Shapes', 'elementor' ),
					'shape' => '<path d="M10.7,6l2.5,2.6l-4,4.3l4,5.4l-2.5,1.9l-4.5-5.2l-3.9,4.2L0.7,17L4,13.1L0,8.6l2.3-1.3l3.9,3.9L10.7,6z M23.9,6.6l4.2,4.5L32,7.2l2.3,1.3l-4,4.5l3.2,3.9L32,19.1l-3.9-3.3l-4.5,4.3l-2.5-1.9l4.4-5.1l-4.2-3.9L23.9,6.6zM73.5,6L76,8.6l-4,4.3l4,5.4l-2.5,1.9l-4.5-5.2l-3.9,4.2L63.5,17l4.1-4.7L63.5,8l2.3-1.3l4.1,3.6L73.5,6z M94,6l2.5,2.6l-4,4.3l4,5.4L94,20.1l-3.9-5l-3.9,4.2L84,17l3.2-3.9L84,8.6l2.3-1.3l3.2,3.9L94,6z M106.9,6l4.5,5.1l3.9-3.9l2.3,1.3l-4,4.5l3.2,3.9l-1.6,2.1l-3.9-4.2l-4.5,5.2l-2.5-1.9l4-5.4l-4-4.3L106.9,6z M53.1,6l2.5,2.6l-4,4.3l4,4.6l-2.5,1.9l-4.5-4.5l-3.5,4.5L43.1,17l3.2-3.9l-4-4.5l2.3-1.3l3.9,3.9L53.1,6z"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 126 26',
				],
				'zigzag_tribal'   => [
					'label' => esc_html_x( 'Zigzag', 'Shapes', 'elementor' ),
					'shape' => '<polygon points="0,14.4 0,21 11.5,12.4 21.3,20 30.4,11.1 40.3,20 51,12.4 60.6,20 69.6,11.1 79.3,20 90.1,12.4 99.6,20 109.7,11.1 120,21 120,14.4 109.7,5 99.6,13 90.1,5 79.3,14.5 71,5.7 60.6,12.4 51,5 40.3,14.5 31.1,5 21.3,13 11.5,5 	"/>',
					'preserve_aspect_ratio' => true,
					'supports_amount' => false,
					'round' => false,
					'group' => 'tribal',
					'view_box' => '0 0 120 26',
				],
			]
		);
	}

	private function filter_styles_by( $styles_array, $key, $value ) {
		return array_filter( $styles_array, function( $style ) use ( $key, $value ) {
			return $value === $style[ $key ];
		} );
	}

	private function get_options_by_groups( $styles, $group = false ) {
		$groups = [
			'line' => [
				'label' => esc_html__( 'Line', 'elementor' ),
				'options' => [
					'solid' => esc_html__( 'Solid', 'elementor' ),
					'double' => esc_html__( 'Double', 'elementor' ),
					'dotted' => esc_html__( 'Dotted', 'elementor' ),
					'dashed' => esc_html__( 'Dashed', 'elementor' ),
				],
			],
		];
		foreach ( $styles as $key => $style ) {
			if ( ! isset( $groups[ $style['group'] ] ) ) {
				$groups[ $style['group'] ] = [
					'label' => ucwords( str_replace( '_', '', $style['group'] ) ),
					'options' => [],
				];
			}
			$groups[ $style['group'] ]['options'][ $key ] = $style['label'];
		}

		if ( $group && isset( $groups[ $group ] ) ) {
			return $groups[ $group ];
		}
		return $groups;
	}

	/**
	 * Register divider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$styles = $this->get_separator_styles();
		$this->start_controls_section(
			'section_divider',
			[
				'label' => esc_html__( 'Divider', 'elementor' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => array_values( $this->get_options_by_groups( $styles ) ),
				'render_type' => 'template',
				'default' => 'solid',
				'selectors' => [
					'{{WRAPPER}}' => '--divider-border-style: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'separator_type',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'pattern',
				'prefix_class' => 'elementor-widget-divider--separator-type-',
				'condition' => [
					'style!' => [
						'',
						'solid',
						'double',
						'dotted',
						'dashed',
					],
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'pattern_spacing_flag',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'no-spacing',
				'prefix_class' => 'elementor-widget-divider--',
				'condition' => [
					'style' => array_keys( $this->filter_styles_by( $styles, 'supports_amount', false ) ),
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'pattern_round_flag',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'bg-round',
				'prefix_class' => 'elementor-widget-divider--',
				'condition' => [
					'style' => array_keys( $this->filter_styles_by( $styles, 'round', true ) ),
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-divider-separator' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-divider' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .elementor-divider-separator' => 'margin: 0 auto; margin-{{VALUE}}: 0',
				],
			]
		);

		$this->add_control(
			'look',
			[
				'label' => esc_html__( 'Add Element', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'line',
				'options' => [
					'line' => [
						'title' => esc_html__( 'None', 'elementor' ),
						'icon' => 'eicon-ban',
					],
					'line_text' => [
						'title' => esc_html__( 'Text', 'elementor' ),
						'icon' => 'eicon-t-letter-bold',
					],
					'line_icon' => [
						'title' => esc_html__( 'Icon', 'elementor' ),
						'icon' => 'eicon-star',
					],
				],
				'separator' => 'before',
				'prefix_class' => 'elementor-widget-divider--view-',
				'toggle' => false,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'text',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'look' => 'line_text',
				],
				'default' => esc_html__( 'Divider', 'elementor' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'html_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'look' => 'line_text',
				],
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'span',
			]
		);

		$this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition' => [
					'look' => 'line_icon',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_divider_style',
			[
				'label' => esc_html__( 'Divider', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => 'none',
				],
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'default' => '#000',
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}}' => '--divider-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'weight',
			[
				'label' => esc_html__( 'Weight', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 0.1,
					],
					'em' => [
						'min' => 0.1,
						'max' => 1,
					],
					'rem' => [
						'min' => 0.1,
						'max' => 1,
					],
				],
				'render_type' => 'template',
				'condition' => [
					'style' => array_keys( $this->get_options_by_groups( $styles, 'line' )['options'] ),
				],
				'selectors' => [
					'{{WRAPPER}}' => '--divider-border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pattern_height',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}}' => '--divider-pattern-height: {{SIZE}}{{UNIT}}',
				],
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'step' => 0.1,
					],
				],
				'condition' => [
					'style!' => [
						'',
						'solid',
						'double',
						'dotted',
						'dashed',
					],
				],
			]
		);

		$this->add_control(
			'pattern_size',
			[
				'label' => esc_html__( 'Amount', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}}' => '--divider-pattern-size: {{SIZE}}{{UNIT}}',
				],
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'step' => 0.1,
					],
					'%' => [
						'step' => 0.01,
					],
				],
				'condition' => [
					'style!' => array_merge( array_keys( $this->filter_styles_by( $styles, 'supports_amount', false ) ), [
						'',
						'solid',
						'double',
						'dotted',
						'dashed',
					] ),
				],
			]
		);

		$this->add_responsive_control(
			'gap',
			[
				'label' => esc_html__( 'Gap', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-divider' => 'padding-block-start: {{SIZE}}{{UNIT}}; padding-block-end: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__( 'Text', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'look' => 'line_text',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-divider__text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .elementor-divider__text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .elementor-divider__text',
			]
		);

		$this->add_control(
			'text_align',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'prefix_class' => 'elementor-widget-divider--element-align-',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'%' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
					'vw' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--divider-element-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'look' => 'line_icon',
				],
			]
		);

		$this->add_control(
			'icon_view',
			[
				'label' => esc_html__( 'View', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'elementor' ),
					'stacked' => esc_html__( 'Stacked', 'elementor' ),
					'framed' => esc_html__( 'Framed', 'elementor' ),
				],
				'default' => 'default',
				'prefix_class' => 'elementor-view-',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--divider-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'min' => 0,
						'max' => 5,
					],
					'rem' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => [
					'icon_view!' => 'default',
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon svg' => 'fill: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
			]
		);

		$this->add_control(
			'secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'icon_view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'prefix_class' => 'elementor-widget-divider--element-align-',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--divider-element-spacing: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'rotate',
			[
				'label' => esc_html__( 'Rotate', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon i, {{WRAPPER}} .elementor-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}})',
				],
			]
		);

		$this->add_control(
			'icon_border_width',
			[
				'label' => esc_html__( 'Border Width', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 20,
					],
					'em' => [
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'icon_view' => 'framed',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'icon_view!' => 'default',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Build SVG
	 *
	 * Build SVG element markup based on the widgets settings.
	 *
	 * @return string - An SVG element.
	 *
	 * @since  2.7.0
	 * @access private
	 */
	private function build_svg() {
		$settings = $this->get_settings_for_display();

		if ( 'pattern' !== $settings['separator_type'] || empty( $settings['style'] ) ) {
			return '';
		}

		$svg_shapes = $this->get_separator_styles();

		$selected_pattern = $svg_shapes[ $settings['style'] ];
		$preserve_aspect_ratio = $selected_pattern['preserve_aspect_ratio'] ? 'xMidYMid meet' : 'none';
		$view_box = isset( $selected_pattern['view_box'] ) ? $selected_pattern['view_box'] : '0 0 24 24';

		$attr = [
			'preserveAspectRatio' => $preserve_aspect_ratio,
			'overflow' => 'visible',
			'height' => '100%',
			'viewBox' => $view_box,
		];

		if ( 'line' !== $selected_pattern['group'] ) {
			$attr['fill'] = 'black';
			$attr['stroke'] = 'none';
		} else {
			$attr['fill'] = 'none';
			$attr['stroke'] = 'black';
			$attr['stroke-width'] = $settings['weight']['size'];
			$attr['stroke-linecap'] = 'square';
			$attr['stroke-miterlimit'] = '10';
		}

		$this->add_render_attribute( 'svg', $attr );

		$pattern_attribute_string = $this->get_render_attribute_string( 'svg' );
		$shape = $selected_pattern['shape'];

		return '<svg xmlns="http://www.w3.org/2000/svg" ' . $pattern_attribute_string . '>' . $shape . '</svg>';
	}

	public function svg_to_data_uri( $svg ) {
		return str_replace(
			[ '<', '>', '"', '#' ],
			[ '%3C', '%3E', "'", '%23' ],
			$svg
		);
	}

	/**
	 * Render divider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$svg_code = $this->build_svg();
		$has_icon = 'line_icon' === ( $settings['look'] ) && ! empty( $settings['icon'] );
		$has_text = 'line_text' === ( $settings['look'] ) && ! empty( $settings['text'] );

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-divider' );

		if ( ! empty( $svg_code ) ) {
			$this->add_render_attribute( 'wrapper', 'style', '--divider-pattern-url: url("data:image/svg+xml,' . $this->svg_to_data_uri( $svg_code ) . '");' );
		}

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<span class="elementor-divider-separator">
			<?php if ( $has_icon ) : ?>
				<div class="elementor-icon elementor-divider__element">
					<?php
					Icons_Manager::render_icon( $settings['icon'], [
						'aria-hidden' => 'true',
					] );
					?></div>
			<?php elseif ( $has_text ) :
				$this->add_inline_editing_attributes( 'text' );
				$this->add_render_attribute( 'text', [ 'class' => [ 'elementor-divider__text', 'elementor-divider__element' ] ] );
				?>
				<<?php Utils::print_validated_html_tag( $settings['html_tag'] ); ?> <?php $this->print_render_attribute_string( 'text' ); ?>>
				<?php
				// PHPCS - the main text of a widget should not be escaped.
				echo $settings['text']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</<?php Utils::print_validated_html_tag( $settings['html_tag'] ); ?>>
			<?php endif; ?>
			</span>
		</div>
		<?php
	}
}
