<?php

namespace Yoast\WP\SEO\Helpers;

/**
 * Represents a file helper.
 */
class Require_File_Helper {

	/**
	 * Activates the plugin based on the given plugin file.
	 *
	 * @param string $path The path to the required file.
	 *
	 * @return void
	 */
	public function require_file_once( $path ) {
		require_once $path;
	}
}
