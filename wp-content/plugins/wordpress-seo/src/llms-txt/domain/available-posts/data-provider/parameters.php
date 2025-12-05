<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
// phpcs:disable Yoast.NamingConventions.NamespaceName.MaxExceeded -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Domain\Available_Posts\Data_Provider;

/**
 * Object representation of the request parameters.
 */
class Parameters {

	/**
	 * The post type.
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * The search filter.
	 *
	 * @var string
	 */
	private $search_filter;

	/**
	 * Class constructor.
	 *
	 * @param string $post_type     The post type.
	 * @param string $search_filter The search filter.
	 */
	public function __construct( string $post_type, string $search_filter ) {
		$this->post_type     = $post_type;
		$this->search_filter = $search_filter;
	}

	/**
	 * Getter for the post type.
	 *
	 * @return string
	 */
	public function get_post_type(): string {
		return $this->post_type;
	}

	/**
	 * Getter for the search filter.
	 *
	 * @return string
	 */
	public function get_search_filter(): string {
		return $this->search_filter;
	}
}
