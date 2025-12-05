<?php
namespace Elementor\Core\Debug\Classes;

abstract class Inspection_Base {

	/**
	 * @return bool
	 */
	abstract public function run();

	/**
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * @return string
	 */
	abstract public function get_message();

	/**
	 * @return string
	 */
	public function get_header_message() {
		return esc_html__( 'The preview could not be loaded', 'elementor' );
	}

	/**
	 * @return string
	 */
	abstract public function get_help_doc_url();
}
