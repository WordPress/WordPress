<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Svg;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Core\Utils\Svg\Svg_Sanitizer;
use Elementor\Modules\AtomicWidgets\Controls\Types\Svg_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Image_Src_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Atomic_Svg extends Atomic_Widget_Base {
	const BASE_STYLE_KEY = 'base';
	const DEFAULT_SVG = 'images/default-svg.svg';
	const DEFAULT_SVG_PATH = ELEMENTOR_ASSETS_PATH . self::DEFAULT_SVG;
	const DEFAULT_SVG_URL = ELEMENTOR_ASSETS_URL . self::DEFAULT_SVG;

	public static function get_element_type(): string {
		return 'e-svg';
	}

	public function get_title() {
		return esc_html__( 'SVG', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic' ];
	}

	public function get_icon() {
		return 'eicon-svg';
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()->default( [] ),
			'svg' => Image_Src_Prop_Type::make()->default_url( static::DEFAULT_SVG_URL ),
			'link' => Link_Prop_Type::make(),
			'attributes' => Attributes_Prop_Type::make(),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( esc_html__( 'Content', 'elementor' ) )
				->set_items( [
					Svg_Control::bind_to( 'svg' )
						->set_label( __( 'SVG', 'elementor' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
	}

	protected function get_settings_controls(): array {
		return [
			Link_Control::bind_to( 'link' )
				->set_placeholder( __( 'Type or paste your URL', 'elementor' ) )
				->set_label( __( 'Link', 'elementor' ) ),
			Text_Control::bind_to( '_cssid' )
				->set_label( __( 'ID', 'elementor' ) )
				->set_meta( $this->get_css_id_control_meta() ),
		];
	}

	protected function define_base_styles(): array {
		$display_value = String_Prop_Type::generate( 'inline-block' );

		$size = Size_Prop_Type::generate( [
			'size' => 65,
			'unit' => 'px',
		] );

		return [
			self::BASE_STYLE_KEY => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'display', $display_value )
						->add_prop( 'width', $size )
						->add_prop( 'height', $size )
				),
		];
	}

	protected function render() {
		$settings = $this->get_atomic_settings();

		$svg = $this->get_svg_content( $settings );

		if ( ! $svg ) {
			return;
		}

		$svg = new \WP_HTML_Tag_Processor( $svg );

		if ( ! $svg->next_tag( 'svg' ) ) {
			return;
		}

		$svg->set_attribute( 'fill', 'currentColor' );
		$this->add_svg_style( $svg, 'width: 100%; height: 100%; overflow: unset;' );

		$svg_html = ( new Svg_Sanitizer() )->sanitize( $svg->get_updated_html() );

		$classes = array_filter( array_merge(
			[ self::BASE_STYLE_KEY => $this->get_base_styles_dictionary()[ self::BASE_STYLE_KEY ] ],
			$settings['classes']
		) );

		$classes_string = implode( ' ', $classes );

		$cssid_attribute = ! empty( $settings['_cssid'] ) ? 'id="' . esc_attr( $settings['_cssid'] ) . '"' : '';

		$all_attributes = trim( $cssid_attribute . ' ' . $settings['attributes'] );

		if ( isset( $settings['link'] ) && ! empty( $settings['link']['href'] ) ) {
			$svg_html = sprintf(
				'<a href="%s" target="%s" class="%s" %s>%s</a>',
				$settings['link']['href'],
				esc_attr( $settings['link']['target'] ),
				esc_attr( $classes_string ),
				$all_attributes,
				$svg_html
			);
		} else {
			$svg_html = sprintf( '<div class="%s" %s>%s</div>', esc_attr( $classes_string ), $all_attributes, $svg_html );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $svg_html;
	}

	private function get_svg_content( $settings ) {
		if ( isset( $settings['svg']['id'] ) ) {
			$content = Utils::file_get_contents(
				get_attached_file( $settings['svg']['id'] )
			);

			if ( $content ) {
				return $content;
			}
		}

		if (
			isset( $settings['svg']['url'] ) &&
			static::DEFAULT_SVG_URL !== $settings['svg']['url']
		) {
			$content = wp_safe_remote_get(
				$settings['svg']['url']
			);

			if ( ! is_wp_error( $content ) ) {
				return $content['body'];
			}
		}

		$content = Utils::file_get_contents(
			static::DEFAULT_SVG_PATH
		);

		return $content ? $content : null;
	}

	private function add_svg_style( &$svg, $new_style ) {
		$svg_style = $svg->get_attribute( 'style' );
		$svg_style = trim( (string) $svg_style );

		if ( empty( $svg_style ) ) {
			$svg_style = $new_style;
		} else {
			$svg_style = rtrim( $svg_style, ';' ) . '; ' . $new_style;
		}

		$svg->set_attribute( 'style', $svg_style );
	}
}
