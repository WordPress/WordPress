/*global wc_geolocation_params */
jQuery( function( $ ) {
	/**
	 * Contains the current geo hash (or false if the hash
	 * is not set/cannot be determined).
	 *
	 * @type {boolean|string}
	 */
	var geo_hash = false;

	/**
	 * Obtains the current geo hash from the `woocommerce_geo_hash` cookie, if set.
	 *
	 * @returns {boolean}
	 */
	function get_geo_hash() {
		var geo_hash_cookie = Cookies.get( 'woocommerce_geo_hash' );

		if ( 'string' === typeof geo_hash_cookie && geo_hash_cookie.length ) {
			geo_hash = geo_hash_cookie;
			return true;
		}

		return false;
	}

	/**
	 * If we have an active geo hash value but it does not match the `?v=` query var in
	 * current page URL, that indicates that we need to refresh the page.
	 *
	 * @returns {boolean}
	 */
	function needs_refresh() {
		return geo_hash && ( new URLSearchParams( window.location.search ) ).get( 'v' ) !== geo_hash;
	}

	/**
	 * Appends (or replaces) the geo hash used for links on the current page.
	 */
	var $append_hashes = function() {
		if ( ! geo_hash ) {
			return;
		}

		$( 'a[href^="' + wc_geolocation_params.home_url + '"]:not(a[href*="v="]), a[href^="/"]:not(a[href*="v="])' ).each( function() {
			var $this      = $( this ),
				href       = $this.attr( 'href' ),
				href_parts = href.split( '#' );

			href = href_parts[0];

			if ( href.indexOf( '?' ) > 0 ) {
				href = href + '&v=' + geo_hash;
			} else {
				href = href + '?v=' + geo_hash;
			}

			if ( typeof href_parts[1] !== 'undefined' && href_parts[1] !== null ) {
				href = href + '#' + href_parts[1];
			}

			$this.attr( 'href', href );
		});
	};

	var $geolocate_customer = {
		url: wc_geolocation_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_customer_location' ),
		type: 'GET',
		success: function( response ) {
			if ( response.success && response.data.hash && response.data.hash !== geo_hash ) {
				$geolocation_redirect( response.data.hash );
			}
		}
	};

	/**
	 * Once we have a new hash, we redirect so a new version of the current page
	 * (with correct pricing for the current region, etc) is displayed.
	 *
	 * @param {string} hash
	 */
	var $geolocation_redirect = function( hash ) {
		// Updates our (cookie-based) cache of the hash value. Expires in 1 hour.
		Cookies.set( 'woocommerce_geo_hash', hash, { expires: 1 / 24 } );

		var this_page = window.location.toString();

		if ( this_page.indexOf( '?v=' ) > 0 || this_page.indexOf( '&v=' ) > 0 ) {
			this_page = this_page.replace( /v=[^&]+/, 'v=' + hash );
		} else if ( this_page.indexOf( '?' ) > 0 ) {
			this_page = this_page + '&v=' + hash;
		} else {
			this_page = this_page + '?v=' + hash;
		}

		window.location = this_page;
	};

	/**
	 * Updates any forms on the page so they use the current geo hash.
	 */
	function update_forms() {
		if ( ! geo_hash ) {
			return;
		}

		$( 'form' ).each( function () {
			var $this = $( this );
			var method = $this.attr( 'method' );
			var hasField = $this.find( 'input[name="v"]' ).length > 0;

			if ( method && 'get' === method.toLowerCase() && ! hasField ) {
				$this.append( '<input type="hidden" name="v" value="' + geo_hash + '" />' );
			} else {
				var href = $this.attr( 'action' );
				if ( href ) {
					if ( href.indexOf( '?' ) > 0 ) {
						$this.attr( 'action', href + '&v=' + geo_hash );
					} else {
						$this.attr( 'action', href + '?v=' + geo_hash );
					}
				}
			}
		});
	}

	// Get the current geo hash. If it doesn't exist, or if it doesn't match the current
	// page URL, perform a geolocation request.
	if ( ! get_geo_hash() || needs_refresh() ) {
		$.ajax( $geolocate_customer );
	}

	// Page updates.
	update_forms();
	$append_hashes();

	$( document.body ).on( 'added_to_cart', function() {
		$append_hashes();
	});

	// Enable user to trigger manual append hashes on AJAX operations
	$( document.body ).on( 'woocommerce_append_geo_hashes', function() {
		$append_hashes();
	});
});
