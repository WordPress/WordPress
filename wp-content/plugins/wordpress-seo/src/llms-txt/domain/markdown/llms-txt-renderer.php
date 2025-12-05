<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Domain\Markdown;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections\Section_Interface;

/**
 * The renderer of the LLMs.txt file.
 */
class Llms_Txt_Renderer {

	/**
	 * The sections.
	 *
	 * @var Section_Interface[]
	 */
	private $sections;

	/**
	 * Adds a section.
	 *
	 * @param Section_Interface $section The section to add.
	 *
	 * @return void
	 */
	public function add_section( Section_Interface $section ): void {
		$this->sections[] = $section;
	}

	/**
	 * Returns the sections.
	 *
	 * @return Section_Interface[]
	 */
	public function get_sections(): array {
		return $this->sections;
	}

	/**
	 * Renders the items of the bucket.
	 *
	 * @return string
	 */
	public function render(): string {
		if ( empty( $this->sections ) ) {
			return '';
		}

		$rendered_sections = [];
		foreach ( $this->sections as $section ) {
			$section_content = $section->render();
			if ( $section_content === '' ) {
				continue;
			}

			$rendered_sections[] = $section->get_prefix() . $section_content . \PHP_EOL;
		}

		return \implode( \PHP_EOL, $rendered_sections );
	}
}
