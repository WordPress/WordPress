<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Application\Score_Results\SEO_Score_Results;

use Yoast\WP\SEO\Dashboard\Application\Score_Results\Abstract_Score_Results_Repository;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups\SEO_Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\SEO_Score_Results\Cached_SEO_Score_Results_Collector;

/**
 * The repository to get SEO score results.
 */
class SEO_Score_Results_Repository extends Abstract_Score_Results_Repository {

	/**
	 * The constructor.
	 *
	 * @param Cached_SEO_Score_Results_Collector $seo_score_results_collector The cached SEO score results collector.
	 * @param SEO_Score_Groups_Interface         ...$seo_score_groups         All SEO score groups.
	 */
	public function __construct(
		Cached_SEO_Score_Results_Collector $seo_score_results_collector,
		SEO_Score_Groups_Interface ...$seo_score_groups
	) {
		$this->score_results_collector = $seo_score_results_collector;
		$this->score_groups            = $seo_score_groups;
	}
}
