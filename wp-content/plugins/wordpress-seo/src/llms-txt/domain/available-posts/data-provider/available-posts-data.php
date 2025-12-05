<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Available_Posts\Data_Provider;

use Yoast\WP\SEO\Llms_Txt\Domain\Content_Types\Content_Type_Entry;

/**
 * Domain object that represents a Available Posts data record.
 */
class Available_Posts_Data implements Data_Interface {

	/**
	 * The content type entry.
	 *
	 * @var Content_Type_Entry
	 */
	private $content_type_entry;

	/**
	 * The constructor.
	 *
	 * @param Content_Type_Entry $content_type_entry The content type entry.
	 */
	public function __construct( Content_Type_Entry $content_type_entry ) {
		$this->content_type_entry = $content_type_entry;
	}

	/**
	 * The array representation of this domain object.
	 *
	 * @return array<string|float|int|string[]>
	 */
	public function to_array(): array {
		return [
			'id'    => $this->content_type_entry->get_id(),
			'title' => $this->content_type_entry->get_title(),
			'slug'  => $this->content_type_entry->get_slug(),
		];
	}
}
