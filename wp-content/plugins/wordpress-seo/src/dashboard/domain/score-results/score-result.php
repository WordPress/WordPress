<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Results;

/**
 * This class describes a score result.
 */
class Score_Result {

	/**
	 * The list of the current scores of the score result.
	 *
	 * @var Current_Scores_List
	 */
	private $current_scores_list;

	/**
	 * The time the query took to get the score results.
	 *
	 * @var float
	 */
	private $query_time;

	/**
	 * Whether cache was used to get the score results.
	 *
	 * @var bool
	 */
	private $is_cached_used;

	/**
	 * The constructor.
	 *
	 * @param Current_Scores_List $current_scores_list The list of the current scores of the score result.
	 * @param float               $query_time          The time the query took to get the score results.
	 * @param bool                $is_cached_used      Whether cache was used to get the score results.
	 */
	public function __construct( Current_Scores_List $current_scores_list, float $query_time, bool $is_cached_used ) {
		$this->current_scores_list = $current_scores_list;
		$this->query_time          = $query_time;
		$this->is_cached_used      = $is_cached_used;
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, array<array<string, string|int|array<string, string>>>|float|bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		return [
			'scores'    => $this->current_scores_list->to_array(),
			'queryTime' => $this->query_time,
			'cacheUsed' => $this->is_cached_used,
		];
	}
}
