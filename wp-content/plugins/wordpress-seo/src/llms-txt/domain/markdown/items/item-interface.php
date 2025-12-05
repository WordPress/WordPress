<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Items;

use Yoast\WP\SEO\Llms_Txt\Application\Markdown_Escaper;
/**
 * Represents a markdown item.
 */
interface Item_Interface {

	/**
	 * Renders the item.
	 *
	 * @return string
	 */
	public function render(): string;

	/**
	 * Escapes the markdown content.
	 *
	 * @param Markdown_Escaper $markdown_escaper The markdown escaper.
	 *
	 * @return void
	 */
	public function escape_markdown( Markdown_Escaper $markdown_escaper ): void;
}
