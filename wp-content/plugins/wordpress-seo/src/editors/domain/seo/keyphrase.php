<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Domain\Seo;

/**
 * This class describes the keyphrase SEO data.
 */
class Keyphrase implements Seo_Plugin_Data_Interface {

	/**
	 * The keyphrase and the associated posts that use it.
	 *
	 * @var array<string>
	 */
	private $keyphrase_usage_count;

	/**
	 * The post types for the given post IDs.
	 *
	 * @var array<string>
	 */
	private $keyphrase_usage_per_type;

	/**
	 * The constructor.
	 *
	 * @param array<string> $keyphrase_usage_count    The keyphrase and the associated posts that use it.
	 * @param array<string> $keyphrase_usage_per_type The post types for the given post IDs.
	 */
	public function __construct( array $keyphrase_usage_count, array $keyphrase_usage_per_type ) {
		$this->keyphrase_usage_count    = $keyphrase_usage_count;
		$this->keyphrase_usage_per_type = $keyphrase_usage_per_type;
	}

	/**
	 * Returns the data as an array format.
	 *
	 * @return array<string>
	 */
	public function to_array(): array {
		return [
			'keyphrase_usage'          => $this->keyphrase_usage_count,
			'keyphrase_usage_per_type' => $this->keyphrase_usage_per_type,
		];
	}

	/**
	 * Returns the data as an array format meant for legacy use.
	 *
	 * @return array<string>
	 */
	public function to_legacy_array(): array {
		return [
			'keyword_usage'            => $this->keyphrase_usage_count,
			'keyword_usage_post_types' => $this->keyphrase_usage_per_type,
		];
	}
}
