<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Domain\Seo;

/**
 * This class describes the title SEO data.
 */
class Title implements Seo_Plugin_Data_Interface {

	/**
	 * The title template.
	 *
	 * @var string
	 */
	private $title_template;

	/**
	 * The title template without the fallback.
	 *
	 * @var string
	 */
	private $title_template_no_fallback;

	/**
	 * The constructor.
	 *
	 * @param string $title_template             The title template.
	 * @param string $title_template_no_fallback The title template without the fallback.
	 */
	public function __construct( string $title_template, string $title_template_no_fallback ) {
		$this->title_template             = $title_template;
		$this->title_template_no_fallback = $title_template_no_fallback;
	}

	/**
	 * Returns the data as an array format.
	 *
	 * @return array<string>
	 */
	public function to_array(): array {
		return [
			'title_template'             => $this->title_template,
			'title_template_no_fallback' => $this->title_template_no_fallback,
		];
	}

	/**
	 * Returns the data as an array format meant for legacy use.
	 *
	 * @return array<string>
	 */
	public function to_legacy_array(): array {
		return [
			'title_template'             => $this->title_template,
			'title_template_no_fallback' => $this->title_template_no_fallback,
		];
	}
}
