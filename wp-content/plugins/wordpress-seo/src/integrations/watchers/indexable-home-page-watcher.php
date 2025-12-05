<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Builders\Indexable_Builder;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Repositories\Indexable_Repository;

/**
 * Home page watcher to save the meta data to an Indexable.
 *
 * Watches the home page options to save the meta information when updated.
 */
class Indexable_Home_Page_Watcher implements Integration_Interface {

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
	protected $indexable_helper;

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
	 * Indexable_Home_Page_Watcher constructor.
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
		\add_action( 'update_option_wpseo_titles', [ $this, 'check_option' ], 15, 3 );
		\add_action( 'update_option_wpseo_social', [ $this, 'check_option' ], 15, 3 );
		\add_action( 'update_option_blog_public', [ $this, 'build_indexable' ] );
		\add_action( 'update_option_blogdescription', [ $this, 'build_indexable' ] );
	}

	/**
	 * Checks if the home page indexable needs to be rebuild based on option values.
	 *
	 * @param array  $old_value The old value of the option.
	 * @param array  $new_value The new value of the option.
	 * @param string $option    The name of the option.
	 *
	 * @return void
	 */
	public function check_option( $old_value, $new_value, $option ) {
		$relevant_keys = [
			'wpseo_titles' => [
				'title-home-wpseo',
				'breadcrumbs-home',
				'metadesc-home-wpseo',
				'open_graph_frontpage_title',
				'open_graph_frontpage_desc',
				'open_graph_frontpage_image',
			],
		];

		if ( ! isset( $relevant_keys[ $option ] ) ) {
			return;
		}

		foreach ( $relevant_keys[ $option ] as $key ) {
			// If both values aren't set they haven't changed.
			if ( ! isset( $old_value[ $key ] ) && ! isset( $new_value[ $key ] ) ) {
				continue;
			}

			// If the value was set but now isn't, is set but wasn't or is not the same it has changed.
			if ( ! isset( $old_value[ $key ] ) || ! isset( $new_value[ $key ] ) || $old_value[ $key ] !== $new_value[ $key ] ) {
				$this->build_indexable();
				return;
			}
		}
	}

	/**
	 * Saves the home page.
	 *
	 * @return void
	 */
	public function build_indexable() {
		$indexable = $this->repository->find_for_home_page( false );

		if ( $indexable === false && ! $this->indexable_helper->should_index_indexables() ) {
			return;
		}

		$indexable = $this->builder->build_for_home_page( $indexable );

		if ( $indexable ) {
			$indexable->object_last_modified = \max( $indexable->object_last_modified, \current_time( 'mysql' ) );
			$indexable->save();
		}
	}
}
