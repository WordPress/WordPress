<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\Content;

use WP_Post;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Llms_Txt\Domain\Content\Post_Collection_Interface;
use Yoast\WP\SEO\Llms_Txt\Domain\Content_Types\Content_Type_Entry;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Surfaces\Meta_Surface;

/**
 * The class that handles the automatic post collection. Based on either indexables or WP_Query.
 *
 * @makePublic
 */
class Automatic_Post_Collection implements Post_Collection_Interface {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options_helper;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The meta surface.
	 *
	 * @var Meta_Surface
	 */
	private $meta;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * Constructs the class.
	 *
	 * @param Options_Helper       $options_helper       The options helper.
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 * @param Meta_Surface         $meta                 The meta surface.
	 * @param Indexable_Helper     $indexable_helper     The indexable helper.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Indexable_Repository $indexable_repository,
		Meta_Surface $meta,
		Indexable_Helper $indexable_helper
	) {
		$this->options_helper       = $options_helper;
		$this->indexable_repository = $indexable_repository;
		$this->meta                 = $meta;
		$this->indexable_helper     = $indexable_helper;
	}

	/**
	 * Gets the posts that are relevant for the LLMs.txt.
	 *
	 * @param string $post_type The post type.
	 * @param int    $limit     The maximum number of posts to return.
	 *
	 * @return array<int, array<Content_Type_Entry>> The posts that are relevant for the LLMs.txt.
	 */
	public function get_posts( string $post_type, int $limit ): array {
		$posts = $this->get_recent_cornerstone_content( $post_type, $limit );

		if ( \count( $posts ) >= $limit ) {
			return $posts;
		}

		$recent_posts = $this->get_recent_posts( $post_type, $limit );
		foreach ( $recent_posts as $recent_post ) {
			// If the post is already in the list because it's cornerstone, don't add it again.
			if ( isset( $posts[ $recent_post->get_id() ] ) ) {
				continue;
			}

			$posts[ $recent_post->get_id() ] = $recent_post;

			if ( \count( $posts ) >= $limit ) {
				break;
			}
		}

		return $posts;
	}

	/**
	 * Gets the most recently modified cornerstone content.
	 *
	 * @param string $post_type The post type.
	 * @param int    $limit     The maximum number of posts to return.
	 *
	 * @return array<int, array<Content_Type_Entry>> The most recently modified cornerstone content.
	 */
	private function get_recent_cornerstone_content( string $post_type, int $limit ): array {
		if ( ! $this->options_helper->get( 'enable_cornerstone_content' ) ) {
			return [];
		}

		$cornerstone_limit = ( \is_post_type_hierarchical( $post_type ) ) ? null : $limit;
		$cornerstones      = $this->indexable_repository->get_recent_cornerstone_for_post_type( $post_type, $cornerstone_limit );

		$recent_cornerstone_posts = [];
		foreach ( $cornerstones as $cornerstone ) {
			$cornerstone_meta = $this->meta->for_indexable( $cornerstone );
			if ( $cornerstone_meta->post instanceof WP_Post ) {
				$recent_cornerstone_posts[ $cornerstone_meta->post->ID ] = Content_Type_Entry::from_meta( $cornerstone_meta );
			}
		}

		return $recent_cornerstone_posts;
	}

	/**
	 * Gets the most recently modified posts.
	 *
	 * @param string $post_type                   The post type.
	 * @param int    $limit                       The maximum number of posts to return.
	 * @param string $search_filter               Optional. The search filter to apply to the query.
	 * @param bool   $disable_excluding_old_posts Optional. Whether to disable excluding posts older than one year.
	 *
	 * @return array<Content_Type_Entry> The most recently modified posts.
	 */
	public function get_recent_posts( string $post_type, int $limit, string $search_filter = '', bool $disable_excluding_old_posts = false ): array {
		$exclude_older_than_one_year = false;

		if ( $post_type === 'post' && ! $disable_excluding_old_posts ) {
			$exclude_older_than_one_year = true;
		}

		if ( $this->indexable_helper->should_index_indexables() ) {
			return $this->get_recently_modified_posts_indexables( $post_type, $limit, $exclude_older_than_one_year, $search_filter );
		}

		return $this->get_recently_modified_posts_wp_query( $post_type, $limit, $exclude_older_than_one_year, $search_filter );
	}

	/**
	 * Returns most recently modified posts of a post type, using indexables.
	 *
	 * @param string $post_type                   The post type.
	 * @param int    $limit                       The maximum number of posts to return.
	 * @param bool   $exclude_older_than_one_year Whether to exclude posts older than one year.
	 * @param string $search_filter               Optional. The search filter to apply to the query.
	 *
	 * @return array<Content_Type_Entry> The most recently modified posts.
	 */
	private function get_recently_modified_posts_indexables( string $post_type, int $limit, bool $exclude_older_than_one_year, string $search_filter = '' ): array {
		$posts                        = [];
		$recently_modified_indexables = $this->indexable_repository->get_recently_modified_posts( $post_type, $limit, $exclude_older_than_one_year, $search_filter );

		foreach ( $recently_modified_indexables as $indexable ) {
			$indexable_meta = $this->meta->for_indexable( $indexable );
			if ( $indexable_meta->post instanceof WP_Post ) {
				$posts[] = Content_Type_Entry::from_meta( $indexable_meta );
			}
		}

		return $posts;
	}

	/**
	 * Returns most recently modified posts of a post type, using WP_Query.
	 *
	 * @param string $post_type                   The post type.
	 * @param int    $limit                       The maximum number of posts to return.
	 * @param bool   $exclude_older_than_one_year Whether to exclude posts older than one year.
	 * @param string $search_filter               Optional. The search filter to apply to the query.
	 *
	 * @return array<WP_Post> The most recently modified posts.
	 */
	private function get_recently_modified_posts_wp_query( string $post_type, int $limit, bool $exclude_older_than_one_year, string $search_filter = '' ): array {
		$args = [
			'post_type'      => $post_type,
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'has_password'   => false,
		];

		if ( $exclude_older_than_one_year === true ) {
			$args['date_query'] = [
				[
					'after' => '12 months ago',
				],
			];
		}

		if ( $search_filter !== '' ) {
			$args['s'] = $search_filter;
		}

		$posts = [];
		foreach ( \get_posts( $args ) as $post ) {
			$posts[] = Content_Type_Entry::from_post( $post, \get_permalink( $post->ID ) );
		}

		return $posts;
	}
}
