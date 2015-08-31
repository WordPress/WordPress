<?php
/**
 * @package WPSEO\Admin
 * @since      1.7.0
 */

/**
 * Contains list of conflicting plugins.
 */
class WPSEO_Plugin_Conflict extends Yoast_Plugin_Conflict {

	/**
	 * The plugins must be grouped per section.
	 *
	 * It's possible to check for each section if there are conflicting plugin
	 *
	 * @var array
	 */
	protected $plugins = array(
		// The plugin which are writing OG metadata.
		'open_graph'   => array(
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
			'header-footer/plugin.php',                              // Header and Footer.
			'network-publisher/networkpub.php',                      // Network Publisher.
			'nextgen-facebook/nextgen-facebook.php',                 // NextGEN Facebook OG.
			'opengraph/opengraph.php',                               // Open Graph.
			'open-graph-protocol-framework/open-graph-protocol-framework.php',
			// Open Graph Protocol Framework.
			'seo-facebook-comments/seofacebook.php',                 // SEO Facebook Comments.
			'seo-ultimate/seo-ultimate.php',                         // SEO Ultimate.
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
		),
		'xml_sitemaps' => array(
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
		),
	);

	/**
	 * Overrides instance to set with this class as class
	 *
	 * @param string $class_name
	 *
	 * @return Yoast_Plugin_Conflict
	 */
	public static function get_instance( $class_name = __CLASS__ ) {
		return parent::get_instance( $class_name );
	}

	/**
	 * After activating any plugin, this method will be executed by a hook.
	 *
	 * If the activated plugin is conflicting with ours a notice will be shown.
	 *
	 * @param string|bool $plugin
	 */
	public static function hook_check_for_plugin_conflicts( $plugin = false ) {
		// The instance of itself.
		$instance = self::get_instance();

		// Only add plugin as active plugin if $plugin isn't false.
		if ( $plugin ) {
			// Because it's just activated.
			$instance->add_active_plugin( $instance->find_plugin_category( $plugin ), $plugin );
		}

		$plugin_sections = array(
			/* translators: %1$s expands to Yoast SEO, %2%s: 'Facebook' plugin name of possibly conflicting plugin with regard to creating OpenGraph output*/
			'open_graph'   => __( 'Both %1$s and %2$s create OpenGraph output, which might make Facebook, Twitter, LinkedIn and other social networks use the wrong texts and images when your pages are being shared.', 'wordpress-seo' )
				. '<br/><br/>'
				. '<a target="_blank" class="button" href="' . admin_url( 'admin.php?page=wpseo_social#top#facebook' ) . '">'
				/* translators: %1$s expands to Yoast SEO */
				. sprintf( __( 'Configure %1$s\'s OpenGraph settings', 'wordpress-seo' ), 'Yoast SEO' )
				. '</a>',
			/* translators: %1$s expands to Yoast SEO, %2$s: 'Google XML Sitemaps' plugin name of possibly conflicting plugin with regard to the creation of sitemaps*/
			'xml_sitemaps' => __( 'Both %1$s and %2$s can create XML sitemaps. Having two XML sitemaps is not beneficial for search engines, yet might slow down your site.', 'wordpress-seo' )
				. '<br/><br/>'
				. '<a target="_blank" class="button" href="' . admin_url( 'admin.php?page=wpseo_xml' ) . '">'
				/* translators: %1$s expands to Yoast SEO */
				. sprintf( __( 'Configure %1$s\'s XML Sitemap settings', 'wordpress-seo' ), 'Yoast SEO' )
				. '</a>',
		);

		$instance->check_plugin_conflicts( $plugin_sections );
	}

}
