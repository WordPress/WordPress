<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\User_Interface\Scores;

use Yoast\WP\SEO\Dashboard\Application\Score_Results\SEO_Score_Results\SEO_Score_Results_Repository;

/**
 * Registers a route to get SEO scores.
 */
class SEO_Scores_Route extends Abstract_Scores_Route {

	/**
	 * The prefix of the route.
	 *
	 * @var string
	 */
	public const ROUTE_PREFIX = '/seo_scores';

	/**
	 * Constructs the class.
	 *
	 * @param SEO_Score_Results_Repository $seo_score_results_repository The SEO score results repository.
	 */
	public function __construct( SEO_Score_Results_Repository $seo_score_results_repository ) {
		$this->score_results_repository = $seo_score_results_repository;
	}
}
