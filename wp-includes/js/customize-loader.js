window.wp = window.wp || {};

(function( exports, $ ){
	var api = wp.customize,
		Loader;

	$.extend( $.support, {
		history: !! ( window.history && history.pushState ),
		hashchange: ('onhashchange' in window) && (document.documentMode === undefined || document.documentMode > 7)
	});

	Loader = $.extend( {}, api.Events, {
		initialize: function() {
			this.body = $( document.body );

			// Ensure the loader is supported.
			// Check for settings, postMessage support, and whether we require CORS support.
			if ( ! Loader.settings || ! $.support.postMessage || ( ! $.support.cors && Loader.settings.isCrossDomain ) ) {
				return;
			}

			this.window  = $( window );
			this.element = $( '<div id="customize-container" />' ).appendTo( this.body );

			this.bind( 'open', this.overlay.show );
			this.bind( 'close', this.overlay.hide );

			$('#wpbody').on( 'click', '.load-customize', function( event ) {
				event.preventDefault();

				// Store a reference to the link that opened the customizer.
				Loader.link = $(this);
				// Load the theme.
				Loader.open( Loader.link.attr('href') );
			});

			// Add navigation listeners.
			if ( $.support.history )
				this.window.on( 'popstate', Loader.popstate );

			if ( $.support.hashchange ) {
				this.window.on( 'hashchange', Loader.hashchange );
				this.window.triggerHandler( 'hashchange' );
			}
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

			if ( hash && 0 === hash.indexOf( 'wp_customize=on' ) )
				Loader.open( Loader.settings.url + '?' + hash );

			if ( ! hash && ! $.support.history )
				Loader.close();
		},

		open: function( src ) {
			var hash;

			if ( this.active )
				return;

			// Load the full page on mobile devices.
			if ( Loader.settings.browser.mobile )
				return window.location = src;

			this.active = true;
			this.body.addClass('customize-loading');

			this.iframe = $( '<iframe />', { src: src }).appendTo( this.element );
			this.iframe.one( 'load', this.loaded );

			// Create a postMessage connection with the iframe.
			this.messenger = new api.Messenger({
				url: src,
				channel: 'loader',
				targetWindow: this.iframe[0].contentWindow
			});

			// Wait for the connection from the iframe before sending any postMessage events.
			this.messenger.bind( 'ready', function() {
				Loader.messenger.send( 'back' );
			});

			this.messenger.bind( 'close', function() {
				if ( $.support.history )
					history.back();
				else if ( $.support.hashchange )
					window.location.hash = '';
				else
					Loader.close();
			});

			this.messenger.bind( 'activated', function( location ) {
				if ( location )
					window.location = location;
			});

			hash = src.split('?')[1];

			// Ensure we don't call pushState if the user hit the forward button.
			if ( $.support.history && window.location.href !== src )
				history.pushState( { customize: src }, '', src );
			else if ( ! $.support.history && $.support.hashchange && hash )
				window.location.hash = 'wp_customize=on&' + hash;

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

			// Return focus to link that was originally clicked.
			if ( this.link )
				this.link.focus();
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
		Loader.settings = _wpCustomizeLoaderSettings;
		Loader.initialize();
	});

	// Expose the API to the world.
	api.Loader = Loader;
})( wp, jQuery );
