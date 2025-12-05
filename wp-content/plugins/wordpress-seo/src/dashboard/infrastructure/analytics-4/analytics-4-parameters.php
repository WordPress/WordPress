<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Analytics_4;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Parameters;

/**
 * Domain object to add Analytics 4 specific data to the parameters.
 */
class Analytics_4_Parameters extends Parameters {

	/**
	 * The dimensions to query.
	 *
	 * @var array<array<string, string>> $dimensions
	 */
	private $dimensions = [];

	/**
	 * The dimensions filters.
	 *
	 * @var array<string, array<string>> $dimension_filters
	 */
	private $dimension_filters = [];

	/**
	 * The metrics.
	 *
	 * @var array<array<string, string>> $metrics
	 */
	private $metrics = [];

	/**
	 * The order by.
	 *
	 * @var array<array<string, array<string, string>>> $order_by
	 */
	private $order_by = [];

	/**
	 * Sets the dimensions.
	 *
	 * @link https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/Dimension
	 *
	 * @param array<string> $dimensions The dimensions.
	 *
	 * @return void
	 */
	public function set_dimensions( array $dimensions ): void {
		foreach ( $dimensions as $dimension ) {
			$this->dimensions[] = [ 'name' => $dimension ];
		}
	}

	/**
	 * Getter for the dimensions.
	 *
	 * @return array<array<string, string>>
	 */
	public function get_dimensions(): array {
		return $this->dimensions;
	}

	/**
	 * Sets the dimension filters.
	 *
	 * @param array<string, array<string>> $dimension_filters The dimension filters.
	 *
	 * @return void
	 */
	public function set_dimension_filters( array $dimension_filters ): void {
		$this->dimension_filters = $dimension_filters;
	}

	/**
	 * Getter for the dimension filters.
	 *
	 * @return array<string, array<string>>
	 */
	public function get_dimension_filters(): array {
		return $this->dimension_filters;
	}

	/**
	 * Sets the metrics.
	 *
	 * @link https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/Metric
	 *
	 * @param array<string> $metrics The metrics.
	 *
	 * @return void
	 */
	public function set_metrics( array $metrics ): void {
		foreach ( $metrics as $metric ) {
			$this->metrics[] = [ 'name' => $metric ];
		}
	}

	/**
	 * Getter for the metrics.
	 *
	 * @return array<array<string, string>>
	 */
	public function get_metrics(): array {
		return $this->metrics;
	}

	/**
	 * Sets the order by.
	 *
	 * @link https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/OrderBy
	 *
	 * @param string $key  The key to order by.
	 * @param string $name The name to order by.
	 *
	 * @return void
	 */
	public function set_order_by( string $key, string $name ): void {
		$order_by = [
			[
				$key => [
					$key . 'Name' => $name,
				],
			],
		];

		$this->order_by = $order_by;
	}

	/**
	 * Getter for the order by.
	 *
	 * @return array<array<string, array<string, string>>>
	 */
	public function get_order_by(): array {
		return $this->order_by;
	}
}
