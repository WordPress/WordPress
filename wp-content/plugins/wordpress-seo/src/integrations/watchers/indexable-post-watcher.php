<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Exception;
use WP_Post;
use Yoast\WP\SEO\Builders\Indexable_Builder;
use Yoast\WP\SEO\Builders\Indexable_Link_Builder;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Author_Archive_Helper;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Post_Helper;
use Yoast\WP\SEO\Integrations\Cleanup_Integration;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Loggers\Logger;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Hierarchy_Repository;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use YoastSEO_Vendor\Psr\Log\LogLevel;

/**
 * WordPress Post watcher.
 *
 * Fills the Indexable according to Post data.
 */
class Indexable_Post_Watcher implements Integration_Interface {

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
	 * The indexable hierarchy repository.
	 *
	 * @var Indexable_Hierarchy_Repository
	 */
	private $hierarchy_repository;

	/**
	 * The link builder.
	 *
	 * @var Indexable_Link_Builder
	 */
	protected $link_builder;

	/**
	 * The author archive helper.
	 *
	 * @var Author_Archive_Helper
	 */
	private $author_archive;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * Holds the Post_Helper instance.
	 *
	 * @var Post_Helper
	 */
	private $post;

	/**
	 * Holds the logger.
	 *
	 * @var Logger
	 */
	protected $logger;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array<string> The conditionals.
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Post_Watcher constructor.
	 *
	 * @param Indexable_Repository           $repository           The repository to use.
	 * @param Indexable_Builder              $builder              The post builder to use.
	 * @param Indexable_Hierarchy_Repository $hierarchy_repository The hierarchy repository to use.
	 * @param Indexable_Link_Builder         $link_builder         The link builder.
	 * @param Author_Archive_Helper          $author_archive       The author archive helper.
	 * @param Indexable_Helper               $indexable_helper     The indexable helper.
	 * @param Post_Helper                    $post                 The post helper.
	 * @param Logger                         $logger               The logger.
	 */
	public function __construct(
		Indexable_Repository $repository,
		Indexable_Builder $builder,
		Indexable_Hierarchy_Repository $hierarchy_repository,
		Indexable_Link_Builder $link_builder,
		Author_Archive_Helper $author_archive,
		Indexable_Helper $indexable_helper,
		Post_Helper $post,
		Logger $logger
	) {
		$this->repository           = $repository;
		$this->builder              = $builder;
		$this->hierarchy_repository = $hierarchy_repository;
		$this->link_builder         = $link_builder;
		$this->author_archive       = $author_archive;
		$this->indexable_helper     = $indexable_helper;
		$this->post                 = $post;
		$this->logger               = $logger;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wp_insert_post', [ $this, 'build_indexable' ], \PHP_INT_MAX );
		\add_action( 'delete_post', [ $this, 'delete_indexable' ] );

		\add_action( 'edit_attachment', [ $this, 'build_indexable' ], \PHP_INT_MAX );
		\add_action( 'add_attachment', [ $this, 'build_indexable' ], \PHP_INT_MAX );
		\add_action( 'delete_attachment', [ $this, 'delete_indexable' ] );
	}

	/**
	 * Deletes the meta when a post is deleted.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function delete_indexable( $post_id ) {
		$indexable = $this->repository->find_by_id_and_type( $post_id, 'post', false );

		// Only interested in post indexables.
		if ( ! $indexable || $indexable->object_type !== 'post' ) {
			return;
		}

		$this->update_relations( $this->post->get_post( $post_id ) );

		$this->update_has_public_posts( $indexable );

		$this->hierarchy_repository->clear_ancestors( $indexable->id );
		$this->link_builder->delete( $indexable );
		$indexable->delete();
		\do_action( 'wpseo_indexable_deleted', $indexable );
	}

	/**
	 * Updates the relations when the post indexable is built.
	 *
	 * @param Indexable $indexable The indexable.
	 * @param WP_Post   $post      The post.
	 *
	 * @return void
	 */
	public function updated_indexable( $indexable, $post ) {
		// Only interested in post indexables.
		if ( $indexable->object_type !== 'post' ) {
			return;
		}

		if ( \is_a( $post, Indexable::class ) ) {
			\_deprecated_argument( __FUNCTION__, '17.7', 'The $old_indexable argument has been deprecated.' );
			$post = $this->post->get_post( $indexable->object_id );
		}

		$this->update_relations( $post );
	}

	/**
	 * Saves post meta.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function build_indexable( $post_id ) {
		// Bail if this is a multisite installation and the site has been switched.
		if ( $this->is_multisite_and_switched() ) {
			return;
		}

		try {
			$indexable = $this->repository->find_by_id_and_type( $post_id, 'post', false );
			$indexable = $this->builder->build_for_id_and_type( $post_id, 'post', $indexable );

			$post = $this->post->get_post( $post_id );

			/*
			 * Update whether an author has public posts.
			 * For example this post could be set to Draft or Private,
			 * which can influence if its author has any public posts at all.
			 */
			if ( $indexable ) {
				$this->update_has_public_posts( $indexable );
			}

