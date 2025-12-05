<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Generator\Domain\Endpoint;

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
