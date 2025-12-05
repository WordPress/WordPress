<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Search_Console;

use Yoast\WP\SEO\Dashboard\Domain\Data_Provider\Parameters;

/**
 * Domain object to add search console specific data to the parameters.
 */
class Search_Console_Parameters extends Parameters {

	/**
	 * The search dimensions to query.
	 *
	 * @var string[]
	 */
	private $dimensions;

	/**
	 * Sets the dimension parameter.
	 *
	 * @param array<string> $dimensions The dimensions.
	 *
	 * @return void
	 */
	public function set_dimensions( array $dimensions ): void {
		$this->dimensions = $dimensions;
	}

	/**
	 * Getter for the dimensions.
	 *
	 * @return string[]
	 */
	public function get_dimensions(): array {
		return $this->dimensions;
	}
}
