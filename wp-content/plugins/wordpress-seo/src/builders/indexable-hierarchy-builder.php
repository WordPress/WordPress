<?php

namespace Yoast\WP\SEO\Builders;

use WP_Post;
use WP_Term;
use WPSEO_Meta;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Hierarchy_Repository;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Repositories\Primary_Term_Repository;

/**
 * Builder for the indexables hierarchy.
 *
 * Builds the indexable hierarchy for indexables.
 */
class Indexable_Hierarchy_Builder {

	/**
	 * Holds a list of indexable ids where the ancestors are saved for.
	 *
	 * @var array<int>
	 */
	protected $saved_ancestors = [];

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The indexable hierarchy repository.
	 *
	 * @var Indexable_Hierarchy_Repository
	 */
	private $indexable_hierarchy_repository;

	/**
	 * The primary term repository.
	 *
	 * @var Primary_Term_Repository
	 */
	private $primary_term_repository;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * Holds the Post_Helper instance.
	 *
	 * @var Post_Helper
	 */
	private $post;

	/**
	 * Holds the Indexable_Helper instance.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * Indexable_Author_Builder constructor.
	 *
	 * @param Indexable_Hierarchy_Repository $indexable_hierarchy_repository The indexable hierarchy repository.
	 * @param Primary_Term_Repository        $primary_term_repository        The primary term repository.
	 * @param Options_Helper                 $options                        The options helper.
	 * @param Post_Helper                    $post                           The post helper.
	 * @param Indexable_Helper               $indexable_helper               The indexable helper.
	 */
	public function __construct(
		Indexable_Hierarchy_Repository $indexable_hierarchy_repository,
		Primary_Term_Repository $primary_term_repository,
		Options_Helper $options,
		Post_Helper $post,
		Indexable_Helper $indexable_helper
	) {
		$this->indexable_hierarchy_repository = $indexable_hierarchy_repository;
		$this->primary_term_repository        = $primary_term_repository;
		$this->options                        = $options;
		$this->post                           = $post;
		$this->indexable_helper               = $indexable_helper;
	}

	/**
	 * Sets the indexable repository. Done to avoid circular dependencies.
	 *
	 * @required
	 *
	 * @param Indexable_Repository $indexable_repository The indexable repository.
	 *
	 * @return void
	 */
	public function set_indexable_repository( Indexable_Repository $indexable_repository ) {
		$this->indexable_repository = $indexable_repository;
	}

	/**
	 * Builds the ancestor hierarchy for an indexable.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return Indexable The indexable.
	 */
	public function build( Indexable $indexable ) {
		if ( $this->hierarchy_is_built( $indexable ) ) {
			return $indexable;
		}

		if ( ! $this->indexable_helper->should_index_indexable( $indexable ) ) {
			return $indexable;
		}

		$this->indexable_hierarchy_repository->clear_ancestors( $indexable->id );
		$indexable_id = $this->get_indexable_id( $indexable );
		$ancestors    = [];
		if ( $indexable->object_type === 'post' ) {
			$this->add_ancestors_for_post( $indexable_id, $indexable->object_id, $ancestors );
		}

		if ( $indexable->object_type === 'term' ) {
			$this->add_ancestors_for_term( $indexable_id, $indexable->object_id, $ancestors );
		}
		$indexable->ancestors     = \array_reverse( \array_values( $ancestors ) );
		$indexable->has_ancestors = ! empty( $ancestors );
		if ( $indexable->id ) {
			$this->save_ancestors( $indexable );
		}

		return $indexable;
	}

	/**
	 * Checks if a hierarchy is built already for the given indexable.
	 *
	 * @param Indexable $indexable The indexable to check.
	 *
	 * @return bool True when indexable has a built hierarchy.
	 */
	protected function hierarchy_is_built( Indexable $indexable ) {
		if ( \in_array( $indexable->id, $this->saved_ancestors, true ) ) {
			return true;
		}

		$this->saved_ancestors[] = $indexable->id;

		return false;
	}

