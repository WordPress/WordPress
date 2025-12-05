<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 * @since   1.7.0
 */

use Yoast\WP\SEO\Config\Conflicting_Plugins;

/**
 * Contains list of conflicting plugins.
 */
class WPSEO_Plugin_Conflict extends Yoast_Plugin_Conflict {

	/**
	 * The plugins must be grouped per section.
	 *
	 * It's possible to check for each section if there are conflicting plugin.
	 *
	 * NOTE: when changing this array, be sure to update the array in Conflicting_Plugins_Service too.
	 *
	 * @var array<string, array<string>>
	 */
	protected $plugins = [
		// The plugin which are writing OG metadata.
		'open_graph'   => Conflicting_Plugins::OPEN_GRAPH_PLUGINS,
		'xml_sitemaps' => Conflicting_Plugins::XML_SITEMAPS_PLUGINS,
		'cloaking'     => Conflicting_Plugins::CLOAKING_PLUGINS,
		'seo'          => Conflicting_Plugins::SEO_PLUGINS,
	];

	/**
	 * Overrides instance to set with this class as class.
	 *
	 * @param string $class_name Optional class name.
	 *
	 * @return Yoast_Plugin_Conflict
	 */
	public static function get_instance( $class_name = self::class ) {
		return parent::get_instance( $class_name );
	}

	/**
	 * After activating any plugin, this method will be executed by a hook.
	 *
	 * If the activated plugin is conflicting with ours a notice will be shown.
	 *
	 * @param string|bool $plugin Optional plugin basename to check.
	 *
	 * @return void
	 */
	public static function hook_check_for_plugin_conflicts( $plugin = false ) {
		// The instance of the plugin.
		$instance = self::get_instance();

		// Only add the plugin as an active plugin if $plugin isn't false.
		if ( $plugin && is_string( $plugin ) ) {
			$instance->add_active_plugin( $instance->find_plugin_category( $plugin ), $plugin );
		}

		$plugin_sections = [];

		// Only check for open graph problems when they are enabled.
		if ( WPSEO_Options::get( 'opengraph' ) ) {
			/* translators: %1$s expands to Yoast SEO, %2$s: 'Facebook' plugin name of possibly conflicting plugin with regard to creating OpenGraph output. */
			$plugin_sections['open_graph'] = __( 'Both %1$s and %2$s create Open Graph output, which might make Facebook, X, LinkedIn and other social networks use the wrong texts and images when your pages are being shared.', 'wordpress-seo' )
				. '<br/><br/>'
				. '<a class="button" href="' . admin_url( 'admin.php?page=wpseo_page_settings#/site-features#card-wpseo_social-opengraph' ) . '">'
				/* translators: %1$s expands to Yoast SEO. */
				. sprintf( __( 'Configure %1$s\'s Open Graph settings', 'wordpress-seo' ), 'Yoast SEO' )
				. '</a>';
		}

		// Only check for XML conflicts if sitemaps are enabled.
		if ( WPSEO_Options::get( 'enable_xml_sitemap' ) ) {
			/* translators: %1$s expands to Yoast SEO, %2$s: 'Google XML Sitemaps' plugin name of possibly conflicting plugin with regard to the creation of sitemaps. */
			$plugin_sections['xml_sitemaps'] = __( 'Both %1$s and %2$s can create XML sitemaps. Having two XML sitemaps is not beneficial for search engines and might slow down your site.', 'wordpress-seo' )
				. '<br/><br/>'
				. '<a class="button" href="' . admin_url( 'admin.php?page=wpseo_page_settings#/site-features#card-wpseo-enable_xml_sitemap' ) . '">'
				/* translators: %1$s expands to Yoast SEO. */
				. sprintf( __( 'Toggle %1$s\'s XML Sitemap', 'wordpress-seo' ), 'Yoast SEO' )
				. '</a>';
		}

		/* translators: %2$s expands to 'RS Head Cleaner' plugin name of possibly conflicting plugin with regard to differentiating output between search engines and normal users. */
		$plugin_sections['cloaking'] = __( 'The plugin %2$s changes your site\'s output and in doing that differentiates between search engines and normal users, a process that\'s called cloaking. We highly recommend that you disable it.', 'wordpress-seo' );

		/* translators: %1$s expands to Yoast SEO, %2$s: 'SEO' plugin name of possibly conflicting plugin with regard to the creation of duplicate SEO meta. */
		$plugin_sections['seo'] = __( 'Both %1$s and %2$s manage the SEO of your site. Running two SEO plugins at the same time is detrimental.', 'wordpress-seo' );

		$instance->check_plugin_conflicts( $plugin_sections );
	}
}
