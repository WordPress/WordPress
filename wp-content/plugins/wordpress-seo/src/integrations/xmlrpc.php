<?php

namespace Yoast\WP\SEO\Integrations;

use Yoast\WP\SEO\Conditionals\XMLRPC_Conditional;

/**
 * Noindexes the xmlrpc.php file and all ways to request it.
 *
 * @phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded -- Known false positive with acronyms. Fix expected in YoastCS 3.x.
 */
class XMLRPC implements Integration_Interface {

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * In this case when the current request is an XML-RPC request.
	 *
	 * @return array The conditionals based on which this class should be loaded.
	 */
	public static function get_conditionals() {
		return [ XMLRPC_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'xmlrpc_methods', [ $this, 'robots_header' ] );
	}

	/**
	 * Sets a noindex, follow x-robots-tag header on all XMLRPC requests.
	 *
	 * @codeCoverageIgnore Basically impossible to test from the command line.
	 *
	 * @param array $methods The methods.
	 *
	 * @return array The methods.
	 */
	public function robots_header( $methods ) {
		if ( \headers_sent() === false ) {
			\header( 'X-Robots-Tag: noindex, follow', true );
		}

		return $methods;
	}
}
