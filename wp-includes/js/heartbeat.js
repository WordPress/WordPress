/**
 * Heartbeat API
 */

 // Ensure the global `wp` object exists.
window.wp = window.wp || {};

(function($){
	var Heartbeat = function() {
		var self = this,
			running,
			timeout,
			nonce,
			screen = typeof pagenow != 'undefined' ? pagenow : '',
			settings,
			tick = 0,
			queue = {},
			interval,
			lastconnect = 0;

		this.url = typeof ajaxurl != 'undefined' ? ajaxurl : 'wp-admin/admin-ajax.php';
		this.autostart = true;

		if ( typeof( window.heartbeatSettings != 'undefined' ) ) {
			settings = $.extend( {}, window.heartbeatSettings );
			delete window.heartbeatSettings;

			// Add private vars
			nonce = settings.nonce || '';
			delete settings.nonce;

			interval = settings.interval || 15000; // default interval
			delete settings.interval;
			
			// todo: needed?
			// 'pagenow' can be added from settings if not already defined
			screen = screen || settings.pagenow;
			delete settings.pagenow;

			// Add public vars
			$.extend( this, settings );
		}

		function time(s) {
			if ( s )
				return parseInt( (new Date()).getTime() / 1000 );

			return (new Date()).getTime();
		}

		// Set error state and fire an event if it persists for over 3 min
		function errorstate() {
			var since;

			if ( lastconnect ) {
				since = time() - lastconnect;

				if ( since > 180000 ) {
					self.connectionLost = true;
					$(document).trigger( 'heartbeat-connection-lost', parseInt(since / 1000) );
				} else if ( self.connectionLost ) {
					self.connectionLost = false;
					$(document).trigger( 'heartbeat-connection-restored' );
				}
			}
		}

		function connect() {
			var data = {};
			tick = time();

			data.data = $.extend( {}, queue );
			queue = {};

			data.interval = interval / 1000;
			data._nonce = nonce;
			data.action = 'heartbeat';
			data.pagenow = screen;

			self.xhr = $.post( self.url, data, function(r){
				lastconnect = time();
				// Clear error state
				if ( self.connectionLost )
					errorstate();
				
				self.tick(r);
			}, 'json' ).always( function(){
				next();
			}).fail( function(r){
				errorstate();
				self.error(r);
			});
		};

		function next() {
			var delta = time() - tick;

			if ( !running )
				return;

			if ( delta < interval ) {
				timeout = window.setTimeout(
					function(){
						if ( running )
							connect();
					},
					interval - delta
				);
			} else {
				window.clearTimeout(timeout); // this has already expired?
				connect();
			}
		};

		this.interval = function(seconds) {
			if ( seconds ) {
				// Limit
				if ( 5 > seconds || seconds > 60 )
					return false;

				interval = seconds * 1000;
			} else if ( seconds === 0 ) {
				// Allow long polling to be turned on
				interval = 0;
			}
			return interval / 1000;
		};

		this.start = function() {
			// start only once
			if ( running )
				return false;

			running = true;
			connect();

			return true;
		};

		this.stop = function() {
			if ( !running )
				return false;

			if ( self.xhr )
				self.xhr.abort();

			running = false;
			return true;
		}

		this.send = function(action, data) {
			if ( action )
				queue[action] = data;
		}

		if ( this.autostart ) {
			$(document).ready( function(){
				// Start one tick (15 sec) after DOM ready
				running = true;
				tick = time();
				next();
			});
		}
			
	}

	$.extend( Heartbeat.prototype, {
		tick: function(r) {
			$(document).trigger( 'heartbeat-tick', r );
		},
		error: function(r) {
			$(document).trigger( 'heartbeat-error', r );
		}
	});

	wp.heartbeat = new Heartbeat();

}(jQuery));
