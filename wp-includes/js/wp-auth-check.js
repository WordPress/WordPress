// Interim login dialog
(function($){
	var wrap, check, next;

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
					// Catch "silent" iframe origin exceptions in WebKit after another page is loaded in the iframe
					wrap.addClass('fallback');
					parent.css( 'max-height', '' );
					form.remove();
					noframe.focus();
				}
			}).attr( 'src', form.data('src') );

			$('#wp-auth-check-form').append( frame );
		}

		wrap.removeClass('hidden');

		if ( frame ) {
			frame.focus();
			// WebKit doesn't throw an error if the iframe fails to load because of "X-Frame-Options: DENY" header.
			// Wait for 10 sec. and switch to the fallback text.
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

	function hide() {
		$(window).off( 'beforeunload.wp-auth-check' );

		// When on the Edit Post screen, speed up heartbeat after the user logs in to quickly refresh nonces
		if ( typeof adminpage != 'undefined' && ( adminpage == 'post-php' || adminpage == 'post-new-php' )
			 && typeof wp != 'undefined' && wp.heartbeat ) {

			wp.heartbeat.interval( 'fast', 1 );
		}

		wrap.fadeOut( 200, function() {
			wrap.addClass('hidden').css('display', '');
			$('#wp-auth-check-frame').remove();
		});
	}

	function schedule() {
		var interval = parseInt( window.authcheckL10n.interval, 10 ) || 180; // in seconds, default 3 min.
		next = ( new Date() ).getTime() + ( interval * 1000 );
	}

	$( document ).on( 'heartbeat-tick.wp-auth-check', function( e, data ) {
		if ( 'wp-auth-check' in data ) {
			schedule();
			if ( ! data['wp-auth-check'] && wrap.hasClass('hidden') )
				show();
			else if ( data['wp-auth-check'] && ! wrap.hasClass('hidden') )
				hide();
		}
	}).on( 'heartbeat-send.wp-auth-check', function( e, data ) {
		if ( ( new Date() ).getTime() > next )
			data['wp-auth-check'] = true;
	}).ready( function() {
		schedule();
		wrap = $('#wp-auth-check-wrap');
		wrap.find('.wp-auth-check-close').on( 'click', function(e) {
			hide();
		});
	});

}(jQuery));
