/**
 * @output wp-includes/js/api-request.js
 */

/**
 * Thin jQuery.ajax wrapper for WP REST API requests.
 *
 * Currently only applies to requests that do not use the `wp-api.js` Backbone
 * client library, though this may change.  Serves several purposes:
 *
 * - Allows overriding these requests as needed by customized WP installations.
 * - Sends the REST API nonce as a request header.
 * - Allows specifying only an endpoint namespace/path instead of a full URL.
 *
 * @since 4.9.0
 * @since 5.6.0 Added overriding of the "PUT" and "DELETE" methods with "POST".
 *              Added an "application/json" Accept header to all requests.
 *
 * @param {JQueryStatic} $ The jQuery object.
 */
( function( $ ) {
	var wpApiSettings = window.wpApiSettings;

	/**
	 * Makes an API request.
	 *
	 * @param {Object} options                 The options for the API request.
	 * @param {string} [options.url]           The full URL for the request.
	 * @param {string} [options.path]          The API path (e.g., 'wp/v2/posts'). Used if url is not provided.
	 * @param {string} [options.namespace]     The API namespace (e.g., 'wp/v2'). Used with endpoint to build path.
	 * @param {string} [options.endpoint]      The API endpoint (e.g., 'posts'). Used with namespace to build path.
	 * @param {string} [options.method]        The HTTP method (GET, POST, PUT, DELETE, etc.).
	 * @param {Object} [options.data]          The request data to send.
	 * @param {string} [options.data._wpnonce] Nonce for security.
	 * @param {Object} [options.headers]       Custom headers to include in the request.
	 * @return {JQueryXHR} The jQuery XHR object.
	 */
	function apiRequest( options ) {
		options = apiRequest.buildAjaxOptions( options );
		return apiRequest.transport( options );
	}

	/**
	 * Builds the options for the jQuery.ajax request.
	 *
	 * @param {Object} options             The options for the API request.
	 * @param {string} [options.url]       The full URL for the request.
	 * @param {string} [options.path]      The API path (e.g., 'wp/v2/posts'). Used if url is not provided.
	 * @param {string} [options.namespace] The API namespace (e.g., 'wp/v2'). Used with endpoint to build path.
	 * @param {string} [options.endpoint]  The API endpoint (e.g., 'posts'). Used with namespace to build path.
	 * @param {string} [options.method]    The HTTP method (GET, POST, PUT, DELETE, etc.).
	 * @param {Object} [options.data]      The request data to send.
	 * @param {Object} [options.headers]   Custom headers to include in the request.
	 * @return {Object} The options for the jQuery.ajax request.
	 */
	apiRequest.buildAjaxOptions = function( options ) {
		var url = options.url;
		var path = options.path;
		var method = options.method;
		var namespaceTrimmed, endpointTrimmed, apiRoot;
		var headers, addNonceHeader, addAcceptHeader, headerName;

		if (
			typeof options.namespace === 'string' &&
			typeof options.endpoint === 'string'
		) {
			namespaceTrimmed = options.namespace.replace( /^\/|\/$/g, '' );
			endpointTrimmed = options.endpoint.replace( /^\//, '' );
			if ( endpointTrimmed ) {
				path = namespaceTrimmed + '/' + endpointTrimmed;
			} else {
				path = namespaceTrimmed;
			}
		}
		if ( typeof path === 'string' ) {
			apiRoot = wpApiSettings.root;
			path = path.replace( /^\//, '' );

			// API root may already include query parameter prefix
			// if site is configured to use plain permalinks.
			if ( 'string' === typeof apiRoot && -1 !== apiRoot.indexOf( '?' ) ) {
				path = path.replace( '?', '&' );
			}

			url = apiRoot + path;
		}

		// If ?_wpnonce=... is present, no need to add a nonce header.
		addNonceHeader = ! ( options.data && options.data._wpnonce );
		addAcceptHeader = true;

		headers = options.headers || {};

		for ( headerName in headers ) {
			if ( ! headers.hasOwnProperty( headerName ) ) {
				continue;
			}

			// If an 'X-WP-Nonce' or 'Accept' header (or any case-insensitive variation
			// thereof) was specified, no need to add the header again.
			switch ( headerName.toLowerCase() ) {
				case 'x-wp-nonce':
					addNonceHeader = false;
					break;
				case 'accept':
					addAcceptHeader = false;
					break;
			}
		}

		if ( addNonceHeader ) {
			// Do not mutate the original headers object, if any.
			headers = $.extend( {
				'X-WP-Nonce': wpApiSettings.nonce
			}, headers );
		}

		if ( addAcceptHeader ) {
			headers = $.extend( {
				'Accept': 'application/json, */*;q=0.1'
			}, headers );
		}

		if ( typeof method === 'string' ) {
			method = method.toUpperCase();

			if ( 'PUT' === method || 'DELETE' === method ) {
				headers = $.extend( {
					'X-HTTP-Method-Override': method
				}, headers );

				method = 'POST';
			}
		}

		// Do not mutate the original options object.
		options = $.extend( {}, options, {
			headers: headers,
			url: url,
			method: method
		} );

		delete options.path;
		delete options.namespace;
		delete options.endpoint;

		return options;
	};

	apiRequest.transport = $.ajax;

	/** @namespace wp */
	window.wp = window.wp || {};
	window.wp.apiRequest = apiRequest;
} )( jQuery );
