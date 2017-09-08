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
 * @since     4.9.0
 */

( function( $ ) {
	var wpApiSettings = window.wpApiSettings;

	function apiRequest( options ) {
		options = apiRequest.buildAjaxOptions( options );
		return apiRequest.transport( options );
	}

	apiRequest.buildAjaxOptions = function( options ) {
		var url = options.url;
		var path = options.path;
		var namespaceTrimmed, endpointTrimmed;
		var headers, addNonceHeader, headerName;

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
			url = wpApiSettings.root + path.replace( /^\//, '' );
		}

		// If ?_wpnonce=... is present, no need to add a nonce header.
		addNonceHeader = ! ( options.data && options.data._wpnonce );

		headers = options.headers || {};

		// If an 'X-WP-Nonce' header (or any case-insensitive variation
		// thereof) was specified, no need to add a nonce header.
		if ( addNonceHeader ) {
			for ( headerName in headers ) {
				if ( headers.hasOwnProperty( headerName ) ) {
					if ( headerName.toLowerCase() === 'x-wp-nonce' ) {
						addNonceHeader = false;
						break;
					}
				}
			}
		}

		if ( addNonceHeader ) {
			// Do not mutate the original headers object, if any.
			headers = $.extend( {
				'X-WP-Nonce': wpApiSettings.nonce
			}, headers );
		}

		// Do not mutate the original options object.
		options = $.extend( {}, options, {
			headers: headers,
			url: url
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
