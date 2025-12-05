<?php
/**
 * File with the class to handle data from Smartcrawl SEO.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class with functionality to import & clean Smartcrawl SEO post metadata.
 */
class WPSEO_Import_Smartcrawl_SEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'Smartcrawl SEO';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_wds_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_wds_metadesc',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_wds_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_wds_canonical',
			'new_key' => 'canonical',
		],
		[
			'old_key' => '_wds_focus-keywords',
			'new_key' => 'focuskw',
		],
		[
			'old_key' => '_wds_meta-robots-noindex',
			'new_key' => 'meta-robots-noindex',
		],
		[
			'old_key' => '_wds_meta-robots-nofollow',
			'new_key' => 'meta-robots-nofollow',
		],
	];

	/**
	 * Used for importing Twitter and Facebook meta's.
	 *
	 * @var array
	 */
	protected $social_keys = [];

	/**
	 * Handles post meta data to import.
	 *
	 * @return bool Import success status.
	 */
	protected function import() {
		$return = parent::import();
		if ( $return ) {
			$this->import_opengraph();
			$this->import_twitter();
		}

		return $return;
	}

	/**
	 * Imports the OpenGraph meta keys saved by Smartcrawl.
	 *
	 * @return bool Import status.
	 */
	protected function import_opengraph() {
		$this->social_keys = [
			'title'       => 'opengraph-title',
			'description' => 'opengraph-description',
			'images'      => 'opengraph-image',
		];
		return $this->post_find_import( '_wds_opengraph' );
	}

	/**
	 * Imports the Twitter meta keys saved by Smartcrawl.
	 *
	 * @return bool Import status.
	 */
	protected function import_twitter() {
		$this->social_keys = [
			'title'       => 'twitter-title',
			'description' => 'twitter-description',
		];
		return $this->post_find_import( '_wds_twitter' );
	}

	/**
	 * Imports a post's serialized post meta values.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     The meta key to import.
	 *
	 * @return void
	 */
	protected function import_serialized_post_meta( $post_id, $key ) {
		$data = get_post_meta( $post_id, $key, true );
		$data = maybe_unserialize( $data );
		foreach ( $this->social_keys as $key => $meta_key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return;
			}
			$value = $data[ $key ];
			if ( is_array( $value ) ) {
				$value = $value[0];
			}
			$this->maybe_save_post_meta( $meta_key, $value, $post_id );
		}
	}

	/**
	 * Finds all the posts with a certain meta key and imports its values.
	 *
	 * @param string $key The meta key to search for.
	 *
	 * @return bool Import status.
	 */
	protected function post_find_import( $key ) {
		$query_posts = new WP_Query( 'post_type=any&meta_key=' . $key . '&order=ASC&fields=ids&nopaging=true' );

		if ( empty( $query_posts->posts ) ) {
			return false;
		}

		foreach ( array_values( $query_posts->posts ) as $post_id ) {
			$this->import_serialized_post_meta( $post_id, $key );
		}

		return true;
	}
}
