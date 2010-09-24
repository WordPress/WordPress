var ThemeViewer;

(function($){
	ThemeViewer = function( args ) {

		function filter_count() {
			var count = $( '#filters :checked' ).length;
			var text  = $( '#filter-click' ).text();

			if ( text.indexOf( '(' ) != -1 )
				text = text.substr( 0, text.indexOf( '(' ) );

			if ( count == 0 )
				$( '#filter-click' ).text( text );
			else
				$( '#filter-click' ).text( text + ' (' + count + ')' );
		}

		function init() {
			$( '#filter-click, #mini-filter-click' ).unbind( 'click' ).click( function() {
				$( '#filter-click' ).toggleClass( 'current' );
				$( '#filters' ).slideToggle();
				$( '#current-theme' ).slideToggle( 300 );
				return false;
			});

			$( '#filters :checkbox' ).unbind( 'click' ).click( function() {
				filter_count();
			});

			$( 'p.tags a' ).unbind( 'click' ).click(function() {
				$( 'p.tags a' ).unbind( 'click' ).click(function() { slow_down(); return false });   // Stop further clicks until we've done
				$( '.loading' ).fadeIn();
				$( '.random-info' ).fadeOut();

				var item = this.href.replace( /.*?s=(.*?)#.*/, '$1' );

				// Is this in the features list?
				if ( $( 'input[value="' + item + '"]' ).length > 0 ) {
					$( 'input[value="' + item + '"]' ).attr( 'checked', $( 'input[value="' + item + '"]' ).attr( 'checked' ) ? false : true );

					filter_count();
				}
				else
					$( 'input[name=s]' ).val( item );   // Can't find it, just use a search

				// Set the options
				opts.search = $( 'input[name=s]' ).val();
				opts.order  = document.location.href.match( /order=(\w*)/ ) ? document.location.href.match( /order=(\w*)/ )[1] : 'random';
				
				$( '#availablethemes td' ).fadeTo( 500, 0.1, function() {
					$( '#availablethemes td img' ).hide();
				} );

				return false;
			});
		}
		
		// These are the functions we expose
		var api = {
			init: init
		};

  	return api;
	}
})(jQuery);

jQuery( document ).ready( function($) {
	theme_viewer = new ThemeViewer();
	theme_viewer.init();
});	