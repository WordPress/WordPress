<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded
namespace Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections;

use Yoast\WP\SEO\Llms_Txt\Application\Markdown_Escaper;

/**
 * Represents the title section.
 */
class Title implements Section_Interface {

	/**
	 * The site title.
	 *
	 * @var string
	 */
	private $site_title;

	/**
	 * The site tagline.
	 *
	 * @var string
	 */
	private $site_tagline;

	/**
	 * Class constructor.
	 *
	 * @param string $site_title   The site title.
	 * @param string $site_tagline The site tagline.
	 */
	public function __construct(
		string $site_title,
		string $site_tagline
	) {
		$this->site_title   = $site_title;
		$this->site_tagline = $site_tagline;
	}

	/**
	 * Returns the prefix of the section.
	 *
	 * @return string
	 */
	public function get_prefix(): string {
		return '# ';
	}

	/**
	 * Renders the title section.
	 *
	 * @return string
	 */
	public function render(): string {
		if ( $this->site_tagline === '' ) {
			return $this->site_title;
		}

		if ( $this->site_title === '' ) {
			return $this->site_tagline;
		}

		return "$this->site_title: $this->site_tagline";
	}

	/**
	 * Escapes the markdown content.
	 *
	 * @param Markdown_Escaper $markdown_escaper The markdown escaper.
	 *
	 * @return void
	 */
	public function escape_markdown( Markdown_Escaper $markdown_escaper ): void {
		$this->site_title   = $markdown_escaper->escape_markdown_content( $this->site_title );
		$this->site_tagline = $markdown_escaper->escape_markdown_content( $this->site_tagline );
	}
}
