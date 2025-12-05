<?php
namespace Elementor\Modules\Components\Widgets;

use Elementor\Modules\AtomicWidgets\Elements\Atomic_Widget_Base;
use Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Component extends Atomic_Widget_Base {

	public static function get_element_type(): string {
		return 'e-component';
	}

	public function show_in_panel() {
		return false;
	}

	public function get_title() {
		$title = esc_html__( 'Component', 'elementor' );

		if ( isset( $this->get_settings ) && null !== $this->get_settings( 'component_id' ) ) {
			$post_id = $this->get_settings( 'component_id' )['value'];
			$title   = Plugin::$instance->documents->get( $post_id )->get_title();
		}

		return $title;
	}

	public function get_keywords() {
		return [ 'component' ];
	}

	public function get_icon() {
		return 'eicon-star';
	}

	protected static function define_props_schema(): array {
		return [
			'component_id' => Number_Prop_Type::make(),
		];
	}

	protected function define_atomic_controls(): array {
		return [];
	}

	protected function get_settings_controls(): array {
		return [];
	}

	protected function render(): void {
		if ( null === $this->get_settings( 'component_id' ) ) {
			return;
		}

		$post_id = $this->get_settings( 'component_id' )['value'];
		$content = Plugin::$instance->frontend->get_builder_content( $post_id );
		$html    = sprintf( '<div class="e-component">%s</div>', $content );

		// PHPCS - should not be escaped.
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
