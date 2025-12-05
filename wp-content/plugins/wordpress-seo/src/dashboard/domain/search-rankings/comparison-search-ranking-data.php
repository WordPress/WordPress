<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Search_Rankings;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Interface;

/**
 * Domain object that represents a Comparison Search Ranking record.
 */
class Comparison_Search_Ranking_Data implements Data_Interface {

	/**
	 * The current search ranking data.
	 *
	 * @var Search_Ranking_Data[]
	 */
	private $current_search_ranking_data = [];

	/**
	 * The previous search ranking data.
	 *
	 * @var Search_Ranking_Data[]
	 */
	private $previous_search_ranking_data = [];

	/**
	 * Sets the current search ranking data.
	 *
	 * @param Search_Ranking_Data $current_search_ranking_data The current search ranking data.
	 *
	 * @return void
	 */
	public function add_current_traffic_data( Search_Ranking_Data $current_search_ranking_data ): void {
		\array_push( $this->current_search_ranking_data, $current_search_ranking_data );
	}

	/**
	 * Sets the previous search ranking data.
	 *
	 * @param Search_Ranking_Data $previous_search_ranking_data The previous search ranking data.
	 *
	 * @return void
	 */
	public function add_previous_traffic_data( Search_Ranking_Data $previous_search_ranking_data ): void {
		\array_push( $this->previous_search_ranking_data, $previous_search_ranking_data );
	}

	/**
	 * The array representation of this domain object.
	 *
	 * @return array<array<string, int>>
	 */
	public function to_array(): array {
		return [
			'current'  => $this->parse_data( $this->current_search_ranking_data ),
			'previous' => $this->parse_data( $this->previous_search_ranking_data ),
		];
	}

	/**
	 * Parses search ranking data into the expected format.
	 *
	 * @param Search_Ranking_Data[] $search_ranking_data The search ranking data to be parsed.
	 *
	 * @return array<string, int> The parsed data
	 */
	private function parse_data( array $search_ranking_data ): array {
		$parsed_data      = [
			'total_clicks'      => 0,
			'total_impressions' => 0,
		];
		$weighted_postion = 0;

		foreach ( $search_ranking_data as $search_ranking ) {
			$parsed_data['total_clicks']      += $search_ranking->get_clicks();
			$parsed_data['total_impressions'] += $search_ranking->get_impressions();
			$weighted_postion                 += ( $search_ranking->get_position() * $search_ranking->get_impressions() );
		}

		if ( $parsed_data['total_impressions'] !== 0 ) {
			$parsed_data['average_ctr']      = ( $parsed_data['total_clicks'] / $parsed_data['total_impressions'] );
			$parsed_data['average_position'] = ( $weighted_postion / $parsed_data['total_impressions'] );
		}

		return $parsed_data;
	}
}
