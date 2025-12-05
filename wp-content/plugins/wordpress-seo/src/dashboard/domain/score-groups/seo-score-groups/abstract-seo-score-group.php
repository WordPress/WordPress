<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Dashboard\Domain\Score_Groups\SEO_Score_Groups;

use Yoast\WP\SEO\Dashboard\Domain\Score_Groups\Abstract_Score_Group;

/**
 * Abstract class for an SEO score group.
 */
abstract class Abstract_SEO_Score_Group extends Abstract_Score_Group implements SEO_Score_Groups_Interface {

	/**
	 * Gets the key of the SEO score group that is used when filtering on the posts page.
	 *
	 * @return string The name of the SEO score group that is used when filtering on the posts page.
	 */
	public function get_filter_key(): string {
		return 'seo_filter';
	}
}
