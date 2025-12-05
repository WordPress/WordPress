<?php

namespace Yoast\WP\SEO\Builders;

use Yoast\WP\SEO\Exceptions\Indexable\Not_Built_Exception;
use Yoast\WP\SEO\Exceptions\Indexable\Source_Exception;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Services\Indexables\Indexable_Version_Manager;

/**
 * Builder for the indexables.
 *
 * Creates all the indexables.
 */
class Indexable_Builder {

	/**
	 * The author builder.
	 *
	 * @var Indexable_Author_Builder
	 */
	private $author_builder;

	/**
	 * The post builder.
	 *
	 * @var Indexable_Post_Builder
	 */
	private $post_builder;

	/**
	 * The term builder.
	 *
	 * @var Indexable_Term_Builder
	 */
	private $term_builder;

	/**
	 * The home page builder.
	 *
	 * @var Indexable_Home_Page_Builder
	 */
	private $home_page_builder;

	/**
	 * The post type archive builder.
	 *
	 * @var Indexable_Post_Type_Archive_Builder
	 */
	private $post_type_archive_builder;

	/**
	 * The data archive builder.
	 *
	 * @var Indexable_Date_Archive_Builder
	 */
	private $date_archive_builder;

	/**
	 * The system page builder.
	 *
	 * @var Indexable_System_Page_Builder
	 */
	private $system_page_builder;

	/**
	 * The indexable hierarchy builder.
	 *
	 * @var Indexable_Hierarchy_Builder
	 */
	private $hierarchy_builder;

	/**
	 * The primary term builder
	 *
	 * @var Primary_Term_Builder
	 */
	private $primary_term_builder;

	/**
	 * The link builder
	 *
	 * @var Indexable_Link_Builder
	 */
	private $link_builder;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	private $indexable_repository;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * The Indexable Version Manager.
	 *
	 * @var Indexable_Version_Manager
	 */
	protected $version_manager;

