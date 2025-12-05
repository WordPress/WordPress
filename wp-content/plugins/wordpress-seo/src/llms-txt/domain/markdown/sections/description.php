<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections;

use Yoast\WP\SEO\Llms_Txt\Application\Markdown_Escaper;
/**
 * Represents the description section.
 */
class Description implements Section_Interface {

	/**
	 * The description.
	 *
	 * @var string
	 */
	private $description;

	/**
	 * Class constructor.
	 *
	 * @param string $description The description.
	 */
	public function __construct( string $description ) {
		$this->description = $description;
	}

	/**
	 * Returns the prefix of the description section.
	 *
	 * @return string
	 */
	public function get_prefix(): string {
		return '> ';
	}

	/**
	 * Renders the description section.
	 *
	 * @return string
	 */
	public function render(): string {
		return $this->description;
	}

	/**
	 * Escapes the markdown content.
	 *
	 * @param Markdown_Escaper $markdown_escaper The markdown escaper.
	 *
	 * @return void
	 */
	public function escape_markdown( Markdown_Escaper $markdown_escaper ): void {
		$this->description = $markdown_escaper->escape_markdown_content( $this->description );
	}
}
