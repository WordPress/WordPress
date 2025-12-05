<?php

namespace Yoast\WP\SEO\Integrations\Front_End;

use WPSEO_Sitemaps_Router;
use Yoast\WP\SEO\Conditionals\Robots_Txt_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Robots_Txt_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Robots_Txt_Presenter;

/**
 * Handles adding the sitemap to the `robots.txt`.
 */
class Robots_Txt_Integration implements Integration_Interface {

	/**
	 * Holds the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Holds the robots txt helper.
	 *
	 * @var Robots_Txt_Helper
	 */
	protected $robots_txt_helper;

	/**
	 * Holds the robots txt presenter.
	 *
	 * @var Robots_Txt_Presenter
	 */
	protected $robots_txt_presenter;

	/**
	 * Sets the helpers.
	 *
	 * @param Options_Helper       $options_helper       Options helper.
	 * @param Robots_Txt_Helper    $robots_txt_helper    Robots txt helper.
	 * @param Robots_Txt_Presenter $robots_txt_presenter Robots txt presenter.
	 */
	public function __construct( Options_Helper $options_helper, Robots_Txt_Helper $robots_txt_helper, Robots_Txt_Presenter $robots_txt_presenter ) {
		$this->options_helper       = $options_helper;
		$this->robots_txt_helper    = $robots_txt_helper;
		$this->robots_txt_presenter = $robots_txt_presenter;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Robots_Txt_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'robots_txt', [ $this, 'filter_robots' ], 99999 );

