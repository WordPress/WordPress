<?php

namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Divider;

use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\Elements\Has_Template;
use Elementor\Modules\AtomicWidgets\PropTypes\Background_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Color_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Atomic_Divider extends Atomic_Widget_Base {

	use Has_Template;

	protected function get_css_id_control_meta(): array {
		return [
			'layout' => 'two-columns',
			'topDivider' => false,
		];
	}

	public static function get_element_type(): string {
		return 'e-divider';
	}

	public function get_title() {
		return esc_html__( 'Divider', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic', 'divider', 'hr', 'line', 'border', 'separator' ];
	}

	public function get_icon() {
		return 'eicon-e-divider';
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),

			'attributes' => Attributes_Prop_Type::make(),
		];
	}

	protected function define_atomic_controls(): array {
		return [
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
		$border_width_value = Size_Prop_Type::generate([
			'size' => 0,
			'unit' => 'px',
		]);

		$height_value = Size_Prop_Type::generate([
			'size' => 1,
			'unit' => 'px',
		]);

		$background_value = Background_Prop_Type::generate([
			'color' => Color_Prop_Type::generate( '#000' ),
		]);

		return [
			'base' => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'border-width', $border_width_value )
						->add_prop( 'border-color', 'transparent' )
						->add_prop( 'border-style', 'none' )
						->add_prop( 'background', $background_value )
						->add_prop( 'height', $height_value )
				),
		];
	}

	protected function get_templates(): array {
		return [
			'elementor/elements/atomic-divider' => __DIR__ . '/atomic-divider.html.twig',
		];
	}
}
