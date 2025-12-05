<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong
namespace Yoast\WP\SEO\Llms_Txt\Infrastructure\Markdown_Services;

use WPSEO_Options;
use WPSEO_Sitemaps_Router;
use Yoast\WP\SEO\Llms_Txt\Domain\Markdown\Items\Link;

/**
 * The sitemap link collector.
 */
class Sitemap_Link_Collector {

	/**
	 * Gets the link for the sitemap.
	 *
	 * @return Link The link for the sitemap.
	 */
	public function get_link(): ?Link {
		if ( WPSEO_Options::get( 'enable_xml_sitemap' ) ) {
			$sitemap_url = WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' );
			return new Link( 'this link', $sitemap_url );
		}

		$sitemap_url = \get_sitemap_url( 'index' );

		if ( $sitemap_url !== false ) {
			return new Link( 'this link', $sitemap_url );
		}

		return null;
	}
}
