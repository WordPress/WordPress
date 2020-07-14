<?php
/**
 * Sitemaps: WP_Sitemaps_Posts class
 *
 * Builds the sitemaps for the 'post' object type.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Posts XML sitemap provider.
 *
 * @since 5.5.0
 */
class WP_Sitemaps_Posts extends WP_Sitemaps_Provider {
	/**
	 * WP_Sitemaps_Posts constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$this->name        = 'posts';
		$this->object_type = 'post';
	}

	/**
	 * Returns the public post types, which excludes nav_items and similar types.
	 * Attachments are also excluded. This includes custom post types with public = true.
	 *
	 * @since 5.5.0
	 *
	 * @return WP_Post_Type[] Array of registered post type objects keyed by their name.
	 */
	public function get_object_subtypes() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );

		$post_types = array_filter( $post_types, 'is_post_type_viewable' );

		/**
		 * Filters the list of post object sub types available within the sitemap.
		 *
		 * @since 5.5.0
		 *
		 * @param WP_Post_Type[] $post_types Array of registered post type objects keyed by their name.
		 */
		return apply_filters( 'wp_sitemaps_post_types', $post_types );
	}

	/**
	 * Gets a URL list for a post type sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param int    $page_num  Page of results.
	 * @param string $post_type Optional. Post type name. Default empty.
	 * @return array Array of URLs for a sitemap.
	 */
	public function get_url_list( $page_num, $post_type = '' ) {
		// Bail early if the queried post type is not supported.
		$supported_types = $this->get_object_subtypes();

		if ( ! isset( $supported_types[ $post_type ] ) ) {
			return array();
		}

		/**
		 * Filters the posts URL list before it is generated.
		 *
		 * Passing a non-null value will effectively short-circuit the generation,
		 * returning that value instead.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $url_list  The URL list. Default null.
		 * @param string $post_type Post type name.
		 * @param int    $page_num  Page of results.
		 */
		$url_list = apply_filters(
			'wp_sitemaps_posts_pre_url_list',
			null,
			$post_type,
			$page_num
		);

		if ( null !== $url_list ) {
			return $url_list;
		}

		$args          = $this->get_posts_query_args( $post_type );
		$args['paged'] = $page_num;

		$query = new WP_Query( $args );

		$url_list = array();

		/*
		 * Add a URL for the homepage in the pages sitemap.
		 * Shows only on the first page if the reading settings are set to display latest posts.
		 */
		if ( 'page' === $post_type && 1 === $page_num && 'posts' === get_option( 'show_on_front' ) ) {
			// Extract the data needed for home URL to add to the array.
			$sitemap_entry = array(
				'loc' => home_url( '/' ),
			);

			/**
			 * Filters the sitemap entry for the home page when the 'show_on_front' option equals 'posts'.
			 *
			 * @since 5.5.0
			 *
			 * @param array $sitemap_entry Sitemap entry for the home page.
			 */
			$sitemap_entry = apply_filters( 'wp_sitemaps_posts_show_on_front_entry', $sitemap_entry );
			$url_list[]    = $sitemap_entry;
		}

		foreach ( $query->posts as $post ) {
			$sitemap_entry = array(
				'loc' => get_permalink( $post ),
			);

			/**
			 * Filters the sitemap entry for an individual post.
			 *
			 * @since 5.5.0
			 *
			 * @param array   $sitemap_entry Sitemap entry for the post.
			 * @param WP_Post $post          Post object.
			 * @param string  $post_type     Name of the post_type.
			 */
			$sitemap_entry = apply_filters( 'wp_sitemaps_posts_entry', $sitemap_entry, $post, $post_type );
			$url_list[]    = $sitemap_entry;
		}

		return $url_list;
	}

	/**
	 * Gets the max number of pages available for the object type.
	 *
	 * @since 5.5.0
	 *
	 * @param string $post_type Optional. Post type name. Default empty.
	 * @return int Total number of pages.
	 */
	public function get_max_num_pages( $post_type = '' ) {
		if ( empty( $post_type ) ) {
			return 0;
		}

		/**
		 * Filters the max number of pages before it is generated.
		 *
		 * Passing a non-null value will short-circuit the generation,
		 * returning that value instead.
		 *
		 * @since 5.5.0
		 *
		 * @param int|null $max_num_pages The maximum number of pages. Default null.
		 * @param string   $post_type     Post type name.
		 */
		$max_num_pages = apply_filters( 'wp_sitemaps_posts_pre_max_num_pages', null, $post_type );

		if ( null !== $max_num_pages ) {
			return $max_num_pages;
		}

		$args                  = $this->get_posts_query_args( $post_type );
		$args['fields']        = 'ids';
		$args['no_found_rows'] = false;

		$query = new WP_Query( $args );

		$min_num_pages = ( 'page' === $post_type && 'posts' === get_option( 'show_on_front' ) ) ? 1 : 0;
		return isset( $query->max_num_pages ) ? max( $min_num_pages, $query->max_num_pages ) : 1;
	}

	/**
	 * Returns the query args for retrieving posts to list in the sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param string $post_type Post type name.
	 * @return array Array of WP_Query arguments.
	 */
	protected function get_posts_query_args( $post_type ) {
		/**
		 * Filters the query arguments for post type sitemap queries.
		 *
		 * @see WP_Query for a full list of arguments.
		 *
		 * @since 5.5.0
		 *
		 * @param array  $args      Array of WP_Query arguments.
		 * @param string $post_type Post type name.
		 */
		$args = apply_filters(
			'wp_sitemaps_posts_query_args',
			array(
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'post_type'              => $post_type,
				'posts_per_page'         => wp_sitemaps_get_max_urls( $this->object_type ),
				'post_status'            => array( 'publish' ),
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			),
			$post_type
		);

		return $args;
	}
}
