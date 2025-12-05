<?php
namespace Elementor\Modules\AtomicWidgets\Library;

use Elementor\Modules\Library\Documents\Library_Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Flexbox library document.
 *
 * Elementor flexbox library document handler class is responsible for
 * handling a document of a flexbox type.
 *
 * @since 3.29.0
 */
class Flexbox extends Library_Document {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['support_kit'] = true;

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'e-flexbox';
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
		return esc_html__( 'Flexbox', 'elementor' );
	}

	/**
	 * Get Type
	 *
	 * Return the flexbox document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'e-flexbox';
	}
}
