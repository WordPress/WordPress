<?php
namespace Elementor\Modules\NestedElements\Base;

use Elementor\Plugin;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Used to create a new widget that can be nested inside other widgets.
 */
abstract class Widget_Nested_Base extends Widget_Base {

	/**
	 * Get default children elements structure.
	 *
	 * @return array
	 */
	abstract protected function get_default_children_elements();

	/**
	 * Get repeater title setting key name.
	 *
	 * @return string
	 */
	abstract protected function get_default_repeater_title_setting_key();

	/**
	 * Get default children title for the navigator, using `%d` as index in the format.
	 *
	 * @note The title in this method is used to set the default title for each created child in nested element.
	 * for handling the children title for new created widget(s), use `get_default_children_elements()` method,
	 * eg:
	 * [
	 *      'elType' => 'container',
	 *      'settings' => [
	 *          '_title' => __( 'Tab #1', 'elementor' ),
	 *      ],
	 * ],
	 * @return string
	 */
	protected function get_default_children_title() {
		/* translators: %d: Item index. */
		return esc_html__( 'Item #%d', 'elementor' );
	}

	/**
	 * Get default children placeholder selector, Empty string, means will be added at the end view.
	 *
	 * @return string
	 */
	protected function get_default_children_placeholder_selector() {
		return '';
	}

	protected function get_default_children_container_placeholder_selector() {
		return '';
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * @inheritDoc
	 *
	 * To support nesting.
	 */
	protected function _get_default_child_type( array $element_data ) {
		return Plugin::$instance->elements_manager->get_element_types( $element_data['elType'] );
	}

	/**
	 * @inheritDoc
	 *
	 * Adding new 'defaults' config for handling children elements.
	 */
	protected function get_initial_config() {
		return array_merge( parent::get_initial_config(), [
			'defaults' => [
				'elements' => $this->get_default_children_elements(),
				'elements_title' => $this->get_default_children_title(),
				'elements_placeholder_selector' => $this->get_default_children_placeholder_selector(),
				'child_container_placeholder_selector' => $this->get_default_children_container_placeholder_selector(),
				'repeater_title_setting' => $this->get_default_repeater_title_setting_key(),
			],
			'support_nesting' => true,
		] );
	}

	/**
	 * @inheritDoc
	 *
	 * Each element including its children elements.
	 */
	public function get_raw_data( $with_html_content = false ) {
		$elements = [];
		$data = $this->get_data();

		$children = $this->get_children();

		foreach ( $children as $child ) {
			$child_raw_data = $child->get_raw_data( $with_html_content );

			$elements[] = $child_raw_data;
		}

		return [
			'id' => $this->get_id(),
			'elType' => $data['elType'],
			'widgetType' => $data['widgetType'],
			'settings' => $data['settings'],
			'elements' => $elements,
		];
	}

	/**
	 * Print child, helper method to print the child element.
	 *
	 * @param int $index
	 */
	public function print_child( $index ) {
		$children = $this->get_children();

		if ( ! empty( $children[ $index ] ) ) {
			$children[ $index ]->print_element();
		}
	}

	protected function content_template_single_repeater_item() {}

	public function print_template() {
		parent::print_template();
		if ( $this->get_initial_config()['support_improved_repeaters'] ?? false ) {
			?>
			<script type="text/html" id="tmpl-elementor-<?php echo esc_attr( $this->get_name() ); ?>-content-single">
				<?php $this->content_template_single_repeater_item(); ?>
			</script>
			<?php
		}
	}
}
