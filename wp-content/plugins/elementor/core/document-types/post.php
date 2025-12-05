<?php
namespace Elementor\Core\DocumentTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Post extends PageBase {

	/**
	 * Get Properties
	 *
	 * Return the post document configuration properties.
	 *
	 * @access public
	 * @static
	 *
	 * @return array
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['support_kit'] = true;
		$properties['cpt'] = [ 'post' ];

		return $properties;
	}

	/**
	 * Get Type
	 *
	 * Return the post document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'wp-post';
	}

	/**
	 * Get Title
	 *
	 * Return the post document title.
	 *
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'Post', 'elementor' );
	}

	/**
	 * Get Plural Title
	 *
	 * Return the post document plural title.
	 *
	 * @access public
	 * @static
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'Posts', 'elementor' );
	}
}
