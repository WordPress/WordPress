<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use WP_Post;
use WPSEO_Meta;
use Yoast\WP\SEO\Builders\Indexable_Hierarchy_Builder;
use Yoast\WP\SEO\Conditionals\Admin\Doing_Post_Quick_Edit_Save_Conditional;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Models\Indexable;
use Yoast\WP\SEO\Repositories\Indexable_Repository;
use Yoast\WP\SEO\Repositories\Primary_Term_Repository;

/**
 * Class Primary_Category_Quick_Edit_Watcher
 */
class Primary_Category_Quick_Edit_Watcher implements Integration_Interface {

	/**
	 * Holds the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Holds the primary term repository.
	 *
	 * @var Primary_Term_Repository
	 */
	protected $primary_term_repository;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	protected $post_type_helper;

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $indexable_repository;

	/**
	 * The indexable hierarchy builder.
	 *
	 * @var Indexable_Hierarchy_Builder
	 */
	protected $indexable_hierarchy_builder;

	/**
	 * Primary_Category_Quick_Edit_Watcher constructor.
	 *
	 * @param Options_Helper              $options_helper              The options helper.
	 * @param Primary_Term_Repository     $primary_term_repository     The primary term repository.
	 * @param Post_Type_Helper            $post_type_helper            The post type helper.
	 * @param Indexable_Repository        $indexable_repository        The indexable repository.
	 * @param Indexable_Hierarchy_Builder $indexable_hierarchy_builder The indexable hierarchy repository.
	 */
	public function __construct(
		Options_Helper $options_helper,
		Primary_Term_Repository $primary_term_repository,
		Post_Type_Helper $post_type_helper,
		Indexable_Repository $indexable_repository,
		Indexable_Hierarchy_Builder $indexable_hierarchy_builder
	) {
		$this->options_helper              = $options_helper;
		$this->primary_term_repository     = $primary_term_repository;
		$this->post_type_helper            = $post_type_helper;
		$this->indexable_repository        = $indexable_repository;
		$this->indexable_hierarchy_builder = $indexable_hierarchy_builder;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'set_object_terms', [ $this, 'validate_primary_category' ], 10, 4 );
	}

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array The conditionals.
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class, Doing_Post_Quick_Edit_Save_Conditional::class ];
	}

	/**
	 * Validates if the current primary category is still present. If not just remove the post meta for it.
	 *
	 * @param int    $object_id Object ID.
	 * @param array  $terms     Unused. An array of object terms.
	 * @param array  $tt_ids    An array of term taxonomy IDs.
	 * @param string $taxonomy  Taxonomy slug.
	 *
	 * @return void
	 */
	public function validate_primary_category( $object_id, $terms, $tt_ids, $taxonomy ) {
		$post = \get_post( $object_id );
		if ( $post === null ) {
			return;
		}

		$main_taxonomy = $this->options_helper->get( 'post_types-' . $post->post_type . '-maintax' );
		if ( ! $main_taxonomy || $main_taxonomy === '0' ) {
			return;
		}

		if ( $main_taxonomy !== $taxonomy ) {
			return;
		}

		$primary_category = $this->get_primary_term_id( $post->ID, $main_taxonomy );
		if ( $primary_category === false ) {
			return;
		}

		// The primary category isn't removed.
		if ( \in_array( (string) $primary_category, $tt_ids, true ) ) {
			return;
		}

		$this->remove_primary_term( $post->ID, $main_taxonomy );

		// Rebuild the post hierarchy for this post now the primary term has been changed.
		$this->build_post_hierarchy( $post );
	}

	/**
	 * Returns the primary term id of a post.
	 *
	 * @param int    $post_id       The post ID.
	 * @param string $main_taxonomy The main taxonomy.
	 *
	 * @return int|false The ID of the primary term, or `false` if the post ID is invalid.
	 */
	private function get_primary_term_id( $post_id, $main_taxonomy ) {
		$primary_term = $this->primary_term_repository->find_by_post_id_and_taxonomy( $post_id, $main_taxonomy, false );

		if ( $primary_term ) {
			return $primary_term->term_id;
		}

		return \get_post_meta( $post_id, WPSEO_Meta::$meta_prefix . 'primary_' . $main_taxonomy, true );
	}

	/**
	 * Removes the primary category.
	 *
	 * @param int    $post_id       The post id to set primary taxonomy for.
	 * @param string $main_taxonomy Name of the taxonomy that is set to be the primary one.
	 *
	 * @return void
	 */
	private function remove_primary_term( $post_id, $main_taxonomy ) {
		$primary_term = $this->primary_term_repository->find_by_post_id_and_taxonomy( $post_id, $main_taxonomy, false );
		if ( $primary_term ) {
			$primary_term->delete();
		}

		// Remove it from the post meta.
		\delete_post_meta( $post_id, WPSEO_Meta::$meta_prefix . 'primary_' . $main_taxonomy );
	}

	/**
	 * Builds the hierarchy for a post.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return void
	 */
	public function build_post_hierarchy( $post ) {
		if ( $this->post_type_helper->is_excluded( $post->post_type ) ) {
			return;
		}

		$indexable = $this->indexable_repository->find_by_id_and_type( $post->ID, 'post' );

		if ( $indexable instanceof Indexable ) {
			$this->indexable_hierarchy_builder->build( $indexable );
		}
	}
}
