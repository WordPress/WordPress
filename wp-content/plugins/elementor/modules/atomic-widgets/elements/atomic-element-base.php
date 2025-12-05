<?php

namespace Elementor\Modules\AtomicWidgets\Elements;

use Elementor\Element_Base;
use Elementor\Modules\AtomicWidgets\PropDependencies\Manager as Dependency_Manager;
use Elementor\Modules\AtomicWidgets\PropTypes\Contracts\Prop_Type;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Atomic_Element_Base extends Element_Base {
	use Has_Atomic_Base;

	protected $version = '0.0';
	protected $styles = [];
	protected $editor_settings = [];

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		$this->version = $data['version'] ?? '0.0';
		$this->styles = $data['styles'] ?? [];
		$this->editor_settings = $data['editor_settings'] ?? [];
	}

	abstract protected function define_atomic_controls(): array;

	public function get_global_scripts() {
		return [];
	}

	final public function get_initial_config() {
		$config = parent::get_initial_config();
		$props_schema = static::get_props_schema();

		$config['atomic_controls'] = $this->get_atomic_controls();
		$config['atomic_props_schema'] = $props_schema;
		$config['dependencies_per_target_mapping'] = Dependency_Manager::get_source_to_dependents( $props_schema );
		$config['base_styles'] = $this->get_base_styles();
		$config['version'] = $this->version;
		$config['show_in_panel'] = $this->should_show_in_panel();
		$config['categories'] = [ 'v4-elements' ];
		$config['hide_on_search'] = false;
		$config['controls'] = [];
		$config['keywords'] = $this->get_keywords();
		$config['default_children'] = $this->define_default_children();
		$config['initial_attributes'] = $this->define_initial_attributes();
		$config['include_in_widgets_config'] = true;
		$config['default_html_tag'] = $this->define_default_html_tag();

		return $config;
	}

	protected function should_show_in_panel() {
		return true;
	}

	protected function define_default_children() {
		return [];
	}

	protected function define_default_html_tag() {
		return 'div';
	}

	protected function define_initial_attributes() {
		return [];
	}

	/**
	 * Get Element keywords.
	 *
	 * Retrieve the element keywords.
	 *
	 * @since 3.29
	 * @access public
	 *
	 * @return array Element keywords.
	 */
	public function get_keywords() {
		return [];
	}

	/**
	 * @return array<string, Prop_Type>
	 */
	abstract protected static function define_props_schema(): array;

	/**
	 * Get the HTML tag for rendering.
	 *
	 * @return string
	 */
	protected function get_html_tag(): string {
		$settings = $this->get_atomic_settings();
		$default_html_tag = $this->define_default_html_tag();

		return ! empty( $settings['link']['href'] ) ? 'a' : ( $settings['tag'] ?? $default_html_tag );
	}

	/**
	 * Print safe HTML tag for the element based on the element settings.
	 *
	 * @return void
	 */
	protected function print_html_tag() {
		$html_tag = $this->get_html_tag();
		Utils::print_validated_html_tag( $html_tag );
	}

	/**
	 * Print custom attributes if they exist.
	 *
	 * @return void
	 */
	protected function print_custom_attributes() {
		$settings = $this->get_atomic_settings();
		$attributes = $settings['attributes'] ?? '';
		if ( ! empty( $attributes ) && is_string( $attributes ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ' ' . $attributes;
		}
	}

	/**
	 * Get default child type for container elements.
	 *
	 * @param array $element_data
	 * @return mixed
	 */
	protected function _get_default_child_type( array $element_data ) {
		$el_types = array_keys( Plugin::$instance->elements_manager->get_element_types() );

		if ( in_array( $element_data['elType'], $el_types, true ) ) {
			return Plugin::$instance->elements_manager->get_element_types( $element_data['elType'] );
		}

		return Plugin::$instance->widgets_manager->get_widget_types( $element_data['widgetType'] );
	}

	/**
	 * Default before render for container elements.
	 *
	 * @return void
	 */
	public function before_render() {
		?>
		<<?php $this->print_html_tag(); ?> <?php $this->print_render_attribute_string( '_wrapper' );
		$this->print_custom_attributes(); ?>>
		<?php
	}

	/**
	 * Default after render for container elements.
	 *
	 * @return void
	 */
	public function after_render() {
		?>
		</<?php $this->print_html_tag(); ?>>
		<?php
	}

	/**
	 * Default content template - can be overridden by elements that need custom templates.
	 *
	 * @return void
	 */
	protected function content_template() {
		?>
		<?php
	}

	public static function generate() {
		return Element_Builder::make( static::get_type() );
	}
}
