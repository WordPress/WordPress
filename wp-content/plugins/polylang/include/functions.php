<?php

/**
 * Define wordpress.com VIP equivalent of uncached functions
 * and WordPress backward compatibility functions
 */

if ( ! function_exists( 'wpcom_vip_get_page_by_title' ) ) {
	/**
	 * Retrieve a page given its title.
	 *
	 * @since 2.0
	 *
	 * @param string       $page_title Page title
	 * @param string       $output     Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT
	 * @param string|array $post_type  Optional. Post type or array of post types. Default 'page'.
	 * @return WP_Post|array|null WP_Post (or array) on success, or null on failure.
	 */
	function wpcom_vip_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
		return get_page_by_title( $page_title, $output, $post_type );
	}
}

if ( ! function_exists( 'wpcom_vip_get_category_by_slug' ) ) {
	/**
	 * Retrieve category object by category slug.
	 *
	 * @since 2.0
	 *
	 * @param string $slug The category slug.
	 * @return object Category data object
	 */
	function wpcom_vip_get_category_by_slug( $slug ) {
		return get_category_by_slug( $slug );
	}
}

if ( ! function_exists( 'wpcom_vip_get_term_by' ) ) {
	/**
	 * Get all Term data from database by Term field and data.
	 *
	 * @since 2.0
	 *
	 * @param string     $field    Either 'slug', 'name', 'id' (term_id), or 'term_taxonomy_id'
	 * @param string|int $value    Search for this term value
	 * @param string     $taxonomy Taxonomy name. Optional, if `$field` is 'term_taxonomy_id'.
	 * @param string     $output   Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 * @param string     $filter   Optional, default is raw or no WordPress defined filter will applied.
	 * @return WP_Term|array|false WP_Term instance (or array) on success. Will return false if `$taxonomy` does not exist or `$term` was not found.
	 */
	function wpcom_vip_get_term_by( $field, $value, $taxonomy = '', $output = OBJECT, $filter = 'raw' ) {
		return get_term_by( $field, $value, $taxonomy, $output, $filter );
	}
}

if ( ! function_exists( 'wpcom_vip_get_term_link' ) ) {
	/**
	 * Generate a permalink for a taxonomy term archive.
	 *
	 * @since 2.0
	 *
	 * @param object|int|string $term     The term object, ID, or slug whose link will be retrieved.
	 * @param string            $taxonomy Optional. Taxonomy. Default empty.
	 * @return string|WP_Error HTML link to taxonomy term archive on success, WP_Error if term does not exist.
	 */
	function wpcom_vip_get_term_link( $term, $taxonomy = '' ) {
		return get_term_link( $term, $taxonomy );
	}
}

if ( ! function_exists( 'wp_doing_ajax' ) ) {
	/**
	 * Determines whether the current request is a WordPress Ajax request.
	 * Backward compatibility function for WP < 4.7
	 *
	 * @since 2.2
	 *
	 * @return bool True if it's a WordPress Ajax request, false otherwise.
	 */
	function wp_doing_ajax() {
		/** This filter is documented in wp-includes/load.php */
		return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
	}
}
