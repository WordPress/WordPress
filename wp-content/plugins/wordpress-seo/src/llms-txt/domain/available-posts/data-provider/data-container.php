<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Available_Posts\Data_Provider;

/**
 * The data container.
 */
class Data_Container {

	/**
	 * All the data points.
	 *
	 * @var array<Data_Interface>
	 */
	private $data_container;

	/**
	 * The constructor
	 */
	public function __construct() {
		$this->data_container = [];
	}

	/**
	 * Method to add data.
	 *
	 * @param Data_Interface $data The data.
	 *
	 * @return void
	 */
	public function add_data( Data_Interface $data ) {
		$this->data_container[] = $data;
	}

	/**
	 * Method to get all the data points.
	 *
	 * @return Data_Interface[] All the data points.
	 */
	public function get_data(): array {
		return $this->data_container;
	}

	/**
	 * Converts the data points into an array.
	 *
	 * @return array<string, string> The array of the data points.
	 */
	public function to_array(): array {
		$result = [];
		foreach ( $this->data_container as $data ) {
			$result[] = $data->to_array();
		}

		return $result;
	}
}