			// Build links for this post.
			if ( $post && $indexable && \in_array( $post->post_status, $this->post->get_public_post_statuses(), true ) ) {
				$this->link_builder->build( $indexable, $post->post_content );
				// Save indexable to persist the updated link count.
				$this->indexable_helper->save_indexable( $indexable );
				$this->updated_indexable( $indexable, $post );
			}
		} catch ( Exception $exception ) {
			$this->logger->log( LogLevel::ERROR, $exception->getMessage() );
		}
	}

	/**
	 * Updates the has_public_posts when the post indexable is built.
	 *
	 * @param Indexable $indexable The indexable to check.
	 *
	 * @return void
	 */
	protected function update_has_public_posts( $indexable ) {
		// Update the author indexable's has public posts value.
		try {
			$author_indexable = $this->repository->find_by_id_and_type( $indexable->author_id, 'user' );
			if ( $author_indexable ) {
				$author_indexable->has_public_posts = $this->author_archive->author_has_public_posts( $author_indexable->object_id );
				$this->indexable_helper->save_indexable( $author_indexable );

				if ( $this->indexable_helper->should_index_indexable( $author_indexable ) ) {
					$this->reschedule_cleanup_if_author_has_no_posts( $author_indexable );
				}
			}
		} catch ( Exception $exception ) {
			$this->logger->log( LogLevel::ERROR, $exception->getMessage() );
		}

		// Update possible attachment's has public posts value.
		$this->post->update_has_public_posts_on_attachments( $indexable->object_id, $indexable->is_public );
	}

	/**
	 * Reschedule indexable cleanup if the author does not have any public posts.
	 * This should remove the author from the indexable table, since we do not
	 * want to store authors without public facing posts in the table.
	 *
	 * @param Indexable $author_indexable The author indexable.
	 *
	 * @return void
	 */
	protected function reschedule_cleanup_if_author_has_no_posts( $author_indexable ) {
		if ( $author_indexable->has_public_posts === false ) {
			$cleanup_not_yet_scheduled = ! \wp_next_scheduled( Cleanup_Integration::START_HOOK );
			if ( $cleanup_not_yet_scheduled ) {
				\wp_schedule_single_event( ( \time() + ( \MINUTE_IN_SECONDS * 5 ) ), Cleanup_Integration::START_HOOK );
			}
		}
	}

	/**
	 * Updates the relations on post save or post status change.
	 *
	 * @param WP_Post $post The post that has been updated.
	 *
	 * @return void
	 */
	protected function update_relations( $post ) {
		$related_indexables = $this->get_related_indexables( $post );

		foreach ( $related_indexables as $indexable ) {
			// Ignore everything that is not an actual indexable.
			if ( \is_a( $indexable, Indexable::class ) ) {
				$indexable->object_last_modified = \max( $indexable->object_last_modified, $post->post_modified_gmt );
				$this->indexable_helper->save_indexable( $indexable );
			}
		}
	}

	/**
	 * Retrieves the related indexables for given post.
	 *
	 * @param WP_Post $post The post to get the indexables for.
	 *
	 * @return Indexable[] The indexables.
	 */
	protected function get_related_indexables( $post ) {
		/**
		 * The related indexables.
		 *
		 * @var Indexable[] $related_indexables
		 */
		$related_indexables   = [];
		$related_indexables[] = $this->repository->find_by_id_and_type( $post->post_author, 'user', false );
		$related_indexables[] = $this->repository->find_for_post_type_archive( $post->post_type, false );
		$related_indexables[] = $this->repository->find_for_home_page( false );

		$taxonomies = \get_post_taxonomies( $post->ID );
		$taxonomies = \array_filter( $taxonomies, 'is_taxonomy_viewable' );
		$term_ids   = [];
		foreach ( $taxonomies as $taxonomy ) {
			$terms = \get_the_terms( $post->ID, $taxonomy );

			if ( empty( $terms ) || \is_wp_error( $terms ) ) {
				continue;
			}

			$term_ids = \array_merge( $term_ids, \wp_list_pluck( $terms, 'term_id' ) );
		}
		$related_indexables = \array_merge(
			$related_indexables,
			$this->repository->find_by_multiple_ids_and_type( $term_ids, 'term', false )
		);

		return \array_filter( $related_indexables );
	}

	/**
	 * Tests if the site is multisite and switched.
	 *
	 * @return bool True when the site is multisite and switched
	 */
	protected function is_multisite_and_switched() {
		return \is_multisite() && \ms_is_switched();
	}
}
