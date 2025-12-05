<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Score_Results;

use Yoast\WP\SEO\Dashboard\Domain\Content_Types\Content_Type;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Score_Groups_Interface;

/**
 * The interface of score result collectors.
 */
interface Score_Results_Collector_Interface {

	/**
	 * Retrieves the score results for a content type.
	 *
	 * @param Score_Groups_Interface[] $score_groups       All score groups.
	 * @param Content_Type             $content_type       The content type.
	 * @param int|null                 $term_id            The ID of the term we're filtering for.
	 * @param bool|null                $is_troubleshooting Whether we're in troubleshooting mode.
	 *
	 * @return array<string, string> The score results for a content type.
	 */
	public function get_score_results( array $score_groups, Content_Type $content_type, ?int $term_id, ?bool $is_troubleshooting );
}
