<?php

namespace Elementor\Core\Settings\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class CSS_Model extends Model {

	/**
	 * Get CSS wrapper selector.
	 *
	 * Retrieve the wrapper selector for the current panel.
	 *
	 * @since 1.6.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_css_wrapper_selector();
}
