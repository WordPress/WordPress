<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Search_Console;

use Google\Site_Kit_Dependencies\Google\Service\SearchConsole\ApiDataRow;
use WP_REST_Response;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Container;
use Yoast\WP\SEO\Dashboard\Domain\Search_Console\Failed_Request_Exception;
use Yoast\WP\SEO\Dashboard\Domain\Search_Console\Unexpected_Response_Exception;
use Yoast\WP\SEO\Dashboard\Domain\Search_Rankings\Comparison_Search_Ranking_Data;
use Yoast\WP\SEO\Dashboard\Domain\Search_Rankings\Search_Ranking_Data;

/**
 * The site API adapter to make calls to the Search Console API, via the Site_Kit plugin.
 */
class Site_Kit_Search_Console_Adapter {

	/**
	 * Holds the api call class.
	 *
	 * @var Site_Kit_Search_Console_Api_Call $site_kit_search_console_api_call
	 */
	private $site_kit_search_console_api_call;

	/**
	 * The constructor.
	 *
	 * @param Site_Kit_Search_Console_Api_Call $site_kit_search_console_api_call The api call class.
	 */
	public function __construct( Site_Kit_Search_Console_Api_Call $site_kit_search_console_api_call ) {
		$this->site_kit_search_console_api_call = $site_kit_search_console_api_call;
	}

	/**
	 * The wrapper method to do a Site Kit API request for Search Console.
	 *
	 * @param Search_Console_Parameters $parameters The parameters.
	 *
	 * @throws Failed_Request_Exception      When the request responds with an error from Site Kit.
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 * @return Data_Container The Site Kit API response.
	 */
	public function get_data( Search_Console_Parameters $parameters ): Data_Container {
		$api_parameters = $this->build_parameters( $parameters );

		$response = $this->site_kit_search_console_api_call->do_request( $api_parameters );

		$this->validate_response( $response );

		return $this->parse_response( $response->get_data() );
	}

	/**
	 * The wrapper method to do a comparison Site Kit API request for Search Console.
	 *
	 * @param Search_Console_Parameters $parameters The parameters.
	 *
	 * @throws Failed_Request_Exception      When the request responds with an error from Site Kit.
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 * @return Data_Container The Site Kit API response.
	 */
	public function get_comparison_data( Search_Console_Parameters $parameters ): Data_Container {
		$api_parameters = $this->build_parameters( $parameters );

		// Since we're doing a comparison request, we need to increase the date range to the start of the previous period. We'll later split the data into two periods.
		$api_parameters['startDate'] = $parameters->get_compare_start_date();

		$response = $this->site_kit_search_console_api_call->do_request( $api_parameters );

		$this->validate_response( $response );

		return $this->parse_comparison_response( $response->get_data(), $parameters->get_compare_end_date() );
	}

	/**
	 * Builds the parameters to be used in the Site Kit API request.
	 *
	 * @param Search_Console_Parameters $parameters The parameters.
	 *
	 * @return array<string, array<string, string>> The Site Kit API parameters.
	 */
	private function build_parameters( Search_Console_Parameters $parameters ): array {
		$api_parameters = [
			'startDate'  => $parameters->get_start_date(),
			'endDate'    => $parameters->get_end_date(),
			'dimensions' => $parameters->get_dimensions(),
		];

		if ( $parameters->get_limit() !== 0 ) {
			$api_parameters['limit'] = $parameters->get_limit();
		}

		return $api_parameters;
	}

	/**
	 * Parses a response for a comparison Site Kit API request for Search Analytics.
	 *
	 * @param ApiDataRow[] $response         The response to parse.
	 * @param string       $compare_end_date The compare end date.
	 *
	 * @throws Unexpected_Response_Exception When the comparison request responds with an unexpected format.
	 * @return Data_Container The parsed comparison Site Kit API response.
	 */
	private function parse_comparison_response( array $response, ?string $compare_end_date ): Data_Container {
		$data_container                 = new Data_Container();
		$comparison_search_ranking_data = new Comparison_Search_Ranking_Data();

		foreach ( $response as $ranking_date ) {

			if ( ! \is_a( $ranking_date, ApiDataRow::class ) ) {
				throw new Unexpected_Response_Exception();
			}

			$ranking_data = new Search_Ranking_Data( $ranking_date->getClicks(), $ranking_date->getCtr(), $ranking_date->getImpressions(), $ranking_date->getPosition(), $ranking_date->getKeys()[0] );

			// Now split the data into two periods.
			if ( $ranking_date->getKeys()[0] <= $compare_end_date ) {
				$comparison_search_ranking_data->add_previous_traffic_data( $ranking_data );
			}
			else {
				$comparison_search_ranking_data->add_current_traffic_data( $ranking_data );
			}
		}

		$data_container->add_data( $comparison_search_ranking_data );

		return $data_container;
	}

	/**
	 * Parses a response for a Site Kit API request for Search Analytics.
	 *
	 * @param ApiDataRow[] $response The response to parse.
	 *
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 * @return Data_Container The parsed Site Kit API response.
	 */
	private function parse_response( array $response ): Data_Container {
		$search_ranking_data_container = new Data_Container();

		foreach ( $response as $ranking ) {

			if ( ! \is_a( $ranking, ApiDataRow::class ) ) {
				throw new Unexpected_Response_Exception();
			}

			/**
			 * Filter: 'wpseo_transform_dashboard_subject_for_testing' - Allows overriding subjects like URLs for the dashboard, to facilitate testing in local environments.
			 *
			 * @param string $url The subject to be transformed.
			 *
			 * @internal
			 */
			$subject = \apply_filters( 'wpseo_transform_dashboard_subject_for_testing', $ranking->getKeys()[0] );

			$search_ranking_data_container->add_data( new Search_Ranking_Data( $ranking->getClicks(), $ranking->getCtr(), $ranking->getImpressions(), $ranking->getPosition(), $subject ) );
		}

		return $search_ranking_data_container;
	}

	/**
	 * Validates the response coming from Search Console.
	 *
	 * @param WP_REST_Response $response The response we want to validate.
	 *
	 * @return void.
	 *
	 * @throws Failed_Request_Exception      When the request responds with an error from Site Kit.
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 */
	private function validate_response( WP_REST_Response $response ): void {
		if ( $response->is_error() ) {
			$error_data        = $response->as_error()->get_error_data();
			$error_status_code = ( $error_data['status'] ?? 500 );
			throw new Failed_Request_Exception(
				\wp_kses_post(
					$response->as_error()
						->get_error_message()
				),
				(int) $error_status_code
			);
		}

		if ( ! \is_array( $response->get_data() ) ) {
			throw new Unexpected_Response_Exception();
		}
	}
}
