<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Application\Score_Results;

use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Score_Results\Current_Score;
use Yoast\WP\SEO\Dashboard\Domain\Score_Results\Current_Scores_List;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;
use Yoast\WP\SEO\Dashboard\Infrastructure\Score_Groups\Score_Group_Link_Collector;

/**
 * The current scores repository.
 */
class Current_Scores_Repository {

	/**
	 * The score group link collector.
	 *
	 * @var Score_Group_Link_Collector
	 */
	protected $score_group_link_collector;

	/**
	 * The constructor.
	 *
	 * @param Score_Group_Link_Collector $score_group_link_collector The score group link collector.
	 */
	public function __construct( Score_Group_Link_Collector $score_group_link_collector ) {
		$this->score_group_link_collector = $score_group_link_collector;
	}

	/**
	 * Returns the current results.
	 *
	 * @param Score_Groups_Interface[] $score_groups  The score groups.
	 * @param array<string, string>    $score_results The score results.
	 * @param Content_Type             $content_type  The content type.
	 * @param Taxonomy|null            $taxonomy      The taxonomy of the term we're filtering for.
	 * @param int|null                 $term_id       The ID of the term we're filtering for.
	 *
	 * @return array<array<string, string|int|array<string, string>>> The current results.
	 */
	public function get_current_scores( array $score_groups, array $score_results, Content_Type $content_type, ?Taxonomy $taxonomy, ?int $term_id ): Current_Scores_List {
		$current_scores_list = new Current_Scores_List();

		foreach ( $score_groups as $score_group ) {
			$score_name          = $score_group->get_name();
			$current_score_links = $this->get_current_score_links( $score_group, $content_type, $taxonomy, $term_id );
			$score_amount        = (int) $score_results['scores']->$score_name;
			$score_ids           = ( isset( $score_results['score_ids'] ) ) ? $score_results['score_ids']->$score_name : null;

			$current_score = new Current_Score( $score_name, $score_amount, $score_ids, $current_score_links );
			$current_scores_list->add( $current_score, $score_group->get_position() );
		}

		return $current_scores_list;
	}

	/**
	 * Returns the links for the current scores of a score group.
	 *
	 * @param Score_Groups_Interface $score_group  The scoure group.
	 * @param Content_Type           $content_type The content type.
	 * @param Taxonomy|null          $taxonomy     The taxonomy of the term we're filtering for.
	 * @param int|null               $term_id      The ID of the term we're filtering for.
	 *
	 * @return array<string, string> The current score links.
	 */
	protected function get_current_score_links( Score_Groups_Interface $score_group, Content_Type $content_type, ?Taxonomy $taxonomy, ?int $term_id ): array {
		return [
			'view' => $this->score_group_link_collector->get_view_link( $score_group, $content_type, $taxonomy, $term_id ),
		];
	}
}
