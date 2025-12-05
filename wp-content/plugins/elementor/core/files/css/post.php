<?php
namespace Elementor\Core\Files\CSS;

use Elementor\Controls_Stack;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Kits\Manager;
use Elementor\Element_Base;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor post CSS file.
 *
 * Elementor CSS file handler class is responsible for generating the single
 * post CSS file.
 *
 * @since 1.2.0
 */
class Post extends Base {

	/**
	 * Elementor post CSS file prefix.
	 */
	const FILE_PREFIX = 'post-';

	const META_KEY = '_elementor_css';

	/**
	 * Post ID.
	 *
	 * Holds the current post ID.
	 *
	 * @var int
	 */
	private $post_id;

	protected function is_global_parsing_supported() {
		return true;
	}

	/**
	 * Post CSS file constructor.
	 *
	 * Initializing the CSS file of the post. Set the post ID and initiate the stylesheet.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;

		parent::__construct( static::FILE_PREFIX . $post_id . '.css' );
	}

	/**
	 * Get CSS file name.
	 *
	 * Retrieve the CSS file name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string CSS file name.
	 */
	public function get_name() {
		return 'post';
	}

	/**
	 * Get post ID.
	 *
	 * Retrieve the ID of current post.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return int Post ID.
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get unique element selector.
	 *
	 * Retrieve the unique selector for any given element.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param Element_Base $element The element.
	 *
	 * @return string Unique element selector.
	 */
	public function get_element_unique_selector( Element_Base $element ) {
		return '.elementor-' . $this->post_id . ' .elementor-element' . $element->get_unique_selector();
	}

	/**
	 * Load meta data.
	 *
	 * Retrieve the post CSS file meta data.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return array Post CSS file meta data.
	 */
	protected function load_meta() {
		return get_post_meta( $this->post_id, static::META_KEY, true );
	}

	/**
	 * Update meta data.
	 *
	 * Update the global CSS file meta data.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @param array $meta New meta data.
	 */
	protected function update_meta( $meta ) {
		update_post_meta( $this->post_id, static::META_KEY, $meta );
	}

	/**
	 * Delete meta.
	 *
	 * Delete the file meta data.
	 *
	 * @since  2.1.0
	 * @access protected
	 */
	protected function delete_meta() {
		delete_post_meta( $this->post_id, static::META_KEY );
	}

	/**
	 * Get post data.
	 *
	 * Retrieve raw post data from the database.
	 *
	 * @since 1.9.0
	 * @access protected
	 *
	 * @return array Post data.
	 */
	protected function get_data() {
		$document = Plugin::$instance->documents->get( $this->post_id );
		return $document ? $document->get_elements_data() : [];
	}

	/**
	 * Render CSS.
	 *
	 * Parse the CSS for all the elements.
	 *
	 * @since 1.2.0
	 * @access protected
	 */
	protected function render_css() {
		$data = $this->get_data();

		if ( ! empty( $data ) ) {
			foreach ( $data as $element_data ) {
				$element = Plugin::$instance->elements_manager->create_element_instance( $element_data );

				if ( ! $element ) {
					continue;
				}

				$this->render_styles( $element );
			}
		}
	}

	/**
	 * Enqueue CSS.
	 *
	 * Enqueue the post CSS file in Elementor.
	 *
	 * This method ensures that the post was actually built with elementor before
	 * enqueueing the post CSS file.
	 *
	 * @since 1.2.2
	 * @access public
	 */
	public function enqueue() {
		$document = Plugin::$instance->documents->get( $this->post_id );

		if ( ! $document || ! $document->is_built_with_elementor() ) {
			return;
		}

		parent::enqueue();
	}

	/**
	 * Add controls-stack style rules.
	 *
	 * Parse the CSS for all the elements inside any given controls stack.
	 *
	 * This method recursively renders the CSS for all the child elements in the stack.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param Controls_Stack $controls_stack The controls stack.
	 * @param array          $controls       Controls array.
	 * @param array          $values         Values array.
	 * @param array          $placeholders   Placeholders.
	 * @param array          $replacements   Replacements.
	 * @param array          $all_controls   All controls.
	 */
	public function add_controls_stack_style_rules( Controls_Stack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, ?array $all_controls = null ) {
		parent::add_controls_stack_style_rules( $controls_stack, $controls, $values, $placeholders, $replacements, $all_controls );

		if ( $controls_stack instanceof Element_Base ) {
			foreach ( $controls_stack->get_children() as $child_element ) {
				$this->render_styles( $child_element );
			}
		}
	}

