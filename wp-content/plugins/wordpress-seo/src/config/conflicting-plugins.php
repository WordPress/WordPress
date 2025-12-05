<?php

namespace Yoast\WP\SEO\Config;

/**
 * Conflicting_Plugins class that holds all known conflicting plugins.
 */
class Conflicting_Plugins {

	public const OPEN_GRAPH_PLUGINS = [
		'2-click-socialmedia-buttons/2-click-socialmedia-buttons.php',
		// 2 Click Social Media Buttons.
		'add-link-to-facebook/add-link-to-facebook.php',         // Add Link to Facebook.
		'add-meta-tags/add-meta-tags.php',                       // Add Meta Tags.
		'easy-facebook-share-thumbnails/esft.php',               // Easy Facebook Share Thumbnail.
		'facebook/facebook.php',                                 // Facebook (official plugin).
		'facebook-awd/AWD_facebook.php',                         // Facebook AWD All in one.
		'facebook-featured-image-and-open-graph-meta-tags/fb-featured-image.php',
		// Facebook Featured Image & OG Meta Tags.
		'facebook-meta-tags/facebook-metatags.php',              // Facebook Meta Tags.
		'wonderm00ns-simple-facebook-open-graph-tags/wonderm00n-open-graph.php',
		// Facebook Open Graph Meta Tags for WordPress.
		'facebook-revised-open-graph-meta-tag/index.php',        // Facebook Revised Open Graph Meta Tag.
		'facebook-thumb-fixer/_facebook-thumb-fixer.php',        // Facebook Thumb Fixer.
		'facebook-and-digg-thumbnail-generator/facebook-and-digg-thumbnail-generator.php',
		// Fedmich's Facebook Open Graph Meta.
		'network-publisher/networkpub.php',                      // Network Publisher.
		'nextgen-facebook/nextgen-facebook.php',                 // NextGEN Facebook OG.
		'opengraph/opengraph.php',                               // Open Graph.
		'open-graph-protocol-framework/open-graph-protocol-framework.php',
		// Open Graph Protocol Framework.
		'seo-facebook-comments/seofacebook.php',                 // SEO Facebook Comments.
		'sexybookmarks/sexy-bookmarks.php',                      // Shareaholic.
		'shareaholic/sexy-bookmarks.php',                        // Shareaholic.
		'sharepress/sharepress.php',                             // SharePress.
		'simple-facebook-connect/sfc.php',                       // Simple Facebook Connect.
		'social-discussions/social-discussions.php',             // Social Discussions.
		'social-sharing-toolkit/social_sharing_toolkit.php',     // Social Sharing Toolkit.
		'socialize/socialize.php',                               // Socialize.
		'only-tweet-like-share-and-google-1/tweet-like-plusone.php',
		// Tweet, Like, Google +1 and Share.
		'wordbooker/wordbooker.php',                             // Wordbooker.
		'wpsso/wpsso.php',                                       // WordPress Social Sharing Optimization.
		'wp-caregiver/wp-caregiver.php',                         // WP Caregiver.
		'wp-facebook-like-send-open-graph-meta/wp-facebook-like-send-open-graph-meta.php',
		// WP Facebook Like Send & Open Graph Meta.
		'wp-facebook-open-graph-protocol/wp-facebook-ogp.php',   // WP Facebook Open Graph protocol.
		'wp-ogp/wp-ogp.php',                                     // WP-OGP.
		'zoltonorg-social-plugin/zosp.php',                      // Zolton.org Social Plugin.
	];

	public const XML_SITEMAPS_PLUGINS = [
		'google-sitemap-plugin/google-sitemap-plugin.php',
		// Google Sitemap (BestWebSoft).
		'xml-sitemaps/xml-sitemaps.php',
		// XML Sitemaps (Denis de Bernardy and Mike Koepke).
		'bwp-google-xml-sitemaps/bwp-simple-gxs.php',
		// Better WordPress Google XML Sitemaps (Khang Minh).
		'google-sitemap-generator/sitemap.php',
		// Google XML Sitemaps (Arne Brachhold).
		'xml-sitemap-feed/xml-sitemap.php',
		// XML Sitemap & Google News feeds (RavanH).
		'google-monthly-xml-sitemap/monthly-xml-sitemap.php',
		// Google Monthly XML Sitemap (Andrea Pernici).
		'simple-google-sitemap-xml/simple-google-sitemap-xml.php',
		// Simple Google Sitemap XML (iTx Technologies).
		'another-simple-xml-sitemap/another-simple-xml-sitemap.php',
		// Another Simple XML Sitemap.
		'xml-maps/google-sitemap.php',
		// Xml Sitemap (Jason Martens).
		'google-xml-sitemap-generator-by-anton-dachauer/adachauer-google-xml-sitemap.php',
		// Google XML Sitemap Generator by Anton Dachauer (Anton Dachauer).
		'wp-xml-sitemap/wp-xml-sitemap.php',
		// WP XML Sitemap (Team Vivacity).
		'sitemap-generator-for-webmasters/sitemap.php',
		// Sitemap Generator for Webmasters (iwebslogtech).
		'xml-sitemap-xml-sitemapcouk/xmls.php',
		// XML Sitemap - XML-Sitemap.co.uk (Simon Hancox).
		'sewn-in-xml-sitemap/sewn-xml-sitemap.php',
		// Sewn In XML Sitemap (jcow).
		'rps-sitemap-generator/rps-sitemap-generator.php',
		// RPS Sitemap Generator (redpixelstudios).
	];

	public const CLOAKING_PLUGINS = [
		'rs-head-cleaner/rs-head-cleaner.php',
		// RS Head Cleaner Plus https://wordpress.org/plugins/rs-head-cleaner/.
		'rs-head-cleaner-lite/rs-head-cleaner-lite.php',
		// RS Head Cleaner Lite https://wordpress.org/plugins/rs-head-cleaner-lite/.
	];

	public const SEO_PLUGINS = [
		'all-in-one-seo-pack/all_in_one_seo_pack.php',           // All in One SEO Pack.
		'seo-ultimate/seo-ultimate.php',                         // SEO Ultimate.
		'seo-by-rank-math/rank-math.php',                        // Rank Math.
	];

	/**
	 * Returns the list of all conflicting plugins.
	 *
	 * @return array The list of all conflicting plugins.
	 */
	public static function all_plugins() {
		return \array_merge(
			self::OPEN_GRAPH_PLUGINS,
			self::XML_SITEMAPS_PLUGINS,
			self::CLOAKING_PLUGINS,
			self::SEO_PLUGINS
		);
	}
}
