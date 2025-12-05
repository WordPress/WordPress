<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Results;

/**
 * This class describes a list of current scores.
 */
class Current_Scores_List {

	/**
	 * The current scores.
	 *
	 * @var Current_Score[]
	 */
	private $current_scores = [];

	/**
	 * Adds a current score to the list.
	 *
	 * @param Current_Score $current_score The current score to add.
	 * @param int           $position      The position to add the current score.
	 *
	 * @return void
	 */
	public function add( Current_Score $current_score, int $position ): void {
		$this->current_scores[ $position ] = $current_score;
	}

	/**
	 * Parses the current score list to the expected key value representation.
	 *
	 * @return array<array<string, string|int|array<string, string>>> The score list presented as the expected key value representation.
	 */
	public function to_array(): array {
		$array = [];

		\ksort( $this->current_scores );

		foreach ( $this->current_scores as $key => $current_score ) {
			$array[] = [
				'name'   => $current_score->get_name(),
				'amount' => $current_score->get_amount(),
				'links'  => $current_score->get_links_to_array(),
			];

			if ( $current_score->get_ids() !== null ) {
				$array[ $key ]['ids'] = $current_score->get_ids();
			}
		}

		return $array;
	}
}
