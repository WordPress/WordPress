<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Application\Traffic;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Dashboard_Repository_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Container;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Parameters;
use Yoast\WP\SEO\Dashboard\Domain\Time_Based_Seo_Metrics\Data_Source_Not_Available_Exception;
use Yoast\WP\SEO\Dashboard\Infrastructure\Analytics_4\Site_Kit_Analytics_4_Adapter;
use Yoast\WP\SEO\Dashboard\Infrastructure\Integrations\Site_Kit;

/**
 * The data provider for comparison organic sessions data.
 */
class Organic_Sessions_Compare_Repository implements Dashboard_Repository_Interface {

	/**
	 * The adapter.
	 *
	 * @var Site_Kit_Analytics_4_Adapter
	 */
	private $site_kit_analytics_4_adapter;

	/**
	 * The site kit configuration object.
	 *
	 * @var Site_Kit
	 */
	private $site_kit_configuration;

	/**
	 * The constructor.
	 *
	 * @param Site_Kit_Analytics_4_Adapter $site_kit_analytics_4_adapter The adapter.
	 * @param Site_Kit                     $site_kit_configuration       The site kit configuration object.
	 */
	public function __construct(
		Site_Kit_Analytics_4_Adapter $site_kit_analytics_4_adapter,
		Site_Kit $site_kit_configuration
	) {
		$this->site_kit_analytics_4_adapter = $site_kit_analytics_4_adapter;
		$this->site_kit_configuration       = $site_kit_configuration;
	}

	/**
	 * Gets comparison organic sessions' data.
	 *
	 * @param Parameters $parameters The parameter to use for getting the comparison organic sessions' data.
	 *
	 * @return Data_Container
	 *
	 * @throws Data_Source_Not_Available_Exception When getting the comparison organic sessions' data fails.
	 */
	public function get_data( Parameters $parameters ): Data_Container {
		if ( ! $this->site_kit_configuration->is_onboarded() || ! $this->site_kit_configuration->is_ga_connected() ) {
			throw new Data_Source_Not_Available_Exception( 'Comparison organic sessions repository' );
		}

		return $this->site_kit_analytics_4_adapter->get_comparison_data( $parameters );
	}
}
