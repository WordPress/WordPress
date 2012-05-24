if ( typeof wp === 'undefined' )
	var wp = {};

(function( exports, $ ){
	var api = wp.customize,
		Loader;

	Loader = $.extend( {}, api.Events, {
		supports: {
			history:  !! ( window.history && history.pushState ),
			hashchange: ('onhashchange' in window) && (document.documentMode === undefined || document.documentMode > 7)
		},

		initialize: function() {
			this.body    = $( document.body ).addClass('customize-support');
			this.window  = $( window );
			this.element = $( '<div id="customize-container" class="wp-full-overlay" />' ).appendTo( this.body );

			this.bind( 'open', this.overlay.show );
			this.bind( 'close', this.overlay.hide );

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

			if ( ! hash && ! Loader.supports.history )
				Loader.close();
		},

		open: function( src ) {
			var hash;

			if ( this.active )
				return;

			this.active = true;
			this.body.addClass('customize-loading');

			this.iframe = $( '<iframe />', { src: src }).appendTo( this.element );
			this.iframe.one( 'load', this.loaded );

			// Create a postMessage connection with the iframe.
			this.messenger = new api.Messenger( src, this.iframe[0].contentWindow );

			// Wait for the connection from the iframe before sending any postMessage events.
			this.messenger.bind( 'ready', function() {
				Loader.messenger.send( 'back' );
			});

			this.messenger.bind( 'close', function() {
				if ( Loader.supports.history )
					history.back();
				else if ( Loader.supports.hashchange )
					window.location.hash = '';
				else
					Loader.close();
			});

			hash = src.split('?')[1];

			// Ensure we don't call pushState if the user hit the forward button.
			if ( Loader.supports.history && window.location.href !== src )
				history.pushState( { customize: src }, '', src );
			else if ( ! Loader.supports.history && Loader.supports.hashchange && hash )
				window.location.hash = hash;

			this.trigger( 'open' );
		},

		opened: function() {
			Loader.body.addClass( 'customize-active full-overlay-active' );
		},

		close: function() {
			if ( ! this.active )
				return;
			this.active = false;

			this.trigger( 'close' );
		},

		closed: function() {
			Loader.iframe.remove();
			Loader.messenger.destroy();
			Loader.iframe    = null;
			Loader.messenger = null;
			Loader.body.removeClass( 'customize-active full-overlay-active' ).removeClass( 'customize-loading' );
		},

		loaded: function() {
			Loader.body.removeClass('customize-loading');
		},

		overlay: {
			show: function() {
				this.element.fadeIn( 200, Loader.opened );
			},

			hide: function() {
				this.element.fadeOut( 200, Loader.closed );
			}
		}
	});

	$( function() {
		if ( window.postMessage )
			Loader.initialize();
	});

	// Expose the API to the world.
	api.Loader = Loader;
})( wp, jQuery );
