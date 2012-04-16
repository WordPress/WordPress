if ( typeof wp === 'undefined' )
	var wp = {};

(function( exports, $ ){
	var api = wp.customize,
		Loader;

	Loader = {
		supports: {
			history:  !! ( window.history && history.pushState ),
			hashchange: ('onhashchange' in window) && (document.documentMode === undefined || document.documentMode > 7)
		},

		initialize: function() {
			this.body    = $( document.body ).addClass('customize-support');
			this.window  = $( window );
			this.element = $( '<div id="customize-container" class="wp-full-overlay" />' ).appendTo( this.body );

			$('#wpbody').on( 'click', '.load-customize', function( event ) {
				event.preventDefault();

				// Load the theme.
				Loader.open( $(this).attr('href') );
			});

			// Add navigation listeners.
			if ( this.supports.history )
				this.window.on( 'popstate', Loader.popstate );

			if ( this.supports.hashchange )
				this.window.on( 'hashchange', Loader.hashchange );
		},
		popstate: function( e ) {
			var state = e.originalEvent.state;
			if ( state && state.customize )
				Loader.open( state.customize );
			else if ( Loader.active )
				Loader.close();
		},
		hashchange: function( e ) {
			var hash = window.location.toString().split('#')[1];

			if ( hash && 0 === hash.indexOf( 'customize=on' ) )
				Loader.open( wpCustomizeLoaderL10n.url + '?' + hash );

			if ( ! hash )
				Loader.close();
		},
		open: function( src ) {
			if ( this.active )
				return;
			this.active = true;

			this.iframe = $( '<iframe />', { src: src }).appendTo( this.element );

			// Create a postMessage connection with the iframe.
			this.messenger = new api.Messenger( src, this.iframe[0].contentWindow );

			// Wait for the connection from the iframe before sending any postMessage events.
			this.messenger.bind( 'ready', function() {
				Loader.messenger.send( 'back', wpCustomizeLoaderL10n.back );
			});

			this.messenger.bind( 'close', function() {
				if ( Loader.supports.history )
					history.back();
				else if ( Loader.supports.hashchange )
					window.location.hash = '';
				else
					Loader.close();
			});

			this.element.fadeIn( 200, function() {
				var hash = src.split('?')[1];

				Loader.body.addClass( 'customize-active full-overlay-active' );

				// Ensure we don't call pushState if the user hit the forward button.
				if ( Loader.supports.history && window.location.href !== src )
					history.pushState( { customize: src }, '', src );
				else if ( Loader.supports.hashchange && hash )
					window.location.hash = hash;
			});
		},
		close: function() {
			if ( ! this.active )
				return;
			this.active = false;

			this.element.fadeOut( 200, function() {
				Loader.iframe.remove();
				Loader.iframe    = null;
				Loader.messenger = null;
				Loader.body.removeClass( 'customize-active full-overlay-active' );
			});
		}
	};

	$( function() {
		if ( window.postMessage )
			Loader.initialize();
	});

	// Expose the API to the world.
	api.Loader = Loader;
})( wp, jQuery );