	/**
	 * Saves the ancestors.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return void
	 */
	private function save_ancestors( $indexable ) {
		if ( empty( $indexable->ancestors ) ) {
			$this->indexable_hierarchy_repository->add_ancestor( $indexable->id, 0, 0 );
			return;
		}
		$depth = \count( $indexable->ancestors );
		foreach ( $indexable->ancestors as $ancestor ) {
			$this->indexable_hierarchy_repository->add_ancestor( $indexable->id, $ancestor->id, $depth );
			--$depth;
		}
	}

	/**
	 * Adds ancestors for a post.
	 *
	 * @param int   $indexable_id The indexable id, this is the id of the original indexable.
	 * @param int   $post_id      The post id, this is the id of the post currently being evaluated.
	 * @param int[] $parents      The indexable IDs of all parents.
	 *
	 * @return void
	 */
	private function add_ancestors_for_post( $indexable_id, $post_id, &$parents ) {
		$post = $this->post->get_post( $post_id );

		if ( ! isset( $post->post_parent ) ) {
			return;
		}

		if ( $post->post_parent !== 0 && $this->post->get_post( $post->post_parent ) !== null ) {
			$ancestor = $this->indexable_repository->find_by_id_and_type( $post->post_parent, 'post' );
			if ( $this->is_invalid_ancestor( $ancestor, $indexable_id, $parents ) ) {
				return;
			}

			$parents[ $this->get_indexable_id( $ancestor ) ] = $ancestor;

			$this->add_ancestors_for_post( $indexable_id, $ancestor->object_id, $parents );

			return;
		}

		$primary_term_id = $this->find_primary_term_id_for_post( $post );

		if ( $primary_term_id === 0 ) {
			return;
		}

		$ancestor = $this->indexable_repository->find_by_id_and_type( $primary_term_id, 'term' );
		if ( $this->is_invalid_ancestor( $ancestor, $indexable_id, $parents ) ) {
			return;
		}

		$parents[ $this->get_indexable_id( $ancestor ) ] = $ancestor;

		$this->add_ancestors_for_term( $indexable_id, $ancestor->object_id, $parents );
	}

	/**
	 * Adds ancestors for a term.
	 *
	 * @param int   $indexable_id The indexable id, this is the id of the original indexable.
	 * @param int   $term_id      The term id, this is the id of the term currently being evaluated.
	 * @param int[] $parents      The indexable IDs of all parents.
	 *
	 * @return void
	 */
	private function add_ancestors_for_term( $indexable_id, $term_id, &$parents = [] ) {
		$term         = \get_term( $term_id );
		$term_parents = $this->get_term_parents( $term );

		foreach ( $term_parents as $parent ) {
			$ancestor = $this->indexable_repository->find_by_id_and_type( $parent->term_id, 'term' );
			if ( $this->is_invalid_ancestor( $ancestor, $indexable_id, $parents ) ) {
				continue;
			}

			$parents[ $this->get_indexable_id( $ancestor ) ] = $ancestor;
		}
	}

	/**
	 * Gets the primary term ID for a post.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return int The primary term ID. 0 if none exists.
	 */
	private function find_primary_term_id_for_post( $post ) {
		$main_taxonomy = $this->options->get( 'post_types-' . $post->post_type . '-maintax' );

		if ( ! $main_taxonomy || $main_taxonomy === '0' ) {
			return 0;
		}

		$primary_term_id = $this->get_primary_term_id( $post->ID, $main_taxonomy );

		if ( $primary_term_id ) {
			$term = \get_term( $primary_term_id );
			if ( $term !== null && ! \is_wp_error( $term ) ) {
				return $primary_term_id;
			}
		}

		$terms = \get_the_terms( $post->ID, $main_taxonomy );

		if ( ! \is_array( $terms ) || empty( $terms ) ) {
			return 0;
		}

		return $this->find_deepest_term_id( $terms );
	}

