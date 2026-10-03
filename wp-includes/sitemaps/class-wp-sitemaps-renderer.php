<?php
/**
 * Sitemaps: WP_Sitemaps_Renderer class
 *
 * Responsible for rendering Sitemaps data to XML in accordance with sitemap protocol.
 *
 * @package WordPress
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class WP_Sitemaps_Renderer
 *
 * @since 5.5.0
 */
#[AllowDynamicProperties]
class WP_Sitemaps_Renderer {
	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @var string
	 */
	protected $stylesheet = '';

	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @var string
	 */
	protected $stylesheet_index = '';

	/**
	 * WP_Sitemaps_Renderer constructor.
	 *
	 * @since 5.5.0
	 * @since 7.2.0 No longer sets up the stylesheets, which are no longer supported.
	 */
	public function __construct() {}

	/**
	 * Gets the URL for the sitemap stylesheet.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @return string Empty string.
	 */
	public function get_sitemap_stylesheet_url() {
		_deprecated_function( __METHOD__, '7.2.0' );

		/**
		 * Filters the URL for the sitemap stylesheet.
		 *
		 * If a falsey value is returned, no stylesheet will be used and
		 * Following the deprecation of XSLT by browser manufacturers the
		 * outcome of this filter is ignored.
		 *
		 * @since 5.5.0
		 * @deprecated 7.2.0 Stylesheets are no longer supported.
		 *
		 * @param string $sitemap_url Full URL for the sitemaps XSL file.
		 */
		apply_filters_deprecated( 'wp_sitemaps_stylesheet_url', array( '' ), '7.2.0' );

		return '';
	}

	/**
	 * Gets the URL for the sitemap index stylesheet.
	 *
	 * @since 5.5.0
	 * @deprecated 7.2.0 Stylesheets are no longer supported.
	 *
	 * @return string Empty string.
	 */
	public function get_sitemap_index_stylesheet_url() {
		_deprecated_function( __METHOD__, '7.2.0' );

		/**
		 * Filters the URL for the sitemap index stylesheet.
		 *
		 * If a falsey value is returned, no stylesheet will be used and
		 * Following the deprecation of XSLT by browser manufacturers the
		 * outcome of this filter is ignored.
		 *
		 * @since 5.5.0
		 * @deprecated 7.2.0 Stylesheets are no longer supported.
		 *
		 * @param string $sitemap_url Full URL for the sitemaps index XSL file.
		 */
		apply_filters_deprecated( 'wp_sitemaps_stylesheet_index_url', array( '' ), '7.2.0' );

		return '';
	}

