<?php
namespace Elementor\Core\DynamicTags;

use Elementor\Controls_Stack;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Files\CSS\Post_Local_Cache;
use Elementor\Core\Files\CSS\Post_Preview;
use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Dynamic_CSS extends Post_Local_Cache {

	private $post_dynamic_elements_ids;

	private $post_id_for_data;

	protected function get_post_id_for_data() {
		if ( empty( $this->post_dynamic_elements_ids ) ) {
			return null;
		}

		return $this->post_id_for_data;
	}

	protected function is_global_parsing_supported() {
		return false;
	}

	protected function render_styles( Element_Base $element ) {
		$id = $element->get_id();

		if ( in_array( $id, $this->post_dynamic_elements_ids ) ) {
			parent::render_styles( $element );
		}

		foreach ( $element->get_children() as $child_element ) {
			$this->render_styles( $child_element );
		}
	}

	/**
	 * Dynamic_CSS constructor.
	 *
	 * @since 2.0.13
	 * @access public
	 *
	 * @param int      $post_id Post ID.
	 * @param Post_CSS $post_css_file
	 */
	public function __construct( $post_id, Post_CSS $post_css_file ) {
		if ( $post_css_file instanceof Post_Preview ) {
			$this->post_id_for_data = $post_css_file->get_post_id_for_data();
		} else {
			$this->post_id_for_data = $post_id;
		}

		$this->post_dynamic_elements_ids = $post_css_file->get_meta( 'dynamic_elements_ids' );

		parent::__construct( $post_id );
	}

	/**
	 * @since 2.0.13
	 * @access public
	 */
	public function get_name() {
		return 'dynamic';
	}

	/**
	 * Get Responsive Control Duplication Mode
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	protected function get_responsive_control_duplication_mode() {
		return 'dynamic';
	}

	/**
	 * @since 2.0.13
	 * @access protected
	 */
	protected function use_external_file() {
		return false;
	}

	/**
	 * @since 2.0.13
	 * @access protected
	 */
	protected function get_file_handle_id() {
		return 'elementor-post-dynamic-' . $this->get_post_id_for_data();
	}

	/**
	 * @since 2.0.13
	 * @access public
	 */
	public function add_controls_stack_style_rules( Controls_Stack $controls_stack, array $controls, array $values, array $placeholders, array $replacements, ?array $all_controls = null ) {
		$dynamic_settings = $controls_stack->get_settings( '__dynamic__' );

		if ( ! empty( $dynamic_settings ) ) {
			$controls = array_intersect_key( $controls, $dynamic_settings );

			$all_controls = $controls_stack->get_controls();

			$parsed_dynamic_settings = $controls_stack->parse_dynamic_settings( $values, $controls );

			foreach ( $controls as $control ) {
				if ( ! empty( $control['style_fields'] ) ) {
					$this->add_repeater_control_style_rules( $controls_stack, $control, $values[ $control['name'] ], $placeholders, $replacements );
				}

				if ( empty( $control['selectors'] ) ) {
					continue;
				}

				$this->add_control_style_rules( $control, $parsed_dynamic_settings, $all_controls, $placeholders, $replacements );
			}
		}
	}
}
