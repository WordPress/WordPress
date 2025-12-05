<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Analytics_4;

use Google\Site_Kit_Dependencies\Google\Service\AnalyticsData\Row;
use Google\Site_Kit_Dependencies\Google\Service\AnalyticsData\RunReportResponse;
use WP_REST_Response;
use Yoast\WP\SEO\Dashboard\Domain\Analytics_4\Failed_Request_Exception;
use Yoast\WP\SEO\Dashboard\Domain\Analytics_4\Invalid_Request_Exception;
use Yoast\WP\SEO\Dashboard\Domain\Analytics_4\Unexpected_Response_Exception;
use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Container;
use Yoast\WP\SEO\Dashboard\Domain\Traffic\Comparison_Traffic_Data;
use Yoast\WP\SEO\Dashboard\Domain\Traffic\Daily_Traffic_Data;
use Yoast\WP\SEO\Dashboard\Domain\Traffic\Traffic_Data;

/**
 * The site API adapter to make calls to the Analytics 4 API, via the Site_Kit plugin.
 */
class Site_Kit_Analytics_4_Adapter {

	/**
	 * Holds the api call class.
	 *
	 * @var Site_Kit_Analytics_4_Api_Call $site_kit_analytics_4_api_call
	 */
	private $site_kit_search_console_api_call;

	/**
	 * The register method that sets the instance in the adapter.
	 *
	 * @param Site_Kit_Analytics_4_Api_Call $site_kit_analytics_4_api_call The api call class.
	 *
	 * @return void
	 */
	public function __construct( Site_Kit_Analytics_4_Api_Call $site_kit_analytics_4_api_call ) {
		$this->site_kit_search_console_api_call = $site_kit_analytics_4_api_call;
	}

	/**
	 * The wrapper method to do a comparison Site Kit API request for Analytics.
	 *
	 * @param Analytics_4_Parameters $parameters The parameters.
	 *
	 * @return Data_Container The Site Kit API response.
	 *
	 * @throws Failed_Request_Exception      When the request responds with an error from Site Kit.
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 * @throws Invalid_Request_Exception     When the request is invalid due to unexpected parameters.
	 */
	public function get_comparison_data( Analytics_4_Parameters $parameters ): Data_Container {
		$api_parameters = $this->build_parameters( $parameters );

		$response = $this->site_kit_search_console_api_call->do_request( $api_parameters );

		$this->validate_response( $response );

		return $this->parse_comparison_response( $response->get_data() );
	}

	/**
	 * The wrapper method to do a daily Site Kit API request for Analytics.
	 *
	 * @param Analytics_4_Parameters $parameters The parameters.
	 *
	 * @return Data_Container The Site Kit API response.
	 *
	 * @throws Failed_Request_Exception      When the request responds with an error from Site Kit.
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 * @throws Invalid_Request_Exception     When the request is invalid due to unexpected parameters.
	 */
	public function get_daily_data( Analytics_4_Parameters $parameters ): Data_Container {
		$api_parameters = $this->build_parameters( $parameters );

		$response = $this->site_kit_search_console_api_call->do_request( $api_parameters );

		$this->validate_response( $response );

		return $this->parse_daily_response( $response->get_data() );
	}

	/**
	 * Builds the parameters to be used in the Site Kit API request.
	 *
	 * @param Analytics_4_Parameters $parameters The parameters.
	 *
	 * @return array<string, array<string, string>> The Site Kit API parameters.
	 */
	private function build_parameters( Analytics_4_Parameters $parameters ): array {
		$api_parameters = [
			'slug'       => 'analytics-4',
			'datapoint'  => 'report',
			'startDate'  => $parameters->get_start_date(),
			'endDate'    => $parameters->get_end_date(),
		];

		if ( ! empty( $parameters->get_dimension_filters() ) ) {
			$api_parameters['dimensionFilters'] = $parameters->get_dimension_filters();
		}

		if ( ! empty( $parameters->get_dimensions() ) ) {
			$api_parameters['dimensions'] = $parameters->get_dimensions();
		}

		if ( ! empty( $parameters->get_metrics() ) ) {
			$api_parameters['metrics'] = $parameters->get_metrics();
		}

		if ( ! empty( $parameters->get_order_by() ) ) {
			$api_parameters['orderby'] = $parameters->get_order_by();
		}

		if ( ! empty( $parameters->get_compare_start_date() && ! empty( $parameters->get_compare_end_date() ) ) ) {
			$api_parameters['compareStartDate'] = $parameters->get_compare_start_date();
			$api_parameters['compareEndDate']   = $parameters->get_compare_end_date();
		}

		return $api_parameters;
	}

