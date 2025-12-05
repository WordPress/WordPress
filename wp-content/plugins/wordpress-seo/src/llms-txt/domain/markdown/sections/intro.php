<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections;

use Yoast\WP\SEO\Llms_Txt\Application\Markdown_Escaper;
use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Items\Link;

/**
 * Represents the intro section.
 */
class Intro implements Section_Interface {

	/**
	 * The intro content.
	 *
	 * @var string
	 */
	private $intro_content;

	/**
	 * The intro links.
	 *
	 * @var Link[]
	 */
	private $intro_links = [];

	/**
	 * Class constructor.
	 *
	 * @param string $intro_content The intro content.
	 * @param Link[] $intro_links   The intro links.
	 */
	public function __construct( string $intro_content, array $intro_links ) {
		$this->intro_content = $intro_content;

		foreach ( $intro_links as $link ) {
			$this->add_link( $link );
		}
	}

	/**
	 * Returns the prefix of the intro section.
	 *
	 * @return string
	 */
	public function get_prefix(): string {
		return '';
	}

	/**
	 * Adds a link to the intro section.
	 *
	 * @param Link $link The link to add.
	 *
	 * @return void
	 */
	public function add_link( Link $link ): void {
		$this->intro_links[] = $link;
	}

	/**
	 * Returns the content of the intro section.
	 *
	 * @return string
	 */
	public function render(): string {
		if ( \count( $this->intro_links ) === 0 ) {
			return $this->intro_content;
		}

		$rendered_links = \array_map(
			static function ( $link ) {
				return $link->render();
			},
			$this->intro_links
		);

		$this->intro_content = \sprintf(
			$this->intro_content,
			...$rendered_links
		);
		return $this->intro_content;
	}

	/**
	 * Escapes the markdown content.
	 *
	 * @param Markdown_Escaper $markdown_escaper The markdown escaper.
	 *
	 * @return void
	 */
	public function escape_markdown( Markdown_Escaper $markdown_escaper ): void {
		foreach ( $this->intro_links as $link ) {
			$link->escape_markdown( $markdown_escaper );
		}
	}
}
