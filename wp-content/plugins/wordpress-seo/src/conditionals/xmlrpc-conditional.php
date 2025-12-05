<?php

namespace Yoast\WP\SEO\Conditionals;

/**
 * Conditional that is met when the current request is an XML-RPC request.
 */
class XMLRPC_Conditional implements Conditional {

	/**
	 * Returns whether the current request is an XML-RPC request.
	 *
	 * @return bool `true` when the current request is an XML-RPC request, `false` if not.
	 */
	public function is_met() {
		return ( \defined( 'XMLRPC_REQUEST' ) && \XMLRPC_REQUEST );
	}
}
