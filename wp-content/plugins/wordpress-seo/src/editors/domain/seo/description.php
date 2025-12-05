<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Editors\Domain\Seo;

/**
 * This class describes the description SEO data.
 */
class Description implements Seo_Plugin_Data_Interface {

	/**
	 * The formatted description date.
	 *
	 * @var string
	 */
	private $description_date;

	/**
	 * The description template.
	 *
	 * @var string
	 */
	private $description_template;

	/**
	 * The constructor.
	 *
	 * @param string $description_date     The description date.
	 * @param string $description_template The description template.
	 */
	public function __construct( string $description_date, string $description_template ) {
		$this->description_date     = $description_date;
		$this->description_template = $description_template;
	}

	/**
	 * Returns the data as an array format.
	 *
	 * @return array<string>
	 */
	public function to_array(): array {
		return [
			'description_template' => $this->description_template,
			'description_date'     => $this->description_date,
		];
	}

	/**
	 * Returns the data as an array format meant for legacy use.
	 *
	 * @return array<string>
	 */
	public function to_legacy_array(): array {
		return [
			'metadesc_template'   => $this->description_template,
			'metaDescriptionDate' => $this->description_date,
		];
	}
}
