<?php
namespace Elementor\Modules\AtomicWidgets\Elements\Atomic_Tabs;

use Elementor\Modules\AtomicWidgets\Elements\Atomic_Element_Base;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Definition;
use Elementor\Modules\AtomicWidgets\Styles\Style_Variant;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\Controls\Types\Text_Control;
use Elementor\Modules\AtomicWidgets\PropTypes\Classes_Prop_Type;
use Elementor\Modules\AtomicWidgets\PropTypes\Attributes_Prop_Type;
use Elementor\Modules\AtomicWidgets\Controls\Types\Elements\Tabs_Control;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Atomic_Tabs extends Atomic_Element_Base {
	const BASE_STYLE_KEY = 'base';

	public static function get_type() {
		return 'e-tabs';
	}

	public static function get_element_type(): string {
		return 'e-tabs';
	}

	public function get_title() {
		return esc_html__( 'Atomic Tabs', 'elementor' );
	}

	public function get_keywords() {
		return [ 'ato', 'atom', 'atoms', 'atomic' ];
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	protected static function define_props_schema(): array {
		return [
			'classes' => Classes_Prop_Type::make()
				->default( [] ),
			'default-active-tab' => String_Prop_Type::make(),
			'attributes' => Attributes_Prop_Type::make(),
		];
	}

	protected function define_atomic_controls(): array {
		return [
			Section::make()
				->set_label( __( 'Settings', 'elementor' ) )
				->set_id( 'settings' )
				->set_items( [
					Text_Control::bind_to( '_cssid' )
						->set_label( __( 'ID', 'elementor' ) )
						->set_meta( $this->get_css_id_control_meta() ),
					Tabs_Control::make()
						->set_label( __( 'Menu items', 'elementor' ) )
						->set_meta( [
							'topDivider' => true,
							'layout' => 'custom',
						] ),
				] ),
		];
	}

	protected function define_base_styles(): array {
		$display = String_Prop_Type::generate( 'block' );

		return [
			static::BASE_STYLE_KEY => Style_Definition::make()
				->add_variant(
					Style_Variant::make()
						->add_prop( 'display', $display )
						->add_prop( 'padding', $this->get_base_padding() )
						->add_prop( 'min-width', $this->get_base_min_width() )
				),
		];
	}

	protected function get_base_padding(): array {
		return Size_Prop_Type::generate( [
			'size' => 10,
			'unit' => 'px',
		] );
	}

	protected function get_base_min_width(): array {
		return Size_Prop_Type::generate( [
			'size' => 30,
			'unit' => 'px',
		] );
	}

	protected function define_default_children() {
		$default_tab_count = 3;
		$tab_elements = [];
		$tab_panel_elements = [];

		foreach ( range( 1, $default_tab_count ) as $i ) {
			$tab_elements[] = Atomic_Tab::generate()
				->editor_settings( [
					'title' => "Tab {$i}",
				] )
				->is_locked( true )
				->build();

			$tab_panel_elements[] = Atomic_Tab_Panel::generate()
				->is_locked( true )
				->editor_settings( [
					'title' => "Tab {$i} panel",
				] )
				->build();
		}

		$tabs_list = Atomic_Tabs_List::generate()
			->children( $tab_elements )
			->is_locked( true )
			->build();

		$tabs_content = Atomic_Tabs_Content::generate()
			->children( $tab_panel_elements )
			->is_locked( true )
			->build();

		return [
			$tabs_list,
			$tabs_content,
		];
	}

	public function define_initial_attributes() {
		return [
			'data-e-type' => $this->get_type(),
		];
	}

	public function get_script_depends() {
		return [ 'elementor-tabs-handler' ];
	}

	protected function add_render_attributes() {
		parent::add_render_attributes();
		$settings = $this->get_atomic_settings();
		$base_style_class = $this->get_base_styles_dictionary()[ static::BASE_STYLE_KEY ];
		$initial_attributes = $this->define_initial_attributes();

		$attributes = [
			'class' => [
				'e-con',
				'e-atomic-element',
				$base_style_class,
				...( $settings['classes'] ?? [] ),
			],
		];

		if ( ! empty( $settings['default-active-tab'] ) ) {
			$attributes['data-active-tab'] = esc_attr( $settings['default-active-tab'] );
		}

		if ( ! empty( $settings['_cssid'] ) ) {
			$attributes['id'] = esc_attr( $settings['_cssid'] );
		}

		$this->add_render_attribute( '_wrapper', array_merge( $initial_attributes, $attributes ) );
	}
}
