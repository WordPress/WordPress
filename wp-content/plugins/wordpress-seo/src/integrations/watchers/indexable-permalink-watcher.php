<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Config\Indexing_Reasons;
use Yoast\WP\SEO\Helpers\Indexable_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Post_Type_Helper;
use Yoast\WP\SEO\Helpers\Taxonomy_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * WordPress Permalink structure watcher.
 *
 * Handles updates to the permalink_structure for the Indexables table.
 */
class Indexable_Permalink_Watcher implements Integration_Interface {

	/**
	 * Represents the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The taxonomy helper.
	 *
	 * @var Taxonomy_Helper
	 */
	protected $taxonomy_helper;

	/**
	 * The post type helper.
	 *
	 * @var Post_Type_Helper
	 */
	private $post_type;

	/**
	 * The indexable helper.
	 *
	 * @var Indexable_Helper
	 */
	protected $indexable_helper;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Permalink_Watcher constructor.
	 *
	 * @param Post_Type_Helper $post_type       The post type helper.
	 * @param Options_Helper   $options         The options helper.
	 * @param Indexable_Helper $indexable       The indexable helper.
	 * @param Taxonomy_Helper  $taxonomy_helper The taxonomy helper.
	 */
	public function __construct( Post_Type_Helper $post_type, Options_Helper $options, Indexable_Helper $indexable, Taxonomy_Helper $taxonomy_helper ) {
		$this->post_type        = $post_type;
		$this->options_helper   = $options;
		$this->indexable_helper = $indexable;
		$this->taxonomy_helper  = $taxonomy_helper;

		$this->schedule_cron();
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_permalink_structure', [ $this, 'reset_permalinks' ] );
		\add_action( 'update_option_category_base', [ $this, 'reset_permalinks_term' ], 10, 3 );
		\add_action( 'update_option_tag_base', [ $this, 'reset_permalinks_term' ], 10, 3 );
		\add_action( 'wpseo_permalink_structure_check', [ $this, 'force_reset_permalinks' ] );
		\add_action( 'wpseo_deactivate', [ $this, 'unschedule_cron' ] );
	}

	/**
	 * Resets the permalinks for everything that is related to the permalink structure.
	 *
	 * @return void
	 */
	public function reset_permalinks() {

		$post_types = $this->get_post_types();
		foreach ( $post_types as $post_type ) {
			$this->reset_permalinks_post_type( $post_type );
		}

		$taxonomies = $this->get_taxonomies_for_post_types( $post_types );
		foreach ( $taxonomies as $taxonomy ) {
			$this->indexable_helper->reset_permalink_indexables( 'term', $taxonomy );
		}

		$this->indexable_helper->reset_permalink_indexables( 'user' );
		$this->indexable_helper->reset_permalink_indexables( 'date-archive' );
		$this->indexable_helper->reset_permalink_indexables( 'system-page' );

		// Always update `permalink_structure` in the wpseo option.
		$this->options_helper->set( 'permalink_structure', \get_option( 'permalink_structure' ) );
	}

	/**
	 * Resets the permalink for the given post type.
	 *
	 * @param string $post_type The post type to reset.
	 *
	 * @return void
	 */
	public function reset_permalinks_post_type( $post_type ) {
		$this->indexable_helper->reset_permalink_indexables( 'post', $post_type );
		$this->indexable_helper->reset_permalink_indexables( 'post-type-archive', $post_type );
	}

	/**
	 * Resets the term indexables when the base has been changed.
	 *
	 * @param string $old_value Unused. The old option value.
	 * @param string $new_value Unused. The new option value.
	 * @param string $type      The option name.
	 *
	 * @return void
	 */
	public function reset_permalinks_term( $old_value, $new_value, $type ) {
		$subtype = $type;

		$reason = Indexing_Reasons::REASON_PERMALINK_SETTINGS;

		// When the subtype contains _base, just strip it.
		if ( \strstr( $subtype, '_base' ) ) {
			$subtype = \substr( $type, 0, -5 );
		}

		if ( $subtype === 'tag' ) {
			$subtype = 'post_tag';
			$reason  = Indexing_Reasons::REASON_TAG_BASE_PREFIX;
		}

		if ( $subtype === 'category' ) {
			$reason = Indexing_Reasons::REASON_CATEGORY_BASE_PREFIX;
		}

		$this->indexable_helper->reset_permalink_indexables( 'term', $subtype, $reason );
	}

