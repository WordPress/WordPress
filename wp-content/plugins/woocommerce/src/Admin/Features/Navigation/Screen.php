<?php
/**
 * WooCommerce Navigation Screen
 *
 * @package Woocommerce Navigation
 */

namespace Automattic\WooCommerce\Admin\Features\Navigation;

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;

/**
 * Contains logic for the WooCommerce Navigation menu.
 */
class Screen {
	/**
	 * Class instance.
	 *
	 * @var Screen instance
	 */
	protected static $instance = null;

	/**
	 * Screen IDs of registered pages.
	 *
	 * @var array
	 */
	protected static $screen_ids = array();

	/**
	 * Registered post types.
	 *
	 * @var array
	 */
	protected static $post_types = array();

	/**
	 * Registered taxonomies.
	 *
	 * @var array
	 */
	protected static $taxonomies = array();

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/**
	 * Returns an array of filtered screen ids.
	 */
	public static function get_screen_ids() {
		return apply_filters( 'woocommerce_navigation_screen_ids', self::$screen_ids );
	}

	/**
	 * Returns an array of registered post types.
	 */
	public static function get_post_types() {
		return apply_filters( 'woocommerce_navigation_post_types', self::$post_types );
	}

	/**
	 * Returns an array of registered post types.
	 */
	public static function get_taxonomies() {
		return apply_filters( 'woocommerce_navigation_taxonomies', self::$taxonomies );
	}

	/**
	 * Check if we're on a WooCommerce page
	 *
	 * @return bool
	 */
	public static function is_woocommerce_page() {
		global $pagenow;

		// Get taxonomy if on a taxonomy screen.
		$taxonomy = '';
		if ( in_array( $pagenow, array( 'edit-tags.php', 'term.php' ), true ) ) {
			if ( isset( $_GET['taxonomy'] ) ) { // phpcs:ignore CSRF ok.
				$taxonomy = sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ); // phpcs:ignore CSRF ok.
			}
		}
		$taxonomies = self::get_taxonomies();

		// Get post type if on a post screen.
		$post_type = '';
		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) {
			if ( isset( $_GET['post'] ) ) { // phpcs:ignore CSRF ok.
				$post_type = get_post_type( (int) $_GET['post'] ); // phpcs:ignore CSRF ok.
			} elseif ( isset( $_GET['post_type'] ) ) { // phpcs:ignore CSRF ok.
				$post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore CSRF ok.
			}
		}
		$post_types = self::get_post_types();

		// Get current screen ID.
		$current_screen    = get_current_screen();
		$screen_ids        = self::get_screen_ids();
		$current_screen_id = $current_screen ? $current_screen->id : null;

		if (
			in_array( $post_type, $post_types, true ) ||
			in_array( $taxonomy, $taxonomies, true ) ||
			self::is_woocommerce_core_taxonomy( $taxonomy ) ||
			in_array( $current_screen_id, $screen_ids, true )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a given taxonomy is a WooCommerce core related taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @return bool
	 */
	public static function is_woocommerce_core_taxonomy( $taxonomy ) {
		if ( in_array( $taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
			return true;
		}

		if ( 'pa_' === substr( $taxonomy, 0, 3 ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add navigation classes to body.
	 *
	 * @param string $classes Classes.
	 * @return string
	 */
	public function add_body_class( $classes ) {
		if ( self::is_woocommerce_page() ) {
			$classes .= ' has-woocommerce-navigation';

			/**
			 * Adds the ability to skip disabling of the WP toolbar.
			 *
			 * @param boolean $bool WP Toolbar disabled.
			 */
			if ( apply_filters( 'woocommerce_navigation_wp_toolbar_disabled', true ) ) {
				$classes .= ' is-wp-toolbar-disabled';
			}
		}

		return $classes;
	}

	/**
	 * Adds a screen ID to the list of screens that use the navigtion.
	 * Finds the parent if none is given to grab the correct screen ID.
	 *
	 * @param string      $callback Callback or URL for page.
	 * @param string|null $parent   Parent screen ID.
	 */
	public static function add_screen( $callback, $parent = null ) {
		global $submenu;

		$plugin_page = self::get_plugin_page( $callback );

		if ( ! $parent ) {
			$parent = Menu::get_parent_key( $callback );
		}

		$screen_id = get_plugin_page_hookname( $plugin_page, $parent );

		// This screen has already been added.
		if ( in_array( $screen_id, self::$screen_ids, true ) ) {
			return;
		}

		self::$screen_ids[] = $screen_id;
	}

	/**
	 * Get the plugin page slug.
	 *
	 * @param string $callback Callback.
	 * @return string
	 */
	public static function get_plugin_page( $callback ) {
		$url   = Menu::get_callback_url( $callback );
		$parts = wp_parse_url( $url );

		if ( ! isset( $parts['query'] ) ) {
			return $callback;
		}

		parse_str( $parts['query'], $query );

		if ( ! isset( $query['page'] ) ) {
			return $callback;
		}

		$plugin_page = wp_unslash( $query['page'] );
		$plugin_page = plugin_basename( $plugin_page );
		return $plugin_page;
	}

	/**
	 * Register post type for use in WooCommerce Navigation screens.
	 *
	 * @param string $post_type Post type to add.
	 */
	public static function register_post_type( $post_type ) {
		if ( ! in_array( $post_type, self::$post_types, true ) ) {
			self::$post_types[] = $post_type;
		}
	}

	/**
	 * Register taxonomy for use in WooCommerce Navigation screens.
	 *
	 * @param string $taxonomy Taxonomy to add.
	 */
	public static function register_taxonomy( $taxonomy ) {
		if ( ! in_array( $taxonomy, self::$taxonomies, true ) ) {
			self::$taxonomies[] = $taxonomy;
		}
	}
}
