<?php
/**
 * WP_Navigation_Fallback class
 *
 * Manages fallback behavior for Navigation menus.
 *
 * @package WordPress
 * @subpackage Navigation
 * @since 6.3.0
 */

/**
 * Manages fallback behavior for Navigation menus.
 *
 * @access public
 * @since 6.3.0
 */
class WP_Navigation_Fallback {

	/**
	 * Gets (and/or creates) an appropriate fallback Navigation Menu.
	 *
	 * @since 6.3.0
	 *
	 * @return WP_Post|null the fallback Navigation Post or null.
	 */
	public static function get_fallback() {

		/**
		 * Filters whether or not a fallback should be created.
		 *
		 * @since 6.3.0
		 *
		 * @param bool Whether to create a fallback navigation menu. Default true.
		 */
		$should_create_fallback = apply_filters( 'wp_navigation_should_create_fallback', true );

		$fallback = static::get_most_recently_published_navigation();

		if ( $fallback || ! $should_create_fallback ) {
			return $fallback;
		}

		$fallback = static::create_classic_menu_fallback();

		if ( $fallback && ! is_wp_error( $fallback ) ) {
			// Return the newly created fallback post object which will now be the most recently created navigation menu.
			return $fallback instanceof WP_Post ? $fallback : static::get_most_recently_published_navigation();
		}

		$fallback = static::create_default_fallback();

		if ( $fallback && ! is_wp_error( $fallback ) ) {
			// Return the newly created fallback post object which will now be the most recently created navigation menu.
			return $fallback instanceof WP_Post ? $fallback : static::get_most_recently_published_navigation();
		}

		return null;
	}

	/**
	 * Finds the most recently published `wp_navigation` post type.
	 *
	 * @since 6.3.0
	 *
	 * @return WP_Post|null the first non-empty Navigation or null.
	 */
	private static function get_most_recently_published_navigation() {

		$parsed_args = array(
			'post_type'              => 'wp_navigation',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'order'                  => 'DESC',
			'orderby'                => 'date',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
		);

		$navigation_post = new WP_Query( $parsed_args );

		if ( count( $navigation_post->posts ) > 0 ) {
			return $navigation_post->posts[0];
		}

		return null;
	}

	/**
	 * Creates a Navigation Menu post from a Classic Menu.
	 *
	 * @since 6.3.0
	 *
	 * @return int|WP_Error The post ID of the default fallback menu or a WP_Error object.
	 */
	private static function create_classic_menu_fallback() {
		// See if we have a classic menu.
		$classic_nav_menu = static::get_fallback_classic_menu();

		if ( ! $classic_nav_menu ) {
			return new WP_Error( 'no_classic_menus', __( 'No Classic Menus found.' ) );
		}

		// If there is a classic menu then convert it to blocks.
		$classic_nav_menu_blocks = WP_Classic_To_Block_Menu_Converter::convert( $classic_nav_menu );

		if ( is_wp_error( $classic_nav_menu_blocks ) ) {
			return $classic_nav_menu_blocks;
		}

		if ( empty( $classic_nav_menu_blocks ) ) {
			return new WP_Error( 'cannot_convert_classic_menu', __( 'Unable to convert Classic Menu to blocks.' ) );
		}

		// Create a new navigation menu from the classic menu.
		$classic_menu_fallback = wp_insert_post(
			array(
				'post_content' => $classic_nav_menu_blocks,
				'post_title'   => $classic_nav_menu->name,
				'post_name'    => $classic_nav_menu->slug,
				'post_status'  => 'publish',
				'post_type'    => 'wp_navigation',
			),
			true // So that we can check whether the result is an error.
		);

		return $classic_menu_fallback;
	}

	/**
	 * Determines the most appropriate classic navigation menu to use as a fallback.
	 *
	 * @since 6.3.0
	 *
	 * @return WP_Term|null The most appropriate classic navigation menu to use as a fallback.
	 */
	private static function get_fallback_classic_menu() {
		$classic_nav_menus = wp_get_nav_menus();

		if ( ! $classic_nav_menus || is_wp_error( $classic_nav_menus ) ) {
			return null;
		}

		$nav_menu = static::get_nav_menu_at_primary_location();

		if ( $nav_menu ) {
			return $nav_menu;
		}

		$nav_menu = static::get_nav_menu_with_primary_slug( $classic_nav_menus );

		if ( $nav_menu ) {
			return $nav_menu;
		}

		return static::get_most_recently_created_nav_menu( $classic_nav_menus );
	}


	/**
	 * Sorts the classic menus and returns the most recently created one.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_Term[] $classic_nav_menus Array of classic nav menu term objects.
	 * @return WP_Term The most recently created classic nav menu.
	 */
	private static function get_most_recently_created_nav_menu( $classic_nav_menus ) {
		usort(
			$classic_nav_menus,
			static function( $a, $b ) {
				return $b->term_id - $a->term_id;
			}
		);

		return $classic_nav_menus[0];
	}

	/**
	 * Returns the classic menu with the slug `primary` if it exists.
	 *
	 * @since 6.3.0
	 *
	 * @param WP_Term[] $classic_nav_menus Array of classic nav menu term objects.
	 * @return WP_Term|null The classic nav menu with the slug `primary` or null.
	 */
	private static function get_nav_menu_with_primary_slug( $classic_nav_menus ) {
		foreach ( $classic_nav_menus as $classic_nav_menu ) {
			if ( 'primary' === $classic_nav_menu->slug ) {
				return $classic_nav_menu;
			}
		}

		return null;
	}


	/**
	 * Gets the classic menu assigned to the `primary` navigation menu location
	 * if it exists.
	 *
	 * @since 6.3.0
	 *
	 * @return WP_Term|null The classic nav menu assigned to the `primary` location or null.
	 */
	private static function get_nav_menu_at_primary_location() {
		$locations = get_nav_menu_locations();

		if ( isset( $locations['primary'] ) ) {
			$primary_menu = wp_get_nav_menu_object( $locations['primary'] );

			if ( $primary_menu ) {
				return $primary_menu;
			}
		}

		return null;
	}

	/**
	 * Creates a default Navigation Block Menu fallback.
	 *
	 * @since 6.3.0
	 *
	 * @return int|WP_Error The post ID of the default fallback menu or a WP_Error object.
	 */
	private static function create_default_fallback() {

		$default_blocks = static::get_default_fallback_blocks();

		// Create a new navigation menu from the fallback blocks.
		$default_fallback = wp_insert_post(
			array(
				'post_content' => $default_blocks,
				'post_title'   => _x( 'Navigation', 'Title of a Navigation menu' ),
				'post_name'    => 'navigation',
				'post_status'  => 'publish',
				'post_type'    => 'wp_navigation',
			),
			true // So that we can check whether the result is an error.
		);

		return $default_fallback;
	}

	/**
	 * Gets the rendered markup for the default fallback blocks.
	 *
	 * @since 6.3.0
	 *
	 * @return string default blocks markup to use a the fallback.
	 */
	private static function get_default_fallback_blocks() {
		$registry = WP_Block_Type_Registry::get_instance();

		// If `core/page-list` is not registered then use empty blocks.
		return $registry->is_registered( 'core/page-list' ) ? '<!-- wp:page-list /-->' : '';
	}
}
