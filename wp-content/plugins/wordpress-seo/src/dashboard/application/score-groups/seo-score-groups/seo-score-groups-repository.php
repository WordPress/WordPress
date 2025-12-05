<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Application\Score_Groups\SEO_Score_Groups;

use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups\No_SEO_Score_Group;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups\SEO_Score_Groups_Interface;

/**
 * The repository to get SEO score groups.
 */
class SEO_Score_Groups_Repository {

	/**
	 * All SEO score groups.
	 *
	 * @var SEO_Score_Groups_Interface[]
	 */
	private $seo_score_groups;

	/**
	 * The constructor.
	 *
	 * @param SEO_Score_Groups_Interface ...$seo_score_groups All SEO score groups.
	 */
	public function __construct( SEO_Score_Groups_Interface ...$seo_score_groups ) {
		$this->seo_score_groups = $seo_score_groups;
	}

	/**
	 * Returns the SEO score group that a SEO score belongs to.
	 *
	 * @param int $seo_score The SEO score to be assigned into a group.
	 *
	 * @return SEO_Score_Groups_Interface The SEO score group that the SEO score belongs to.
	 */
	public function get_seo_score_group( ?int $seo_score ): SEO_Score_Groups_Interface {
		if ( $seo_score === null || $seo_score === 0 ) {
			return new No_SEO_Score_Group();
		}

		foreach ( $this->seo_score_groups as $seo_score_group ) {
			if ( $seo_score_group->get_max_score() === null ) {
				continue;
			}

			if ( $seo_score >= $seo_score_group->get_min_score() && $seo_score <= $seo_score_group->get_max_score() ) {
				return $seo_score_group;
			}
		}

		return new No_SEO_Score_Group();
	}
}
