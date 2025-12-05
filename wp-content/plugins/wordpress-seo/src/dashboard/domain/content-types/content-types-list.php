<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Domain\Content_Types;

/**
 * This class describes a list of content types.
 */
class Content_Types_List {

	/**
	 * The content types.
	 *
	 * @var array<Content_Type>
	 */
	private $content_types = [];

	/**
	 * Adds a content type to the list.
	 *
	 * @param Content_Type $content_type The content type to add.
	 *
	 * @return void
	 */
	public function add( Content_Type $content_type ): void {
		$this->content_types[ $content_type->get_name() ] = $content_type;
	}

	/**
	 * Returns the content types in the list.
	 *
	 * @return array<Content_Type> The content types in the list.
	 */
	public function get(): array {
		return $this->content_types;
	}

	/**
	 * Parses the content type list to the expected key value representation.
	 *
	 * @return array<array<string, array<string, array<string, array<string, string|null>>>>> The content type list presented as the expected key value representation.
	 */
	public function to_array(): array {
		$array = [];
		foreach ( $this->content_types as $content_type ) {
			$array[] = [
				'name'     => $content_type->get_name(),
				'label'    => $content_type->get_label(),
				'taxonomy' => ( $content_type->get_taxonomy() ) ? $content_type->get_taxonomy()->to_array() : null,
			];
		}

		return $array;
	}
}
