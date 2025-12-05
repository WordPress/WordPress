<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

/**
 * Sitemap provider for author archives.
 */
class WPSEO_Author_Sitemap_Provider implements WPSEO_Sitemap_Provider {

	/**
	 * Check if provider supports given item type.
	 *
	 * @param string $type Type string to check for.
	 *
	 * @return bool
	 */
	public function handles_type( $type ) {
		// If the author archives have been disabled, we don't do anything.
		if ( WPSEO_Options::get( 'disable-author', false ) || WPSEO_Options::get( 'noindex-author-wpseo', false ) ) {
			return false;
		}

		return $type === 'author';
	}

	/**
	 * Get the links for the sitemap index.
	 *
	 * @param int $max_entries Entries per sitemap.
	 *
	 * @return array
	 */
	public function get_index_links( $max_entries ) {

		if ( ! $this->handles_type( 'author' ) ) {
			return [];
		}

		// @todo Consider doing this less often / when necessary. R.
		$this->update_user_meta();

		$has_exclude_filter = has_filter( 'wpseo_sitemap_exclude_author' );

		$query_arguments = [];

		if ( ! $has_exclude_filter ) { // We only need full users if legacy filter(s) hooked to exclusion logic. R.
			$query_arguments['fields'] = 'ID';
		}

		$users = $this->get_users( $query_arguments );

		if ( $has_exclude_filter ) {
			$users = $this->exclude_users( $users );
			$users = wp_list_pluck( $users, 'ID' );
		}

		if ( empty( $users ) ) {
			return [];
		}

		$index      = [];
		$user_pages = array_chunk( $users, $max_entries );

		foreach ( $user_pages as $page_counter => $users_page ) {

			$current_page = ( $page_counter === 0 ) ? '' : ( $page_counter + 1 );

			$user_id = array_shift( $users_page ); // Time descending, first user on page is most recently updated.
			$user    = get_user_by( 'id', $user_id );
			$index[] = [
				'loc'     => WPSEO_Sitemaps_Router::get_base_url( 'author-sitemap' . $current_page . '.xml' ),
				'lastmod' => ( $user->_yoast_wpseo_profile_updated ) ? YoastSEO()->helpers->date->format_timestamp( $user->_yoast_wpseo_profile_updated ) : null,
			];
		}

		return $index;
	}

	/**
	 * Retrieve users, taking account of all necessary exclusions.
	 *
	 * @param array $arguments Arguments to add.
	 *
	 * @return array
	 */
	protected function get_users( $arguments = [] ) {

		global $wpdb;

		$defaults = [
			'capability' => [ 'edit_posts' ],
			'meta_key'   => '_yoast_wpseo_profile_updated',
			'orderby'    => 'meta_value_num',
			'order'      => 'DESC',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'     => $wpdb->get_blog_prefix() . 'user_level',
					'value'   => '0',
					'compare' => '!=',
				],
				[
					'relation' => 'OR',
					[
						'key'     => 'wpseo_noindex_author',
						'value'   => 'on',
						'compare' => '!=',
					],
					[
						'key'     => 'wpseo_noindex_author',
						'compare' => 'NOT EXISTS',
					],
				],
			],
		];

		if ( WPSEO_Options::get( 'noindex-author-noposts-wpseo', true ) ) {
			unset( $defaults['capability'] ); // Otherwise it cancels out next argument.
			$defaults['has_published_posts'] = YoastSEO()->helpers->author_archive->get_author_archive_post_types();
		}

		return get_users( array_merge( $defaults, $arguments ) );
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

		$links = [];

		if ( ! $this->handles_type( 'author' ) ) {
			return $links;
		}

		$user_criteria = [
			'offset' => ( ( $current_page - 1 ) * $max_entries ),
			'number' => $max_entries,
		];

		$users = $this->get_users( $user_criteria );

		// Throw an exception when there are no users in the sitemap.
		if ( count( $users ) === 0 ) {
			throw new OutOfBoundsException( 'Invalid sitemap page requested' );
		}

		$users = $this->exclude_users( $users );
		if ( empty( $users ) ) {
			$users = [];
		}

		$time = time();

		foreach ( $users as $user ) {

			$author_link = get_author_posts_url( $user->ID );

			if ( empty( $author_link ) ) {
				continue;
			}

			$mod = $time;

			if ( isset( $user->_yoast_wpseo_profile_updated ) ) {
				$mod = $user->_yoast_wpseo_profile_updated;
			}

			$url = [
				'loc' => $author_link,
				'mod' => date( DATE_W3C, $mod ),

				// Deprecated, kept for backwards data compat. R.
				'chf' => 'daily',
				'pri' => 1,
			];

			/** This filter is documented at inc/sitemaps/class-post-type-sitemap-provider.php */
			$url = apply_filters( 'wpseo_sitemap_entry', $url, 'user', $user );

			if ( ! empty( $url ) ) {
				$links[] = $url;
			}
		}

		return $links;
	}

	/**
	 * Update any users that don't have last profile update timestamp.
	 *
	 * @return int Count of users updated.
	 */
	protected function update_user_meta() {

		$user_criteria = [
			'capability' => [ 'edit_posts' ],
			'meta_query' => [
				[
					'key'     => '_yoast_wpseo_profile_updated',
					'compare' => 'NOT EXISTS',
				],
			],
		];

		$users = get_users( $user_criteria );

		$time = time();

		foreach ( $users as $user ) {
			update_user_meta( $user->ID, '_yoast_wpseo_profile_updated', $time );
		}

		return count( $users );
	}

	/**
	 * Wrap legacy filter to deduplicate calls.
	 *
	 * @param array $users Array of user objects to filter.
	 *
	 * @return array
	 */
	protected function exclude_users( $users ) {

		/**
		 * Filter the authors, included in XML sitemap.
		 *
		 * @param array $users Array of user objects to filter.
		 */
		return apply_filters( 'wpseo_sitemap_exclude_author', $users );
	}
}
