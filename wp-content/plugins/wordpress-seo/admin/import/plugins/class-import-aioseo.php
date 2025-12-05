<?php
/**
 * File with the class to handle data from All in One SEO Pack, versions 3 and under.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean All in One SEO Pack post metadata, versions 3 and under.
 */
class WPSEO_Import_AIOSEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'All In One SEO Pack';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_aioseop_%';

	/**
	 * OpenGraph keys to import.
	 *
	 * @var array
	 */
	protected $import_keys = [
		'aioseop_opengraph_settings_title'             => 'opengraph-title',
		'aioseop_opengraph_settings_desc'              => 'opengraph-description',
		'aioseop_opengraph_settings_customimg'         => 'opengraph-image',
		'aioseop_opengraph_settings_customimg_twitter' => 'twitter-image',
	];

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_aioseop_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_aioseop_description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_aioseop_noindex',
			'new_key' => 'meta-robots-noindex',
			'convert' => [ 'on' => 1 ],
		],
		[
			'old_key' => '_aioseop_nofollow',
			'new_key' => 'meta-robots-nofollow',
			'convert' => [ 'on' => 1 ],
		],
	];

	/**
	 * Import All In One SEO meta values.
	 *
	 * @return bool Import success status.
	 */
	protected function import() {
		$status = parent::import();
		if ( $status ) {
			$this->import_opengraph();
		}
		return $status;
	}

	/**
	 * Imports the OpenGraph and Twitter settings for all posts.
	 *
	 * @return bool
	 */
	protected function import_opengraph() {
		$query_posts = new WP_Query( 'post_type=any&meta_key=_aioseop_opengraph_settings&order=ASC&fields=ids&nopaging=true' );

		if ( ! empty( $query_posts->posts ) ) {
			foreach ( array_values( $query_posts->posts ) as $post_id ) {
				$this->import_post_opengraph( $post_id );
			}
		}

		return true;
	}

	/**
	 * Imports the OpenGraph and Twitter settings for a single post.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	private function import_post_opengraph( $post_id ) {
		$meta = get_post_meta( $post_id, '_aioseop_opengraph_settings', true );
		$meta = maybe_unserialize( $meta );

		foreach ( $this->import_keys as $old_key => $new_key ) {
			$this->maybe_save_post_meta( $new_key, $meta[ $old_key ], $post_id );
		}
	}
}
