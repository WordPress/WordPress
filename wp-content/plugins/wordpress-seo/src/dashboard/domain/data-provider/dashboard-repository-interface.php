<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Data_Provider;

/**
 * Interface describing the way to get data for a specific data provider.
 */
interface Dashboard_Repository_Interface {

	/**
	 * Method to get dashboard related data from a provider.
	 *
	 * @param Parameters $parameters The parameter to get the dashboard data for.
	 *
	 * @return Data_Container
	 */
	public function get_data( Parameters $parameters ): Data_Container;
}
