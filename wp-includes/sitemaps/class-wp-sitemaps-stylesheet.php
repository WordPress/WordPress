<?php
/**
 * Sitemaps: WP_Sitemaps_Stylesheet class
 *
 * This class is retained for backward compatibility.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 * @deprecated 7.2.0 Stylesheets are no longer supported.
 */

/**
 * Stylesheet provider class.
 *
 * @since 5.5.0
 * @deprecated 7.2.0 Stylesheets are no longer supported.
 */
#[AllowDynamicProperties]
class WP_Sitemaps_Stylesheet {
	/**
	 * Renders the XSL stylesheet depending on whether it's the sitemap index or not.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @param string $type Stylesheet type. Either 'sitemap' or 'index'.
	 * @return never
	 */
	public function render_stylesheet( $type ) {
		unset( $type );
		status_header( 410 );
		_deprecated_function( __METHOD__, '7.2.0' );
		exit;
	}

	/**
	 * Returns the escaped XSL for all sitemaps, except index.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @return string Empty string.
	 */
	public function get_sitemap_stylesheet() {
		_deprecated_function( __METHOD__, '7.2.0' );
		return '';
	}

	/**
	 * Returns the escaped XSL for the index sitemaps.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @return string Empty string.
	 */
	public function get_sitemap_index_stylesheet() {
		_deprecated_function( __METHOD__, '7.2.0' );
		return '';
	}

	/**
	 * Gets the CSS to be included in sitemap XSL stylesheets.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @return string Empty string.
	 */
	public function get_stylesheet_css() {
		_deprecated_function( __METHOD__, '7.2.0' );

		/**
		 * Filters the CSS only for the sitemap stylesheet.
		 *
		 * Following the deprecation of XSLT by browser manufacturers the
		 * outcome of this filter is ignored.
		 *
		 * @since 5.5.0
		 * @deprecated 7.2.0 Stylesheets are no longer supported.
		 *
		 * @param string $css CSS to be applied to default XSL file.
		 */
		apply_filters_deprecated( 'wp_sitemaps_stylesheet_css', array( '' ), '7.2.0' );

		return '';
	}
}
