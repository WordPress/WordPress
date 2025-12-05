<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Content_Types;

use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;

/**
 * This class describes a Content Type.
 */
class Content_Type {

	/**
	 * The name of the content type.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * The label of the content type.
	 *
	 * @var string
	 */
	private $label;

	/**
	 * The taxonomy that filters the content type.
	 *
	 * @var Taxonomy
	 */
	private $taxonomy;

	/**
	 * The constructor.
	 *
	 * @param string        $name     The name of the content type.
	 * @param string        $label    The label of the content type.
	 * @param Taxonomy|null $taxonomy The taxonomy that filters the content type.
	 */
	public function __construct( string $name, string $label, ?Taxonomy $taxonomy = null ) {
		$this->name     = $name;
		$this->label    = $label;
		$this->taxonomy = $taxonomy;
	}

	/**
	 * Gets name of the content type.
	 *
	 * @return string The name of the content type.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets label of the content type.
	 *
	 * @return string The label of the content type.
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Gets the taxonomy that filters the content type.
	 *
	 * @return Taxonomy|null The taxonomy that filters the content type.
	 */
	public function get_taxonomy(): ?Taxonomy {
		return $this->taxonomy;
	}

	/**
	 * Sets the taxonomy that filters the content type.
	 *
	 * @param Taxonomy|null $taxonomy The taxonomy that filters the content type.
	 *
	 * @return void
	 */
	public function set_taxonomy( ?Taxonomy $taxonomy ): void {
		$this->taxonomy = $taxonomy;
	}
}