	/**
	 * Find the deepest term in an array of term objects.
	 *
	 * @param array<WP_Term> $terms Terms set.
	 *
	 * @return int The deepest term ID.
	 */
	private function find_deepest_term_id( $terms ) {
		/*
		 * Let's find the deepest term in this array, by looping through and then
		 * unsetting every term that is used as a parent by another one in the array.
		 */
		$terms_by_id = [];
		foreach ( $terms as $term ) {
			$terms_by_id[ $term->term_id ] = $term;
		}
		foreach ( $terms as $term ) {
			unset( $terms_by_id[ $term->parent ] );
		}

		/*
		 * As we could still have two subcategories, from different parent categories,
		 * let's pick the one with the lowest ordered ancestor.
		 */
		$parents_count = -1;
		$term_order    = 9999; // Because ASC.
		$deepest_term  = \reset( $terms_by_id );
		foreach ( $terms_by_id as $term ) {
			$parents = $this->get_term_parents( $term );

			$new_parents_count = \count( $parents );

			if ( $new_parents_count < $parents_count ) {
				continue;
			}

			$parent_order = 9999; // Set default order.
			foreach ( $parents as $parent ) {
				if ( $parent->parent === 0 && isset( $parent->term_order ) ) {
					$parent_order = $parent->term_order;
				}
			}

			// Check if parent has lowest order.
			if ( $new_parents_count > $parents_count || $parent_order < $term_order ) {
				$term_order   = $parent_order;
				$deepest_term = $term;
			}

			$parents_count = $new_parents_count;
		}

		return $deepest_term->term_id;
	}

	/**
	 * Get a term's parents.
	 *
	 * @param WP_Term $term Term to get the parents for.
	 *
	 * @return WP_Term[] An array of all this term's parents.
	 */
	private function get_term_parents( $term ) {
		$tax     = $term->taxonomy;
		$parents = [];
		while ( (int) $term->parent !== 0 ) {
			$term      = \get_term( $term->parent, $tax );
			$parents[] = $term;
		}

		return $parents;
	}

	/**
	 * Checks if an ancestor is valid to add.
	 *
	 * @param Indexable $ancestor     The ancestor (presumed indexable) to check.
	 * @param int       $indexable_id The indexable id we're adding ancestors for.
	 * @param int[]     $parents      The indexable ids of the parents already added.
	 *
	 * @return bool
	 */
	private function is_invalid_ancestor( $ancestor, $indexable_id, $parents ) {
		// If the ancestor is not an Indexable, it is invalid by default.
		if ( ! \is_a( $ancestor, 'Yoast\WP\SEO\Models\Indexable' ) ) {
			return true;
		}

		// Don't add ancestors if they're unindexed, already added or the same as the main object.
		if ( $ancestor->post_status === 'unindexed' ) {
			return true;
		}

		$ancestor_id = $this->get_indexable_id( $ancestor );
		if ( \array_key_exists( $ancestor_id, $parents ) ) {
			return true;
		}

		if ( $ancestor_id === $indexable_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the ID for an indexable. Catches situations where the id is null due to errors.
	 *
	 * @param Indexable $indexable The indexable.
	 *
	 * @return string|int A unique ID for the indexable.
	 */
	private function get_indexable_id( Indexable $indexable ) {
		if ( $indexable->id === 0 ) {
			return "{$indexable->object_type}:{$indexable->object_id}";
		}

		return $indexable->id;
	}

	/**
	 * Returns the primary term id of a post.
	 *
	 * @param int    $post_id       The post ID.
	 * @param string $main_taxonomy The main taxonomy.
	 *
	 * @return int The ID of the primary term.
	 */
	private function get_primary_term_id( $post_id, $main_taxonomy ) {
		$primary_term = $this->primary_term_repository->find_by_post_id_and_taxonomy( $post_id, $main_taxonomy, false );

		if ( $primary_term ) {
			return $primary_term->term_id;
		}

		return \get_post_meta( $post_id, WPSEO_Meta::$meta_prefix . 'primary_' . $main_taxonomy, true );
	}
}
