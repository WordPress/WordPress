<?php

namespace Yoast\WP\SEO\Helpers;

use WP_Query;
use Yoast\WP\Lib\Model;

/**
 * A helper object for author archives.
 */
class Author_Archive_Helper {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type_helper;

	/**
	 * Creates a new author archive helper.
	 *
	 * @param Options_Helper   $options_helper   The options helper.
	 * @param Post_Type_Helper $post_type_helper The post type helper.
	 */
	public function __construct( Options_Helper $options_helper, Post_Type_Helper $post_type_helper ) {
		$this->options_helper   = $options_helper;
		$this->post_type_helper = $post_type_helper;
	}

	/**
	 * Gets the array of post types that are shown on an author's archive.
	 *
	 * @return array The post types that are shown on an author's archive.
	 */
	public function get_author_archive_post_types() {
		/**
		 * Filters the array of post types that are shown on an author's archive.
		 *
		 * @param array $args The post types that are shown on an author archive.
		 */
		return \apply_filters( 'wpseo_author_archive_post_types', [ 'post' ] );
	}

	/**
	 * Returns whether the author has at least one public post.
	 *
	 * @param int $author_id The author ID.
	 *
	 * @return bool|null Whether the author has at least one public post.
	 */
	public function author_has_public_posts( $author_id ) {
		// First check if the author has at least one public post.
		$has_public_post = $this->author_has_a_public_post( $author_id );
		if ( $has_public_post ) {
			return true;
		}

		// Then check if the author has at least one post where the status is the same as the global setting.
		$has_public_post_depending_on_the_global_setting = $this->author_has_a_post_with_is_public_null( $author_id );
		if ( $has_public_post_depending_on_the_global_setting ) {
			return null;
		}

		return false;
	}

	/**
	 * Returns whether the author has at least one public post.
	 *
	 * **Note**: It uses WP_Query to determine the number of posts,
	 * not the indexables table.
	 *
	 * @param string $user_id The user ID.
	 *
	 * @return bool Whether the author has at least one public post.
	 */
	public function author_has_public_posts_wp( $user_id ) {
		$post_types        = \array_intersect( $this->get_author_archive_post_types(), $this->post_type_helper->get_indexable_post_types() );
		$public_post_stati = \array_values( \array_filter( \get_post_stati(), 'is_post_status_viewable' ) );

		$args = [
			'post_type'              => $post_types,
			'post_status'            => $public_post_stati,
			'author'                 => $user_id,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'no_found_rows'          => true,
			'fields'                 => 'ids',
			'posts_per_page'         => 1,
		];

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks whether author archives are disabled.
	 *
	 * @return bool `true` if author archives are disabled, `false` if not.
	 */
	public function are_disabled() {
		return $this->options_helper->get( 'disable-author' );
	}

	/**
	 * Returns whether the author has at least one public post.
	 *
	 * @codeCoverageIgnore It looks for the first ID through the ORM and converts it to a boolean.
	 *
	 * @param int $author_id The author ID.
	 *
	 * @return bool Whether the author has at least one public post.
	 */
	protected function author_has_a_public_post( $author_id ) {
		$cache_key        = 'author_has_a_public_post_' . $author_id;
		$indexable_exists = \wp_cache_get( $cache_key );

		if ( $indexable_exists === false ) {
			$indexable_exists = Model::of_type( 'Indexable' )
				->select( 'id' )
				->where( 'object_type', 'post' )
				->where_in( 'object_sub_type', $this->get_author_archive_post_types() )
				->where( 'author_id', $author_id )
				->where( 'is_public', 1 )
				->find_one();

			if ( $indexable_exists === false ) {
				// Cache no results to prevent full table scanning on authors with no public posts.
				\wp_cache_set( $cache_key, 0, '', \wp_rand( ( 2 * \HOUR_IN_SECONDS ), ( 4 * \HOUR_IN_SECONDS ) ) );
			}
		}

		return (bool) $indexable_exists;
	}

	/**
	 * Returns whether the author has at least one post with the is public null.
	 *
	 * @codeCoverageIgnore It looks for the first ID through the ORM and converts it to a boolean.
	 *
	 * @param int $author_id The author ID.
	 *
	 * @return bool Whether the author has at least one post with the is public null.
	 */
	protected function author_has_a_post_with_is_public_null( $author_id ) {
		$cache_key        = 'author_has_a_post_with_is_public_null_' . $author_id;
		$indexable_exists = \wp_cache_get( $cache_key );

		if ( $indexable_exists === false ) {
			$indexable_exists = Model::of_type( 'Indexable' )
				->select( 'id' )
				->where( 'object_type', 'post' )
				->where_in( 'object_sub_type', $this->get_author_archive_post_types() )
				->where( 'author_id', $author_id )
				->where_null( 'is_public' )
				->find_one();

			if ( $indexable_exists === false ) {
				// Cache no results to prevent full table scanning on authors with no is public null posts.
				\wp_cache_set( $cache_key, 0, '', \wp_rand( ( 2 * \HOUR_IN_SECONDS ), ( 4 * \HOUR_IN_SECONDS ) ) );
			}
		}

		return (bool) $indexable_exists;
	}
}