	/**
	 * Resets the permalink indexables automatically, if necessary.
	 *
	 * @return bool Whether the reset request ran.
	 */
	public function force_reset_permalinks() {
		if ( \get_option( 'tag_base' ) !== $this->options_helper->get( 'tag_base_url' ) ) {
			$this->reset_permalinks_term( null, null, 'tag_base' );
			$this->options_helper->set( 'tag_base_url', \get_option( 'tag_base' ) );
		}
		if ( \get_option( 'category_base' ) !== $this->options_helper->get( 'category_base_url' ) ) {
			$this->reset_permalinks_term( null, null, 'category_base' );
			$this->options_helper->set( 'category_base_url', \get_option( 'category_base' ) );
		}

		if ( $this->should_reset_permalinks() ) {
			$this->reset_permalinks();

			return true;
		}

		$this->reset_altered_custom_taxonomies();

		return true;
	}

	/**
	 * Checks whether the permalinks should be reset after `permalink_structure` has changed.
	 *
	 * @return bool Whether the permalinks should be reset.
	 */
	public function should_reset_permalinks() {
		return \get_option( 'permalink_structure' ) !== $this->options_helper->get( 'permalink_structure' );
	}

	/**
	 * Resets custom taxonomies if their slugs have changed.
	 *
	 * @return void
	 */
	public function reset_altered_custom_taxonomies() {
		$taxonomies            = $this->taxonomy_helper->get_custom_taxonomies();
		$custom_taxonomy_bases = $this->options_helper->get( 'custom_taxonomy_slugs', [] );
		$new_taxonomy_bases    = [];

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_slug = $this->taxonomy_helper->get_taxonomy_slug( $taxonomy );

			$new_taxonomy_bases[ $taxonomy ] = $taxonomy_slug;

			if ( ! \array_key_exists( $taxonomy, $custom_taxonomy_bases ) ) {
				continue;
			}

			if ( $taxonomy_slug !== $custom_taxonomy_bases[ $taxonomy ] ) {
				$this->indexable_helper->reset_permalink_indexables( 'term', $taxonomy );
			}
		}

		$this->options_helper->set( 'custom_taxonomy_slugs', $new_taxonomy_bases );
	}

	/**
	 * Retrieves a list with the public post types.
	 *
	 * @return array The post types.
	 */
	protected function get_post_types() {
		/**
		 * Filter: Gives the possibility to filter out post types.
		 *
		 * @param array $post_types The post type names.
		 *
		 * @return array The post types.
		 */
		$post_types = \apply_filters( 'wpseo_post_types_reset_permalinks', $this->post_type->get_public_post_types() );

		return $post_types;
	}

	/**
	 * Retrieves the taxonomies that belongs to the public post types.
	 *
	 * @param array $post_types The post types to get taxonomies for.
	 *
	 * @return array The retrieved taxonomies.
	 */
	protected function get_taxonomies_for_post_types( $post_types ) {
		$taxonomies = [];
		foreach ( $post_types as $post_type ) {
			$taxonomies[] = \get_object_taxonomies( $post_type, 'names' );
		}

		$taxonomies = \array_merge( [], ...$taxonomies );
		$taxonomies = \array_unique( $taxonomies );

		return $taxonomies;
	}

	/**
	 * Schedules the WP-Cron job to check the permalink_structure status.
	 *
	 * @return void
	 */
	protected function schedule_cron() {
		if ( \wp_next_scheduled( 'wpseo_permalink_structure_check' ) ) {
			return;
		}

		\wp_schedule_event( \time(), 'daily', 'wpseo_permalink_structure_check' );
	}

	/**
	 * Unschedules the WP-Cron job to check the permalink_structure status.
	 *
	 * @return void
	 */
	public function unschedule_cron() {
		if ( ! \wp_next_scheduled( 'wpseo_permalink_structure_check' ) ) {
			return;
		}

		\wp_clear_scheduled_hook( 'wpseo_permalink_structure_check' );
	}
}
