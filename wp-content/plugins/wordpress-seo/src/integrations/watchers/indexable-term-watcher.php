<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Builders\Indexable_Builder;
use Yoast\WP\SEO\Builders\Indexable_Link_Builder;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Site_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Watches Terms/Taxonomies to fill the related Indexable.
 */
class Indexable_Term_Watcher implements Integration_Interface {

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $repository;

	/**
	 * The indexable builder.
	 *
	 * @var Indexable_Builder
	 */
	protected $builder;

	/**
	 * The link builder.
	 *
	 * @var Indexable_Link_Builder
	 */
	protected $link_builder;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * Represents the site helper.
	 *
	 * @var Site_Helper
	 */
	protected $site;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Term_Watcher constructor.
	 *
	 * @param Indexable_Repository   $repository       The repository to use.
	 * @param Indexable_Builder      $builder          The post builder to use.
	 * @param Indexable_Link_Builder $link_builder     The lint builder to use.
	 * @param Indexable_Helper       $indexable_helper The indexable helper.
	 * @param Site_Helper            $site             The site helper.
	 */
	public function __construct(
		Indexable_Repository $repository,
		Indexable_Builder $builder,
		Indexable_Link_Builder $link_builder,
		Indexable_Helper $indexable_helper,
		Site_Helper $site
	) {
		$this->repository       = $repository;
		$this->builder          = $builder;
		$this->link_builder     = $link_builder;
		$this->indexable_helper = $indexable_helper;
		$this->site             = $site;
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'created_term', [ $this, 'build_indexable' ], \PHP_INT_MAX );
		\add_action( 'edited_term', [ $this, 'build_indexable' ], \PHP_INT_MAX );
		\add_action( 'delete_term', [ $this, 'delete_indexable' ], \PHP_INT_MAX );
	}

	/**
	 * Deletes a term from the index.
	 *
	 * @param int $term_id The Term ID to delete.
	 *
	 * @return void
	 */
	public function delete_indexable( $term_id ) {
		$indexable = $this->repository->find_by_id_and_type( $term_id, 'term', false );

		if ( ! $indexable ) {
			return;
		}

		$indexable->delete();
		\do_action( 'wpseo_indexable_deleted', $indexable );
	}

	/**
	 * Update the taxonomy meta data on save.
	 *
	 * @param int $term_id ID of the term to save data for.
	 *
	 * @return void
	 */
	public function build_indexable( $term_id ) {
		// Bail if this is a multisite installation and the site has been switched.
		if ( $this->site->is_multisite_and_switched() ) {
			return;
		}

		$term = \get_term( $term_id );

		if ( $term === null || \is_wp_error( $term ) ) {
			return;
		}

		if ( ! \is_taxonomy_viewable( $term->taxonomy ) ) {
			return;
		}

		$indexable = $this->repository->find_by_id_and_type( $term_id, 'term', false );

		// If we haven't found an existing indexable, create it. Otherwise update it.
		$indexable = $this->builder->build_for_id_and_type( $term_id, 'term', $indexable );

		if ( ! $indexable ) {
			return;
		}

		// Update links.
		$this->link_builder->build( $indexable, $term->description );

		$indexable->object_last_modified = \max( $indexable->object_last_modified, \current_time( 'mysql' ) );
		$this->indexable_helper->save_indexable( $indexable );
	}
}
