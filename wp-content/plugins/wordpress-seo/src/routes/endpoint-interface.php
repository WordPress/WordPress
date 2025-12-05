<?php

namespace Yoast\WP\SEO\Routes;

interface Endpoint_Interface {

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Gets the namespace.
	 *
	 * @return string
	 */
	public function get_namespace(): string;

	/**
	 * Gets the route.
	 *
	 * @return string
	 */
	public function get_route(): string;

	/**
	 * Gets the URL.
	 *
	 * @return string
	 */
	public function get_url(): string;
}
