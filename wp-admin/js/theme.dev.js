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
