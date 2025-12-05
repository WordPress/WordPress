<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use WPSEO_Meta;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * WordPress post meta watcher.
 */
class Indexable_Post_Meta_Watcher implements Integration_Interface {

	/**
	 * The post watcher.
	 *
	 * @var Indexable_Post_Watcher
	 */
	protected $post_watcher;

	/**
	 * An array of post IDs that need to be updated.
	 *
	 * @var array<int>
	 */
	protected $post_ids_to_update = [];

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return string[]
	 */
	public static function get_conditionals() {
		return [ Migrations_Conditional::class ];
	}

	/**
	 * Indexable_Postmeta_Watcher constructor.
	 *
	 * @param Indexable_Post_Watcher $post_watcher The post watcher.
	 */
	public function __construct( Indexable_Post_Watcher $post_watcher ) {
		$this->post_watcher = $post_watcher;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Register all posts whose meta have changed.
		\add_action( 'added_post_meta', [ $this, 'add_post_id' ], 10, 3 );
		\add_action( 'updated_post_meta', [ $this, 'add_post_id' ], 10, 3 );
		\add_action( 'deleted_post_meta', [ $this, 'add_post_id' ], 10, 3 );

		// Remove posts that get saved as they are handled by the Indexable_Post_Watcher.
		\add_action( 'wp_insert_post', [ $this, 'remove_post_id' ] );
		\add_action( 'delete_post', [ $this, 'remove_post_id' ] );
		\add_action( 'edit_attachment', [ $this, 'remove_post_id' ] );
		\add_action( 'add_attachment', [ $this, 'remove_post_id' ] );
		\add_action( 'delete_attachment', [ $this, 'remove_post_id' ] );

		// Update indexables of all registered posts.
		\register_shutdown_function( [ $this, 'update_indexables' ] );
	}

	/**
	 * Adds a post id to the array of posts to update.
	 *
	 * @param int|string $meta_id  The meta ID.
	 * @param int|string $post_id  The post ID.
	 * @param string     $meta_key The meta key.
	 *
	 * @return void
	 */
	public function add_post_id( $meta_id, $post_id, $meta_key ) {
		// Only register changes to our own meta.
		if ( \is_string( $meta_key ) && \strpos( $meta_key, WPSEO_Meta::$meta_prefix ) !== 0 ) {
			return;
		}

		if ( ! \in_array( $post_id, $this->post_ids_to_update, true ) ) {
			$this->post_ids_to_update[] = (int) $post_id;
		}
	}

	/**
	 * Removes a post id from the array of posts to update.
	 *
	 * @param int|string $post_id The post ID.
	 *
	 * @return void
	 */
	public function remove_post_id( $post_id ) {
		$this->post_ids_to_update = \array_diff( $this->post_ids_to_update, [ (int) $post_id ] );
	}

	/**
	 * Updates all indexables changed during the request.
	 *
	 * @return void
	 */
	public function update_indexables() {
		foreach ( $this->post_ids_to_update as $post_id ) {
			$this->post_watcher->build_indexable( $post_id );
		}
	}
}
