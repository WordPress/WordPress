<?php

namespace Yoast\WP\SEO\Repositories;

use Yoast\WP\Lib\Model;
use Yoast\WP\Lib\ORM;
use Yoast\WP\SEO\Builders\Indexable_Hierarchy_Builder;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Models\Indexable;

/**
 * Class Indexable_Hierarchy_Repository.
 */
class Indexable_Hierarchy_Repository {

	/**
	 * Represents the indexable hierarchy builder.
	 *
	 * @var Indexable_Hierarchy_Builder
	 */
	protected $builder;

	/**
	 * Represents the indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Sets the hierarchy builder.
	 *
	 * @required
	 *
	 * @param Indexable_Hierarchy_Builder $builder The indexable hierarchy builder.
	 *
	 * @return void
	 */
	public function set_builder( Indexable_Hierarchy_Builder $builder ) {
		$this->builder = $builder;
	}

	/**
	 * Sets the indexable helper.
	 *
	 * @required
	 *
	 * @param Indexable_Helper $indexable_helper The indexable helper.
	 *
	 * @return void
	 */
	public function set_helper( Indexable_Helper $indexable_helper ) {
		$this->indexable_helper = $indexable_helper;
	}

	/**
	 * Removes all ancestors for an indexable.
	 *
	 * @param int $indexable_id The indexable id.
	 *
	 * @return bool Whether or not the indexables were successfully deleted.
	 */
	public function clear_ancestors( $indexable_id ) {
		return $this->query()->where( 'indexable_id', $indexable_id )->delete_many();
	}

	/**
	 * Adds an ancestor to an indexable.
	 *
	 * @param int $indexable_id The indexable id.
	 * @param int $ancestor_id  The ancestor id.
	 * @param int $depth        The depth.
	 *
	 * @return bool Whether or not the ancestor was added successfully.
	 */
	public function add_ancestor( $indexable_id, $ancestor_id, $depth ) {
		if ( ! $this->indexable_helper->should_index_indexables() ) {
			return false;
		}

		$hierarchy = $this->query()->create(
			[
				'indexable_id' => $indexable_id,
				'ancestor_id'  => $ancestor_id,
				'depth'        => $depth,
				'blog_id'      => \get_current_blog_id(),
			]
		);

		return $hierarchy->save();
	}

	/**
	 * Retrieves the ancestors. Create them when empty.
	 *
	 * @param Indexable $indexable The indexable to get the ancestors for.
	 *
	 * @return int[] The indexable id's of the ancestors in order of grandparent to child.
	 */
	public function find_ancestors( Indexable $indexable ) {
		$ancestors = $this->query()
			->select( 'ancestor_id' )
			->where( 'indexable_id', $indexable->id )
			->order_by_desc( 'depth' )
			->find_array();

		if ( ! empty( $ancestors ) ) {
			if ( \count( $ancestors ) === 1 && $ancestors[0]['ancestor_id'] === '0' ) {
				return [];
			}
			return \wp_list_pluck( $ancestors, 'ancestor_id' );
		}

		$indexable = $this->builder->build( $indexable );

		return \wp_list_pluck( $indexable->ancestors, 'id' );
	}

	/**
	 * Finds the children for a given indexable.
	 *
	 * @param Indexable $indexable The indexable to find the children for.
	 *
	 * @return array Array with indexable id's for the children.
	 */
	public function find_children( Indexable $indexable ) {
		$children = $this->query()
			->select( 'indexable_id' )
			->where( 'ancestor_id', $indexable->id )
			->find_array();

		if ( empty( $children ) ) {
			return [];
		}

		return \wp_list_pluck( $children, 'indexable_id' );
	}

	/**
	 * Starts a query for this repository.
	 *
	 * @return ORM
	 */
	public function query() {
		return Model::of_type( 'Indexable_Hierarchy' );
	}

	/**
	 * Finds all the children by given ancestor id's.
	 *
	 * @param array $object_ids List of id's to get the children for.
	 *
	 * @return array List of indexable id's for the children.
	 */
	public function find_children_by_ancestor_ids( array $object_ids ) {
		if ( empty( $object_ids ) ) {
			return [];
		}

		$children = $this->query()
			->select( 'indexable_id' )
			->where_in( 'ancestor_id', $object_ids )
			->find_array();

		if ( empty( $children ) ) {
			return [];
		}

		return \wp_list_pluck( $children, 'indexable_id' );
	}
}
