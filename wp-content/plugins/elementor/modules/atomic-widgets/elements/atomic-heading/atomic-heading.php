<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Heading;

use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Select_Control;
use Elementor\Modules\AtomicWidgets\Controls\Types\Textarea_Control;
use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Elements\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Atomic_Heading extends Atomic_Widget_Base {
	use Has_Template;

	const LINK_BASE_STYLE_KEY = 'link-base';

	public static function get_element_type(): string {
		return 'e-heading';
	}

	public function get_title() {
		return esc_html__( 'Heading', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic' ];
	}

	public function get_icon() {
		return 'eicon-e-heading';
	}

	protected static function define_props_schema(): array {
		$props = [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),

			'tag' => String_Prop_Type::make()
				->enum( [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ] )
				->default( 'h2' ),

			'title' => String_Prop_Type::make()
				->default( __( 'This is a title', 'elementor' ) ),

			'link' => Link_Prop_Type::make(),

			'attributes' => Attributes_Prop_Type::make(),
		];

		return $props;
	}

	protected function define_atomic_controls(): array {
		$content_section = Section::make()
			->set_label( __( 'Content', 'elementor' ) )
			->set_items( [
				Textarea_Control::bind_to( 'title' )
				->set_placeholder( __( 'Type your title here', 'elementor' ) )
					->set_label( __( 'Title', 'elementor' ) ),
			] );

		return [
			$content_section,
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( $this->get_settings_controls() ),
		];
	}

	protected function get_settings_controls(): array {
		return [
			Select_Control::bind_to( 'tag' )
				->set_options([
					[
						'value' => 'h1',
						'label' => 'H1',
					],
					[
						'value' => 'h2',
						'label' => 'H2',
					],
					[
						'value' => 'h3',
						'label' => 'H3',
					],
					[
						'value' => 'h4',
						'label' => 'H4',
					],
					[
						'value' => 'h5',
						'label' => 'H5',
					],
					[
						'value' => 'h6',
						'label' => 'H6',
					],
				])
				->set_label( __( 'Tag', 'elementor' ) ),
			Link_Control::bind_to( 'link' )
				->set_placeholder( __( 'Type or paste your URL', 'elementor' ) )
				->set_label( __( 'Link', 'elementor' ) )
				->set_meta( [
					'topDivider' => true,
				] ),
			Text_Control::bind_to( '_cssid' )
			->set_label( __( 'ID', 'elementor' ) )
			->set_meta( $this->get_css_id_control_meta() ),
		];
	}

	protected function define_base_styles(): array {
		$margin_value = Size_Prop_Type::generate( [
			'unit' => 'px',
			'size' => 0 ,
		] );

		return [
			'base' => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'margin', $margin_value )
				),
			self::LINK_BASE_STYLE_KEY => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'all', 'unset' )
						->add_prop( 'cursor', 'pointer' )
				),
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-heading' => __DIR__ . '/atomic-heading.html.twig',
		];
	}
}
