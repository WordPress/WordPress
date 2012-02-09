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

var wpThemes;

(function($){
	var inputs = {}, Query;

	wpThemes = {
		timeToTriggerQuery: 150,
		minQueryAJAXDuration: 200,
		outListBottomThreshold: 200,
		noMoreResults: false,
		
		init : function() {
			$( '.pagination-links' ).hide();

			inputs.nonce = $('#_ajax_fetch_list_nonce').val();
	
			// Parse Query
			inputs.queryString = window.location.search;			
			inputs.queryArray = wpThemes.parseQuery( inputs.queryString.substring( 1 ) );

			// Handle Inputs from Query
			inputs.search = inputs.queryArray['s'];
			inputs.features = inputs.queryArray['features'];
			inputs.startPage = parseInt( inputs.queryArray['paged'] );
			inputs.tab = inputs.queryArray['tab'];
			inputs.type = inputs.queryArray['type'];

			if ( isNaN( inputs.startPage ) )
				inputs.startPage = 2;
			else
				inputs.startPage++;

			// FIXME: Debug Features Array
			// console.log("Features:" + inputs.features);

			// Link to output and start polling
			inputs.outList = $('#availablethemes');

			// Generate Query
			wpThemes.query = new Query();

			// Start Polling
			$(window).scroll( function(){ wpThemes.maybeLoad(); });
		},
		delayedCallback : function( func, delay ) {
			var timeoutTriggered, funcTriggered, funcArgs, funcContext;

			if ( ! delay )
				return func;

			setTimeout( function() {
				if ( funcTriggered )
					return func.apply( funcContext, funcArgs );
				// Otherwise, wait.
				timeoutTriggered = true;
			}, delay);

			return function() {
				if ( timeoutTriggered )
					return func.apply( this, arguments );
				// Otherwise, wait.
				funcArgs = arguments;
				funcContext = this;
				funcTriggered = true;
			};
		},
		ajax: function( callback ) {
			var self = this,
				response = wpThemes.delayedCallback( function( results, params ) {
					self.process( results, params );
					if ( callback )
						callback( results, params );
				}, wpThemes.minQueryAJAXDuration );

			this.query.ajax( response );
		},
		process: function( results, params ) {
			// If no Results, for now, mark as no Matches, and bail.
			// Alternately: inputs.outList.append(wpThemesL10n.noMatchesFound);
			if ( ( results === undefined ) ||
				 ( results.rows.indexOf( "no-items" ) != -1 ) ) {
				this.noMoreResults = true;
			} else {
				inputs.outList.append(results.rows);
			}
		},
		maybeLoad: function() {
			var self = this,
				el = $(document),
				bottom = el.scrollTop() + $(window).innerHeight();

			/* // FIXME: Debug scroll trigger.
			console.log('scrollTop:'+ el.scrollTop() + 
				'; scrollBottom:' + bottom +
				'; height:' + el.height() +
				'; checkVal:' + (el.height() - wpThemes.outListBottomThreshold));
			*/

			if ( this.noMoreResults ||
				 !this.query.ready() || 
				 ( bottom < inputs.outList.height() - wpThemes.outListBottomThreshold ) )
				return;

			setTimeout( function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + $(window).innerHeight();

				if ( !self.query.ready() ||
					 ( newBottom < inputs.outList.height() - wpThemes.outListBottomThreshold ) )
					return;

				/* FIXME: Create/Add Spinner.
				self.waiting.show(); // Show Spinner
				el.scrollTop( newTop + self.waiting.outerHeight() ); // Scroll down?
				self.ajax( function() { self.waiting.hide(); }); // Hide Spinner
				*/
				self.ajax();
			}, wpThemes.timeToTriggerQuery );
		},
		parseQuery: function( query ) {
			var Params = {};
			if ( ! query ) {return Params;}// return empty object
			var Pairs = query.split(/[;&]/);
			for ( var i = 0; i < Pairs.length; i++ ) {
				var KeyVal = Pairs[i].split('=');
				if ( ! KeyVal || KeyVal.length != 2 ) {continue;}
				var key = unescape( KeyVal[0] );
				var val = unescape( KeyVal[1] );
				val = val.replace(/\+/g, ' ');
				key = key.replace(/\[.*\]$/g, '');
	
				if ( Params[key] === undefined ) {
					Params[key] = val;
				} else {
					var oldVal = Params[key];
					if ( ! jQuery.isArray( Params[key] ) )
						Params[key] = new Array( oldVal, val );
					else
						Params[key].push( val );
				}
			}
			return Params;
		}
	}

	Query = function() {
		this.failedRequest = false;
		this.querying = false;
		this.page = inputs.startPage;
	}
	
	$.extend( Query.prototype, {
		ready: function() {
			return !( this.querying || this.failedRequest );
		},
		ajax: function( callback ) {
			var self = this,
			query = {
				action: 'fetch-list',
				paged: this.page,
				s: inputs.search,
				'features[]': inputs.features,
				'list_args': list_args,
				'tab': inputs.tab,
				'type': inputs.type,
				'_ajax_fetch_list_nonce': inputs.nonce
			};

			this.querying = true;
			$.get( ajaxurl, query, function(r) {
				self.page++;
				self.querying = false;
				self.failedRequest = !r;
				callback( r, query );
			}, "json" );
		}
	});

	$(document).ready( wpThemes.init );

})(jQuery);
