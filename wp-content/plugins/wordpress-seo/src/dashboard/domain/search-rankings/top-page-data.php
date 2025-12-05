<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Search_Rankings;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups\SEO_Score_Groups_Interface;

/**
 * Domain object that represents a single Top Page Data record.
 */
class Top_Page_Data implements Data_Interface {

	/**
	 * The search ranking data for the top page.
	 *
	 * @var Search_Ranking_Data
	 */
	private $search_ranking_data;

	/**
	 * The SEO score group the top page belongs to.
	 *
	 * @var SEO_Score_Groups_Interface
	 */
	private $seo_score_group;

	/**
	 * The edit link of the top page.
	 *
	 * @var string
	 */
	private $edit_link;

	/**
	 * The constructor.
	 *
	 * @param Search_Ranking_Data        $search_ranking_data The search ranking data for the top page.
	 * @param SEO_Score_Groups_Interface $seo_score_group     The SEO score group the top page belongs to.
	 * @param string                     $edit_link           The edit link of the top page.
	 */
	public function __construct(
		Search_Ranking_Data $search_ranking_data,
		SEO_Score_Groups_Interface $seo_score_group,
		?string $edit_link = null
	) {
		$this->search_ranking_data = $search_ranking_data;
		$this->seo_score_group     = $seo_score_group;
		$this->edit_link           = $edit_link;
	}

	/**
	 * The array representation of this domain object.
	 *
	 * @return array<string|float|int|string[]>
	 */
	public function to_array(): array {
		$top_page_data             = $this->search_ranking_data->to_array();
		$top_page_data['seoScore'] = $this->seo_score_group->get_name();
		$top_page_data['links']    = [];

		if ( $this->edit_link !== null ) {
			$top_page_data['links']['edit'] = $this->edit_link;
		}

		return $top_page_data;
	}
}
