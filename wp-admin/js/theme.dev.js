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


/**
 * Class that provides infinite scroll for Themes admin screens
 *
 * @since 3.4
 *
 * @uses ajaxurl
 * @uses list_args
 * @uses theme_list_args
 * @uses $('#_ajax_fetch_list_nonce').val()
* */
var ThemeScroller;
(function($){
	ThemeScroller = {
		nonce: '',
		nextPage: 2, // By default, assume we're on the first page.
		querying: false,
		scrollPollingDelay: 500,
		failedRetryDelay: 4000,
		outListBottomThreshold: 300,

		/**
		 * Initializer
		 *
		 * @since 3.4
		 * @access private
		 */
		init: function() {
			var self = this,
				startPage;

			// Get out early if we don't have the required arguments.
			if ( typeof ajaxurl === 'undefined' ||
				 typeof list_args === 'undefined' ||
				 typeof theme_list_args === 'undefined' ) {
					$('.pagination-links').show();
					return;
			}

			// Handle inputs
			this.nonce = $('#_ajax_fetch_list_nonce').val();

			startPage = theme_list_args.paged;
			if ( startPage !== undefined )
				this.nextPage = ( startPage + 1 );

			// Cache jQuery selectors
			this.$outList = $('#availablethemes');
			this.$spinner = $('div.tablenav.bottom').children( 'img.ajax-loading' );
			this.$window = $(window);
			this.$document = $(document);

			/**
			 * If there are more pages to query, then start polling to track
			 * when user hits the bottom of the current page
			 */
			if ( theme_list_args.total_pages !== undefined &&
				 theme_list_args.total_pages >= this.nextPage )
				this.pollInterval =
					setInterval( function() {
						return self.poll();
					}, this.scrollPollingDelay );
		},

		/**
		 * Checks to see if user has scrolled to bottom of page.
		 * If so, requests another page of content from self.ajax().
		 *
		 * @since 3.4
		 * @access private
		 */
		poll: function() {
			var bottom = this.$document.scrollTop() + this.$window.innerHeight();

			if ( this.querying ||
				( bottom < this.$outList.height() - this.outListBottomThreshold ) )
				return;

			this.ajax();
		},

		/**
		 * Applies results passed from this.ajax() to $outList
		 *
		 * @since 3.4
		 * @access private
		 *
		 * @param results Array with results from this.ajax() query.
		 */
		process: function( results ) {
			if ( ( results === undefined ) ||
				 ( results.rows === undefined ) ||
				 ( results.rows.indexOf( 'no-items' ) != -1 ) ) {
				clearInterval( this.pollInterval );
				return;
			}

			if ( this.nextPage > theme_list_args.total_pages )
				clearInterval( this.pollInterval );

			if ( this.nextPage <= ( theme_list_args.total_pages + 1 ) )
				this.$outList.append( results.rows );
		},

		/**
		 * Queries next page of themes
		 *
		 * @since 3.4
		 * @access private
		 */
		ajax: function() {
			var self = this;

			this.querying = true;

			var query = {
				action: 'fetch-list',
				paged: this.nextPage,
				s: theme_list_args.search,
				tab: theme_list_args.tab,
				type: theme_list_args.type,
				_ajax_fetch_list_nonce: this.nonce,
				'features[]': theme_list_args.features,
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
					setTimeout( function() { self.ajax(); }, self.failedRetryDelay );
				});
		}
	}

	$(document).ready( function($) {
		ThemeScroller.init();
	});

})(jQuery);
