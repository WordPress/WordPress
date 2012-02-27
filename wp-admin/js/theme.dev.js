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

var ThemeScroller;

(function($){
	ThemeScroller = {
		// Inputs
		nonce: '',
		search: '',
		tab: '',
		type: '',
		nextPage: 2,
		features: {},

		// Preferences
		scrollPollingDelay: 500,
		failedRetryDelay: 4000,
		outListBottomThreshold: 300,

		// Flags
		scrolling: false,
		querying: false,

		init: function() {
			var self = this,
				startPage,
				queryArray = {},
				queryString = window.location.search;

			// We're using infinite scrolling, so hide all pagination.
			$('.pagination-links').hide();

			// Parse GET query string
			queryArray = this.parseQuery( queryString.substring( 1 ) );

			// Handle inputs
			this.nonce = $('#_ajax_fetch_list_nonce').val();
			this.search = queryArray['s'];
			this.features = queryArray['features'];
			this.tab = queryArray['tab'];
			this.type = queryArray['type'];

			startPage = parseInt( queryArray['paged'], 10 );
			if ( ! isNaN( startPage ) )
				this.nextPage = ( startPage + 1 );

			// Cache jQuery selectors
			this.$outList = $('#availablethemes');
			this.$spinner = $('div.tablenav.bottom').children( 'img.ajax-loading' );
			this.$window = $(window);
			this.$document = $(document);

			if ( $('.tablenav-pages').length )
				this.pollInterval =
					setInterval( function() {
						return self.poll();
					}, this.scrollPollingDelay );
		},
		poll: function() {
			var bottom = this.$document.scrollTop() + this.$window.innerHeight();

			if ( this.querying ||
				( bottom < this.$outList.height() - this.outListBottomThreshold ) )
				return;

			this.ajax();
		},
		process: function( results ) {
			if ( ( results === undefined ) ||
				( results.rows.indexOf( 'no-items' ) != -1 ) ) {
				clearInterval( this.pollInterval );
				return;
			}

			var totalPages = parseInt( results.total_pages, 10 );
			if ( this.nextPage > totalPages )
				clearInterval( this.pollInterval );

			if ( this.nextPage <= ( totalPages + 1 ) )
				this.$outList.append( results.rows );
		},
		ajax: function() {
			var self = this;
			this.querying = true;

			var query = {
				action: 'fetch-list',
				tab: this.tab,
				paged: this.nextPage,
				s: this.search,
				type: this.type,
				_ajax_fetch_list_nonce: this.nonce,
				'features[]': this.features,
				'list_args': list_args
			};

			this.$spinner.css( 'visibility', 'visible' );
			$.getJSON( ajaxurl, query )
				.done( function( response ) {
					self.nextPage++;
					self.process( response );
					self.$spinner.css( 'visibility', 'hidden' );
					self.querying = false;
				})
				.fail( function() {
					self.$spinner.css( 'visibility', 'hidden' );
					self.querying = false;
					setTimeout( function() { self.ajax(); }, self.failedRetryDelay )
				});
		},
		parseQuery: function( query ) {
			var params = {};
			if ( ! query )
				return params;

			var pairs = query.split( /[;&]/ );
			for ( var i = 0; i < pairs.length; i++ ) {
				var keyVal = pairs[i].split( '=' );

				if ( ! keyVal || keyVal.length != 2 )
					continue;

				var key = unescape( keyVal[0] );
				var val = unescape( keyVal[1] );
				val = val.replace( /\+/g, ' ' );
				key = key.replace( /\[.*\]$/g, '' );

				if ( params[key] === undefined ) {
					params[key] = val;
				} else {
					var oldVal = params[key];
					if ( ! $.isArray( params[key] ) )
						params[key] = new Array( oldVal, val );
					else
						params[key].push( val );
				}
			}
			return params;
		}
	}

	$(document).ready( function( $ ) { ThemeScroller.init(); });

})(jQuery);
