<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\Readability_Score_Results;

use Yoast\WP\Lib\Model;
use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Readability_Score_Groups\Readability_Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Score_Results\Score_Results_Not_Found_Exception;
use Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\Score_Results_Collector_Interface;

/**
 * Getting readability score results from the indexable database table.
 */
class Readability_Score_Results_Collector implements Score_Results_Collector_Interface {

	/**
	 * Retrieves readability score results for a content type.
	 *
	 * @param Readability_Score_Groups_Interface[] $readability_score_groups All readability score groups.
	 * @param Content_Type                         $content_type             The content type.
	 * @param int|null                             $term_id                  The ID of the term we're filtering for.
	 * @param bool|null                            $is_troubleshooting       Whether we're in troubleshooting mode.
	 *
	 * @return array<string, object|bool|float> The readability score results for a content type.
	 *
	 * @throws Score_Results_Not_Found_Exception When the query of getting score results fails.
	 */
	public function get_score_results( array $readability_score_groups, Content_Type $content_type, ?int $term_id, ?bool $is_troubleshooting ) {
		global $wpdb;
		$results = [];

		$content_type_name = $content_type->get_name();
		$select            = $this->build_select( $readability_score_groups, $is_troubleshooting );

		$replacements = \array_merge(
			\array_values( $select['replacements'] ),
			[
				Model::get_table_name( 'Indexable' ),
				$content_type_name,
			]
		);

		if ( $term_id === null ) {
			//phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- $replacements is an array with the correct replacements.
			//phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $select['fields'] is an array of simple strings with placeholders.
			$query = $wpdb->prepare(
				"
				SELECT {$select['fields']}
				FROM %i AS I
				WHERE ( I.post_status = 'publish' OR I.post_status IS NULL )
					AND I.object_type = 'post'
					AND I.object_sub_type = %s",
				$replacements
			);
			//phpcs:enable
		}
		else {
			$replacements[] = $wpdb->term_relationships;
			$replacements[] = $term_id;

			//phpcs:disable WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- $replacements is an array with the correct replacements.
			//phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $select['fields'] is an array of simple strings with placeholders.
			$query = $wpdb->prepare(
				"
				SELECT {$select['fields']}
				FROM %i AS I
				WHERE ( I.post_status = 'publish' OR I.post_status IS NULL )
					AND I.object_type = 'post'
					AND I.object_sub_type = %s
					AND I.object_id IN (
						SELECT object_id
						FROM %i
						WHERE term_taxonomy_id = %d
				)",
				$replacements
			);
			//phpcs:enable
		}

		$start_time = \microtime( true );

		//phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- $query is prepared above.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Reason: Most performant way.
		//phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
		$current_scores = $wpdb->get_row( $query );
		//phpcs:enable

		if ( $current_scores === null ) {
			throw new Score_Results_Not_Found_Exception();
		}

		$end_time = \microtime( true );

		$results['scores']     = $current_scores;
		$results['query_time'] = ( $end_time - $start_time );
		return $results;
	}

	/**
	 * Builds the select statement for the readability scores query.
	 *
	 * @param Readability_Score_Groups_Interface[] $readability_score_groups All readability score groups.
	 * @param bool|null                            $is_troubleshooting       Whether we're in troubleshooting mode.
	 *
	 * @return array<string, string> The select statement for the readability scores query.
	 */
	private function build_select( array $readability_score_groups, ?bool $is_troubleshooting ): array {
		$select_fields       = [];
		$select_replacements = [];

		// When we don't troubleshoot, we're interested in the amount of posts in a group, when we troubleshoot we want to gather the actual IDs.
		$select_operation = ( $is_troubleshooting === true ) ? 'GROUP_CONCAT' : 'COUNT';
		$selected_info    = ( $is_troubleshooting === true ) ? 'I.object_id' : '1';

		foreach ( $readability_score_groups as $readability_score_group ) {
			$min  = $readability_score_group->get_min_score();
			$max  = $readability_score_group->get_max_score();
			$name = $readability_score_group->get_name();

			if ( $min === null && $max === null ) {
				$select_fields[]       = "{$select_operation}(CASE WHEN I.readability_score = 0 AND I.estimated_reading_time_minutes IS NULL THEN {$selected_info} END) AS %i";
				$select_replacements[] = $name;
			}
			else {
				$needs_ert             = ( $min === 1 ) ? ' OR (I.readability_score = 0 AND I.estimated_reading_time_minutes IS NOT NULL)' : '';
				$select_fields[]       = "{$select_operation}(CASE WHEN ( I.readability_score >= %d AND I.readability_score <= %d ){$needs_ert} THEN {$selected_info} END) AS %i";
				$select_replacements[] = $min;
				$select_replacements[] = $max;
				$select_replacements[] = $name;
			}
		}

		$select_fields = \implode( ', ', $select_fields );

		return [
			'fields'       => $select_fields,
			'replacements' => $select_replacements,
		];
	}
}
