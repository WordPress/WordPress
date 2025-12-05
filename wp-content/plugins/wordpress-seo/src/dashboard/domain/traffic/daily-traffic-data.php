<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Traffic;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Data_Interface;

/**
 * Domain object that represents a single Daily Traffic record.
 */
class Daily_Traffic_Data implements Data_Interface {

	/**
	 * The date of the traffic data, in YYYYMMDD format.
	 *
	 * @var string
	 */
	private $date;

	/**
	 * The traffic data for the date.
	 *
	 * @var Traffic_Data
	 */
	private $traffic_data;

	/**
	 * The constructor.
	 *
	 * @param string       $date         The date of the traffic data, in YYYYMMDD format.
	 * @param Traffic_Data $traffic_data The traffic data for the date.
	 */
	public function __construct( string $date, Traffic_Data $traffic_data ) {
		$this->date         = $date;
		$this->traffic_data = $traffic_data;
	}

	/**
	 * The array representation of this domain object.
	 *
	 * @return array<string, string|int>
	 */
	public function to_array(): array {
		$result         = [];
		$result['date'] = $this->date;

		return \array_merge( $result, $this->traffic_data->to_array() );
	}
}
