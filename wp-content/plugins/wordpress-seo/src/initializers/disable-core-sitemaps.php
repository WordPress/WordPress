<?php

namespace Yoast\WP\SEO\Initializers;

use Yoast\WP\SEO\Conditionals\No_Conditionals;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Helpers\Redirect_Helper;

/**
 * Disables the WP core sitemaps.
 */
class Disable_Core_Sitemaps implements Initializer_Interface {

	use No_Conditionals;

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The redirect helper.
	 *
	 * @var Redirect_Helper
	 */
	private $redirect;

	/**
	 * Sitemaps_Enabled_Conditional constructor.
	 *
	 * @codeCoverageIgnore
	 *
	 * @param Options_Helper  $options  The options helper.
	 * @param Redirect_Helper $redirect The redirect helper.
	 */
	public function __construct( Options_Helper $options, Redirect_Helper $redirect ) {
		$this->options  = $options;
		$this->redirect = $redirect;
	}

	/**
	 * Disable the WP core XML sitemaps.
	 *
	 * @return void
	 */
	public function initialize() {
		// This needs to be on priority 15 as that is after our options initialize.
		\add_action( 'plugins_loaded', [ $this, 'maybe_disable_core_sitemaps' ], 15 );
	}

	/**
	 * Disables the core sitemaps if Yoast SEO sitemaps are enabled.
	 *
	 * @return void
	 */
	public function maybe_disable_core_sitemaps() {
		if ( $this->options->get( 'enable_xml_sitemap' ) ) {
			\add_filter( 'wp_sitemaps_enabled', '__return_false' );

			\add_action( 'template_redirect', [ $this, 'template_redirect' ], 0 );
		}
	}

	/**
	 * Redirects requests to the WordPress sitemap to the Yoast sitemap.
	 *
	 * @return void
	 */
	public function template_redirect() {
		// If there is no path, nothing to do.
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$path = \sanitize_text_field( \wp_unslash( $_SERVER['REQUEST_URI'] ) );

		// If it's not a wp-sitemap request, nothing to do.
		if ( \substr( $path, 0, 11 ) !== '/wp-sitemap' ) {
			return;
		}

		$redirect = $this->get_redirect_url( $path );

		if ( ! $redirect ) {
			return;
		}

		$this->redirect->do_safe_redirect( \home_url( $redirect ), 301 );
	}

	/**
	 * Returns the relative sitemap URL to redirect to.
	 *
	 * @param string $path The original path.
	 *
	 * @return string|false The path to redirct to. False if no redirect should be done.
	 */
	private function get_redirect_url( $path ) {
		// Start with the simple string comparison so we avoid doing unnecessary regexes.
		if ( $path === '/wp-sitemap.xml' ) {
			return '/sitemap_index.xml';
		}

		if ( \preg_match( '/^\/wp-sitemap-(posts|taxonomies)-(\w+)-(\d+)\.xml$/', $path, $matches ) ) {
			$index = ( (int) $matches[3] - 1 );
			$index = ( $index === 0 ) ? '' : (string) $index;

			return '/' . $matches[2] . '-sitemap' . $index . '.xml';
		}

		if ( \preg_match( '/^\/wp-sitemap-users-(\d+)\.xml$/', $path, $matches ) ) {
			$index = ( (int) $matches[1] - 1 );
			$index = ( $index === 0 ) ? '' : (string) $index;

			return '/author-sitemap' . $index . '.xml';
		}

		return false;
	}
}
