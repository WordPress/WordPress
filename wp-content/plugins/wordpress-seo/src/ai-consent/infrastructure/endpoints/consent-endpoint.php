<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\AI_Consent\Infrastructure\Endpoints;

use Exception;
use Yoast\WP\SEO\AI_Consent\Domain\Endpoint\Endpoint_Interface;
use Yoast\WP\SEO\AI_Consent\User_Interface\Consent_Route;

/**
 * Represents the setup steps tracking endpoint.
 */
class Consent_Endpoint implements Endpoint_Interface {

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'consent';
	}

	/**
	 * Gets the namespace.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return Consent_Route::ROUTE_NAMESPACE;
	}

	/**
	 * Gets the route.
	 *
	 * @throws Exception If the route prefix is not overwritten this throws.
	 * @return string
	 */
	public function get_route(): string {
		return Consent_Route::ROUTE_PREFIX;
	}

	/**
	 * Gets the URL.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return \rest_url( $this->get_namespace() . $this->get_route() );
	}
}
