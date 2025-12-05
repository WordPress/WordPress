<?php

namespace Yoast\WP\SEO\Integrations;

use Yoast\WP\SEO\Loadable_Interface;

/**
 * An interface for registering integrations with WordPress.
 *
 * @codeCoverageIgnore It represents an interface.
 */
interface Integration_Interface extends Loadable_Interface {

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks();
}
