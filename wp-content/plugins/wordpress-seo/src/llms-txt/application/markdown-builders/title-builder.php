<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\Markdown_Builders;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections\Title;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services\Title_Adapter;


/**
 * The builder of the title section.
 */
class Title_Builder {

	/**
	 * The title adapter.
	 *
	 * @var Title_Adapter
	 */
	protected $title_adapter;

	/**
	 * The constructor.
	 *
	 * @param Title_Adapter $title_adapter The title adapter.
	 */
	public function __construct(
		Title_Adapter $title_adapter
	) {
		$this->title_adapter = $title_adapter;
	}

	/**
	 * Builds the title section.
	 *
	 * @return Title The title section.
	 */
	public function build_title(): Title {
		return $this->title_adapter->get_title();
	}
}
