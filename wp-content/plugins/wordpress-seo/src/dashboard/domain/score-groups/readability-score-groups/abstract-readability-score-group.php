<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Readability_Score_Groups;

use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Abstract_Score_Group;

/**
 * Abstract class for a readability score group.
 */
abstract class Abstract_Readability_Score_Group extends Abstract_Score_Group implements Readability_Score_Groups_Interface {

	/**
	 * Gets the key of the readability score group that is used when filtering on the posts page.
	 *
	 * @return string The name of the readability score group that is used when filtering on the posts page.
	 */
	public function get_filter_key(): string {
		return 'readability_filter';
	}
}