	/**
	 * Parses a response for a Site Kit API request that requests daily data for Analytics 4.
	 *
	 * @param RunReportResponse $response The response to parse.
	 *
	 * @return Data_Container The parsed response.
	 *
	 * @throws Invalid_Request_Exception When the request is invalid due to unexpected parameters.
	 */
	private function parse_daily_response( RunReportResponse $response ): Data_Container {
		if ( ! $this->is_daily_request( $response ) ) {
			throw new Invalid_Request_Exception( 'Unexpected parameters for the request' );
		}

		$data_container = new Data_Container();

		foreach ( $response->getRows() as $daily_traffic ) {
			$traffic_data = new Traffic_Data();

			foreach ( $response->getMetricHeaders() as $key => $metric ) {

				// As per https://developers.google.com/analytics/devguides/reporting/data/v1/basics#read_the_response,
				// the order of the columns is consistent in the request, header, and rows.
				// So we can use the key of the header to get the correct metric value from the row.
				$metric_value = $daily_traffic->getMetricValues()[ $key ]->getValue();

				if ( $metric->getName() === 'sessions' ) {
					$traffic_data->set_sessions( (int) $metric_value );
				}
				elseif ( $metric->getName() === 'totalUsers' ) {
					$traffic_data->set_total_users( (int) $metric_value );
				}
			}

			// Since we're here, we know that the first dimension is date, so we know that dimensionValues[0]->value is a date.
			$data_container->add_data( new Daily_Traffic_Data( $daily_traffic->getDimensionValues()[0]->getValue(), $traffic_data ) );
		}

		return $data_container;
	}

	/**
	 * Parses a response for a Site Kit API request for Analytics 4 that compares data ranges.
	 *
	 * @param RunReportResponse $response The response to parse.
	 *
	 * @return Data_Container The parsed response.
	 *
	 * @throws Invalid_Request_Exception When the request is invalid due to unexpected parameters.
	 */
	private function parse_comparison_response( RunReportResponse $response ): Data_Container {
		if ( ! $this->is_comparison_request( $response ) ) {
			throw new Invalid_Request_Exception( 'Unexpected parameters for the request' );
		}

		$data_container          = new Data_Container();
		$comparison_traffic_data = new Comparison_Traffic_Data();

		// First row is the current date range's data, second row is the previous date range's data.
		foreach ( $response->getRows() as $date_range_row ) {
			$traffic_data = new Traffic_Data();

			// Loop through all the metrics of the date range.
			foreach ( $response->getMetricHeaders() as $key => $metric ) {

				// As per https://developers.google.com/analytics/devguides/reporting/data/v1/basics#read_the_response,
				// the order of the columns is consistent in the request, header, and rows.
				// So we can use the key of the header to get the correct metric value from the row.
				$metric_value = $date_range_row->getMetricValues()[ $key ]->getValue();

				if ( $metric->getName() === 'sessions' ) {
					$traffic_data->set_sessions( (int) $metric_value );
				}
				elseif ( $metric->getName() === 'totalUsers' ) {
					$traffic_data->set_total_users( (int) $metric_value );
				}
			}

			$period = $this->get_period( $date_range_row );

			if ( $period === Comparison_Traffic_Data::CURRENT_PERIOD_KEY ) {
				$comparison_traffic_data->set_current_traffic_data( $traffic_data );
			}
			elseif ( $period === Comparison_Traffic_Data::PREVIOUS_PERIOD_KEY ) {
				$comparison_traffic_data->set_previous_traffic_data( $traffic_data );
			}
		}

		$data_container->add_data( $comparison_traffic_data );

		return $data_container;
	}

	/**
	 * Parses the response row and returns whether it's about the current period or the previous period.
	 *
	 * @see https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/DateRange
	 *
	 * @param Row $date_range_row The response row.
	 *
	 * @return string The key associated with the current or the previous period.
	 *
	 * @throws Invalid_Request_Exception When the request is invalid due to unexpected parameters.
	 */
	private function get_period( Row $date_range_row ): string {
		foreach ( $date_range_row->getDimensionValues() as $dimension_value ) {
			if ( $dimension_value->getValue() === 'date_range_0' ) {
				return Comparison_Traffic_Data::CURRENT_PERIOD_KEY;
			}
			elseif ( $dimension_value->getValue() === 'date_range_1' ) {
				return Comparison_Traffic_Data::PREVIOUS_PERIOD_KEY;
			}
		}

		throw new Invalid_Request_Exception( 'Unexpected date range names' );
	}

	/**
	 * Checks the response of the request to detect if it's a comparison request.
	 *
	 * @param RunReportResponse $response The response.
	 *
	 * @return bool Whether it's a comparison request.
	 */
	private function is_comparison_request( RunReportResponse $response ): bool {
		return \count( $response->getDimensionHeaders() ) === 1 && $response->getDimensionHeaders()[0]->getName() === 'dateRange';
	}

	/**
	 * Checks the response of the request to detect if it's a daily request.
	 *
	 * @param RunReportResponse $response The response.
	 *
	 * @return bool Whether it's a daily request.
	 */
	private function is_daily_request( RunReportResponse $response ): bool {
		return \count( $response->getDimensionHeaders() ) === 1 && $response->getDimensionHeaders()[0]->getName() === 'date';
	}

	/**
	 * Validates the response coming from Google Analytics.
	 *
	 * @param WP_REST_Response $response The response we want to validate.
	 *
	 * @return void
	 *
	 * @throws Failed_Request_Exception      When the request responds with an error from Site Kit.
	 * @throws Unexpected_Response_Exception When the request responds with an unexpected format.
	 */
	private function validate_response( WP_REST_Response $response ): void {
		if ( $response->is_error() ) {
			$error_data        = $response->as_error()->get_error_data();
			$error_status_code = ( $error_data['status'] ?? 500 );
			throw new Failed_Request_Exception( \wp_kses_post( $response->as_error()->get_error_message() ), (int) $error_status_code );
		}

		if ( ! \is_a( $response->get_data(), RunReportResponse::class ) ) {
			throw new Unexpected_Response_Exception();
		}
	}
}