	/**
	 * Renders a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-Type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();

		$index_xml = $this->get_sitemap_index_xml( $sitemaps );

		if ( ! empty( $index_xml ) ) {
			// All output is escaped within get_sitemap_index_xml().
			echo $index_xml;
		}
	}

	/**
	 * Gets XML for a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_index_xml( $sitemaps ) {
		$this->apply_deprecated_stylesheet_filters( 'index' );

		$sitemap_index = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $sitemaps as $entry ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );

			// Add each element as a child node to the <sitemap> entry.
			foreach ( $entry as $name => $value ) {
				if ( 'loc' === $name ) {
					$sitemap->addChild( $name, esc_url( $value ) );
				} elseif ( 'lastmod' === $name ) {
					$sitemap->addChild( $name, esc_xml( $value ) );
				} else {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: %s: List of element names. */
							__( 'Fields other than %s are not currently supported for the sitemap index.' ),
							implode( ',', array( 'loc', 'lastmod' ) )
						),
						'5.5.0'
					);
				}
			}
		}

		return $sitemap_index->asXML();
	}

	/**
	 * Renders a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list ) {
		header( 'Content-Type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();

		$sitemap_xml = $this->get_sitemap_xml( $url_list );

		if ( ! empty( $sitemap_xml ) ) {
			// All output is escaped within get_sitemap_xml().
			echo $sitemap_xml;
		}
	}

	/**
	 * Gets XML for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_xml( $url_list ) {
		$this->apply_deprecated_stylesheet_filters( 'sitemap' );

		$urlset = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );

			// Add each element as a child node to the <url> entry.
			foreach ( $url_item as $name => $value ) {
				if ( 'loc' === $name ) {
					$url->addChild( $name, esc_url( $value ) );
				} elseif ( in_array( $name, array( 'lastmod', 'changefreq', 'priority' ), true ) ) {
					$url->addChild( $name, esc_xml( $value ) );
				} else {
					_doing_it_wrong(
						__METHOD__,
						sprintf(
							/* translators: %s: List of element names. */
							__( 'Fields other than %s are not currently supported for sitemaps.' ),
							implode( ',', array( 'loc', 'lastmod', 'changefreq', 'priority' ) )
						),
						'5.5.0'
					);
				}
			}
		}

		return $urlset->asXML();
	}

	/**
	 * Applies the removed stylesheet filters so that their callbacks trigger deprecation notices.
	 *
	 * The stylesheet filters no longer have any effect. Applying them where a sitemap is
	 * generated ensures a callback still added to one is reported rather than silently ignored.
	 * No notice is triggered for a filter that has no callbacks.
	 *
	 * @since 7.2.0
	 *
	 * @param 'sitemap'|'index' $type Sitemap type.
	 */
	private function apply_deprecated_stylesheet_filters( string $type ): void {
		if ( 'index' === $type ) {
			/** This filter is documented in wp-includes/sitemaps/class-wp-sitemaps-renderer.php */
			apply_filters_deprecated( 'wp_sitemaps_stylesheet_index_url', array( '' ), '7.2.0' );

			/**
			 * Filters the content of the sitemap index stylesheet.
			 *
			 * Following the deprecation of XSLT by browser manufacturers the
			 * outcome of this filter is ignored.
			 *
			 * @since 5.5.0
			 * @deprecated 7.2.0 Stylesheets are no longer supported.
			 *
			 * @param string $xsl_content Full content for the XML stylesheet.
			 */
			apply_filters_deprecated( 'wp_sitemaps_stylesheet_index_content', array( '' ), '7.2.0' );
		} else {
			/** This filter is documented in wp-includes/sitemaps/class-wp-sitemaps-renderer.php */
			apply_filters_deprecated( 'wp_sitemaps_stylesheet_url', array( '' ), '7.2.0' );

			/**
			 * Filters the content of the sitemap stylesheet.
			 *
			 * Following the deprecation of XSLT by browser manufacturers the
			 * outcome of this filter is ignored.
			 *
			 * @since 5.5.0
			 * @deprecated 7.2.0 Stylesheets are no longer supported.
			 *
			 * @param string $xsl_content Full content for the XML stylesheet.
			 */
			apply_filters_deprecated( 'wp_sitemaps_stylesheet_content', array( '' ), '7.2.0' );
		}

		/** This filter is documented in wp-includes/sitemaps/class-wp-sitemaps-stylesheet.php */
		apply_filters_deprecated( 'wp_sitemaps_stylesheet_css', array( '' ), '7.2.0' );
	}

	/**
	 * Checks for the availability of the SimpleXML extension and errors if missing.
	 *
	 * @since 5.5.0
	 */
	private function check_for_simple_xml_availability() {
		if ( ! class_exists( 'SimpleXMLElement' ) ) {
			add_filter(
				'wp_die_handler',
				static function () {
					return '_xml_wp_die_handler';
				}
			);

			wp_die(
				sprintf(
					/* translators: %s: SimpleXML */
					esc_xml( __( 'Could not generate XML sitemap due to missing %s extension' ) ),
					'SimpleXML'
				),
				esc_xml( __( 'WordPress &rsaquo; Error' ) ),
				array(
					'response' => 501, // "Not implemented".
				)
			);
		}
	}
}
