<?php
/**
 * @package WPSEO\Frontend
 */

/**
 * This code handles the category rewrites.
 */
class WPSEO_Rewrite {

	/**
	 * Class constructor
	 */
	function __construct() {
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_filter( 'category_link', array( $this, 'no_category_base' ) );
		add_filter( 'request', array( $this, 'request' ) );
		add_filter( 'category_rewrite_rules', array( $this, 'category_rewrite_rules' ) );

		add_action( 'created_category', array( $this, 'schedule_flush' ) );
		add_action( 'edited_category', array( $this, 'schedule_flush' ) );
		add_action( 'delete_category', array( $this, 'schedule_flush' ) );

		add_action( 'init', array( $this, 'flush' ), 999 );
	}

	/**
	 * Save an option that triggers a flush on the next init.
	 *
	 * @since 1.2.8
	 */
	function schedule_flush() {
		update_option( 'wpseo_flush_rewrite', 1 );
	}

	/**
	 * If the flush option is set, flush the rewrite rules.
	 *
	 * @since 1.2.8
	 * @return bool
	 */
	function flush() {
		if ( get_option( 'wpseo_flush_rewrite' ) ) {

			add_action( 'shutdown', 'flush_rewrite_rules' );
			delete_option( 'wpseo_flush_rewrite' );

			return true;
		}

		return false;
	}

	/**
	 * Override the category link to remove the category base.
	 *
	 * @param string $link Unused, overridden by the function.
	 *
	 * @return string
	 */
	function no_category_base( $link ) {
		$category_base = get_option( 'category_base' );

		if ( '' == $category_base ) {
			$category_base = 'category';
		}

		// Remove initial slash, if there is one (we remove the trailing slash in the regex replacement and don't want to end up short a slash).
		if ( '/' == substr( $category_base, 0, 1 ) ) {
			$category_base = substr( $category_base, 1 );
		}

		$category_base .= '/';

		return preg_replace( '`' . preg_quote( $category_base, '`' ) . '`u', '', $link, 1 );
	}

	/**
	 * Update the query vars with the redirect var when stripcategorybase is active
	 *
	 * @param array $query_vars
	 *
	 * @return array
	 */
	function query_vars( $query_vars ) {
		$options = WPSEO_Options::get_all();

		if ( $options['stripcategorybase'] === true ) {
			$query_vars[] = 'wpseo_category_redirect';
		}

		return $query_vars;
	}

	/**
	 * Redirect the "old" category URL to the new one.
	 *
	 * @param array $query_vars Query vars to check for existence of redirect var.
	 *
	 * @return array
	 */
	function request( $query_vars ) {
		if ( isset( $query_vars['wpseo_category_redirect'] ) ) {
			$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['wpseo_category_redirect'], 'category' );

			wp_redirect( $catlink, 301 );
			exit;
		}

		return $query_vars;
	}

	/**
	 * This function taken and only slightly adapted from WP No Category Base plugin by Saurabh Gupta
	 *
	 * @return array
	 */
	function category_rewrite_rules() {
		global $wp_rewrite;

		$category_rewrite = array();

		$taxonomy = get_taxonomy( 'category' );

		$blog_prefix = '';
		if ( is_multisite() && ! is_subdomain_install() && is_main_site() ) {
			$blog_prefix = 'blog/';
		}

		$categories = get_categories( array( 'hide_empty' => false ) );
		if ( is_array( $categories ) && $categories !== array() ) {
			foreach ( $categories as $category ) {
				$category_nicename = $category->slug;
				if ( $category->parent == $category->cat_ID ) {
					// Recursive recursion.
					$category->parent = 0;
				}
				elseif ( $taxonomy->rewrite['hierarchical'] != 0 && $category->parent != 0 ) {
					$parents = get_category_parents( $category->parent, false, '/', true );
					if ( ! is_wp_error( $parents ) ) {
						$category_nicename = $parents . $category_nicename;
					}
					unset( $parents );
				}

				$category_rewrite[ $blog_prefix . '(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$' ]                = 'index.php?category_name=$matches[1]&feed=$matches[2]';
				$category_rewrite[ $blog_prefix . '(' . $category_nicename . ')/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
				$category_rewrite[ $blog_prefix . '(' . $category_nicename . ')/?$' ]                                                   = 'index.php?category_name=$matches[1]';
			}
			unset( $categories, $category, $category_nicename );
		}

		// Redirect support from Old Category Base.
		$old_base                            = $wp_rewrite->get_category_permastruct();
		$old_base                            = str_replace( '%category%', '(.+)', $old_base );
		$old_base                            = trim( $old_base, '/' );
		$category_rewrite[ $old_base . '$' ] = 'index.php?wpseo_category_redirect=$matches[1]';

		return $category_rewrite;
	}
} /* End of class */
