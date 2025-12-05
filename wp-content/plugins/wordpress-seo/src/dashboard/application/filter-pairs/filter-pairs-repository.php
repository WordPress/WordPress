<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Application\Filter_Pairs;

use Yoast\WP\SEO\Dashboard\Domain\Filter_Pairs\Filter_Pairs_Interface;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;
use Yoast\WP\SEO\Dashboard\Infrastructure\Taxonomies\Taxonomies_Collector;

/**
 * The repository to get hardcoded filter pairs.
 */
class Filter_Pairs_Repository {

	/**
	 * The taxonomies collector.
	 *
	 * @var Taxonomies_Collector
	 */
	private $taxonomies_collector;

	/**
	 * All filter pairs.
	 *
	 * @var Filter_Pairs_Interface[]
	 */
	private $filter_pairs;

	/**
	 * The constructor.
	 *
	 * @param Taxonomies_Collector   $taxonomies_collector The taxonomies collector.
	 * @param Filter_Pairs_Interface ...$filter_pairs      All filter pairs.
	 */
	public function __construct(
		Taxonomies_Collector $taxonomies_collector,
		Filter_Pairs_Interface ...$filter_pairs
	) {
		$this->taxonomies_collector = $taxonomies_collector;
		$this->filter_pairs         = $filter_pairs;
	}

	/**
	 * Returns a taxonomy based on a content type, by looking into hardcoded filter pairs.
	 *
	 * @param string $content_type The content type.
	 *
	 * @return Taxonomy|null The taxonomy filter.
	 */
	public function get_taxonomy( string $content_type ): ?Taxonomy {
		foreach ( $this->filter_pairs as $filter_pair ) {
			if ( $filter_pair->get_filtered_content_type() === $content_type ) {
				return $this->taxonomies_collector->get_taxonomy( $filter_pair->get_filtering_taxonomy(), $content_type );
			}
		}

		return null;
	}
}
