<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Sitemap provider for author archives.
 */
class WPSEO_Taxonomy_Sitemap_Provider implements WPSEO_Sitemap_Provider {

	/**
	 * Holds image parser instance.
	 *
	 * @var WPSEO_Sitemap_Image_Parser
	 */
	protected static $image_parser;

	/**
	 * Determines whether images should be included in the XML sitemap.
	 *
	 * @var bool
	 */
	private $include_images;

	/**
	 * Set up object properties for data reuse.
	 */
	public function __construct() {
		/**
		 * Filter - Allows excluding images from the XML sitemap.
		 *
		 * @param bool $include True to include, false to exclude.
		 */
		$this->include_images = apply_filters( 'wpseo_xml_sitemap_include_images', true );
	}

	/**
	 * Check if provider supports given item type.
	 *
	 * @param string $type Type string to check for.
	 *
	 * @return bool
	 */
	public function handles_type( $type ) {

		$taxonomy = get_taxonomy( $type );

		if ( $taxonomy === false || ! $this->is_valid_taxonomy( $taxonomy->name ) || ! $taxonomy->public ) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the links for the sitemap.
	 *
	 * @param int $max_entries Entries per sitemap.
	 *
	 * @return array
	 */
	public function get_index_links( $max_entries ) {

		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

		if ( empty( $taxonomies ) ) {
			return [];
		}

		$taxonomy_names = array_filter( array_keys( $taxonomies ), [ $this, 'is_valid_taxonomy' ] );
		$taxonomies     = array_intersect_key( $taxonomies, array_flip( $taxonomy_names ) );

		// Retrieve all the taxonomies and their terms so we can do a proper count on them.

		/**
		 * Filter the setting of excluding empty terms from the XML sitemap.
		 *
		 * @param bool  $exclude        Defaults to true.
		 * @param array $taxonomy_names Array of names for the taxonomies being processed.
		 */
		$hide_empty = apply_filters( 'wpseo_sitemap_exclude_empty_terms', true, $taxonomy_names );

		$all_taxonomies = [];

		foreach ( $taxonomy_names as $taxonomy_name ) {
			/**
			 * Filter the setting of excluding empty terms from the XML sitemap for a specific taxonomy.
			 *
			 * @param bool   $exclude       Defaults to the sitewide setting.
			 * @param string $taxonomy_name The name of the taxonomy being processed.
			 */
			$hide_empty_tax = apply_filters( 'wpseo_sitemap_exclude_empty_terms_taxonomy', $hide_empty, $taxonomy_name );

			$term_args      = [
				'taxonomy'   => $taxonomy_name,
				'hide_empty' => $hide_empty_tax,
				'fields'     => 'ids',
			];
			$taxonomy_terms = get_terms( $term_args );

			if ( count( $taxonomy_terms ) > 0 ) {
				$all_taxonomies[ $taxonomy_name ] = $taxonomy_terms;
			}
		}

		$index = [];

		foreach ( $taxonomies as $tax_name => $tax ) {

			if ( ! isset( $all_taxonomies[ $tax_name ] ) ) { // No eligible terms found.
				continue;
			}

			$total_count = ( isset( $all_taxonomies[ $tax_name ] ) ) ? count( $all_taxonomies[ $tax_name ] ) : 1;
			$max_pages   = 1;

			if ( $total_count > $max_entries ) {
				$max_pages = (int) ceil( $total_count / $max_entries );
			}

			$last_modified_gmt = WPSEO_Sitemaps::get_last_modified_gmt( $tax->object_type );

			for ( $page_counter = 0; $page_counter < $max_pages; $page_counter++ ) {

				$current_page = ( $page_counter === 0 ) ? '' : ( $page_counter + 1 );

				if ( ! is_array( $tax->object_type ) || count( $tax->object_type ) === 0 ) {
					continue;
				}

				$terms = array_splice( $all_taxonomies[ $tax_name ], 0, $max_entries );

				if ( ! $terms ) {
					continue;
				}

				$args  = [
					'post_type'      => $tax->object_type,
					'tax_query'      => [
						[
							'taxonomy' => $tax_name,
							'terms'    => $terms,
						],
					],
					'orderby'        => 'modified',
					'order'          => 'DESC',
					'posts_per_page' => 1,
				];
				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					$date = $query->posts[0]->post_modified_gmt;
				}
				else {
					$date = $last_modified_gmt;
				}

				$index[] = [
					'loc'     => WPSEO_Sitemaps_Router::get_base_url( $tax_name . '-sitemap' . $current_page . '.xml' ),
					'lastmod' => $date,
				];
			}
		}

		return $index;
	}

