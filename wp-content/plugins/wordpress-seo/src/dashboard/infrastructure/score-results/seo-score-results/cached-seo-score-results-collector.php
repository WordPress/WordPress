<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\SEO_Score_Results;

use WPSEO_Utils;
use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups\SEO_Score_Groups_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Score_Results\Score_Results_Not_Found_Exception;
use Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results\Score_Results_Collector_Interface;

/**
 * The caching decorator to get readability score results.
 */
class Cached_SEO_Score_Results_Collector implements Score_Results_Collector_Interface {

	public const SEO_SCORES_TRANSIENT = 'wpseo_seo_scores';

	/**
	 * The actual collector implementation.
	 *
	 * @var SEO_Score_Results_Collector
	 */
	private $seo_score_results_collector;

	/**
	 * The constructor.
	 *
	 * @param SEO_Score_Results_Collector $seo_score_results_collector The collector implementation to use.
	 */
	public function __construct( SEO_Score_Results_Collector $seo_score_results_collector ) {
		$this->seo_score_results_collector = $seo_score_results_collector;
	}

	/**
	 * Retrieves the SEO score results for a content type.
	 * Based on caching returns either the result or gets it from the collector.
	 *
	 * @param SEO_Score_Groups_Interface[] $score_groups       All SEO score groups.
	 * @param Content_Type                 $content_type       The content type.
	 * @param int|null                     $term_id            The ID of the term we're filtering for.
	 * @param bool|null                    $is_troubleshooting Whether we're in troubleshooting mode.
	 *
	 * @return array<string, object|bool|float> The SEO score results for a content type.
	 *
	 * @throws Score_Results_Not_Found_Exception When the query of getting score results fails.
	 */
	public function get_score_results(
		array $score_groups,
		Content_Type $content_type,
		?int $term_id,
		?bool $is_troubleshooting
	) {
		$content_type_name = $content_type->get_name();
		$transient_name    = self::SEO_SCORES_TRANSIENT . '_' . $content_type_name . ( ( $term_id === null ) ? '' : '_' . $term_id );
		$results           = [];
		$transient         = \get_transient( $transient_name );
		if ( $is_troubleshooting !== true && $transient !== false ) {
			$results['scores']     = \json_decode( $transient, false );
			$results['cache_used'] = true;
			$results['query_time'] = 0;

			return $results;
		}

		$results               = $this->seo_score_results_collector->get_score_results( $score_groups, $content_type, $term_id, $is_troubleshooting );
		$results['cache_used'] = false;
		if ( $is_troubleshooting !== true ) {
			\set_transient( $transient_name, WPSEO_Utils::format_json_encode( $results['scores'] ), \MINUTE_IN_SECONDS );
		}
		return $results;
	}
}
