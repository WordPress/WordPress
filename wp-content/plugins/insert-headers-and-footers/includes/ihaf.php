<?php


/**
 * Get the main instance of WPCode.
 *
 * @return WPCode
 */
function WPCode() {// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WPCode::instance();
}