	/**
	 * Get enqueue dependencies.
	 *
	 * Retrieve the name of the stylesheet used by `wp_enqueue_style()`.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return array Name of the stylesheet.
	 */
	protected function get_enqueue_dependencies() {
		return [ 'elementor-frontend' ];
	}

	/**
	 * Get inline dependency.
	 *
	 * Retrieve the name of the stylesheet used by `wp_add_inline_style()`.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return string Name of the stylesheet.
	 */
	protected function get_inline_dependency() {
		return 'elementor-frontend';
	}

	/**
	 * Get file handle ID.
	 *
	 * Retrieve the handle ID for the post CSS file.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @return string CSS file handle ID.
	 */
	protected function get_file_handle_id() {
		return 'elementor-post-' . $this->post_id;
	}

	/**
	 * Render styles.
	 *
	 * Parse the CSS for any given element.
	 *
	 * @since 1.2.0
	 * @access protected
	 *
	 * @param Element_Base $element The element.
	 */
	protected function render_styles( Element_Base $element ) {
		/**
		 * Before element parse CSS.
		 *
		 * Fires before the CSS of the element is parsed.
		 *
		 * @since 1.2.0
		 *
		 * @param Post         $this    The post CSS file.
		 * @param Element_Base $element The element.
		 */
		do_action( 'elementor/element/before_parse_css', $this, $element );

		$this->render_element_global_styles( $element );
		$this->render_element_styles( $element );

		/**
		 * After element parse CSS.
		 *
		 * Fires after the CSS of the element is parsed.
		 *
		 * @since 1.2.0
		 *
		 * @param Post         $this    The post CSS file.
		 * @param Element_Base $element The element.
		 */
		do_action( 'elementor/element/parse_css', $this, $element );
	}

	private function render_element_styles( Element_Base $element ) {
		$this->add_controls_stack_style_rules(
			$element,
			$this->get_style_controls( $element, null, $element->get_parsed_dynamic_settings() ),
			$element->get_settings(),
			[ '{{ID}}', '{{WRAPPER}}' ],
			[ $element->get_id(), $this->get_element_unique_selector( $element ) ]
		);
	}

	private function render_element_global_styles( Element_Base $element ) {
		if ( $this instanceof Dynamic_CSS ) {
			return;
		}

		/** @var Manager $module */
		$kits_manager = Plugin::$instance->kits_manager;
		$custom_colors_enabled = $kits_manager->is_custom_colors_enabled();
		$custom_typography_enabled = $kits_manager->is_custom_typography_enabled();

		$controls = $element->get_controls();
		$global_controls = [];
		$global_values['__globals__'] = [];

		foreach ( $controls as $control ) {
			$this->build_global_controls_and_values(
				$control,
				$controls,
				$global_controls,
				$global_values,
				$custom_colors_enabled,
				$custom_typography_enabled
			);
		}

		foreach ( $global_controls as $control ) {
			$this->add_control_rules(
				$control,
				$controls,
				function( $control ) {},
				[ '{{WRAPPER}}' ],
				[ '.elementor-widget-' . $element->get_name() ],
				$global_values
			);
		}
	}

	private function build_global_controls_and_values( $control, $controls, &$global_controls, &$global_values, $custom_colors_enabled, $custom_typography_enabled ) {
		$is_color_control = 'color' === $control['type'];
		$is_typography_control = isset( $control['groupType'] ) && 'typography' === $control['groupType'];

		// If it is a color/typography control and default colors/typography are disabled,
		// don't add the default CSS.
		if ( ( $is_color_control && ! $custom_colors_enabled ) || ( $is_typography_control && ! $custom_typography_enabled ) ) {
			return;
		}

		$global_control = $control;

		// Handle group controls that don't have a default global property.
		if ( ! empty( $control['groupType'] ) ) {
			$global_control = $controls[ $control['groupPrefix'] . $control['groupType'] ];
		}

		// If the control has a default global defined, add it to the globals array
		// that is used in add_control_rules.
		if ( ! empty( $control['global']['default'] ) ) {
			$global_values['__globals__'][ $control['name'] ] = $global_control['global']['default'];
		}

		if ( ! empty( $global_control['global']['default'] ) ) {
			$global_controls[] = $control;
		}
	}
}
