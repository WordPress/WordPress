// Interim login dialog
(function($){
	var wrap;

	function show() {
		var parent = $('#wp-auth-check'), form = $('#wp-auth-check-form'), noframe = wrap.find('.wp-auth-fallback-expired'), frame, loaded = false;

		if ( form.length ) {
			// Add unload confirmation to counter (frame-busting) JS redirects
			$(window).on( 'beforeunload.wp-auth-check', function(e) {
				e.originalEvent.returnValue = window.authcheckL10n.beforeunload;
			});

			// Add 'sandbox' for browsers that support it, only restrict access to the top window.
			frame = $('<iframe id="wp-auth-check-frame" sandbox="allow-same-origin allow-forms allow-scripts" frameborder="0">').attr( 'title', noframe.text() );
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
				}

				if ( height ) {
					if ( body && body.hasClass('interim-login-success') ) {
						height += 35;
						parent.find('.wp-auth-check-close').show();
						wrap.data('logged-in', 1);
						setTimeout( function() { hide(); }, 3000 );
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

		wrap.fadeOut( 200, function() {
			wrap.addClass('hidden').css('display', '');
			$('#wp-auth-check-frame').remove();
		});
	}

	$( document ).on( 'heartbeat-tick.wp-auth-check', function( e, data ) {
		if ( data['wp-auth-check'] && wrap.hasClass('hidden') ) {
			show();
		} else if ( ! data['wp-auth-check'] && ! wrap.hasClass('hidden') && ! wrap.data('logged-in') ) {
			hide();
		}
	}).on( 'heartbeat-send.wp-auth-check', function( e, data ) {
		data['wp-auth-check'] = 1;
	}).ready( function() {
		wrap = $('#wp-auth-check-wrap').data('logged-in', 0);
		wrap.find('.wp-auth-check-close').on( 'click', function(e) {
			hide();
		});
	});

}(jQuery));
