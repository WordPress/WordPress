<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WP_Post;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Admin_Columns_Cache_Integration class.
 */
class Admin_Columns_Cache_Integration implements Integration_Interface {

	/**
	 * Cache of indexables.
	 *
	 * @var Indexable[]
	 */
	protected $indexable_cache = [];

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * In this case: only when on an admin page.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Admin_Columns_Cache_Integration constructor.
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 */
	public function __construct( Indexable_Repository $indexable_repository ) {
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Registers the appropriate actions and filters to fill the cache with
	 * indexables on admin pages.
	 *
	 * This cache is used in showing the Yoast SEO columns on the posts overview
	 * page (e.g. keyword score, incoming link count, etc.)
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Hook into tablenav to calculate links and linked.
		\add_action( 'manage_posts_extra_tablenav', [ $this, 'maybe_fill_cache' ] );
	}

	/**
	 * Makes sure we calculate all values in one query by filling our cache beforehand.
	 *
	 * @param string $target Extra table navigation location which is triggered.
	 *
	 * @return void
	 */
	public function maybe_fill_cache( $target ) {
		if ( $target === 'top' ) {
			$this->fill_cache();
		}
	}

	/**
	 * Fills the cache of indexables for all known post IDs.
	 *
	 * @return void
	 */
	public function fill_cache() {
		global $wp_query;

		// No need to continue building a cache if the main query did not return anything to cache.
		if ( empty( $wp_query->posts ) ) {
			return;
		}

		$posts    = $wp_query->posts;
		$post_ids = [];

		// Post lists return a list of objects.
		if ( isset( $posts[0] ) && \is_a( $posts[0], 'WP_Post' ) ) {
			$post_ids = \wp_list_pluck( $posts, 'ID' );
		}
		elseif ( isset( $posts[0] ) && \is_object( $posts[0] ) ) {
			$post_ids = $this->get_current_page_page_ids( $posts );
		}
		elseif ( ! empty( $posts ) ) {
			// Page list returns an array of post IDs.
			$post_ids = \array_keys( $posts );
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		if ( isset( $posts[0] ) && ! \is_a( $posts[0], WP_Post::class ) ) {
			// Prime the post caches as core would to avoid duplicate queries.
			// This needs to be done as this executes before core does.
			\_prime_post_caches( $post_ids );
		}

		$indexables = $this->indexable_repository->find_by_multiple_ids_and_type( $post_ids, 'post', false );

		foreach ( $indexables as $indexable ) {
			if ( $indexable instanceof Indexable ) {
				$this->indexable_cache[ $indexable->object_id ] = $indexable;
			}
		}
	}

	/**
	 * Returns the indexable for a given post ID.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return Indexable|false The indexable. False if none could be found.
	 */
	public function get_indexable( $post_id ) {
		if ( ! \array_key_exists( $post_id, $this->indexable_cache ) ) {
			$this->indexable_cache[ $post_id ] = $this->indexable_repository->find_by_id_and_type( $post_id, 'post' );
		}
		return $this->indexable_cache[ $post_id ];
	}

	/**
	 * Gets all the page IDs set to be shown on the current page.
	 * This is copied over with some changes from WP_Posts_List_Table::_display_rows_hierarchical.
	 *
	 * @param array $pages The pages, each containing an ID and post_parent.
	 *
	 * @return array The IDs of all pages shown on the current page.
	 */
	private function get_current_page_page_ids( $pages ) {
		global $per_page;
		$pagenum = isset( $_REQUEST['paged'] ) ? \absint( $_REQUEST['paged'] ) : 0;
		$pagenum = \max( 1, $pagenum );

		/*
		 * Arrange pages into two parts: top level pages and children_pages
		 * children_pages is two dimensional array, eg.
		 * children_pages[10][] contains all sub-pages whose parent is 10.
		 * It only takes O( N ) to arrange this and it takes O( 1 ) for subsequent lookup operations
		 * If searching, ignore hierarchy and treat everything as top level
		 */
		if ( empty( $_REQUEST['s'] ) ) {
			$top_level_pages = [];
			$children_pages  = [];
			$pages_map       = [];

			foreach ( $pages as $page ) {

				// Catch and repair bad pages.
				if ( $page->post_parent === $page->ID ) {
					$page->post_parent = 0;
				}

				if ( $page->post_parent === 0 ) {
					$top_level_pages[] = $page;
				}
				else {
					$children_pages[ $page->post_parent ][] = $page;
				}
				$pages_map[ $page->ID ] = $page;
			}

			$pages = $top_level_pages;
		}

		$count      = 0;
		$start      = ( ( $pagenum - 1 ) * $per_page );
		$end        = ( $start + $per_page );
		$to_display = [];

		foreach ( $pages as $page ) {
			if ( $count >= $end ) {
				break;
			}

			if ( $count >= $start ) {
				$to_display[] = $page->ID;
			}

			++$count;

			$this->get_child_page_ids( $children_pages, $count, $page->ID, $start, $end, $to_display, $pages_map );
		}

		// If it is the last pagenum and there are orphaned pages, display them with paging as well.
		if ( isset( $children_pages ) && $count < $end ) {
			foreach ( $children_pages as $orphans ) {
				foreach ( $orphans as $op ) {
					if ( $count >= $end ) {
						break;
					}

					if ( $count >= $start ) {
						$to_display[] = $op->ID;
					}

					++$count;
				}
			}
		}

		return $to_display;
	}

	/**
	 * Adds all child pages due to be shown on the current page to the $to_display array.
	 * Copied over with some changes from WP_Posts_List_Table::_page_rows.
	 *
	 * @param array $children_pages The full map of child pages.
	 * @param int   $count          The number of pages already processed.
	 * @param int   $parent_id      The id of the parent that's currently being processed.
	 * @param int   $start          The number at which the current overview starts.
	 * @param int   $end            The number at which the current overview ends.
	 * @param int   $to_display     The page IDs to be shown.
	 * @param int   $pages_map      A map of page ID to an object with ID and post_parent.
	 *
	 * @return void
	 */
	private function get_child_page_ids( &$children_pages, &$count, $parent_id, $start, $end, &$to_display, &$pages_map ) {
		if ( ! isset( $children_pages[ $parent_id ] ) ) {
			return;
		}

		foreach ( $children_pages[ $parent_id ] as $page ) {
			if ( $count >= $end ) {
				break;
			}

			// If the page starts in a subtree, print the parents.
			if ( $count === $start && $page->post_parent > 0 ) {
				$my_parents = [];
				$my_parent  = $page->post_parent;
				while ( $my_parent ) {
					// Get the ID from the list or the attribute if my_parent is an object.
					$parent_id = $my_parent;
					if ( \is_object( $my_parent ) ) {
						$parent_id = $my_parent->ID;
					}

					$my_parent    = $pages_map[ $parent_id ];
					$my_parents[] = $my_parent;
					if ( ! $my_parent->post_parent ) {
						break;
					}
					$my_parent = $my_parent->post_parent;
				}
				while ( $my_parent = \array_pop( $my_parents ) ) {
					$to_display[] = $my_parent->ID;
				}
			}

			if ( $count >= $start ) {
				$to_display[] = $page->ID;
			}

			++$count;

			$this->get_child_page_ids( $children_pages, $count, $page->ID, $start, $end, $to_display, $pages_map );
		}

		unset( $children_pages[ $parent_id ] ); // Required in order to keep track of orphans.
	}
}
