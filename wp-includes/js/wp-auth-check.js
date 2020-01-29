/**
 * Interim login dialog.
 *
 * @output wp-includes/js/wp-auth-check.js
 */

/* global adminpage */
(function($){
	var wrap, next;

	/**
	 * Shows the authentication form popup.
	 *
	 * @since 3.6.0
	 * @private
	 */
	function show() {
		var parent = $('#wp-auth-check'),
			form = $('#wp-auth-check-form'),
			noframe = wrap.find('.wp-auth-fallback-expired'),
			frame, loaded = false;

		if ( form.length ) {
			// Add unload confirmation to counter (frame-busting) JS redirects.
			$(window).on( 'beforeunload.wp-auth-check', function(e) {
				e.originalEvent.returnValue = window.authcheckL10n.beforeunload;
			});

			frame = $('<iframe id="wp-auth-check-frame" frameborder="0">').attr( 'title', noframe.text() );
			frame.on( 'load', function() {
				var height, body;

				loaded = true;
				// Remove the spinner to avoid unnecessary CPU/GPU usage.
				form.removeClass( 'loading' );

				try {
					body = $(this).contents().find('body');
					height = body.height();
				} catch(e) {
					wrap.addClass('fallback');
					parent.css( 'max-height', '' );
					form.remove();
					noframe.focus();
					return;
				}

				if ( height ) {
					if ( body && body.hasClass('interim-login-success') )
						hide();
					else
						parent.css( 'max-height', height + 40 + 'px' );
				} else if ( ! body || ! body.length ) {
					// Catch "silent" iframe origin exceptions in WebKit
					// after another page is loaded in the iframe.
					wrap.addClass('fallback');
					parent.css( 'max-height', '' );
					form.remove();
					noframe.focus();
				}
			}).attr( 'src', form.data('src') );

			form.append( frame );
		}

		$( 'body' ).addClass( 'modal-open' );
		wrap.removeClass('hidden');

		if ( frame ) {
			frame.focus();
			/*
			 * WebKit doesn't throw an error if the iframe fails to load
			 * because of "X-Frame-Options: DENY" header.
			 * Wait for 10 seconds and switch to the fallback text.
			 */
			setTimeout( function() {
				if ( ! loaded ) {
					wrap.addClass('fallback');
					form.remove();
					noframe.focus();
				}
			}, 10000 );
		} else {
			noframe.focus();
		}
	}

	/**
	 * Hides the authentication form popup.
	 *
	 * @since 3.6.0
	 * @private
	 */
	function hide() {
		$(window).off( 'beforeunload.wp-auth-check' );

		// When on the Edit Post screen, speed up heartbeat
		// after the user logs in to quickly refresh nonces.
		if ( typeof adminpage !== 'undefined' && ( adminpage === 'post-php' || adminpage === 'post-new-php' ) &&
			typeof wp !== 'undefined' && wp.heartbeat ) {

			$(document).off( 'heartbeat-tick.wp-auth-check' );
			wp.heartbeat.connectNow();
		}

		wrap.fadeOut( 200, function() {
			wrap.addClass('hidden').css('display', '');
			$('#wp-auth-check-frame').remove();
			$( 'body' ).removeClass( 'modal-open' );
		});
	}

	/**
	 * Schedules when the next time the authentication check will be done.
	 *
	 * @since 3.6.0
	 * @private
	 */
	function schedule() {
		// In seconds, default 3 min.
		var interval = parseInt( window.authcheckL10n.interval, 10 ) || 180;
		next = ( new Date() ).getTime() + ( interval * 1000 );
	}

	/**
	 * Binds to the Heartbeat Tick event.
	 *
	 * - Shows the authentication form popup if user is not logged in.
	 * - Hides the authentication form popup if it is already visible and user is
	 *   logged in.
	 *
	 * @ignore
	 *
	 * @since 3.6.0
	 *
	 * @param {Object} e The heartbeat-tick event that has been triggered.
	 * @param {Object} data Response data.
	 */
	$( document ).on( 'heartbeat-tick.wp-auth-check', function( e, data ) {
		if ( 'wp-auth-check' in data ) {
			schedule();
			if ( ! data['wp-auth-check'] && wrap.hasClass('hidden') ) {
				show();
			} else if ( data['wp-auth-check'] && ! wrap.hasClass('hidden') ) {
				hide();
			}
		}

	/**
	 * Binds to the Heartbeat Send event.
	 *
	 * @ignore
	 *
	 * @since 3.6.0
	 *
	 * @param {Object} e The heartbeat-send event that has been triggered.
	 * @param {Object} data Response data.
	 */
	}).on( 'heartbeat-send.wp-auth-check', function( e, data ) {
		if ( ( new Date() ).getTime() > next ) {
			data['wp-auth-check'] = true;
		}

	}).ready( function() {
		schedule();

		/**
		 * Hides the authentication form popup when the close icon is clicked.
		 *
		 * @ignore
		 *
		 * @since 3.6.0
		 */
		wrap = $('#wp-auth-check-wrap');
		wrap.find('.wp-auth-check-close').on( 'click', function() {
			hide();
		});
	});

}(jQuery));
