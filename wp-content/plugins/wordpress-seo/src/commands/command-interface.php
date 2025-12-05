<?php

namespace Yoast\WP\SEO\Commands;

/**
 * Interface definition for WP CLI commands.
 *
 * An interface for registering integrations with WordPress.
 */
interface Command_Interface {

	/**
	 * Returns the namespace of this command.
	 *
	 * @return string
	 */
	public static function get_namespace();
}
