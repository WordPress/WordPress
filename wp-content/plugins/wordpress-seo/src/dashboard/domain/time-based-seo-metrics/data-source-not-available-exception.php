<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Time_Based_Seo_Metrics;

use Exception;

/**
 * Exception for when the integration is not yet onboarded.
 */
class Data_Source_Not_Available_Exception extends Exception {

	/**
	 * Constructor of the exception.
	 *
	 * @param string $data_source_name The name of the data source that is not found.
	 */
	public function __construct( $data_source_name ) {
		parent::__construct( "$data_source_name is not available yet. Not all prerequisites have been met.", 400 );
	}
}
