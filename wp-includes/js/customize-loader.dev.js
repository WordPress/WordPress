if ( typeof wp === 'undefined' )
	var wp = {};

(function( exports, $ ){
	var api = wp.customize,
		Loader;

	Loader = {
		initialize: function() {
			this.body      = $( document.body ).addClass('customize-support');
			this.element   = $( '<div id="customize-container" class="wp-full-overlay" />' ).appendTo( this.body );

			$('#wpbody').on( 'click', '.load-customize', function( event ) {
				event.preventDefault();

				// Load the theme.
				Loader.open( $(this).attr('href') );
			});
		},
		open: function( src ) {
			this.iframe = $( '<iframe />', { src: src }).appendTo( this.element );

			// Create a postMessage connection with the iframe.
			this.messenger = new api.Messenger( src, this.iframe[0].contentWindow );

			// Wait for the connection from the iframe before sending any postMessage events.
			this.messenger.bind( 'ready', function() {
				Loader.messenger.send( 'back', wpCustomizeLoaderL10n.back );
			});

			this.messenger.bind( 'close', function() {
				Loader.close();
			});

			this.element.fadeIn( 200, function() {
				Loader.body.addClass( 'customize-active full-overlay-active' );
			});
		},
		close: function() {
			this.element.fadeOut( 200, function() {
				Loader.iframe.remove();
				Loader.iframe    = null;
				Loader.messenger = null;
				Loader.body.removeClass( 'customize-active full-overlay-active' );
			});
		}
	};

	$( function() {
		if ( !! window.postMessage )
			Loader.initialize();
	});

	// Expose the API to the world.
	api.Loader = Loader;
})( wp, jQuery );
