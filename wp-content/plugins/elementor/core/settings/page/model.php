<?php
namespace Elementor\Core\Settings\Page;

use Elementor\Core\Settings\Base\CSS_Model;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor page settings model.
 *
 * Elementor page settings model handler class is responsible for registering
 * and managing Elementor page settings models.
 *
 * @since 1.6.0
 */
class Model extends CSS_Model {

	/**
	 * WordPress post object.
	 *
	 * Holds an instance of `WP_Post` containing the post object.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @var \WP_Post
	 */
	private $post;

	/**
	 * @var \WP_Post
	 */
	private $post_parent;

	/**
	 * Model constructor.
	 *
	 * Initializing Elementor page settings model.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $data Optional. Model data. Default is an empty array.
	 */
	public function __construct( array $data = [] ) {
		$this->post = get_post( $data['id'] );

		if ( ! $this->post ) {
			$this->post = new \WP_Post( (object) [] );
		}

		if ( wp_is_post_revision( $this->post->ID ) ) {
			$this->post_parent = get_post( $this->post->post_parent );
		} else {
			$this->post_parent = $this->post;
		}

		parent::__construct( $data );
	}

	/**
	 * Get model name.
	 *
	 * Retrieve page settings model name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Model name.
	 */
	public function get_name() {
		return 'page-settings';
	}

	/**
	 * Get model unique name.
	 *
	 * Retrieve page settings model unique name.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string Model unique name.
	 */
	public function get_unique_name() {
		return $this->get_name() . '-' . $this->post->ID;
	}

	/**
	 * Get CSS wrapper selector.
	 *
	 * Retrieve the wrapper selector for the page settings model.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return string CSS wrapper selector.
	 */
	public function get_css_wrapper_selector() {
		$document = Plugin::$instance->documents->get( $this->post_parent->ID );
		return $document->get_css_wrapper_selector();
	}

	/**
	 * Get panel page settings.
	 *
	 * Retrieve the panel setting for the page settings model.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array {
	 *    Panel settings.
	 *
	 *    @type string $title The panel title.
	 * }
	 */
	public function get_panel_page_settings() {
		$document = Plugin::$instance->documents->get( $this->post->ID );

		return [
			'title' => sprintf(
				/* translators: %s: Document title. */
				esc_html__( '%s Settings', 'elementor' ),
				$document::get_title()
			),
		];
	}

	/**
	 * On export post meta.
	 *
	 * When exporting data, check if the post is not using page template and
	 * exclude it from the exported Elementor data.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $element_data Element data.
	 *
	 * @return array Element data to be exported.
	 */
	public function on_export( $element_data ) {
		if ( ! empty( $element_data['settings']['template'] ) ) {
			/**
			 * @var \Elementor\Modules\PageTemplates\Module $page_templates_module
			 */
			$page_templates_module = Plugin::$instance->modules_manager->get_modules( 'page-templates' );
			$is_elementor_template = (bool) $page_templates_module->get_template_path( $element_data['settings']['template'] );

			if ( ! $is_elementor_template ) {
				unset( $element_data['settings']['template'] );
			}
		}

		return $element_data;
	}

	/**
	 * Register model controls.
	 *
	 * Used to add new controls to the page settings model.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		// Check if it's a real model, or abstract (for example - on import ).
		if ( $this->post->ID ) {
			$document = Plugin::$instance->documents->get_doc_or_auto_save( $this->post->ID );

			if ( $document ) {
				$controls = $document->get_controls();

				foreach ( $controls as $control_id => $args ) {
					$this->add_control( $control_id, $args );
				}
			}
		}
	}
}