	/**
	 * Returns the instance of this class constructed through the ORM Wrapper.
	 *
	 * @param Indexable_Author_Builder            $author_builder            The author builder for creating missing indexables.
	 * @param Indexable_Post_Builder              $post_builder              The post builder for creating missing indexables.
	 * @param Indexable_Term_Builder              $term_builder              The term builder for creating missing indexables.
	 * @param Indexable_Home_Page_Builder         $home_page_builder         The front page builder for creating missing indexables.
	 * @param Indexable_Post_Type_Archive_Builder $post_type_archive_builder The post type archive builder for creating missing indexables.
	 * @param Indexable_Date_Archive_Builder      $date_archive_builder      The date archive builder for creating missing indexables.
	 * @param Indexable_System_Page_Builder       $system_page_builder       The search result builder for creating missing indexables.
	 * @param Indexable_Hierarchy_Builder         $hierarchy_builder         The hierarchy builder for creating the indexable hierarchy.
	 * @param Primary_Term_Builder                $primary_term_builder      The primary term builder for creating primary terms for posts.
	 * @param Indexable_Helper                    $indexable_helper          The indexable helper.
	 * @param Indexable_Version_Manager           $version_manager           The indexable version manager.
	 * @param Indexable_Link_Builder              $link_builder              The link builder for creating missing SEO links.
	 */
	public function __construct(
		Indexable_Author_Builder $author_builder,
		Indexable_Post_Builder $post_builder,
		Indexable_Term_Builder $term_builder,
		Indexable_Home_Page_Builder $home_page_builder,
		Indexable_Post_Type_Archive_Builder $post_type_archive_builder,
		Indexable_Date_Archive_Builder $date_archive_builder,
		Indexable_System_Page_Builder $system_page_builder,
		Indexable_Hierarchy_Builder $hierarchy_builder,
		Primary_Term_Builder $primary_term_builder,
		Indexable_Helper $indexable_helper,
		Indexable_Version_Manager $version_manager,
		Indexable_Link_Builder $link_builder
	) {
		$this->author_builder            = $author_builder;
		$this->post_builder              = $post_builder;
		$this->term_builder              = $term_builder;
		$this->home_page_builder         = $home_page_builder;
		$this->post_type_archive_builder = $post_type_archive_builder;
		$this->date_archive_builder      = $date_archive_builder;
		$this->system_page_builder       = $system_page_builder;
		$this->hierarchy_builder         = $hierarchy_builder;
		$this->primary_term_builder      = $primary_term_builder;
		$this->indexable_helper          = $indexable_helper;
		$this->version_manager           = $version_manager;
		$this->link_builder              = $link_builder;
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
	 * Creates a clean copy of an Indexable to allow for later database operations.
	 *
	 * @param Indexable $indexable The Indexable to copy.
	 *
	 * @return bool|Indexable
	 */
	protected function deep_copy_indexable( $indexable ) {
		return $this->indexable_repository
			->query()
			->create( $indexable->as_array() );
	}

	/**
	 * Creates an indexable by its ID and type.
	 *
	 * @param int            $object_id   The indexable object ID.
	 * @param string         $object_type The indexable object type.
	 * @param Indexable|bool $indexable   Optional. An existing indexable to overwrite.
	 *
	 * @return bool|Indexable Instance of indexable. False when unable to build.
	 */
	public function build_for_id_and_type( $object_id, $object_type, $indexable = false ) {
		$defaults = [
			'object_type' => $object_type,
			'object_id'   => $object_id,
		];

		$indexable = $this->build( $indexable, $defaults );

		return $indexable;
	}

	/**
	 * Creates an indexable for the homepage.
	 *
	 * @param Indexable|bool $indexable Optional. An existing indexable to overwrite.
	 *
	 * @return Indexable The home page indexable.
	 */
	public function build_for_home_page( $indexable = false ) {
		return $this->build( $indexable, [ 'object_type' => 'home-page' ] );
	}

	/**
	 * Creates an indexable for the date archive.
	 *
	 * @param Indexable|bool $indexable Optional. An existing indexable to overwrite.
	 *
	 * @return Indexable The date archive indexable.
	 */
	public function build_for_date_archive( $indexable = false ) {
		return $this->build( $indexable, [ 'object_type' => 'date-archive' ] );
	}

	/**
	 * Creates an indexable for a post type archive.
	 *
	 * @param string         $post_type The post type.
	 * @param Indexable|bool $indexable Optional. An existing indexable to overwrite.
	 *
	 * @return Indexable The post type archive indexable.
	 */
	public function build_for_post_type_archive( $post_type, $indexable = false ) {
		$defaults = [
			'object_type'     => 'post-type-archive',
			'object_sub_type' => $post_type,
		];
		return $this->build( $indexable, $defaults );
	}

	/**
	 * Creates an indexable for a system page.
	 *
	 * @param string         $page_type The type of system page.
	 * @param Indexable|bool $indexable Optional. An existing indexable to overwrite.
	 *
	 * @return Indexable The search result indexable.
	 */
	public function build_for_system_page( $page_type, $indexable = false ) {
		$defaults = [
			'object_type'     => 'system-page',
			'object_sub_type' => $page_type,
		];
		return $this->build( $indexable, $defaults );
	}

	/**
	 * Ensures we have a valid indexable. Creates one if false is passed.
	 *
	 * @param Indexable|false $indexable The indexable.
	 * @param array           $defaults  The initial properties of the Indexable.
	 *
	 * @return Indexable The indexable.
	 */
	protected function ensure_indexable( $indexable, $defaults = [] ) {
		if ( ! $indexable ) {
			return $this->indexable_repository->query()->create( $defaults );
		}

		return $indexable;
	}

	/**
	 * Build and author indexable from an author id if it does not exist yet, or if the author indexable needs to be upgraded.
	 *
	 * @param int $author_id The author id.
	 *
	 * @return Indexable|false The author indexable if it has been built, `false` if it could not be built.
	 */
	protected function maybe_build_author_indexable( $author_id ) {
		$author_indexable = $this->indexable_repository->find_by_id_and_type(
			$author_id,
			'user',
			false
		);
		if ( ! $author_indexable || $this->version_manager->indexable_needs_upgrade( $author_indexable ) ) {
			// Try to build the author.
			$author_defaults  = [
				'object_type' => 'user',
				'object_id'   => $author_id,
			];
			$author_indexable = $this->build( $author_indexable, $author_defaults );
		}
		return $author_indexable;
	}

	/**
	 * Checks if the indexable type is one that is not supposed to have object ID for.
	 *
	 * @param string $type The type of the indexable.
	 *
	 * @return bool Whether the indexable type is one that is not supposed to have object ID for.
	 */
	protected function is_type_with_no_id( $type ) {
		return \in_array( $type, [ 'home-page', 'date-archive', 'post-type-archive', 'system-page' ], true );
	}

	// phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing -- Exceptions are handled by the catch statement in the method.

	/**
	 * Rebuilds an Indexable from scratch.
	 *
	 * @param Indexable  $indexable The Indexable to (re)build.
	 * @param array|null $defaults  The object type of the Indexable.
	 *
	 * @return Indexable|false The resulting Indexable.
	 */
	public function build( $indexable, $defaults = null ) {
		// Backup the previous Indexable, if there was one.
		$indexable_before = ( $indexable ) ? $this->deep_copy_indexable( $indexable ) : null;

		// Make sure we have an Indexable to work with.
		$indexable = $this->ensure_indexable( $indexable, $defaults );

		try {
			if ( $indexable->object_id === 0 ) {
				throw Not_Built_Exception::invalid_object_id( $indexable->object_id );
			}
			switch ( $indexable->object_type ) {

				case 'post':
					$indexable = $this->post_builder->build( $indexable->object_id, $indexable );

					// Save indexable, to make sure it can be queried when building related objects like the author indexable and hierarchy.
					$indexable = $this->indexable_helper->save_indexable( $indexable, $indexable_before );

					// For attachments, we have to make sure to patch any potentially previously cleaned up SEO links.
					if ( \is_a( $indexable, Indexable::class ) && $indexable->object_sub_type === 'attachment' ) {
						$this->link_builder->patch_seo_links( $indexable );
					}

					// Always rebuild the primary term.
					$this->primary_term_builder->build( $indexable->object_id );

					// Always rebuild the hierarchy; this needs the primary term to run correctly.
					$this->hierarchy_builder->build( $indexable );

					$this->maybe_build_author_indexable( $indexable->author_id );

					// The indexable is already saved, so return early.
					return $indexable;

				case 'user':
					$indexable = $this->author_builder->build( $indexable->object_id, $indexable );
					break;

				case 'term':
					$indexable = $this->term_builder->build( $indexable->object_id, $indexable );

					// Save indexable, to make sure it can be queried when building hierarchy.
					$indexable = $this->indexable_helper->save_indexable( $indexable, $indexable_before );

					$this->hierarchy_builder->build( $indexable );

					// The indexable is already saved, so return early.
					return $indexable;

				case 'home-page':
					$indexable = $this->home_page_builder->build( $indexable );
					break;

				case 'date-archive':
					$indexable = $this->date_archive_builder->build( $indexable );
					break;

				case 'post-type-archive':
					$indexable = $this->post_type_archive_builder->build( $indexable->object_sub_type, $indexable );
					break;

				case 'system-page':
					$indexable = $this->system_page_builder->build( $indexable->object_sub_type, $indexable );
					break;
			}

			return $this->indexable_helper->save_indexable( $indexable, $indexable_before );
		} catch ( Source_Exception $exception ) {
			if ( ! $this->is_type_with_no_id( $indexable->object_type ) && ! isset( $indexable->object_id ) ) {
				return false;
			}

			/**
			 * The current indexable could not be indexed. Create a placeholder indexable, so we can
			 * skip this indexable in future indexing runs.
			 *
			 * @var Indexable $indexable
			 */
			$indexable = $this->ensure_indexable(
				$indexable,
				[
					'object_id'   => $indexable->object_id,
					'object_type' => $indexable->object_type,
					'post_status' => 'unindexed',
					'version'     => 0,
				]
			);
			// If we already had an existing indexable, mark it as unindexed. We cannot rely on its validity anymore.
			$indexable->post_status = 'unindexed';
			// Make sure that the indexing process doesn't get stuck in a loop on this broken indexable.
			$indexable = $this->version_manager->set_latest( $indexable );

			return $this->indexable_helper->save_indexable( $indexable, $indexable_before );
		} catch ( Not_Built_Exception $exception ) {
			return false;
		}
	}

	// phpcs:enable
}
