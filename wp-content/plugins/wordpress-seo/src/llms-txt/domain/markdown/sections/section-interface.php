<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Items\Item_Interface;

/**
 * Represents a section.
 */
interface Section_Interface extends Item_Interface {

	/**
	 * Returns the prefix of the section.
	 *
	 * @return string
	 */
	public function get_prefix(): string;
}