	/**
	 * Get set of sitemap link data.
	 *
	 * @param string $type         Sitemap type.
	 * @param int    $max_entries  Entries per sitemap.
	 * @param int    $current_page Current page of the sitemap.
	 *
	 * @return array
	 *
	 * @throws OutOfBoundsException When an invalid page is requested.
	 */
	public function get_sitemap_links( $type, $max_entries, $current_page ) {
		global $wpdb;

		$links = [];
		if ( ! $this->handles_type( $type ) ) {
			return $links;
		}

		$taxonomy = get_taxonomy( $type );

		$steps  = $max_entries;
		$offset = ( $current_page > 1 ) ? ( ( $current_page - 1 ) * $max_entries ) : 0;

		/** This filter is documented in inc/sitemaps/class-taxonomy-sitemap-provider.php */
		$hide_empty = apply_filters( 'wpseo_sitemap_exclude_empty_terms', true, [ $taxonomy->name ] );
		/** This filter is documented in inc/sitemaps/class-taxonomy-sitemap-provider.php */
		$hide_empty_tax = apply_filters( 'wpseo_sitemap_exclude_empty_terms_taxonomy', $hide_empty, $taxonomy->name );
		$terms          = get_terms(
			[
				'taxonomy'               => $taxonomy->name,
				'hide_empty'             => $hide_empty_tax,
				'update_term_meta_cache' => false,
				'offset'                 => $offset,
				'number'                 => $steps,
			]
		);

		// If there are no terms fetched for this range, we are on an invalid page.
		if ( empty( $terms ) ) {
			throw new OutOfBoundsException( 'Invalid sitemap page requested' );
		}

		$post_statuses = array_map( 'esc_sql', WPSEO_Sitemaps::get_post_statuses() );

		$replacements = array_merge(
			[
				'post_modified_gmt',
				$wpdb->posts,
				$wpdb->term_relationships,
				'object_id',
				'ID',
				$wpdb->term_taxonomy,
				'term_taxonomy_id',
				'term_taxonomy_id',
				'taxonomy',
				'term_id',
				'post_status',
			],
			$post_statuses,
			[ 'post_password' ]
		);

		/**
		 * Filter: 'wpseo_exclude_from_sitemap_by_term_ids' - Allow excluding terms by ID.
		 *
		 * @param array $terms_to_exclude The terms to exclude.
		 */
		$terms_to_exclude = apply_filters( 'wpseo_exclude_from_sitemap_by_term_ids', [] );

		foreach ( $terms as $term ) {

			if ( in_array( $term->term_id, $terms_to_exclude, true ) ) {
				continue;
			}

			$url = [];

			$tax_noindex = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'noindex' );

			if ( $tax_noindex === 'noindex' ) {
				continue;
			}

			$canonical  = WPSEO_Taxonomy_Meta::get_term_meta( $term, $term->taxonomy, 'canonical' );
			$url['loc'] = get_term_link( $term, $term->taxonomy );

			if ( is_string( $canonical ) && $canonical !== '' && $canonical !== $url['loc'] ) {
				continue;
			}

			$current_replacements = $replacements;
			array_splice( $current_replacements, 9, 0, $term->taxonomy );
			array_splice( $current_replacements, 11, 0, $term->term_id );

			//phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- We need to use a direct query here.
			//phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Reason: No relevant caches.
			$url['mod'] = $wpdb->get_var(
				//phpcs:disable WordPress.DB.PreparedSQLPlaceholders -- %i placeholder is still not recognized.
				$wpdb->prepare(
					'
			SELECT MAX(p.%i) AS lastmod
			FROM	%i AS p
			INNER JOIN %i AS term_rel
				ON		term_rel.%i = p.%i
			INNER JOIN %i AS term_tax
				ON		term_tax.%i = term_rel.%i
				AND		term_tax.%i = %s
				AND		term_tax.%i = %d
			WHERE	p.%i IN (' . implode( ', ', array_fill( 0, count( $post_statuses ), '%s' ) ) . ")
				AND		p.%i = ''
			",
					$current_replacements
				)
			);

			if ( $this->include_images ) {
				$url['images'] = $this->get_image_parser()->get_term_images( $term );
			}

			// Deprecated, kept for backwards data compat. R.
			$url['chf'] = 'daily';
			$url['pri'] = 1;

			/** This filter is documented at inc/sitemaps/class-post-type-sitemap-provider.php */
			$url = apply_filters( 'wpseo_sitemap_entry', $url, 'term', $term );

			if ( ! empty( $url ) ) {
				$links[] = $url;
			}
		}

		return $links;
	}

	/**
	 * Check if taxonomy by name is valid to appear in sitemaps.
	 *
	 * @param string $taxonomy_name Taxonomy name to check.
	 *
	 * @return bool
	 */
	public function is_valid_taxonomy( $taxonomy_name ) {

		if ( WPSEO_Options::get( "noindex-tax-{$taxonomy_name}" ) === true ) {
			return false;
		}

		if ( in_array( $taxonomy_name, [ 'link_category', 'nav_menu', 'wp_pattern_category' ], true ) ) {
			return false;
		}

		if ( $taxonomy_name === 'post_format' && WPSEO_Options::get( 'disable-post_format', false ) ) {
			return false;
		}

		/**
		 * Filter to exclude the taxonomy from the XML sitemap.
		 *
		 * @param bool   $exclude       Defaults to false.
		 * @param string $taxonomy_name Name of the taxonomy to exclude..
		 */
		if ( apply_filters( 'wpseo_sitemap_exclude_taxonomy', false, $taxonomy_name ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the Image Parser.
	 *
	 * @return WPSEO_Sitemap_Image_Parser
	 */
	protected function get_image_parser() {
		if ( ! isset( self::$image_parser ) ) {
			self::$image_parser = new WPSEO_Sitemap_Image_Parser();
		}

		return self::$image_parser;
	}
}
