<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Indexables;

use Yoast\WP\SEO\Dashboard\Application\Score_Groups\SEO_Score_Groups\SEO_Score_Groups_Repository;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Container;
use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups\No_SEO_Score_Group;
use Yoast\WP\SEO\Dashboard\Domain\Search_Rankings\Top_Page_Data;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * The indexable collector that gets SEO scores from the indexables of top pages.
 */
class Top_Page_Indexable_Collector {

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The SEO score groups repository.
	 *
	 * @var SEO_Score_Groups_Repository
	 */
	private $seo_score_groups_repository;

	/**
	 * The constructor.
	 *
	 * @param Indexable_Repository        $indexable_repository        The indexable repository.
	 * @param SEO_Score_Groups_Repository $seo_score_groups_repository The SEO score groups repository.
	 */
	public function __construct(
		Indexable_Repository $indexable_repository,
		SEO_Score_Groups_Repository $seo_score_groups_repository
	) {
		$this->indexable_repository        = $indexable_repository;
		$this->seo_score_groups_repository = $seo_score_groups_repository;
	}

	/**
	 * Gets full data for top pages.
	 *
	 * @param Data_Container $top_pages The top pages.
	 *
	 * @return Data_Container Data about SEO scores of top pages.
	 */
	public function get_data( Data_Container $top_pages ): Data_Container {
		$top_page_data_container = new Data_Container();

		foreach ( $top_pages->get_data() as $top_page ) {
			$url = $top_page->get_subject();

			$indexable = $this->get_top_page_indexable( $url );

			if ( $indexable instanceof Indexable ) {
				$seo_score_group = $this->seo_score_groups_repository->get_seo_score_group( $indexable->primary_focus_keyword_score );
				$edit_link       = $this->get_top_page_edit_link( $indexable );

				$top_page_data_container->add_data( new Top_Page_Data( $top_page, $seo_score_group, $edit_link ) );

				continue;
			}

			$seo_score_group = new No_SEO_Score_Group();
			$top_page_data_container->add_data( new Top_Page_Data( $top_page, $seo_score_group ) );
		}

		return $top_page_data_container;
	}

	/**
	 * Gets indexable for a top page URL.
	 *
	 * @param string $url The URL of the top page.
	 *
	 * @return bool|Indexable The indexable of the top page URL or false if there is none.
	 */
	protected function get_top_page_indexable( string $url ) {
		// First check if the URL is the static homepage.
		if ( \trailingslashit( $url ) === \trailingslashit( \get_home_url() ) && \get_option( 'show_on_front' ) === 'page' ) {
			return $this->indexable_repository->find_by_id_and_type( \get_option( 'page_on_front' ), 'post', false );
		}

		return $this->indexable_repository->find_by_permalink( $url );
	}

	/**
	 * Gets edit links from a top page's indexable.
	 *
	 * @param Indexable $indexable The top page's indexable.
	 *
	 * @return string|null The edit link for the top page.
	 */
	protected function get_top_page_edit_link( Indexable $indexable ): ?string {
		if ( $indexable->object_type === 'post' && \current_user_can( 'edit_post', $indexable->object_id ) ) {
			return \get_edit_post_link( $indexable->object_id, '&' );
		}

		if ( $indexable->object_type === 'term' && \current_user_can( 'edit_term', $indexable->object_id ) ) {
			return \get_edit_term_link( $indexable->object_id );
		}

		return null;
	}
}
