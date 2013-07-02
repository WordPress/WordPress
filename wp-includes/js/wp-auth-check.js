// Interim login dialog
(function($){
	var wrap, check, scheduleTimeout, hideTimeout;

	function show() {
		var parent = $('#wp-auth-check'), form = $('#wp-auth-check-form'), noframe = wrap.find('.wp-auth-fallback-expired'), frame, loaded = false;

		if ( form.length ) {
			// Add unload confirmation to counter (frame-busting) JS redirects
			$(window).on( 'beforeunload.wp-auth-check', function(e) {
				e.originalEvent.returnValue = window.authcheckL10n.beforeunload;
			});

			frame = $('<iframe id="wp-auth-check-frame" frameborder="0">').attr( 'title', noframe.text() );
			frame.load( function(e) {
				var height, body;

				loaded = true;

				try {
					body = $(this).contents().find('body');
					height = body.height();
				} catch(e) {
					wrap.addClass('fallback');
					form.remove();
					noframe.focus();
					return;
				}

				if ( height ) {
					if ( body && body.hasClass('interim-login-success') ) {
						height += 35;
						parent.find('.wp-auth-check-close').show();
						wrap.data('logged-in', 1);
						hideTimeout = setTimeout( function() { hide(); }, 3000 );
					}

					parent.css( 'max-height', height + 60 + 'px' );
				}
			}).attr( 'src', form.data('src') );

			$('#wp-auth-check-form').append( frame );
		}

		wrap.removeClass('hidden');

		if ( frame ) {
			frame.focus();
			// WebKit doesn't throw an error if the iframe fails to load because of "X-Frame-Options: DENY" header.
			// Wait for 5 sec. and switch to the fallback text.
			setTimeout( function() {
				if ( ! loaded ) {
					wrap.addClass('fallback');
					form.remove();
					noframe.focus();
				}
			}, 5000 );
		} else {
			noframe.focus();
		}
	}

	function hide() {
		$(window).off( 'beforeunload.wp-auth-check' );
		window.clearTimeout( hideTimeout );

		// When on the Edit Post screen, speed up heartbeat after the user logs in to quickly refresh nonces
		if ( typeof adminpage != 'undefined' && ( adminpage == 'post-php' || adminpage == 'post-new-php' )
			 && typeof wp != 'undefined' && wp.heartbeat ) {

			wp.heartbeat.interval( 'fast', 1 );
		}

		wrap.fadeOut( 200, function() {
			wrap.addClass('hidden').css('display', '').find('.wp-auth-check-close').css('display', '');
			$('#wp-auth-check-frame').remove();
		});
	}

	function schedule() {
		check = false;
		window.clearTimeout( scheduleTimeout );
		scheduleTimeout = window.setTimeout( function(){ check = 1; }, 300000 ); // 5 min.
	}

	$( document ).on( 'heartbeat-tick.wp-auth-check', function( e, data ) {
		if ( check === 2 )
			schedule();

		if ( data['wp-auth-check'] && wrap.hasClass('hidden') ) {
			show();
		} else if ( ! data['wp-auth-check'] && ! wrap.hasClass('hidden') && ! wrap.data('logged-in') ) {
			hide();
		}
	}).ready( function() {
		schedule();
		wrap = $('#wp-auth-check-wrap').data( 'logged-in', 0 );
		wrap.find('.wp-auth-check-close').on( 'click', function(e) {
			hide();
		});
		// Bind later
		$( document ).on( 'heartbeat-send.wp-auth-check', function( e, data ) {
			var i, empty = true;
			// Check if something is using heartbeat. If yes, trigger the logged out check too.
			for ( i in data ) {
				if ( data.hasOwnProperty( i ) ) {
					empty = false;
					break;
				}
			}

			if ( check || ! empty )
				data['wp-auth-check'] = 1;

			if ( check )
				check = 2;
		});
	});

}(jQuery));
