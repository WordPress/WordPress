<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\XML Sitemaps
 */

/**
 * Class that handles the Admin side of XML sitemaps.
 */
class WPSEO_Sitemaps_Admin {

	/**
	 * Post_types that are being imported.
	 *
	 * @var array
	 */
	private $importing_post_types = [];

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'transition_post_status', [ $this, 'status_transition' ], 10, 3 );
		add_action( 'admin_footer', [ $this, 'status_transition_bulk_finished' ] );

		WPSEO_Sitemaps_Cache::register_clear_on_option_update( 'wpseo_titles', '' );
		WPSEO_Sitemaps_Cache::register_clear_on_option_update( 'wpseo', '' );
	}

	/**
	 * Hooked into transition_post_status. Will initiate search engine pings
	 * if the post is being published, is a post type that a sitemap is built for
	 * and is a post that is included in sitemaps.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 *
	 * @return void
	 */
	public function status_transition( $new_status, $old_status, $post ) {
		if ( $new_status !== 'publish' ) {
			return;
		}

		if ( defined( 'WP_IMPORTING' ) ) {
			$this->status_transition_bulk( $new_status, $old_status, $post );

			return;
		}

		$post_type = get_post_type( $post );

		wp_cache_delete( 'lastpostmodified:gmt:' . $post_type, 'timeinfo' ); // #17455.
	}

	/**
	 * Notify Google of the updated sitemap.
	 *
	 * @deprecated 22.0
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function ping_search_engines() {
		_deprecated_function( __METHOD__, 'Yoast SEO 22.0' );
	}

	/**
	 * While bulk importing, just save unique post_types.
	 *
	 * When importing is done, if we have a post_type that is saved in the sitemap
	 * try to ping the search engines.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 *
	 * @return void
	 */
	private function status_transition_bulk( $new_status, $old_status, $post ) {
		$this->importing_post_types[] = get_post_type( $post );
		$this->importing_post_types   = array_unique( $this->importing_post_types );
	}

	/**
	 * After import finished, walk through imported post_types and update info.
	 *
	 * @return void
	 */
	public function status_transition_bulk_finished() {
		if ( ! defined( 'WP_IMPORTING' ) ) {
			return;
		}

		if ( empty( $this->importing_post_types ) ) {
			return;
		}

		$ping_search_engines = false;

		foreach ( $this->importing_post_types as $post_type ) {
			wp_cache_delete( 'lastpostmodified:gmt:' . $post_type, 'timeinfo' ); // #17455.

			// Just have the cache deleted for nav_menu_item.
			if ( $post_type === 'nav_menu_item' ) {
				continue;
			}

			if ( WPSEO_Options::get( 'noindex-' . $post_type, false ) === false ) {
				$ping_search_engines = true;
			}
		}

		// Nothing to do.
		if ( $ping_search_engines === false ) {
			return;
		}

		if ( WP_CACHE ) {
			do_action( 'wpseo_hit_sitemap_index' );
		}
	}
}
