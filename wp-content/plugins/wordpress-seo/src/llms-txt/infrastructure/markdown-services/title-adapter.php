<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services;

use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Sections\Title;
use Yoast\WP\SEO\Services\Health_Check\Default_Tagline_Runner;

/**
 * The adapter of the title.
 */
class Title_Adapter {

	/**
	 * The default tagline runner.
	 *
	 * @var Default_Tagline_Runner
	 */
	private $default_tagline_runner;

	/**
	 * Class constructor.
	 *
	 * @param Default_Tagline_Runner $default_tagline_runner The default tagline runner.
	 */
	public function __construct(
		Default_Tagline_Runner $default_tagline_runner
	) {
		$this->default_tagline_runner = $default_tagline_runner;
	}

	/**
	 * Gets the title.
	 *
	 * @return Title The title.
	 */
	public function get_title(): Title {
		$this->default_tagline_runner->run();
		$tagline = ( $this->default_tagline_runner->is_successful() ? \get_bloginfo( 'description' ) : '' );

		return new Title( \get_bloginfo( 'name' ), $tagline );
	}
}
