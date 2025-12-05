<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Application\Score_Results;

use Exception;
use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Score_Results\Score_Result;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;
use Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\Score_Results_Collector_Interface;

/**
 * The abstract score results repository.
 */
abstract class Abstract_Score_Results_Repository {

	/**
	 * The score results collector.
	 *
	 * @var Score_Results_Collector_Interface
	 */
	protected $score_results_collector;

	/**
	 * The current scores repository.
	 *
	 * @var Current_Scores_Repository
	 */
	protected $current_scores_repository;

	/**
	 * All score groups.
	 *
	 * @var Score_Groups_Interface[]
	 */
	protected $score_groups;

	/**
	 * Sets the repositories.
	 *
	 * @required
	 *
	 * @param Current_Scores_Repository $current_scores_repository The current scores repository.
	 *
	 * @return void
	 */
	public function set_repositories( Current_Scores_Repository $current_scores_repository ) {
		$this->current_scores_repository = $current_scores_repository;
	}

	/**
	 * Returns the score results for a content type.
	 *
	 * @param Content_Type  $content_type       The content type.
	 * @param Taxonomy|null $taxonomy           The taxonomy of the term we're filtering for.
	 * @param int|null      $term_id            The ID of the term we're filtering for.
	 * @param bool|null     $is_troubleshooting Whether we're in troubleshooting mode.
	 *
	 * @return array<array<string, string|int|array<string, string>>> The scores.
	 *
	 * @throws Exception When getting score results from the infrastructure fails.
	 */
	public function get_score_results( Content_Type $content_type, ?Taxonomy $taxonomy, ?int $term_id, ?bool $is_troubleshooting ): array {
		$score_results = $this->score_results_collector->get_score_results( $this->score_groups, $content_type, $term_id, $is_troubleshooting );

		if ( $is_troubleshooting === true ) {
			$score_results['score_ids'] = clone $score_results['scores'];

			foreach ( $score_results['scores'] as &$score ) {
				$score = ( $score !== null ) ? \count( \explode( ',', $score ) ) : 0;
			}
		}

		$current_scores_list = $this->current_scores_repository->get_current_scores( $this->score_groups, $score_results, $content_type, $taxonomy, $term_id );
		$score_result_object = new Score_Result( $current_scores_list, $score_results['query_time'], $score_results['cache_used'] );

		return $score_result_object->to_array();
	}
}
