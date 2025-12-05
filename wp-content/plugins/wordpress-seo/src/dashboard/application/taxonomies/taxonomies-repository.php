<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Dashboard\Application\Taxonomies;

use Yoast\WP\SEO\Dashboard\Application\Filter_Pairs\Filter_Pairs_Repository;
use Yoast\WP\SEO\Dashboard\Domain\Taxonomies\Taxonomy;
use Yoast\WP\SEO\Dashboard\Infrastructure\Taxonomies\Taxonomies_Collector;

/**
 * The repository to get taxonomies.
 */
class Taxonomies_Repository {

	/**
	 * The taxonomies collector.
	 *
	 * @var Taxonomies_Collector
	 */
	private $taxonomies_collector;

	/**
	 * The filter pairs repository.
	 *
	 * @var Filter_Pairs_Repository
	 */
	private $filter_pairs_repository;

	/**
	 * The constructor.
	 *
	 * @param Taxonomies_Collector    $taxonomies_collector    The taxonomies collector.
	 * @param Filter_Pairs_Repository $filter_pairs_repository The filter pairs repository.
	 */
	public function __construct(
		Taxonomies_Collector $taxonomies_collector,
		Filter_Pairs_Repository $filter_pairs_repository
	) {
		$this->taxonomies_collector    = $taxonomies_collector;
		$this->filter_pairs_repository = $filter_pairs_repository;
	}

	/**
	 * Returns the object of the filtering taxonomy of a content type.
	 *
	 * @param string $content_type The content type that the taxonomy filters.
	 *
	 * @return Taxonomy|null The filtering taxonomy of the content type.
	 */
	public function get_content_type_taxonomy( string $content_type ) {
		// First we check if there's a filter that overrides the filtering taxonomy for this content type.
		$taxonomy = $this->taxonomies_collector->get_custom_filtering_taxonomy( $content_type );
		if ( $taxonomy ) {
			return $taxonomy;
		}

		// Then we check if there is a filter explicitly made for this content type.
		$taxonomy = $this->filter_pairs_repository->get_taxonomy( $content_type );
		if ( $taxonomy ) {
			return $taxonomy;
		}

		// If everything else returned empty, we can always try the fallback taxonomy.
		return $this->taxonomies_collector->get_fallback_taxonomy( $content_type );
	}
}
