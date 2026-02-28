<?php

/**
 * Modifies the Post controller endpoint to support orderby_hierarchy.
 *
 * @package gutenberg
 * @since   6.8.0
 */

class Gutenberg_Hierarchical_Sort {
	private static $post_ids = array();
	private static $levels   = array();
	private static $instance;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function run( $args ) {
		$new_args = array_merge(
			$args,
			array(
				'fields'         => 'id=>parent',
				'posts_per_page' => -1,
			)
		);
		$query    = new WP_Query( $new_args );
		$posts    = $query->posts;
		$result   = self::sort( $posts );

		self::$post_ids = $result['post_ids'];
		self::$levels   = $result['levels'];
	}

	/**
	 * Check if the request is eligible for hierarchical sorting.
	 *
	 * @param array $request The request data.
	 *
	 * @return bool Return true if the request is eligible for hierarchical sorting.
	 */
	public static function is_eligible( $request ) {
		if ( ! isset( $request['orderby_hierarchy'] ) || true !== $request['orderby_hierarchy'] ) {
			return false;
		}

		return true;
	}

	public static function get_ancestor( $post_id ) {
		return get_post( $post_id )->post_parent ?? 0;
	}

	/**
	 * Sort posts by hierarchy.
	 *
	 * Takes an array of posts and sorts them based on their parent-child relationships.
	 * It also tracks the level depth of each post in the hierarchy.
	 *
	 * Example input:
	 * ```
	 * [
	 *   ['ID' => 4, 'post_parent' => 2],
	 *   ['ID' => 2, 'post_parent' => 0],
	 *   ['ID' => 3, 'post_parent' => 2],
	 * ]
	 * ```
	 *
	 * Example output:
	 * ```
	 * [
	 *   'post_ids' => [2, 4, 3],
	 *   'levels'   => [0, 1, 1]
	 * ]
	 * ```
	 *
	 * @param array $posts Array of post objects containing ID and post_parent properties.
	 *
	 * @return array {
	 *     Sorted post IDs and their hierarchical levels
	 *
	 *     @type array $post_ids Array of post IDs
	 *     @type array $levels   Array of levels for the corresponding post ID in the same index
	 * }
	 */
	public static function sort( $posts ) {
		/*
		 * Arrange pages in two arrays:
		 *
		 * - $top_level: posts whose parent is 0
		 * - $children: post ID as the key and an array of children post IDs as the value.
		 *   Example: $children[10][] contains all sub-pages whose parent is 10.
		 *
		 * Additionally, keep track of the levels of each post in $levels.
		 * Example: $levels[10] = 0 means the post ID is a top-level page.
		 *
		 */
		$top_level = array();
		$children  = array();
		foreach ( $posts as $post ) {
			if ( empty( $post->post_parent ) ) {
				$top_level[] = $post->ID;
			} else {
				$children[ $post->post_parent ][] = $post->ID;
			}
		}

		$ids    = array();
		$levels = array();
		self::add_hierarchical_ids( $ids, $levels, 0, $top_level, $children );

		// Process remaining children.
		if ( ! empty( $children ) ) {
			foreach ( $children as $parent_id => $child_ids ) {
				$level    = 0;
				$ancestor = $parent_id;
				while ( 0 !== $ancestor ) {
					++$level;
					$ancestor = self::get_ancestor( $ancestor );
				}
				self::add_hierarchical_ids( $ids, $levels, $level, $child_ids, $children );
			}
		}

		return array(
			'post_ids' => $ids,
			'levels'   => $levels,
		);
	}

	private static function add_hierarchical_ids( &$ids, &$levels, $level, $to_process, $children ) {
		foreach ( $to_process as $id ) {
			if ( in_array( $id, $ids, true ) ) {
				continue;
			}
			$ids[]         = $id;
			$levels[ $id ] = $level;

			if ( isset( $children[ $id ] ) ) {
				self::add_hierarchical_ids( $ids, $levels, $level + 1, $children[ $id ], $children );
				unset( $children[ $id ] );
			}
		}
	}

	public static function get_post_ids() {
		return self::$post_ids;
	}

	public static function get_levels() {
		return self::$levels;
	}
}

add_filter(
	'rest_page_collection_params',
	function ( $params ) {
		$params['orderby_hierarchy'] = array(
			'description' => 'Sort pages by hierarchy.',
			'type'        => 'boolean',
			'default'     => false,
		);
		return $params;
	}
);

add_filter(
	'rest_page_query',
	function ( $args, $request ) {
		if ( ! Gutenberg_Hierarchical_Sort::is_eligible( $request ) ) {
			return $args;
		}

		$hs = Gutenberg_Hierarchical_Sort::get_instance();
		$hs->run( $args );

		// Reconfigure the args to display only the ids in the list.
		$args['post__in'] = $hs->get_post_ids();
		$args['orderby']  = 'post__in';

		return $args;
	},
	10,
	2
);

add_filter(
	'rest_prepare_page',
	function ( $response, $post, $request ) {
		if ( ! Gutenberg_Hierarchical_Sort::is_eligible( $request ) ) {
			return $response;
		}

		$hs                      = Gutenberg_Hierarchical_Sort::get_instance();
		$response->data['level'] = $hs->get_levels()[ $post->ID ];

		return $response;
	},
	10,
	3
);
