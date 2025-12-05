<?php
namespace Elementor\Core\Admin\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base_Notice {
	/**
	 * Determine if the notice should be printed or not.
	 *
	 * @return boolean
	 */
	abstract public function should_print();

	/**
	 * Returns the config of the notice itself.
	 * based on that config the notice will be printed.
	 *
	 * @see \Elementor\Core\Admin\Admin_Notices::admin_notices
	 *
	 * @return array
	 */
	abstract public function get_config();
}
