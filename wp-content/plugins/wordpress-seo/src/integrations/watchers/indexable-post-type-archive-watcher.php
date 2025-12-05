<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Builders\Indexable_Builder;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Post type archive watcher to save the meta data to an Indexable.
 *
 * Watches the home page options to save the meta information when updated.
 */
class Indexable_Post_Type_Archive_Watcher implements Integration_Interface {

	/**
	 * The indexable repository.
	 *
	 * @var Indexable_Repository
	 */
	protected $repository;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	private $indexable_helper;

	/**
	 * The indexable builder.
	 *
	 * @var Indexable_Builder
	 */
	protected $builder;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Post_Type_Archive_Watcher constructor.
	 *
	 * @param Indexable_Repository $repository       The repository to use.
	 * @param Indexable_Helper     $indexable_helper The indexable helper.
	 * @param Indexable_Builder    $builder          The post builder to use.
	 */
	public function __construct(
		Indexable_Repository $repository,
		Indexable_Helper $indexable_helper,
		Indexable_Builder $builder
	) {
		$this->repository       = $repository;
		$this->indexable_helper = $indexable_helper;
		$this->builder          = $builder;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_wpseo_titles', [ $this, 'check_option' ], 10, 2 );
	}

	/**
	 * Checks if the home page indexable needs to be rebuild based on option values.
	 *
	 * @param array $old_value The old value of the option.
	 * @param array $new_value The new value of the option.
	 *
	 * @return bool Whether or not the option has been saved.
	 */
	public function check_option( $old_value, $new_value ) {
		$relevant_keys = [ 'title-ptarchive-', 'metadesc-ptarchive-', 'bctitle-ptarchive-', 'noindex-ptarchive-' ];

		// If this is the first time saving the option, thus when value is false.
		if ( $old_value === false ) {
			$old_value = [];
		}

		if ( ! \is_array( $old_value ) || ! \is_array( $new_value ) ) {
			return false;
		}

		$keys               = \array_unique( \array_merge( \array_keys( $old_value ), \array_keys( $new_value ) ) );
		$post_types_rebuild = [];

		foreach ( $keys as $key ) {
			$post_type = false;
			// Check if it's a key relevant to post type archives.
			foreach ( $relevant_keys as $relevant_key ) {
				if ( \strpos( $key, $relevant_key ) === 0 ) {
					$post_type = \substr( $key, \strlen( $relevant_key ) );
					break;
				}
			}

			// If it's not a relevant key or both values aren't set they haven't changed.
			if ( $post_type === false || ( ! isset( $old_value[ $key ] ) && ! isset( $new_value[ $key ] ) ) ) {
				continue;
			}

			// If the value was set but now isn't, is set but wasn't or is not the same it has changed.
			if (
				! \in_array( $post_type, $post_types_rebuild, true )
				&& (
					! isset( $old_value[ $key ] )
					|| ! isset( $new_value[ $key ] )
					|| $old_value[ $key ] !== $new_value[ $key ]
				)
			) {
				$this->build_indexable( $post_type );
				$post_types_rebuild[] = $post_type;
			}
		}

		return true;
	}

	/**
	 * Saves the post type archive.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return void
	 */
	public function build_indexable( $post_type ) {
		$indexable = $this->repository->find_for_post_type_archive( $post_type, false );
		$indexable = $this->builder->build_for_post_type_archive( $post_type, $indexable );

		if ( $indexable ) {
			$indexable->object_last_modified = \max( $indexable->object_last_modified, \current_time( 'mysql' ) );
			$this->indexable_helper->save_indexable( $indexable );
		}
	}
}
