<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Available_Posts\Data_Provider;

/**
 * Interface describing the way to get data for a specific data provider.
 */
interface Available_Posts_Repository_Interface {

	/**
	 * Method to get available posts from a provider.
	 *
	 * @param Parameters $parameters The parameter to get the available posts for.
	 *
	 * @return Data_Container
	 */
	public function get_posts( Parameters $parameters ): Data_Container;
}