		if ( $this->options_helper->get( 'deny_search_crawling' ) && ! \is_multisite() ) {
			\add_action( 'Yoast\WP\SEO\register_robots_rules', [ $this, 'add_disallow_search_to_robots' ], 10, 1 );
		}
		if ( $this->options_helper->get( 'deny_wp_json_crawling' ) && ! \is_multisite() ) {
			\add_action( 'Yoast\WP\SEO\register_robots_rules', [ $this, 'add_disallow_wp_json_to_robots' ], 10, 1 );
		}
		if ( $this->options_helper->get( 'deny_adsbot_crawling' ) && ! \is_multisite() ) {
			\add_action( 'Yoast\WP\SEO\register_robots_rules', [ $this, 'add_disallow_adsbot' ], 10, 1 );
		}
	}

	/**
	 * Filters the robots.txt output.
	 *
	 * @param string $robots_txt The robots.txt output from WordPress.
	 *
	 * @return string Filtered robots.txt output.
	 */
	public function filter_robots( $robots_txt ) {
		$robots_txt = $this->remove_default_robots( $robots_txt );
		$this->maybe_add_xml_sitemap();

		/**
		 * Filter: 'wpseo_should_add_subdirectory_multisite_xml_sitemaps' - Disabling this filter removes subdirectory sites from xml sitemaps.
		 *
		 * @since 19.8
		 *
		 * @param bool $show Whether to display multisites in the xml sitemaps.
		 */
		if ( \apply_filters( 'wpseo_should_add_subdirectory_multisite_xml_sitemaps', true ) ) {
			$this->add_subdirectory_multisite_xml_sitemaps();
		}

		/**
		 * Allow registering custom robots rules to be outputted within the Yoast content block in robots.txt.
		 *
		 * @param Robots_Txt_Helper $robots_txt_helper The Robots_Txt_Helper object.
		 */
		\do_action( 'Yoast\WP\SEO\register_robots_rules', $this->robots_txt_helper );

		return \trim( $robots_txt . \PHP_EOL . $this->robots_txt_presenter->present() . \PHP_EOL );
	}

	/**
	 * Add a disallow rule for search to robots.txt.
	 *
	 * @param Robots_Txt_Helper $robots_txt_helper The robots txt helper.
	 *
	 * @return void
	 */
	public function add_disallow_search_to_robots( Robots_Txt_Helper $robots_txt_helper ) {
		$robots_txt_helper->add_disallow( '*', '/?s=' );
		$robots_txt_helper->add_disallow( '*', '/page/*/?s=' );
		$robots_txt_helper->add_disallow( '*', '/search/' );
	}

	/**
	 * Add a disallow rule for /wp-json/ to robots.txt.
	 *
	 * @param Robots_Txt_Helper $robots_txt_helper The robots txt helper.
	 *
	 * @return void
	 */
	public function add_disallow_wp_json_to_robots( Robots_Txt_Helper $robots_txt_helper ) {
		$robots_txt_helper->add_disallow( '*', '/wp-json/' );
		$robots_txt_helper->add_disallow( '*', '/?rest_route=' );
	}

	/**
	 * Add a disallow rule for AdsBot agents to robots.txt.
	 *
	 * @param Robots_Txt_Helper $robots_txt_helper The robots txt helper.
	 *
	 * @return void
	 */
	public function add_disallow_adsbot( Robots_Txt_Helper $robots_txt_helper ) {
		$robots_txt_helper->add_disallow( 'AdsBot', '/' );
	}

	/**
	 * Replaces the default WordPress robots.txt output.
	 *
	 * @param string $robots_txt Input robots.txt.
	 *
	 * @return string
	 */
	protected function remove_default_robots( $robots_txt ) {
		return \preg_replace(
			'`User-agent: \*[\r\n]+Disallow: /wp-admin/[\r\n]+Allow: /wp-admin/admin-ajax\.php[\r\n]+`',
			'',
			$robots_txt
		);
	}

	/**
	 * Adds XML sitemap reference to robots.txt.
	 *
	 * @return void
	 */
	protected function maybe_add_xml_sitemap() {
		// If the XML sitemap is disabled, bail.
		if ( ! $this->options_helper->get( 'enable_xml_sitemap', false ) ) {
			return;
		}
		$this->robots_txt_helper->add_sitemap( \esc_url( WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' ) ) );
	}

	/**
	 * Adds subdomain multisite' XML sitemap references to robots.txt.
	 *
	 * @return void
	 */
	protected function add_subdirectory_multisite_xml_sitemaps() {
		// If not on a multisite subdirectory, bail.
		if ( ! \is_multisite() || \is_subdomain_install() ) {
			return;
		}

		$sitemaps_enabled = $this->get_xml_sitemaps_enabled();

		foreach ( $sitemaps_enabled as $blog_id => $is_sitemap_enabled ) {
			if ( ! $is_sitemap_enabled ) {
				continue;
			}
			$this->robots_txt_helper->add_sitemap( \esc_url( \get_home_url( $blog_id, 'sitemap_index.xml' ) ) );
		}
	}

	/**
	 * Retrieves whether the XML sitemaps are enabled, keyed by blog ID.
	 *
	 * @return array
	 */
	protected function get_xml_sitemaps_enabled() {
		$is_allowed = $this->is_sitemap_allowed();
		$blog_ids   = $this->get_blog_ids();
		$is_enabled = [];
		foreach ( $blog_ids as $blog_id ) {
			$is_enabled[ $blog_id ] = $is_allowed && $this->is_sitemap_enabled_for( $blog_id );
		}

		return $is_enabled;
	}

	/**
	 * Retrieves whether the sitemap is allowed on a sub site.
	 *
	 * @return bool
	 */
	protected function is_sitemap_allowed() {
		$options = \get_network_option( null, 'wpseo_ms' );
		if ( ! $options || ! isset( $options['allow_enable_xml_sitemap'] ) ) {
			// Default is enabled.
			return true;
		}

		return (bool) $options['allow_enable_xml_sitemap'];
	}

	/**
	 * Retrieves whether the sitemap is enabled on a site.
	 *
	 * @param int $blog_id The blog ID.
	 *
	 * @return bool
	 */
	protected function is_sitemap_enabled_for( $blog_id ) {
		if ( ! $this->is_yoast_active_on( $blog_id ) ) {
			return false;
		}

		$options = \get_blog_option( $blog_id, 'wpseo' );
		if ( ! $options || ! isset( $options['enable_xml_sitemap'] ) ) {
			// Default is enabled.
			return true;
		}

		return (bool) $options['enable_xml_sitemap'];
	}

	/**
	 * Determines whether Yoast SEO is active.
	 *
	 * @param int $blog_id The blog ID.
	 *
	 * @return bool
	 */
	protected function is_yoast_active_on( $blog_id ) {
		return \in_array( 'wordpress-seo/wp-seo.php', (array) \get_blog_option( $blog_id, 'active_plugins', [] ), true ) || $this->is_yoast_active_for_network();
	}

	/**
	 * Determines whether Yoast SEO is active for the entire network.
	 *
	 * @return bool
	 */
	protected function is_yoast_active_for_network() {
		$plugins = \get_network_option( null, 'active_sitewide_plugins' );
		if ( isset( $plugins['wordpress-seo/wp-seo.php'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the blog IDs of public, "active" sites on the network.
	 *
	 * @return array
	 */
	protected function get_blog_ids() {
		$criteria = [
			'archived'   => 0,
			'deleted'    => 0,
			'public'     => 1,
			'spam'       => 0,
			'fields'     => 'ids',
			'network_id' => \get_current_network_id(),
		];

		return \get_sites( $criteria );
	}
}
