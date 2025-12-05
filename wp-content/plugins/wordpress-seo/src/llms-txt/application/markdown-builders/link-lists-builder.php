<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\Markdown_Builders;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections\Link_List;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services\Content_Types_Collector;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services\Terms_Collector;


/**
 * The builder of the link list sections.
 */
class Link_Lists_Builder {

	/**
	 * The content types collector.
	 *
	 * @var Content_Types_Collector
	 */
	private $content_types_collector;

	/**
	 * The terms collector.
	 *
	 * @var Terms_Collector
	 */
	private $terms_collector;

	/**
	 * Constructs the class.
	 *
	 * @param Content_Types_Collector $content_types_collector The content types collector.
	 * @param Terms_Collector         $terms_collector         The terms collector.
	 */
	public function __construct(
		Content_Types_Collector $content_types_collector,
		Terms_Collector $terms_collector
	) {
		$this->content_types_collector = $content_types_collector;
		$this->terms_collector         = $terms_collector;
	}

	/**
	 * Builds the link list sections.
	 *
	 * @return Link_List[] The link list sections.
	 */
	public function build_link_lists(): array {
		return \array_merge(
			$this->content_types_collector->get_content_types_lists(),
			$this->terms_collector->get_terms_lists()
		);
	}
}
