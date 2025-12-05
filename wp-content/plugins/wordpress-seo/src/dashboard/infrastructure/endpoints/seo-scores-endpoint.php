<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Endpoints;

use Exception;
use Yoast\WP\SEO\Dashboard\Domain\Endpoint\Endpoint_Interface;
use Yoast\WP\SEO\Dashboard\User_Interface\Scores\Abstract_Scores_Route;
use Yoast\WP\SEO\Dashboard\User_Interface\Scores\SEO_Scores_Route;

/**
 * Represents the SEO scores endpoint.
 */
class SEO_Scores_Endpoint implements Endpoint_Interface {

	/**
	 * Gets the name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'seoScores';
	}

	/**
	 * Gets the namespace.
	 *
	 * @return string
	 */
	public function get_namespace(): string {
		return Abstract_Scores_Route::ROUTE_NAMESPACE;
	}

	/**
	 * Gets the route.
	 *
	 * @return string
	 *
	 * @throws Exception If the route prefix is not overwritten this throws.
	 */
	public function get_route(): string {
		return SEO_Scores_Route::get_route_prefix();
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
