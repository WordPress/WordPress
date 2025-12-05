<?php

namespace Yoast\WP\SEO\Routes;

use WP_REST_Response;
use Yoast\WP\SEO\Actions\Indexing\Indexation_Action_Interface;

/**
 * Abstract_Indexation_Route class.
 *
 * Reindexing route for indexables.
 */
abstract class Abstract_Indexation_Route extends Abstract_Action_Route {

	/**
	 * Runs an indexing action and returns the response.
	 *
	 * @param Indexation_Action_Interface $indexation_action The indexing action.
	 * @param string                      $url               The url of the indexing route.
	 *
	 * @return WP_REST_Response The response.
	 */
	protected function run_indexation_action( Indexation_Action_Interface $indexation_action, $url ) {
		$indexables = $indexation_action->index();

		$next_url = false;
		if ( \count( $indexables ) >= $indexation_action->get_limit() ) {
			$next_url = \rest_url( $url );
		}

		return $this->respond_with( $indexables, $next_url );
	}
}
