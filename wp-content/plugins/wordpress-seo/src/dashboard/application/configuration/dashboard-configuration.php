<?php


// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Application\Configuration;

use Yoast\WP\SEO\Dashboard\Application\Content_Types\Content_Types_Repository;
use Yoast\WP\SEO\Dashboard\Application\Endpoints\Endpoints_Repository;
use Yoast\WP\SEO\Dashboard\Application\Tracking\Setup_Steps_Tracking;
use Yoast\WP\SEO\Dashboard\Infrastructure\Browser_Cache\Browser_Cache_Configuration;
use Yoast\WP\SEO\Dashboard\Infrastructure\Integrations\Site_Kit;
use Yoast\WP\SEO\Dashboard\Infrastructure\Nonces\Nonce_Repository;
use Yoast\WP\SEO\Editors\Application\Analysis_Features\Enabled_Analysis_Features_Repository;
use Yoast\WP\SEO\Editors\Framework\Keyphrase_Analysis;
use Yoast\WP\SEO\Editors\Framework\Readability_Analysis;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\User_Helper;

/**
 * Responsible for the dashboard configuration.
 */
class Dashboard_Configuration {

	/**
	 * The content types repository.
	 *
	 * @var Content_Types_Repository
	 */
	private $content_types_repository;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * The user helper.
	 *
	 * @var User_Helper
	 */
	private $user_helper;

	/**
	 * The repository.
	 *
	 * @var Enabled_Analysis_Features_Repository
	 */
	private $enabled_analysis_features_repository;

	/**
	 * The endpoints repository.
	 *
	 * @var Endpoints_Repository
	 */
	private $endpoints_repository;

	/**
	 * The nonce repository.
	 *
	 * @var Nonce_Repository
	 */
	private $nonce_repository;

	/**
	 * The Site Kit integration data.
	 *
	 * @var Site_Kit
	 */
	private $site_kit_integration_data;

	/**
	 * The setup steps tracking data.
	 *
	 * @var Setup_Steps_Tracking
	 */
	private $setup_steps_tracking;

	/**
	 * The browser cache configuration.
	 *
	 * @var Browser_Cache_Configuration
	 */
	private $browser_cache_configuration;

	/**
	 * The constructor.
	 *
	 * @param Content_Types_Repository             $content_types_repository             The content types repository.
	 * @param Indexable_Helper                     $indexable_helper                     The indexable helper
	 *                                                                                   repository.
	 * @param User_Helper                          $user_helper                          The user helper.
	 * @param Enabled_Analysis_Features_Repository $enabled_analysis_features_repository The analysis feature.
	 *                                                                                        repository.
	 * @param Endpoints_Repository                 $endpoints_repository                 The endpoints repository.
	 * @param Nonce_Repository                     $nonce_repository                     The nonce repository.
	 * @param Site_Kit                             $site_kit_integration_data            The Site Kit integration data.
	 * @param Setup_Steps_Tracking                 $setup_steps_tracking                 The setup steps tracking data.
	 * @param Browser_Cache_Configuration          $browser_cache_configuration          The browser cache configuration.
	 */
	public function __construct(
		Content_Types_Repository $content_types_repository,
		Indexable_Helper $indexable_helper,
		User_Helper $user_helper,
		Enabled_Analysis_Features_Repository $enabled_analysis_features_repository,
		Endpoints_Repository $endpoints_repository,
		Nonce_Repository $nonce_repository,
		Site_Kit $site_kit_integration_data,
		Setup_Steps_Tracking $setup_steps_tracking,
		Browser_Cache_Configuration $browser_cache_configuration
	) {
		$this->content_types_repository             = $content_types_repository;
		$this->indexable_helper                     = $indexable_helper;
		$this->user_helper                          = $user_helper;
		$this->enabled_analysis_features_repository = $enabled_analysis_features_repository;
		$this->endpoints_repository                 = $endpoints_repository;
		$this->nonce_repository                     = $nonce_repository;
		$this->site_kit_integration_data            = $site_kit_integration_data;
		$this->setup_steps_tracking                 = $setup_steps_tracking;
		$this->browser_cache_configuration          = $browser_cache_configuration;
	}

	/**
	 * Returns a configuration
	 *
	 * @return array<string, array<string>|array<string, string|array<string, array<string, int>>>>
	 */
	public function get_configuration(): array {
		$configuration = [
			'contentTypes'            => $this->content_types_repository->get_content_types(),
			'indexablesEnabled'       => $this->indexable_helper->should_index_indexables(),
			'displayName'             => $this->user_helper->get_current_user_display_name(),
			'enabledAnalysisFeatures' => $this->enabled_analysis_features_repository->get_features_by_keys(
				[
					Readability_Analysis::NAME,
					Keyphrase_Analysis::NAME,
				]
			)->to_array(),
			'endpoints'               => $this->endpoints_repository->get_all_endpoints()->to_array(),
			'nonce'                   => $this->nonce_repository->get_rest_nonce(),
			'setupStepsTracking'      => $this->setup_steps_tracking->to_array(),
		];

		$site_kit_integration_data = $this->site_kit_integration_data->to_array();
		if ( ! empty( $site_kit_integration_data ) ) {
			$configuration ['siteKitConfiguration'] = $site_kit_integration_data;
		}

		$browser_cache_configuration = $this->browser_cache_configuration->get_configuration();
		if ( ! empty( $browser_cache_configuration ) ) {
			$configuration ['browserCache'] = $browser_cache_configuration;
		}

		return $configuration;
	}
}
