<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Traffic;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Interface;

/**
 * Domain object that represents a single Comparison Traffic record.
 */
class Comparison_Traffic_Data implements Data_Interface {

	public const CURRENT_PERIOD_KEY  = 'current';
	public const PREVIOUS_PERIOD_KEY = 'previous';

	/**
	 * The current traffic data.
	 *
	 * @var Traffic_Data
	 */
	private $current_traffic_data;

	/**
	 * The previous traffic data.
	 *
	 * @var Traffic_Data
	 */
	private $previous_traffic_data;

	/**
	 * The constructor.
	 *
	 * @param Traffic_Data $current_traffic_data  The current traffic data.
	 * @param Traffic_Data $previous_traffic_data The previous traffic data.
	 */
	public function __construct( ?Traffic_Data $current_traffic_data = null, ?Traffic_Data $previous_traffic_data = null ) {
		$this->current_traffic_data  = $current_traffic_data;
		$this->previous_traffic_data = $previous_traffic_data;
	}

	/**
	 * Sets the current traffic data.
	 *
	 * @param Traffic_Data $current_traffic_data The current traffic data.
	 *
	 * @return void
	 */
	public function set_current_traffic_data( Traffic_Data $current_traffic_data ): void {
		$this->current_traffic_data = $current_traffic_data;
	}

	/**
	 * Sets the previous traffic data.
	 *
	 * @param Traffic_Data $previous_traffic_data The previous traffic data.
	 *
	 * @return void
	 */
	public function set_previous_traffic_data( Traffic_Data $previous_traffic_data ): void {
		$this->previous_traffic_data = $previous_traffic_data;
	}

	/**
	 * The array representation of this domain object.
	 *
	 * @return array<string|float|int|string[]>
	 */
	public function to_array(): array {
		return [
			'current'  => $this->current_traffic_data->to_array(),
			'previous' => $this->previous_traffic_data->to_array(),
		];
	}
}
