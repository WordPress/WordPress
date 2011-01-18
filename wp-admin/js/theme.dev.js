var ThemeViewer;

(function($){
	ThemeViewer = function( args ) {

		function init() {
			$( '#filter-click, #mini-filter-click' ).unbind( 'click' ).click( function() {
				$( '#filter-click' ).toggleClass( 'current' );
				$( '#filter-box' ).slideToggle();
				$( '#current-theme' ).slideToggle( 300 );
				return false;
			});

			$( '#filter-box :checkbox' ).unbind( 'click' ).click( function() {
				var count = $( '#filter-box :checked' ).length,
					text  = $( '#filter-click' ).text();

				if ( text.indexOf( '(' ) != -1 )
					text = text.substr( 0, text.indexOf( '(' ) );

				if ( count == 0 )
					$( '#filter-click' ).text( text );
				else
					$( '#filter-click' ).text( text + ' (' + count + ')' );
			});

			/* $('#filter-box :submit').unbind( 'click' ).click(function() {
				var features = [];
				$('#filter-box :checked').each(function() {
					features.push($(this).val());
				});

				listTable.update_rows({'features': features}, true, function() {
					$( '#filter-click' ).toggleClass( 'current' );
					$( '#filter-box' ).slideToggle();
					$( '#current-theme' ).slideToggle( 300 );
				});

				return false;
			}); */
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
