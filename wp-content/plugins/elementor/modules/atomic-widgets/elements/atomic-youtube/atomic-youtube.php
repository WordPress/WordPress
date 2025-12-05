<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Youtube;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Switch_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Elements\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Atomic_Youtube extends Atomic_Widget_Base {
	use Has_Template;

	protected function get_css_id_control_meta(): array {
		return [
			'layout' => 'two-columns',
			'topDivider' => false,
		];
	}

	public static function get_element_type(): string {
		return 'e-youtube';
	}

	public function get_title() {
		return esc_html__( 'YouTube', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic' ];
	}

	public function get_icon() {
		return 'eicon-e-youtube';
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),

			'source' => String_Prop_Type::make()
				->default( 'https://www.youtube.com/watch?v=XHOmBV4js_E' ),

			'start' => String_Prop_Type::make(),
			'end' => String_Prop_Type::make(),
			'autoplay' => Boolean_Prop_Type::make()->default( false ),
			'mute' => Boolean_Prop_Type::make()->default( false ),
			'loop' => Boolean_Prop_Type::make()->default( false ),
			'lazyload' => Boolean_Prop_Type::make()->default( false ),
			'player_controls' => Boolean_Prop_Type::make()->default( true ),
			'captions' => Boolean_Prop_Type::make()->default( false ),
			'privacy_mode' => Boolean_Prop_Type::make()->default( false ),
			'rel' => Boolean_Prop_Type::make()->default( true ),

			'attributes' => Attributes_Prop_Type::make(),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Content', 'elementor' ) )
				->set_items( [
					Text_Control::bind_to( 'source' )
						->set_placeholder( esc_html__( 'Type or paste your URL', 'elementor' ) )
						->set_label( esc_html__( 'YouTube URL', 'elementor' ) ),

					Text_Control::bind_to( 'start' )->set_label( esc_html__( 'Start time', 'elementor' ) ),
					Text_Control::bind_to( 'end' )->set_label( esc_html__( 'End time', 'elementor' ) ),
					Switch_Control::bind_to( 'autoplay' )->set_label( esc_html__( 'Autoplay', 'elementor' ) ),
					Switch_Control::bind_to( 'mute' )->set_label( esc_html__( 'Mute', 'elementor' ) ),
					Switch_Control::bind_to( 'loop' )->set_label( esc_html__( 'Loop', 'elementor' ) ),
					Switch_Control::bind_to( 'lazyload' )->set_label( esc_html__( 'Lazy load', 'elementor' ) ),
					Switch_Control::bind_to( 'player_controls' )->set_label( esc_html__( 'Player controls', 'elementor' ) ),
					Switch_Control::bind_to( 'captions' )->set_label( esc_html__( 'Captions', 'elementor' ) ),
					Switch_Control::bind_to( 'privacy_mode' )->set_label( esc_html__( 'Privacy mode', 'elementor' ) ),
					Switch_Control::bind_to( 'rel' )->set_label( esc_html__( 'Related videos', 'elementor' ) ),
				] ),
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
	}

	protected function get_settings_controls(): array {
		return [
			Text_Control::bind_to( '_cssid' )
				->set_label( __( 'ID', 'elementor' ) )
				->set_meta( $this->get_css_id_control_meta() ),
		];
	}

	protected function define_base_styles(): array {
		return [
			'base' => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'aspect-ratio', String_Prop_Type::generate( '16/9' ) )
						->add_prop( 'overflow', String_Prop_Type::generate( 'hidden' ) )
				),
		];
	}

	public function get_script_depends() {
		return [ 'elementor-youtube-handler' ];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-youtube' => __DIR__ . '/atomic-youtube.html.twig',
		];
	}
}
