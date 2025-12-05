<?php
namespace Elementor\Modules\Library\Documents;

use Elementor\Core\DocumentTypes\Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor page library document.
 *
 * Elementor page library document handler class is responsible for
 * handling a document of a page type.
 *
 * @since 2.0.0
 */
class Page extends Library_Document {

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['support_wp_page_templates'] = true;
		$properties['support_kit'] = true;
		$properties['show_in_finder'] = true;

		return $properties;
	}

	public static function get_type() {
		return 'page';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 2.0.0
	 * @access public
	 * @static
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return esc_html__( 'Page', 'elementor' );
	}

	public static function get_plural_title() {
		return esc_html__( 'Pages', 'elementor' );
	}

	public static function get_add_new_title() {
		return esc_html__( 'Add New Page Template', 'elementor' );
	}

	/**
	 * @since 2.1.3
	 * @access public
	 */
	public function get_css_wrapper_selector() {
		return 'body.elementor-page-' . $this->get_main_id();
	}

	/**
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		parent::register_controls();

		Post::register_hide_title_control( $this );

		Post::register_style_controls( $this );
	}

	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$config['type'] = 'page';
		$config['default_route'] = 'templates/pages';

		return $config;
	}
}
