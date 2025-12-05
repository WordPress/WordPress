<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\User_Interface\Scores;

use Yoast\WP\SEO\Dashboard\Application\Score_Results\Readability_Score_Results\Readability_Score_Results_Repository;

/**
 * Registers a route to get readability scores.
 */
class Readability_Scores_Route extends Abstract_Scores_Route {

	/**
	 * The prefix of the route.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/readability_scores';

	/**
	 * Constructs the class.
	 *
	 * @param Readability_Score_Results_Repository $readability_score_results_repository The readability score results repository.
	 */
	public function __construct( Readability_Score_Results_Repository $readability_score_results_repository ) {
		$this->score_results_repository = $readability_score_results_repository;
	}
}
