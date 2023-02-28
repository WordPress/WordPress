<?php
/**
 * Sitemaps: WP_Sitemaps_Taxonomies class
 *
 * Builds the sitemaps for the 'taxonomy' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Taxonomies XML sitemap provider.
 *
 * @since 5.5.0
 */
class WP_Sitemaps_Taxonomies extends WP_Sitemaps_Provider {
	/**
	 * WP_Sitemaps_Taxonomies constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->name        = 'taxonomies';
		$this->object_type = 'term';
	}

	/**
	 * Returns all public, registered taxonomies.
	 *
	 * @since 5.5.0
	 *
	 * @return WP_Taxonomy[] Array of registered taxonomy objects keyed by their name.
	 */
	public function get_object_subtypes() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		$taxonomies = array_filter( $taxonomies, 'is_taxonomy_viewable' );

		/**
		 * Filters the list of taxonomy object subtypes available within the sitemap.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_Taxonomy[] $taxonomies Array of registered taxonomy objects keyed by their name.
		 */
		return apply_filters( 'wp_sitemaps_taxonomies', $taxonomies );
	}

	/**
	 * Gets a URL list for a taxonomy sitemap.
	 *
	 * @since 5.5.0
	 * @since 5.9.0 Renamed `$taxonomy` to `$object_subtype` to match parent class
	 *              for PHP 8 named parameter support.
	 *
	 * @param int    $page_num       Page of results.
	 * @param string $object_subtype Optional. Taxonomy name. Default empty.
	 * @return array[] Array of URL information for a sitemap.
	 */
	public function get_url_list( $page_num, $object_subtype = '' ) {
		// Restores the more descriptive, specific name for use within this method.
		$taxonomy        = $object_subtype;
		$supported_types = $this->get_object_subtypes();

		// Bail early if the queried taxonomy is not supported.
		if ( ! isset( $supported_types[ $taxonomy ] ) ) {
			return array();
		}

		/**
		 * Filters the taxonomies URL list before it is generated.
		 *
		 * Returning a non-null value will effectively short-circuit the generation,
		 * returning that value instead.
		 *
		 * @since 5.5.0
		 *
		 * @param array[]|null $url_list The URL list. Default null.
		 * @param string       $taxonomy Taxonomy name.
		 * @param int          $page_num Page of results.
		 */
		$url_list = apply_filters(
			'wp_sitemaps_taxonomies_pre_url_list',
			null,
			$taxonomy,
			$page_num
		);

		if ( null !== $url_list ) {
			return $url_list;
		}

		$url_list = array();

		// Offset by how many terms should be included in previous pages.
		$offset = ( $page_num - 1 ) * wp_sitemaps_get_max_urls( $this->object_type );

		$args           = $this->get_taxonomies_query_args( $taxonomy );
		$args['fields'] = 'all';
		$args['offset'] = $offset;

		$taxonomy_terms = new WP_Term_Query( $args );

		if ( ! empty( $taxonomy_terms->terms ) ) {
			foreach ( $taxonomy_terms->terms as $term ) {
				$term_link = get_term_link( $term, $taxonomy );

				if ( is_wp_error( $term_link ) ) {
					continue;
				}

				$sitemap_entry = array(
					'loc' => $term_link,
				);

				/**
				 * Filters the sitemap entry for an individual term.
				 *
				 * @since 5.5.0
				 * @since 6.0.0 Added `$term` argument containing the term object.
				 *
				 * @param array   $sitemap_entry Sitemap entry for the term.
				 * @param int     $term_id       Term ID.
				 * @param string  $taxonomy      Taxonomy name.
				 * @param WP_Term $term          Term object.
				 */
				$sitemap_entry = apply_filters( 'wp_sitemaps_taxonomies_entry', $sitemap_entry, $term->term_id, $taxonomy, $term );
				$url_list[]    = $sitemap_entry;
			}
		}

		return $url_list;
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 * @since 5.9.0 Renamed `$taxonomy` to `$object_subtype` to match parent class
	 *              for PHP 8 named parameter support.
	 *
	 * @param string $object_subtype Optional. Taxonomy name. Default empty.
	 * @return int Total number of pages.
	 */
	public function get_max_num_pages( $object_subtype = '' ) {
		if ( empty( $object_subtype ) ) {
			return 0;
		}

		// Restores the more descriptive, specific name for use within this method.
		$taxonomy = $object_subtype;

		/**
		 * Filters the max number of pages for a taxonomy sitemap before it is generated.
		 *
		 * Passing a non-null value will short-circuit the generation,
		 * returning that value instead.
		 *
		 * @since 5.5.0
		 *
		 * @param int|null $max_num_pages The maximum number of pages. Default null.
		 * @param string   $taxonomy      Taxonomy name.
		 */
		$max_num_pages = apply_filters( 'wp_sitemaps_taxonomies_pre_max_num_pages', null, $taxonomy );

		if ( null !== $max_num_pages ) {
			return $max_num_pages;
		}

		$term_count = wp_count_terms( $this->get_taxonomies_query_args( $taxonomy ) );

		return (int) ceil( $term_count / wp_sitemaps_get_max_urls( $this->object_type ) );
	}

	/**
	 * Returns the query args for retrieving taxonomy terms to list in the sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @return array Array of WP_Term_Query arguments.
	 */
	protected function get_taxonomies_query_args( $taxonomy ) {
		/**
		 * Filters the taxonomy terms query arguments.
		 *
		 * Allows modification of the taxonomy query arguments before querying.
		 *
		 * @see WP_Term_Query for a full list of arguments
		 *
		 * @since 5.5.0
		 *
		 * @param array  $args     Array of WP_Term_Query arguments.
		 * @param string $taxonomy Taxonomy name.
		 */
		$args = apply_filters(
			'wp_sitemaps_taxonomies_query_args',
			array(
				'taxonomy'               => $taxonomy,
				'orderby'                => 'term_order',
				'number'                 => wp_sitemaps_get_max_urls( $this->object_type ),
				'hide_empty'             => true,
				'hierarchical'           => false,
				'update_term_meta_cache' => false,
			),
			$taxonomy
		);

		return $args;
	}
}
