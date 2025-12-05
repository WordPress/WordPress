<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Taxonomies;

/**
 * This class describes a Taxonomy.
 */
class Taxonomy {

	/**
	 * The name of the taxonomy.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The label of the taxonomy.
	 *
	 * @var string
	 */
	private $label;

	/**
	 * The REST URL of the taxonomy.
	 *
	 * @var string
	 */
	private $rest_url;

	/**
	 * The constructor.
	 *
	 * @param string $name     The name of the taxonomy.
	 * @param string $label    The label of the taxonomy.
	 * @param string $rest_url The REST URL of the taxonomy.
	 */
	public function __construct(
		string $name,
		string $label,
		string $rest_url
	) {
		$this->name     = $name;
		$this->label    = $label;
		$this->rest_url = $rest_url;
	}

	/**
	 * Returns the name of the taxonomy.
	 *
	 * @return string The name of the taxonomy.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Parses the taxonomy to the expected key value representation.
	 *
	 * @return array<string, array<string, string>> The taxonomy presented as the expected key value representation.
	 */
	public function to_array(): array {
		return [
			'name'  => $this->name,
			'label' => $this->label,
			'links' => [
				'search' => $this->rest_url,
			],
		];
	}
}
