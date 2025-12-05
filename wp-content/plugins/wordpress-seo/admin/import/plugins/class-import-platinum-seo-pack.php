<?php
/**
 * File with the class to handle data from Platinum SEO Pack.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean Ultimate SEO post metadata.
 */
class WPSEO_Import_Platinum_SEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Platinum SEO Pack';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = 'title';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => 'description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => 'title',
			'new_key' => 'title',
		],
	];

	/**
	 * Runs the import of post meta keys stored by Platinum SEO Pack.
	 *
	 * @return bool
	 */
	protected function import() {
		$return = parent::import();
		if ( $return ) {
			$this->import_robots_meta();
		}

		return $return;
	}

	/**
	 * Cleans up all the meta values Platinum SEO pack creates.
	 *
	 * @return bool
	 */
	protected function cleanup() {
		$this->meta_key = 'title';
		parent::cleanup();

		$this->meta_key = 'description';
		parent::cleanup();

		$this->meta_key = 'metarobots';
		parent::cleanup();

		return true;
	}

	/**
	 * Finds all the robotsmeta fields to import and deals with them.
	 *
	 * There are four potential values that Platinum SEO stores:
	 * - index,folllow
	 * - index,nofollow
	 * - noindex,follow
	 * - noindex,nofollow
	 *
	 * We only have to deal with the latter 3, the first is our default.
	 *
	 * @return void
	 */
	protected function import_robots_meta() {
		$this->import_by_meta_robots( 'index,nofollow', [ 'nofollow' ] );
		$this->import_by_meta_robots( 'noindex,follow', [ 'noindex' ] );
		$this->import_by_meta_robots( 'noindex,nofollow', [ 'noindex', 'nofollow' ] );
	}

	/**
	 * Imports the values for all index, nofollow posts.
	 *
	 * @param string $value The meta robots value to find posts for.
	 * @param array  $metas The meta field(s) to save.
	 *
	 * @return void
	 */
	protected function import_by_meta_robots( $value, $metas ) {
		$posts = $this->find_posts_by_robots_meta( $value );
		if ( ! $posts ) {
			return;
		}

		foreach ( $posts as $post_id ) {
			foreach ( $metas as $meta ) {
				$this->maybe_save_post_meta( 'meta-robots-' . $meta, 1, $post_id );
			}
		}
	}

	/**
	 * Finds posts by a given meta robots value.
	 *
	 * @param string $meta_value Robots meta value.
	 *
	 * @return array|bool Array of Post IDs on success, false on failure.
	 */
	protected function find_posts_by_robots_meta( $meta_value ) {
		$posts = get_posts(
			[
				'post_type'  => 'any',
				'meta_key'   => 'robotsmeta',
				'meta_value' => $meta_value,
				'order'      => 'ASC',
				'fields'     => 'ids',
				'nopaging'   => true,
			]
		);
		if ( empty( $posts ) ) {
			return false;
		}
		return $posts;
	}
}
