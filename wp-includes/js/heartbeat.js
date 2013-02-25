/**
 * Heartbeat API
 */

 // Ensure the global `wp` object exists.
window.wp = window.wp || {};

(function($){
	var Heartbeat = function() {
		var self = this,
			running,
			beat,
			nonce,
			screenid = typeof pagenow != 'undefined' ? pagenow : '',
			settings,
			tick = 0,
			queue = {},
			interval,
			lastconnect = 0,
			connecting,
			countdown,
			tempInterval,
			hasFocus = true,
			isUserActive,
			userActiveEvents,
			winBlurTimeout,
			frameBlurTimeout = -1;

		this.url = typeof ajaxurl != 'undefined' ? ajaxurl : 'wp-admin/admin-ajax.php';
		this.autostart = true;

		if ( typeof( window.heartbeatSettings != 'undefined' ) ) {
			settings = $.extend( {}, window.heartbeatSettings );
			window.heartbeatSettings = null;

			// Add private vars
			nonce = settings.nonce || '';
			delete settings.nonce;

			interval = settings.interval || 15; // default interval
			delete settings.interval;
			// The interval can be from 5 to 60 sec.
			if ( interval < 5 )
				interval = 5;
			else if ( interval > 60 )
				interval = 60;

			interval = interval * 1000;

			// todo: needed?
			// 'screenid' can be added from settings on the front-end where the JS global 'pagenow' is not set
			screenid = screenid || settings.screenid || 'site';
			delete settings.screenid;

			// Add or overwrite public vars
			$.extend( this, settings );
		}

		function time(s) {
			if ( s )
				return parseInt( (new Date()).getTime() / 1000 );

			return (new Date()).getTime();
		}

		function isLocalFrame(frame) {
			try {
				if ( frame.contentWindow.document )
					return true;
			} catch(e) {}

			return false;
		}

		// Set error state and fire an event if errors persist for over 2 min when the window has focus
		// or 6 min when the window is in the background
		function errorstate() {
			var since;

			if ( lastconnect ) {
				since = time() - lastconnect, duration = hasFocus ? 120000 : 360000;

				if ( since > duration ) {
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
			$(document).trigger( 'heartbeat-send', [data.data] );

			data.interval = interval / 1000;
			data._nonce = nonce;
			data.action = 'heartbeat';
			data.screenid = screenid;
			data.has_focus = hasFocus;

			connecting = true;
			self.xhr = $.post( self.url, data, 'json' )
			.done( function( data, textStatus, jqXHR ) {
				var interval;

				// Clear the data queue
				queue = {};

				// Clear error state
				lastconnect = time();
				if ( self.connectionLost )
					errorstate();

				// Change the interval from PHP
				interval = data.heartbeat_interval;
				delete data.heartbeat_interval;

				self.tick( data, textStatus, jqXHR );

				// do this last, can trigger the next XHR
				if ( interval )
					self.interval.apply( self, data.heartbeat_interval );
			}).always( function(){
				connecting = false;
				next();
			}).fail( function( jqXHR, textStatus, error ){
				errorstate();
				self.error( jqXHR, textStatus, error );
			});
		};

		function next() {
			var delta = time() - tick, t = interval;

			if ( !running )
				return;

			if ( !hasFocus ) {
				t = 120000; // 2 min
			} else if ( countdown && tempInterval ) {
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

			// temp debug
			if ( self.debug )
				console.log('### blurred(), slow down...')
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

			if ( !connecting )
				next();

			// temp debug
			if ( self.debug )
				console.log('### focused(), speed up... ')
		}

		function setFrameEvents() {
			$('iframe').each( function(i, frame){
				if ( !isLocalFrame(frame) )
					return;

				if ( $.data(frame, 'wp-heartbeat-focus') )
					return;

				$.data(frame, 'wp-heartbeat-focus', 1);

				$(frame.contentWindow).on('focus.wp-heartbeat-focus', function(e){
					focused();
				}).on('blur.wp-heartbeat-focus', function(e){
					setFrameEvents();
					frameBlurTimeout = window.setTimeout( function(){ blurred(); }, 500 );
				});
			});
		}

		$(window).on('blur.wp-heartbeat-focus', function(e){
			setFrameEvents();
			winBlurTimeout = window.setTimeout( function(){ blurred(); }, 500 );
		}).on('focus.wp-heartbeat-focus', function(){
			$('iframe').each( function(i, frame){
				if ( !isLocalFrame(frame) )
					return;

				$.removeData(frame, 'wp-heartbeat-focus');
				$(frame.contentWindow).off('.wp-heartbeat-focus');
			});

			focused();
		});

		function userIsActive() {
			userActiveEvents = false;
			$(document).off('.wp-heartbeat-active');
			$('iframe').each( function(i, frame){
				if ( !isLocalFrame(frame) )
					return;

				$(frame.contentWindow).off('.wp-heartbeat-active');
			});

			focused();

			// temp debug
			if ( self.debug )
				console.log( 'userIsActive()' );
		}

		// Set 'hasFocus = true' if user is active and the window is in the background.
		// Set 'hasFocus = false' if the user has been inactive (no mouse or keyboard activity) for 5 min. even when the window has focus.
		function checkUserActive() {
			var lastActive = isUserActive ? time() - isUserActive : 0;

			// temp debug
			if ( self.debug )
				console.log( 'checkUserActive(), lastActive = %s seconds ago', parseInt(lastActive / 1000) || 'null' );

			// Throttle down when no mouse or keyboard activity for 5 min
			if ( lastActive > 300000 && hasFocus )
				 blurred();

			if ( !userActiveEvents ) {
				$(document).on('mouseover.wp-heartbeat-active keyup.wp-heartbeat-active', function(){ userIsActive(); });
				$('iframe').each( function(i, frame){
					if ( !isLocalFrame(frame) )
						return;

					$(frame.contentWindow).on('mouseover.wp-heartbeat-active keyup.wp-heartbeat-active', function(){ userIsActive(); });
				});
				userActiveEvents = true;
			}
		}

		// Check for user activity every 30 seconds.
		window.setInterval( function(){ checkUserActive(); }, 30000 );

		if ( this.autostart ) {
			$(document).ready( function(){
				// Start one tick (15 sec) after DOM ready
				running = true;
				tick = time();
				next();
			});
		}

		this.winHasFocus = function() {
			return hasFocus;
		}

		/**
		 * Get/Set the interval
		 *
		 * When setting the interval to 'fast', the number of ticks is specified wiht the second argument, default 30.
		 * If the window doesn't have focus, the interval is overridden to 2 min. In this case setting the 'ticks'
		 * will start counting after the window gets focus.
		 *
		 * @param string speed Interval speed: 'fast' (5sec), 'standard' (15sec) default, 'slow' (60sec)
		 * @param int ticks Number of ticks for the changed interval, optional when setting 'standard' or 'slow'
		 * @return int Current interval in seconds
		 */
		this.interval = function(speed, ticks) {
			var reset, seconds;

			if ( speed ) {
				switch ( speed ) {
					case 'fast':
						seconds = 5;
						countdown = parseInt(ticks) || 30;
						break;
					case 'slow':
						seconds = 60;
						countdown = parseInt(ticks) || 0;
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

				if ( countdown ) {
					tempInterval = seconds * 1000;
				} else {
					interval = seconds * 1000;
					tempInterval = 0;
				}

				if ( reset )
					next();
			}

			if ( !hasFocus )
				return 120;

			return tempInterval ? tempInterval / 1000 : interval / 1000;
		};

		// Start. Has no effect if heartbeat is already running
		this.start = function() {
			if ( running )
				return false;

			running = true;
			connect();
			return true;
		};

		// Stop. If a XHR is in progress, abort it
		this.stop = function() {
			if ( self.xhr && self.xhr.readyState != 4 )
				self.xhr.abort();

			running = false;
			return true;
		}

		/**
		 * Send data with the next XHR
		 *
		 * As the data is sent later, this function doesn't return the XHR response.
		 * To see the response, use the custom jQuery event 'heartbeat-tick' on the document, example:
		 *		$(document).on('heartbeat-tick.myname', function(data, textStatus, jqXHR) {
		 *			// code
		 *		});
		 * If the same 'handle' is used more than once, the data is overwritten when the third argument is 'true'.
		 * Use wp.heartbeat.isQueued('handle') to see if any data is already queued for that handle.
		 *
		 * $param string handle Unique handle for the data. The handle is used in PHP to receive the data
		 * $param mixed data The data to be sent
		 * $param bool overwrite Whether to overwrite existing data in the queue
		 * $return bool Whether the data was queued or not
		 */
		this.send = function(handle, data, overwrite) {
			if ( handle ) {
				if ( queue.hasOwnProperty(handle) && !overwrite )
					return false;

				queue[handle] = data;
				return true;
			}
			return false;
		}

		/**
		 * Check if data with a particular handle is queued
		 *
		 * $param string handle The handle for the data
		 * $return mixed The data queued with that handle or null
		 */
		this.isQueued = function(handle) {
			return queue[handle];
		}
	}

	$.extend( Heartbeat.prototype, {
		tick: function(data, textStatus, jqXHR) {
			$(document).trigger( 'heartbeat-tick', [data, textStatus, jqXHR] );
		},
		error: function(jqXHR, textStatus, error) {
			$(document).trigger( 'heartbeat-error', [jqXHR, textStatus, error] );
		}
	});

	wp.heartbeat = new Heartbeat();

}(jQuery));
