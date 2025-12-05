<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

use Yoast\WP\SEO\Conditionals\Deactivating_Yoast_Seo_Conditional;

/**
 * Rewrite setup and handling for sitemaps functionality.
 */
class WPSEO_Sitemaps_Router {

	/**
	 * Sets up init logic.
	 */
	public function __construct() {
		// If we add rewrite rules during the plugin's deactivation, the flush_rewrite_rules that we perform afterwards won't properly flush those new rules.
		if ( YoastSEO()->classes->get( Deactivating_Yoast_Seo_Conditional::class )->is_met() ) {
			return;
		}

		add_action( 'yoast_add_dynamic_rewrite_rules', [ $this, 'add_rewrite_rules' ] );
		add_filter( 'query_vars', [ $this, 'add_query_vars' ] );

		add_filter( 'redirect_canonical', [ $this, 'redirect_canonical' ] );
		add_action( 'template_redirect', [ $this, 'template_redirect' ], 0 );
	}

	/**
	 * Adds rewrite routes for sitemaps.
	 *
	 * @param Yoast_Dynamic_Rewrites $dynamic_rewrites Dynamic rewrites handler instance.
	 *
	 * @return void
	 */
	public function add_rewrite_rules( $dynamic_rewrites ) {
		$dynamic_rewrites->add_rule( 'sitemap_index\.xml$', 'index.php?sitemap=1', 'top' );
		$dynamic_rewrites->add_rule( '([^/]+?)-sitemap([0-9]+)?\.xml$', 'index.php?sitemap=$matches[1]&sitemap_n=$matches[2]', 'top' );
		$dynamic_rewrites->add_rule( '([a-z]+)?-?sitemap\.xsl$', 'index.php?yoast-sitemap-xsl=$matches[1]', 'top' );
	}

	/**
	 * Adds query variables for sitemaps.
	 *
	 * @param  array<string> $query_vars List of query variables to filter.
	 *
	 * @return array<string> Filtered query variables.
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = 'sitemap';
		$query_vars[] = 'sitemap_n';
		$query_vars[] = 'yoast-sitemap-xsl';

		return $query_vars;
	}

	/**
	 * Sets up rewrite rules.
	 *
	 * @deprecated 21.8
	 * @codeCoverageIgnore
	 *
	 * @return void
	 */
	public function init() {
		_deprecated_function( __METHOD__, 'Yoast SEO 21.8' );
	}

	/**
	 * Stop trailing slashes on sitemap.xml URLs.
	 *
	 * @param string $redirect The redirect URL currently determined.
	 *
	 * @return bool|string
	 */
	public function redirect_canonical( $redirect ) {

		if ( get_query_var( 'sitemap' ) || get_query_var( 'yoast-sitemap-xsl' ) ) {
			return false;
		}

		return $redirect;
	}

	/**
	 * Redirects sitemap.xml to sitemap_index.xml.
	 *
	 * @return void
	 */
	public function template_redirect() {
		if ( ! $this->needs_sitemap_index_redirect() ) {
			return;
		}

		YoastSEO()->helpers->redirect->do_safe_redirect( home_url( '/sitemap_index.xml' ), 301, 'Yoast SEO' );
	}

	/**
	 * Checks whether the current request needs to be redirected to sitemap_index.xml.
	 *
	 * @global WP_Query $wp_query Current query.
	 *
	 * @return bool True if redirect is needed, false otherwise.
	 */
	public function needs_sitemap_index_redirect() {
		global $wp_query;

		$protocol = 'http://';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) === 'on' ) {
			$protocol = 'https://';
		}

		$domain = '';
		if ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$domain = sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) );
		}

		$path = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$path = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		// Due to different environment configurations, we need to check both SERVER_NAME and HTTP_HOST.
		$check_urls = [ $protocol . $domain . $path ];
		if ( ! empty( $_SERVER['HTTP_HOST'] ) ) {
			$check_urls[] = $protocol . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . $path;
		}

		return $wp_query->is_404 && in_array( home_url( '/sitemap.xml' ), $check_urls, true );
	}

	/**
	 * Create base URL for the sitemap.
	 *
	 * @param string $page Page to append to the base URL.
	 *
	 * @return string base URL (incl page)
	 */
	public static function get_base_url( $page ) {

		global $wp_rewrite;

		$base = $wp_rewrite->using_index_permalinks() ? 'index.php/' : '/';

		/**
		 * Filter the base URL of the sitemaps.
		 *
		 * @param string $base The string that should be added to home_url() to make the full base URL.
		 */
		$base = apply_filters( 'wpseo_sitemaps_base_url', $base );

		/*
		 * Get the scheme from the configured home URL instead of letting WordPress
		 * determine the scheme based on the requested URI.
		 */
		return home_url( $base . $page, wp_parse_url( get_option( 'home' ), PHP_URL_SCHEME ) );
	}
}
