/**
 * Heartbeat API
 *
 * Note: this API is "experimental" meaning it will likely change a lot
 * in the next few releases based on feedback from 3.6.0. If you intend
 * to use it, please follow the development closely.
 *
 * Heartbeat is a simple server polling API that sends XHR requests to
 * the server every 15 seconds and triggers events (or callbacks) upon
 * receiving data. Currently these 'ticks' handle transports for post locking,
 * login-expiration warnings, and related tasks while a user is logged in.
 *
 * Available filters in ajax-actions.php:
 * - heartbeat_received
 * - heartbeat_send
 * - heartbeat_tick
 * - heartbeat_nopriv_received
 * - heartbeat_nopriv_send
 * - heartbeat_nopriv_tick
 * @see wp_ajax_nopriv_heartbeat(), wp_ajax_heartbeat()
 *
 * @since 3.6.0
 */

 // Ensure the global `wp` object exists.
window.wp = window.wp || {};

(function($){
	var Heartbeat = function() {
		var self = this,
			running,
			beat,
			screenId = typeof pagenow != 'undefined' ? pagenow : '',
			url = typeof ajaxurl != 'undefined' ? ajaxurl : '',
			settings,
			tick = 0,
			queue = {},
			interval,
			connecting,
			countdown = 0,
			errorcount = 0,
			tempInterval,
			hasFocus = true,
			isUserActive,
			userActiveEvents,
			winBlurTimeout,
			frameBlurTimeout = -1,
			hasConnectionError = false;

		/**
		 * Returns a boolean that's indicative of whether or not there is a connection error
		 *
		 * @returns boolean
		 */
		this.hasConnectionError = function() {
			return hasConnectionError;
		};

		if ( typeof( window.heartbeatSettings ) == 'object' ) {
			settings = $.extend( {}, window.heartbeatSettings );

			// Add private vars
			url = settings.ajaxurl || url;
			delete settings.ajaxurl;
			delete settings.nonce;

			interval = settings.interval || 15; // default interval
			delete settings.interval;
			// The interval can be from 15 to 60 sec. and can be set temporarily to 5 sec.
			if ( interval < 15 )
				interval = 15;
			else if ( interval > 60 )
				interval = 60;

			interval = interval * 1000;

			// 'screenId' can be added from settings on the front-end where the JS global 'pagenow' is not set
			screenId = screenId || settings.screenId || 'front';
			delete settings.screenId;

			// Add or overwrite public vars
			$.extend( this, settings );
		}

		function time(s) {
			if ( s )
				return parseInt( (new Date()).getTime() / 1000 );

			return (new Date()).getTime();
		}

		function isLocalFrame( frame ) {
			var origin, src = frame.src;

			if ( src && /^https?:\/\//.test( src ) ) {
				origin = window.location.origin ? window.location.origin : window.location.protocol + '//' + window.location.host;

				if ( src.indexOf( origin ) !== 0 )
					return false;
			}

			try {
				if ( frame.contentWindow.document )
					return true;
			} catch(e) {}

			return false;
		}

		// Set error state and fire an event on XHR errors or timeout
		function errorstate( error ) {
			var trigger;

			if ( error ) {
				switch ( error ) {
					case 'abort':
						// do nothing
						break;
					case 'timeout':
						// no response for 30 sec.
						trigger = true;
						break;
					case 'parsererror':
					case 'error':
					case 'empty':
					case 'unknown':
						errorcount++;

						if ( errorcount > 2 )
							trigger = true;

						break;
				}

				if ( trigger && ! self.hasConnectionError() ) {
					hasConnectionError = true;
					$(document).trigger( 'heartbeat-connection-lost', [error] );
				}
			} else if ( self.hasConnectionError() ) {
				errorcount = 0;
				hasConnectionError = false;
				$(document).trigger( 'heartbeat-connection-restored' );
			}
		}

		function connect() {
			var send = {}, data, i, empty = true,
			nonce = typeof window.heartbeatSettings == 'object' ? window.heartbeatSettings.nonce : '';
			tick = time();

			data = $.extend( {}, queue );
			// Clear the data queue, anything added after this point will be send on the next tick
			queue = {};

			$(document).trigger( 'heartbeat-send', [data] );

			for ( i in data ) {
				if ( data.hasOwnProperty( i ) ) {
					empty = false;
					break;
				}
			}

			// If nothing to send (nothing is expecting a response),
			// schedule the next tick and bail
			if ( empty && ! self.hasConnectionError() ) {
				connecting = false;
				next();
				return;
			}

			send.data = data;
			send.interval = interval / 1000;
			send._nonce = nonce;
			send.action = 'heartbeat';
			send.screen_id = screenId;
			send.has_focus = hasFocus;

			connecting = true;
			self.xhr = $.ajax({
				url: url,
				type: 'post',
				timeout: 30000, // throw an error if not completed after 30 sec.
				data: send,
				dataType: 'json'
			}).done( function( response, textStatus, jqXHR ) {
				var new_interval;

				if ( ! response )
					return errorstate( 'empty' );

				// Clear error state
				if ( self.hasConnectionError() )
					errorstate();

				if ( response.nonces_expired ) {
					$(document).trigger( 'heartbeat-nonces-expired' );
					return;
				}

				// Change the interval from PHP
				if ( response.heartbeat_interval ) {
					new_interval = response.heartbeat_interval;
					delete response.heartbeat_interval;
				}

				self.tick( response, textStatus, jqXHR );

				// do this last, can trigger the next XHR if connection time > 5 sec. and new_interval == 'fast'
				if ( new_interval )
					self.interval.call( self, new_interval );
			}).always( function() {
				connecting = false;
				next();
			}).fail( function( jqXHR, textStatus, error ) {
				errorstate( textStatus || 'unknown' );
				self.error( jqXHR, textStatus, error );
			});
		}

		function next() {
			var delta = time() - tick, t = interval;

			if ( ! running )
				return;

			if ( ! hasFocus ) {
				t = 100000; // 100 sec. Post locks expire after 120 sec.
			} else if ( countdown > 0 && tempInterval ) {
				t = tempInterval;
				countdown--;
			}

			window.clearTimeout(beat);

			if ( delta < t ) {
				beat = window.setTimeout(
					function(){
						if ( running )
							connect();
					},
					t - delta
				);
			} else {
				connect();
			}
		}

		function blurred() {
			window.clearTimeout(winBlurTimeout);
			window.clearTimeout(frameBlurTimeout);
			winBlurTimeout = frameBlurTimeout = 0;

			hasFocus = false;
		}

		function focused() {
			window.clearTimeout(winBlurTimeout);
			window.clearTimeout(frameBlurTimeout);
			winBlurTimeout = frameBlurTimeout = 0;

			isUserActive = time();

			if ( hasFocus )
				return;

			hasFocus = true;
			window.clearTimeout(beat);

			if ( ! connecting )
				next();
		}

		function setFrameEvents() {
			$('iframe').each( function( i, frame ){
				if ( ! isLocalFrame( frame ) )
					return;

				if ( $.data( frame, 'wp-heartbeat-focus' ) )
					return;

				$.data( frame, 'wp-heartbeat-focus', 1 );

				$( frame.contentWindow ).on( 'focus.wp-heartbeat-focus', function(e) {
					focused();
				}).on('blur.wp-heartbeat-focus', function(e) {
					setFrameEvents();
					frameBlurTimeout = window.setTimeout( function(){ blurred(); }, 500 );
				});
			});
		}

		$(window).on( 'blur.wp-heartbeat-focus', function(e) {
			setFrameEvents();
			winBlurTimeout = window.setTimeout( function(){ blurred(); }, 500 );
		}).on( 'focus.wp-heartbeat-focus', function() {
			$('iframe').each( function( i, frame ) {
				if ( !isLocalFrame( frame ) )
					return;

				$.removeData( frame, 'wp-heartbeat-focus' );
				$( frame.contentWindow ).off( '.wp-heartbeat-focus' );
			});

			focused();
		});

		function userIsActive() {
			userActiveEvents = false;
			$(document).off( '.wp-heartbeat-active' );
			$('iframe').each( function( i, frame ) {
				if ( ! isLocalFrame( frame ) )
					return;

				$( frame.contentWindow ).off( '.wp-heartbeat-active' );
			});

			focused();
		}

		// Set 'hasFocus = true' if user is active and the window is in the background.
		// Set 'hasFocus = false' if the user has been inactive (no mouse or keyboard activity) for 5 min. even when the window has focus.
		function checkUserActive() {
			var lastActive = isUserActive ? time() - isUserActive : 0;

			// Throttle down when no mouse or keyboard activity for 5 min
			if ( lastActive > 300000 && hasFocus )
				 blurred();

			if ( ! userActiveEvents ) {
				$(document).on( 'mouseover.wp-heartbeat-active keyup.wp-heartbeat-active', function(){ userIsActive(); } );

				$('iframe').each( function( i, frame ) {
					if ( ! isLocalFrame( frame ) )
						return;

					$( frame.contentWindow ).on( 'mouseover.wp-heartbeat-active keyup.wp-heartbeat-active', function(){ userIsActive(); } );
				});

				userActiveEvents = true;
			}
		}

		// Check for user activity every 30 seconds.
		window.setInterval( function(){ checkUserActive(); }, 30000 );
		$(document).ready( function() {
			// Start one tick (15 sec) after DOM ready
			running = true;
			tick = time();
			next();
		});

		this.hasFocus = function() {
			return hasFocus;
		};

		/**
		 * Get/Set the interval
		 *
		 * When setting to 'fast', the interval is 5 sec. for the next 30 ticks (for 2 min and 30 sec).
		 * If the window doesn't have focus, the interval slows down to 2 min.
		 *
		 * @param string speed Interval speed: 'fast' (5sec), 'standard' (15sec) default, 'slow' (60sec)
		 * @param string ticks Used with speed = 'fast', how many ticks before the speed reverts back
		 * @return int Current interval in seconds
		 */
		this.interval = function( speed, ticks ) {
			var reset, seconds;
			ticks = parseInt( ticks, 10 ) || 30;
			ticks = ticks < 1 || ticks > 30 ? 30 : ticks;

			if ( speed ) {
				switch ( speed ) {
					case 'fast':
						seconds = 5;
						countdown = ticks;
						break;
					case 'slow':
						seconds = 60;
						countdown = 0;
						break;
					case 'long-polling':
						// Allow long polling, (experimental)
						interval = 0;
						return 0;
						break;
					default:
						seconds = 15;
						countdown = 0;
				}

				// Reset when the new interval value is lower than the current one
				reset = seconds * 1000 < interval;

				if ( countdown > 0 ) {
					tempInterval = seconds * 1000;
				} else {
					interval = seconds * 1000;
					tempInterval = 0;
				}

				if ( reset )
					next();
			}

			if ( ! hasFocus )
				return 120;

			return tempInterval ? tempInterval / 1000 : interval / 1000;
		};

		/**
		 * Enqueue data to send with the next XHR
		 *
		 * As the data is sent later, this function doesn't return the XHR response.
		 * To see the response, use the custom jQuery event 'heartbeat-tick' on the document, example:
		 *		$(document).on( 'heartbeat-tick.myname', function( event, data, textStatus, jqXHR ) {
		 *			// code
		 *		});
		 * If the same 'handle' is used more than once, the data is not overwritten when the third argument is 'true'.
		 * Use wp.heartbeat.isQueued('handle') to see if any data is already queued for that handle.
		 *
		 * $param string handle Unique handle for the data. The handle is used in PHP to receive the data.
		 * $param mixed data The data to send.
		 * $param bool dont_overwrite Whether to overwrite existing data in the queue.
		 * $return bool Whether the data was queued or not.
		 */
		this.enqueue = function( handle, data, dont_overwrite ) {
			if ( handle ) {
				if ( queue.hasOwnProperty( handle ) && dont_overwrite )
					return false;

				queue[handle] = data;
				return true;
			}
			return false;
		};

		/**
		 * Check if data with a particular handle is queued
		 *
		 * $param string handle The handle for the data
		 * $return mixed The data queued with that handle or null
		 */
		this.isQueued = function( handle ) {
			return queue[handle];
		};
	};

	$.extend( Heartbeat.prototype, {
		tick: function( data, textStatus, jqXHR ) {
			$(document).trigger( 'heartbeat-tick', [data, textStatus, jqXHR] );
		},
		error: function( jqXHR, textStatus, error ) {
			$(document).trigger( 'heartbeat-error', [jqXHR, textStatus, error] );
		}
	});

	wp.heartbeat = new Heartbeat();

}(jQuery));
