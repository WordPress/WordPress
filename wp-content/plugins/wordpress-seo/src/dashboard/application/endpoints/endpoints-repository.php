<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Application\Endpoints;

use Yoast\WP\SEO\Dashboard\Domain\Endpoint\Endpoint_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Endpoint\Endpoint_List;

/**
 * Repository for endpoints.
 */
class Endpoints_Repository {

	/**
	 * Holds the endpoints.
	 *
	 * @var array<Endpoint_Interface>
	 */
	private $endpoints;

	/**
	 * Constructs the repository.
	 *
	 * @param Endpoint_Interface ...$endpoints The endpoints to add to the repository.
	 */
	public function __construct( Endpoint_Interface ...$endpoints ) {
		$this->endpoints = $endpoints;
	}

	/**
	 * Creates a list with all endpoints.
	 *
	 * @return Endpoint_List The list with all endpoints.
	 */
	public function get_all_endpoints(): Endpoint_List {
		$list = new Endpoint_List();
		foreach ( $this->endpoints as $endpoint ) {
			$list->add_endpoint( $endpoint );
		}

		return $list;
	}
}
