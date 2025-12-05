<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Application\Search_Rankings;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Dashboard_Repository_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Container;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Parameters;
use Yoast\WP\SEO\Dashboard\Domain\Time_Based_Seo_Metrics\Data_Source_Not_Available_Exception;
use Yoast\WP\SEO\Dashboard\Infrastructure\Indexables\Top_Page_Indexable_Collector;
use Yoast\WP\SEO\Dashboard\Infrastructure\Integrations\Site_Kit;
use Yoast\WP\SEO\Dashboard\Infrastructure\Search_Console\Site_Kit_Search_Console_Adapter;

/**
 * The data provider for top page data.
 */
class Top_Page_Repository implements Dashboard_Repository_Interface {

	/**
	 * The adapter.
	 *
	 * @var Site_Kit_Search_Console_Adapter
	 */
	private $site_kit_search_console_adapter;

	/**
	 * The top page indexable collector.
	 *
	 * @var Top_Page_Indexable_Collector
	 */
	private $top_page_indexable_collector;

	/**
	 * The site kit configuration object.
	 *
	 * @var Site_Kit
	 */
	private $site_kit_configuration;

	/**
	 * The constructor.
	 *
	 * @param Site_Kit_Search_Console_Adapter $site_kit_search_console_adapter The adapter.
	 * @param Top_Page_Indexable_Collector    $top_page_indexable_collector    The top page indexable collector.
	 * @param Site_Kit                        $site_kit_configuration          The site kit configuration object.
	 */
	public function __construct(
		Site_Kit_Search_Console_Adapter $site_kit_search_console_adapter,
		Top_Page_Indexable_Collector $top_page_indexable_collector,
		Site_Kit $site_kit_configuration
	) {
		$this->site_kit_search_console_adapter = $site_kit_search_console_adapter;
		$this->top_page_indexable_collector    = $top_page_indexable_collector;
		$this->site_kit_configuration          = $site_kit_configuration;
	}

	/**
	 * Gets the top pages' data.
	 *
	 * @param Parameters $parameters The parameter to use for getting the top pages.
	 *
	 * @return Data_Container
	 *
	 * @throws Data_Source_Not_Available_Exception When this repository is used without the needed prerequisites ready.
	 */
	public function get_data( Parameters $parameters ): Data_Container {
		if ( ! $this->site_kit_configuration->is_onboarded() ) {
			throw new Data_Source_Not_Available_Exception( 'Top page repository' );
		}
		$top_pages_search_ranking_data = $this->site_kit_search_console_adapter->get_data( $parameters );
		$top_pages_full_data           = $this->top_page_indexable_collector->get_data( $top_pages_search_ranking_data );

		return $top_pages_full_data;
	}
}
