if ( typeof wp === 'undefined' )
	var wp = {};

(function( exports, $ ){
	var Loader = {
		initialize: function() {
			this.body      = $( document.body );
			this.element   = $( '#customize-container' );
			this.base      = $( '.admin-url', this.element ).val();
			this.doc_title = $( document ).attr( 'title' );

			this.element.on( 'click', '.close-full-overlay', function() {
				Loader.close();
				return false;
			});

			this.element.on( 'click', '.collapse-sidebar', function() {
				Loader.element.toggleClass('collapsed');
				return false;
			});
		},
		open: function( params ) {
			params.customize = 'on';

			this.element.append( '<iframe />' );
			this.iframe = $( 'iframe' ).attr( 'src', this.base + '?' + jQuery.param( params ) );

			$('iframe').load( function() {
				title = $(this).contents().find( 'title' ).text();
				$( document ).attr( 'title', title );
			});

			this.element.fadeIn( 200, function() {
				Loader.body.addClass( 'customize-active full-overlay-active' );
			});
		},
		close: function() {
			this.element.fadeOut( 200, function() {
				Loader.iframe.remove();
				Loader.iframe = null;
				Loader.body.removeClass( 'customize-active full-overlay-active' );
				$( document ).attr( 'title', Loader.doc_title );
			});
		}
	};

	$( function() {
		Loader.initialize();

		// Override 'preview' links on themes page.
		$('.thickbox-preview').click( function( event ) {
			var href, template, stylesheet;

			// Stop the thickbox.
			event.preventDefault();
			event.stopImmediatePropagation();

			// Extract the template/stylesheet from the preview link's url.
			href = $(this).attr('href');
			template = href.match('template=([^&]*)')[1];
			stylesheet = href.match('stylesheet=([^&]*)')[1];

			// Load the theme.
			Loader.open({
				template: template,
				stylesheet: stylesheet
			});
		}).filter( function() {
			return 'Preview' == $(this).text();
		}).text('Customize');
	});

	// Expose the API to the world.
	exports.CustomizeLoader = Loader;
})( wp, jQuery );
