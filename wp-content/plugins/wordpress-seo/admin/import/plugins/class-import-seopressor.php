<?php
/**
 * File with the class to handle data from SEOPressor.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Import_SEOPressor.
 *
 * Class with functionality to import & clean SEOPressor post metadata.
 */
class WPSEO_Import_SEOPressor extends WPSEO_Plugin_Importer {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = 'SEOpressor';

	/**
	 * Meta key, used in SQL LIKE clause for delete query.
	 *
	 * @var string
	 */
	protected $meta_key = '_seop_settings';

	/**
	 * Array of meta keys to detect and import.
	 *
	 * @var array
	 */
	protected $clone_keys = [
		[
			'old_key' => '_seop_settings',
		],
	];

	/**
	 * Imports the post meta values to Yoast SEO.
	 *
	 * @return bool Import success status.
	 */
	protected function import() {
		// Query for all the posts that have an _seop_settings meta set.
		$query_posts = new WP_Query( 'post_type=any&meta_key=_seop_settings&order=ASC&fields=ids&nopaging=true' );
		foreach ( $query_posts->posts as $post_id ) {
			$this->import_post_focus_keywords( $post_id );
			$this->import_seopressor_post_settings( $post_id );
		}

		return true;
	}

	/**
	 * Removes all the post meta fields SEOpressor creates.
	 *
	 * @return bool Cleanup status.
	 */
	protected function cleanup() {
		global $wpdb;

		// If we get to replace the data, let's do some proper cleanup.
		return $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_seop_%'" );
	}

	/**
	 * Imports the data. SEOpressor stores most of the data in one post array, this loops over it.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	private function import_seopressor_post_settings( $post_id ) {
		$settings = get_post_meta( $post_id, '_seop_settings', true );

		foreach (
			[
				'fb_description'   => 'opengraph-description',
				'fb_title'         => 'opengraph-title',
				'fb_type'          => 'og_type',
				'fb_img'           => 'opengraph-image',
				'meta_title'       => 'title',
				'meta_description' => 'metadesc',
				'meta_canonical'   => 'canonical',
				'tw_description'   => 'twitter-description',
				'tw_title'         => 'twitter-title',
				'tw_image'         => 'twitter-image',
			] as $seopressor_key => $yoast_key ) {
			$this->import_meta_helper( $seopressor_key, $yoast_key, $settings, $post_id );
		}

		if ( isset( $settings['meta_rules'] ) ) {
			$this->import_post_robots( $settings['meta_rules'], $post_id );
		}
	}

	/**
	 * Imports the focus keywords, and stores them for later use.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	private function import_post_focus_keywords( $post_id ) {
		// Import the focus keyword.
		$focuskw = trim( get_post_meta( $post_id, '_seop_kw_1', true ) );
		$this->maybe_save_post_meta( 'focuskw', $focuskw, $post_id );

		// Import additional focus keywords for use in premium.
		$focuskw2 = trim( get_post_meta( $post_id, '_seop_kw_2', true ) );
		$focuskw3 = trim( get_post_meta( $post_id, '_seop_kw_3', true ) );

		$focus_keywords = [];
		if ( ! empty( $focuskw2 ) ) {
			$focus_keywords[] = $focuskw2;
		}
		if ( ! empty( $focuskw3 ) ) {
			$focus_keywords[] = $focuskw3;
		}

		if ( $focus_keywords !== [] ) {
			$this->maybe_save_post_meta( 'focuskeywords', WPSEO_Utils::format_json_encode( $focus_keywords ), $post_id );
		}
	}

	/**
	 * Retrieves the SEOpressor robot value and map this to Yoast SEO values.
	 *
	 * @param string $meta_rules The meta rules taken from the SEOpressor settings array.
	 * @param int    $post_id    The post id of the current post.
	 *
	 * @return void
	 */
	private function import_post_robots( $meta_rules, $post_id ) {
		$seopressor_robots = explode( '#|#|#', $meta_rules );
		$robot_value       = $this->get_robot_value( $seopressor_robots );

		// Saving the new meta values for Yoast SEO.
		$this->maybe_save_post_meta( 'meta-robots-noindex', $robot_value['index'], $post_id );
		$this->maybe_save_post_meta( 'meta-robots-nofollow', $robot_value['follow'], $post_id );
		$this->maybe_save_post_meta( 'meta-robots-adv', $robot_value['advanced'], $post_id );
	}

	/**
	 * Gets the robot config by given SEOpressor robots value.
	 *
	 * @param array $seopressor_robots The value in SEOpressor that needs to be converted to the Yoast format.
	 *
	 * @return array The robots values in Yoast format.
	 */
	private function get_robot_value( $seopressor_robots ) {
		$return = [
			'index'    => 2,
			'follow'   => 0,
			'advanced' => '',
		];

		if ( in_array( 'noindex', $seopressor_robots, true ) ) {
			$return['index'] = 1;
		}
		if ( in_array( 'nofollow', $seopressor_robots, true ) ) {
			$return['follow'] = 1;
		}
		foreach ( [ 'noarchive', 'nosnippet', 'noimageindex' ] as $needle ) {
			if ( in_array( $needle, $seopressor_robots, true ) ) {
				$return['advanced'] .= $needle . ',';
			}
		}
		$return['advanced'] = rtrim( $return['advanced'], ',' );

		return $return;
	}
}
