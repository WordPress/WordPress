<?php

namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Button;

use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control;
use Elementor\Modules\AtomicWidgets\Elements\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Background_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Color_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Dimensions_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Button extends Atomic_Widget_Base {
	use Has_Template;

	public static function get_element_type(): string {
		return 'e-button';
	}

	public function get_title() {
		return esc_html__( 'Button', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic' ];
	}

	public function get_icon() {
		return 'eicon-e-button';
	}

	protected static function define_props_schema(): array {
		$props = [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),

			'text' => String_Prop_Type::make()
				->default( __( 'Click here', 'elementor' ) ),

			'link' => Link_Prop_Type::make(),

			'attributes' => Attributes_Prop_Type::make(),
		];

		return $props;
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Content', 'elementor' ) )
				->set_items( [
					Text_Control::bind_to( 'text' )
						->set_placeholder( __( 'Type your button text here', 'elementor' ) )
						->set_label( __( 'Button text', 'elementor' ) ),
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
		$background_color_value = Background_Prop_Type::generate( [
			'color' => Color_Prop_Type::generate( '#375EFB' ),
		] );
		$display_value = String_Prop_Type::generate( 'inline-block' );
		$padding_value = Dimensions_Prop_Type::generate( [
			'block-start' => Size_Prop_Type::generate( [
				'size' => 12,
				'unit' => 'px',
			]),
			'inline-end' => Size_Prop_Type::generate( [
				'size' => 24,
				'unit' => 'px',
			]),
			'block-end' => Size_Prop_Type::generate( [
				'size' => 12,
				'unit' => 'px',
			]),
			'inline-start' => Size_Prop_Type::generate( [
				'size' => 24,
				'unit' => 'px',
			]),
		]);
		$border_radius_value = Size_Prop_Type::generate( [
			'size' => 2,
			'unit' => 'px',
		] );
		$border_width_value = Size_Prop_Type::generate( [
			'size' => 0,
			'unit' => 'px',
		] );
		$align_text_value = String_Prop_Type::generate( 'center' );

		return [
			'base' => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'background', $background_color_value )
						->add_prop( 'display', $display_value )
						->add_prop( 'padding', $padding_value )
						->add_prop( 'border-radius', $border_radius_value )
						->add_prop( 'border-width', $border_width_value )
						->add_prop( 'text-align', $align_text_value )
				),
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-button' => __DIR__ . '/atomic-button.html.twig',
		];
	}
}
