<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Tracking
 */

/**
 * Represents the default data.
 */
class WPSEO_Tracking_Default_Data implements WPSEO_Collection {

	/**
	 * Returns the collection data.
	 *
	 * @return array The collection data.
	 */
	public function get() {
		return [
			'siteTitle'       => get_option( 'blogname' ),
			'@timestamp'      => (int) gmdate( 'Uv' ),
			'wpVersion'       => $this->get_wordpress_version(),
			'homeURL'         => home_url(),
			'adminURL'        => admin_url(),
			'isMultisite'     => is_multisite(),
			'siteLanguage'    => get_bloginfo( 'language' ),
			'gmt_offset'      => get_option( 'gmt_offset' ),
			'timezoneString'  => get_option( 'timezone_string' ),
			'migrationStatus' => get_option( 'yoast_migrations_free' ),
			'countPosts'      => $this->get_post_count( 'post' ),
			'countPages'      => $this->get_post_count( 'page' ),
		];
	}

	/**
	 * Returns the number of posts of a certain type.
	 *
	 * @param string $post_type The post type return the count for.
	 *
	 * @return int The count for this post type.
	 */
	protected function get_post_count( $post_type ) {
		$count = wp_count_posts( $post_type );
		if ( isset( $count->publish ) ) {
			return $count->publish;
		}
		return 0;
	}

	/**
	 * Returns the WordPress version.
	 *
	 * @return string The version.
	 */
	protected function get_wordpress_version() {
		global $wp_version;

		return $wp_version;
	}
}
