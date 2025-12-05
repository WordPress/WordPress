<?php

namespace Elementor\Modules\AtomicWidgets\Elements;

use Elementor\Element_Base;
use Elementor\Modules\AtomicWidgets\Base\Atomic_Control_Base;
use Elementor\Modules\AtomicWidgets\Base\Element_Control_Base;
use Elementor\Modules\AtomicWidgets\Controls\Section;
use Elementor\Modules\AtomicWidgets\PropsResolver\Render_Props_Resolver;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Modules\AtomicWidgets\Styles\Style_Schema;
use Elementor\Modules\AtomicWidgets\Parsers\Props_Parser;
use Elementor\Modules\AtomicWidgets\Parsers\Style_Parser;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @mixin Element_Base
 */
trait Has_Atomic_Base {
	use Has_Base_Styles;

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	abstract public static function get_element_type(): string;

	final public function get_name() {
		return static::get_element_type();
	}

	private function get_valid_controls( array $schema, array $controls ): array {
		$valid_controls = [];

		foreach ( $controls as $control ) {
			if ( $control instanceof Section ) {
				$cloned_section = clone $control;

				$cloned_section->set_items(
					$this->get_valid_controls( $schema, $control->get_items() )
				);

				$valid_controls[] = $cloned_section;
				continue;
			}

			if ( $control instanceof Element_Control_Base ) {
				$valid_controls[] = $control;
				continue;
			}

			if ( ! ( $control instanceof Atomic_Control_Base ) ) {
				Utils::safe_throw( 'Control must be an instance of `Atomic_Control_Base`.' );
				continue;
			}

			$prop_name = $control->get_bind();

			if ( ! $prop_name ) {
				Utils::safe_throw( 'Control is missing a bound prop from the schema.' );
				continue;
			}

			if ( ! array_key_exists( $prop_name, $schema ) ) {
				Utils::safe_throw( "Prop `{$prop_name}` is not defined in the schema of `{$this->get_name()}`." );
				continue;
			}

			$valid_controls[] = $control;
		}

		return $valid_controls;
	}

	private static function validate_schema( array $schema ) {
		$widget_name = static::class;

		foreach ( $schema as $key => $prop ) {
			if ( ! ( $prop instanceof Prop_Type ) ) {
				Utils::safe_throw( "Prop `$key` must be an instance of `Prop_Type` in `{$widget_name}`." );
			}
		}
	}

	private function parse_atomic_styles( array $styles ): array {
		$style_parser = Style_Parser::make( Style_Schema::get() );

		foreach ( $styles as $style_id => $style ) {
			$result = $style_parser->parse( $style );

			if ( ! $result->is_valid() ) {
				throw new \Exception( esc_html( "Styles validation failed for style `$style_id`. " . $result->errors()->to_string() ) );
			}

			$styles[ $style_id ] = $result->unwrap();
		}

		return $styles;
	}

	private function parse_atomic_settings( array $settings ): array {
		$schema = static::get_props_schema();
		$props_parser = Props_Parser::make( $schema );

		$result = $props_parser->parse( $settings );

		if ( ! $result->is_valid() ) {
			throw new \Exception( esc_html( 'Settings validation failed. ' . $result->errors()->to_string() ) );
		}

		return $result->unwrap();
	}

	public function get_atomic_controls() {
		$controls = apply_filters(
			'elementor/atomic-widgets/controls',
			$this->define_atomic_controls(),
			$this
		);

		$schema = static::get_props_schema();

		// Validate the schema only in the Editor.
		static::validate_schema( $schema );

		return $this->get_valid_controls( $schema, $controls );
	}

	protected function get_css_id_control_meta(): array {
		return [
			'layout' => 'two-columns',
			'topDivider' => true,
		];
	}

	final public function get_controls( $control_id = null ) {
		if ( ! empty( $control_id ) ) {
			return null;
		}

		return [];
	}

	final public function get_data_for_save() {
		$data = parent::get_data_for_save();

		$data['version'] = $this->version;
		$data['settings'] = $this->parse_atomic_settings( $data['settings'] );
		$data['styles'] = $this->parse_atomic_styles( $data['styles'] );
		$data['editor_settings'] = $this->parse_editor_settings( $data['editor_settings'] );

		return $data;
	}

	final public function get_raw_data( $with_html_content = false ) {
		$raw_data = parent::get_raw_data( $with_html_content );

		$raw_data['styles'] = $this->styles;
		$raw_data['editor_settings'] = $this->editor_settings;

		return $raw_data;
	}

	final public function get_stack( $with_common_controls = true ) {
		return [
			'controls' => [],
			'tabs' => [],
		];
	}

	public function get_atomic_settings(): array {
		$schema = static::get_props_schema();
		$props = $this->get_settings();

		return Render_Props_Resolver::for_settings()->resolve( $schema, $props );
	}

	private function parse_editor_settings( array $data ): array {
		$editor_data = [];

		if ( isset( $data['title'] ) && is_string( $data['title'] ) ) {
			$editor_data['title'] = sanitize_text_field( $data['title'] );
		}

		return $editor_data;
	}

	public static function get_props_schema(): array {
		$schema = static::define_props_schema();
		$schema['_cssid'] = String_Prop_Type::make();

		return apply_filters(
			'elementor/atomic-widgets/props-schema',
			$schema
		);
	}
}
