<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Application\Score_Results\Readability_Score_Results;

use Yoast\WP\SEO\Dashboard\Application\Score_Results\Abstract_Score_Results_Repository;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Readability_Score_Groups\Readability_Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\Readability_Score_Results\Cached_Readability_Score_Results_Collector;

/**
 * The repository to get readability score results.
 */
class Readability_Score_Results_Repository extends Abstract_Score_Results_Repository {

	/**
	 * The constructor.
	 *
	 * @param Cached_Readability_Score_Results_Collector $readability_score_results_collector The cached readability score results collector.
	 * @param Readability_Score_Groups_Interface         ...$readability_score_groups         All readability score groups.
	 */
	public function __construct(
		Cached_Readability_Score_Results_Collector $readability_score_results_collector,
		Readability_Score_Groups_Interface ...$readability_score_groups
	) {
		$this->score_results_collector = $readability_score_results_collector;
		$this->score_groups            = $readability_score_groups;
	}
}
