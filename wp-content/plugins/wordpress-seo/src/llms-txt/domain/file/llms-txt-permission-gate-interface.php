<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Domain\File;

/**
 * This interface is responsible for defining ways to make sure we can edit/regenerate the llms.txt file.
 */
interface Llms_Txt_Permission_Gate_Interface {

	/**
	 * Checks if Yoast SEO manages the llms.txt.
	 *
	 * @return bool Checks if Yoast SEO manages the llms.txt.
	 */
	public function is_managed_by_yoast_seo(): bool;
}
