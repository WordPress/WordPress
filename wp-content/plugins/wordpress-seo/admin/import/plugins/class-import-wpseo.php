<?php
/**
 * File with the class to handle data from wpSEO.de.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Import_WPSEO.
 *
 * Class with functionality to import & clean wpSEO.de post metadata.
 */
class WPSEO_Import_WPSEO extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'wpSEO.de';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_wpseo_edit_%';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_wpseo_edit_description',
			'new_key' => 'metadesc',
		],
		[
			'old_key' => '_wpseo_edit_title',
			'new_key' => 'title',
		],
		[
			'old_key' => '_wpseo_edit_canonical',
			'new_key' => 'canonical',
		],
		[
			'old_key' => '_wpseo_edit_og_title',
			'new_key' => 'opengraph-title',
		],
		[
			'old_key' => '_wpseo_edit_og_description',
			'new_key' => 'opengraph-description',
		],
		[
			'old_key' => '_wpseo_edit_og_image',
			'new_key' => 'opengraph-image',
		],
		[
			'old_key' => '_wpseo_edit_twittercard_title',
			'new_key' => 'twitter-title',
		],
		[
			'old_key' => '_wpseo_edit_twittercard_description',
			'new_key' => 'twitter-description',
		],
		[
			'old_key' => '_wpseo_edit_twittercard_image',
			'new_key' => 'twitter-image',
		],
	];

	/**
	 * The values 1 - 6 are the configured values from wpSEO. This array will map the values of wpSEO to our values.
	 *
	 * There are some double array like 1-6 and 3-4. The reason is they only set the index value. The follow value is
	 * the default we use in the cases there isn't a follow value present.
	 *
	 * @var array
	 */
	private $robot_values = [
		// In wpSEO: index, follow.
		1 => [
			'index'  => 2,
			'follow' => 0,
		],
		// In wpSEO: index, nofollow.
		2 => [
			'index'  => 2,
			'follow' => 1,
		],
		// In wpSEO: noindex.
		3 => [
			'index'  => 1,
			'follow' => 0,
		],
		// In wpSEO: noindex, follow.
		4 => [
			'index'  => 1,
			'follow' => 0,
		],
		// In wpSEO: noindex, nofollow.
		5 => [
			'index'  => 1,
			'follow' => 1,
		],
		// In wpSEO: index.
		6 => [
			'index'  => 2,
			'follow' => 0,
		],
	];

	/**
	 * Imports wpSEO settings.
	 *
	 * @return bool Import success status.
	 */
	protected function import() {
		$status = parent::import();
		if ( $status ) {
			$this->import_post_robots();
			$this->import_taxonomy_metas();
		}

		return $status;
	}

	/**
	 * Removes wpseo.de post meta's.
	 *
	 * @return bool Cleanup status.
	 */
	protected function cleanup() {
		$this->cleanup_term_meta();
		$result = $this->cleanup_post_meta();
		return $result;
	}

	/**
	 * Detects whether there is post meta data to import.
	 *
	 * @return bool Boolean indicating whether there is something to import.
	 */
	protected function detect() {
		if ( parent::detect() ) {
			return true;
		}

		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE 'wpseo_category_%'" );
		if ( $count !== '0' ) {
			return true;
		}

		return false;
	}

	/**
	 * Imports the robot values from WPSEO plugin. These have to be converted to the Yoast format.
	 *
	 * @return void
	 */
	private function import_post_robots() {
		$query_posts = new WP_Query( 'post_type=any&meta_key=_wpseo_edit_robots&order=ASC&fields=ids&nopaging=true' );

		if ( ! empty( $query_posts->posts ) ) {
			foreach ( array_values( $query_posts->posts ) as $post_id ) {
				$this->import_post_robot( $post_id );
			}
		}
	}

	/**
	 * Gets the wpSEO robot value and map this to Yoast SEO values.
	 *
	 * @param int $post_id The post id of the current post.
	 *
	 * @return void
	 */
	private function import_post_robot( $post_id ) {
		$wpseo_robots = get_post_meta( $post_id, '_wpseo_edit_robots', true );
		$robot_value  = $this->get_robot_value( $wpseo_robots );

		// Saving the new meta values for Yoast SEO.
		$this->maybe_save_post_meta( 'meta-robots-noindex', $robot_value['index'], $post_id );
		$this->maybe_save_post_meta( 'meta-robots-nofollow', $robot_value['follow'], $post_id );
	}

	/**
	 * Imports the taxonomy metas from wpSEO.
	 *
	 * @return void
	 */
	private function import_taxonomy_metas() {
		$terms    = get_terms(
			[
				'taxonomy'   => get_taxonomies(),
				'hide_empty' => false,
			]
		);
		$tax_meta = get_option( 'wpseo_taxonomy_meta' );

		foreach ( $terms as $term ) {
			$this->import_taxonomy_description( $tax_meta, $term->taxonomy, $term->term_id );
			$this->import_taxonomy_robots( $tax_meta, $term->taxonomy, $term->term_id );
		}

		update_option( 'wpseo_taxonomy_meta', $tax_meta );
	}

	/**
	 * Imports the meta description to Yoast SEO.
	 *
	 * @param array  $tax_meta The array with the current metadata.
	 * @param string $taxonomy String with the name of the taxonomy.
	 * @param string $term_id  The ID of the current term.
	 *
	 * @return void
	 */
	private function import_taxonomy_description( &$tax_meta, $taxonomy, $term_id ) {
		$description = get_option( 'wpseo_' . $taxonomy . '_' . $term_id, false );
		if ( $description !== false ) {
			// Import description.
			$tax_meta[ $taxonomy ][ $term_id ]['wpseo_desc'] = $description;
		}
	}

	/**
	 * Imports the robot value to Yoast SEO.
	 *
	 * @param array  $tax_meta The array with the current metadata.
	 * @param string $taxonomy String with the name of the taxonomy.
	 * @param string $term_id  The ID of the current term.
	 *
	 * @return void
	 */
	private function import_taxonomy_robots( &$tax_meta, $taxonomy, $term_id ) {
		$wpseo_robots = get_option( 'wpseo_' . $taxonomy . '_' . $term_id . '_robots', false );
		if ( $wpseo_robots === false ) {
			return;
		}
		// The value 1, 2 and 6 are the index values in wpSEO.
		$new_robot_value = 'noindex';

		if ( in_array( (int) $wpseo_robots, [ 1, 2, 6 ], true ) ) {
			$new_robot_value = 'index';
		}

		$tax_meta[ $taxonomy ][ $term_id ]['wpseo_noindex'] = $new_robot_value;
	}

	/**
	 * Deletes the wpSEO taxonomy meta data.
	 *
	 * @param string $taxonomy String with the name of the taxonomy.
	 * @param string $term_id  The ID of the current term.
	 *
	 * @return void
	 */
	private function delete_taxonomy_metas( $taxonomy, $term_id ) {
		delete_option( 'wpseo_' . $taxonomy . '_' . $term_id );
		delete_option( 'wpseo_' . $taxonomy . '_' . $term_id . '_robots' );
	}

	/**
	 * Gets the robot config by given wpSEO robots value.
	 *
	 * @param string $wpseo_robots The value in wpSEO that needs to be converted to the Yoast format.
	 *
	 * @return string The correct robot value.
	 */
	private function get_robot_value( $wpseo_robots ) {
		if ( array_key_exists( $wpseo_robots, $this->robot_values ) ) {
			return $this->robot_values[ $wpseo_robots ];
		}

		return $this->robot_values[1];
	}

	/**
	 * Deletes wpSEO postmeta from the database.
	 *
	 * @return bool Cleanup status.
	 */
	private function cleanup_post_meta() {
		global $wpdb;

		// If we get to replace the data, let's do some proper cleanup.
		return $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_wpseo_edit_%'" );
	}

	/**
	 * Cleans up the wpSEO term meta.
	 *
	 * @return void
	 */
	private function cleanup_term_meta() {
		$terms = get_terms(
			[
				'taxonomy'   => get_taxonomies(),
				'hide_empty' => false,
			]
		);

		foreach ( $terms as $term ) {
			$this->delete_taxonomy_metas( $term->taxonomy, $term->term_id );
		}
	}
}
