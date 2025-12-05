<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Application\Markdown_Builders;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections\Description;
use Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services\Description_Adapter;

/**
 * The builder of the description section.
 */
class Description_Builder {

	/**
	 * The description adapter.
	 *
	 * @var Description_Adapter
	 */
	protected $description_adapter;

	/**
	 * Class constructor.
	 *
	 * @param Description_Adapter $description_adapter The description adapter.
	 */
	public function __construct(
		Description_Adapter $description_adapter
	) {
		$this->description_adapter = $description_adapter;
	}

	/**
	 * Builds the description section.
	 *
	 * @return Description The description section.
	 */
	public function build_description(): Description {
		return $this->description_adapter->get_description();
	}
}
