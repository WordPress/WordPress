<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Available_Posts\Data_Provider;

/**
 * The interface to describe the data domain.
 */
interface Data_Interface {

	/**
	 * A to array method.
	 *
	 * @return array<string>
	 */
	public function to_array(): array;
}
