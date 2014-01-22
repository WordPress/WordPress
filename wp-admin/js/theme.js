/* global _wpThemeSettings, confirm */
window.wp = window.wp || {};

( function($) {

// Set up our namespace...
var themes, l10n;
themes = wp.themes = wp.themes || {};

// Store the theme data and settings for organized and quick access
// themes.data.settings, themes.data.themes, themes.data.l10n
themes.data = _wpThemeSettings;
l10n = themes.data.l10n;

// Setup app structure
_.extend( themes, { model: {}, view: {}, routes: {}, router: {}, template: wp.template });

themes.model = Backbone.Model.extend({});

// Main view controller for themes.php
// Unifies and renders all available views
themes.view.Appearance = wp.Backbone.View.extend({

	el: '#wpbody-content .wrap .theme-browser',

	window: $( window ),
	// Pagination instance
	page: 0,

	// Sets up a throttler for binding to 'scroll'
	initialize: function() {
		// Scroller checks how far the scroll position is
		_.bindAll( this, 'scroller' );

		// Bind to the scroll event and throttle
		// the results from this.scroller
		this.window.bind( 'scroll', _.throttle( this.scroller, 300 ) );
	},

	// Main render control
	render: function() {
		// Setup the main theme view
		// with the current theme collection
		this.view = new themes.view.Themes({
			collection: this.collection,
			parent: this
		});

		// Render search form.
		this.search();

		// Render and append
		this.view.render();
		this.$el.empty().append( this.view.el ).addClass('rendered');
		this.$el.append( '<br class="clear"/>' );
	},

	// Search input and view
	// for current theme collection
	search: function() {
		var view,
			self = this;

		// Don't render the search if there is only one theme
		if ( themes.data.themes.length === 1 ) {
			return;
		}

		view = new themes.view.Search({ collection: self.collection });

		// Render and append after screen title
		view.render();
		$('#wpbody h2:first')
			.append( $.parseHTML( '<label class="screen-reader-text" for="theme-search-input">' + l10n.search + '</label>' ) )
			.append( view.el );
	},

	// Checks when the user gets close to the bottom
	// of the mage and triggers a theme:scroll event
	scroller: function() {
		var self = this,
			bottom, threshold;

		bottom = this.window.scrollTop() + self.window.height();
		threshold = self.$el.offset().top + self.$el.outerHeight( false ) - self.window.height();
		threshold = Math.round( threshold * 0.9 );

		if ( bottom > threshold ) {
			this.trigger( 'theme:scroll' );
		}
	}
});

// Set up the Collection for our theme data
// @has 'id' 'name' 'screenshot' 'author' 'authorURI' 'version' 'active' ...
themes.Collection = Backbone.Collection.extend({

	model: themes.model,

	// Search terms
	terms: '',

	// Controls searching on the current theme collection
	// and triggers an update event
	doSearch: function( value ) {

		// Don't do anything if we've already done this search
		// Useful because the Search handler fires multiple times per keystroke
		if ( this.terms === value ) {
			return;
		}

		// Updates terms with the value passed
		this.terms = value;

		// If we have terms, run a search...
		if ( this.terms.length > 0 ) {
			this.search( this.terms );
		}

		// If search is blank, show all themes
		// Useful for resetting the views when you clean the input
		if ( this.terms === '' ) {
			this.reset( themes.data.themes );
		}

		// Trigger an 'update' event
		this.trigger( 'update' );
	},

	// Performs a search within the collection
	// @uses RegExp
	search: function( term ) {
		var match, results, haystack;

		// Start with a full collection
		this.reset( themes.data.themes, { silent: true } );

		// The RegExp object to match
		//
		// Consider spaces as word delimiters and match the whole string
		// so matching terms can be combined
		term = term.replace( ' ', ')(?=.*' );
		match = new RegExp( '^(?=.*' + term + ').+', 'i' );

		// Find results
		// _.filter and .test
		results = this.filter( function( data ) {
			haystack = _.union( data.get( 'name' ), data.get( 'id' ), data.get( 'description' ), data.get( 'author' ), data.get( 'tags' ) );

			if ( match.test( data.get( 'author' ) ) && term.length > 2 ) {
				data.set( 'displayAuthor', true );
			}

			return match.test( haystack );
		});

		this.reset( results );
	},

	// Paginates the collection with a helper method
	// that slices the collection
	paginate: function( instance ) {
		var collection = this;
		instance = instance || 0;

		// Themes per instance are set at 15
		collection = _( collection.rest( 15 * instance ) );
		collection = _( collection.first( 15 ) );

		return collection;
	}
});

// This is the view that controls each theme item
// that will be displayed on the screen
themes.view.Theme = wp.Backbone.View.extend({

	// Wrap theme data on a div.theme element
	className: 'theme',

	// Reflects which theme view we have
	// 'grid' (default) or 'detail'
	state: 'grid',

	// The HTML template for each element to be rendered
	html: themes.template( 'theme' ),

	events: {
		'click': 'expand',
		'keydown': 'expand',
		'touchend': 'expand',
		'touchmove': 'preventExpand'
	},

	touchDrag: false,

	render: function() {
		var data = this.model.toJSON();
		// Render themes using the html template
		this.$el.html( this.html( data ) ).attr({
			tabindex: 0,
			'aria-describedby' : data.id + '-action ' + data.id + '-name'
		});

		// Renders active theme styles
		this.activeTheme();

		if ( this.model.get( 'displayAuthor' ) ) {
			this.$el.addClass( 'display-author' );
		}
	},

	// Adds a class to the currently active theme
	// and to the overlay in detailed view mode
	activeTheme: function() {
		if ( this.model.get( 'active' ) ) {
			this.$el.addClass( 'active' );
		}
	},

	// Single theme overlay screen
	// It's shown when clicking a theme
	expand: function( event ) {
		var self = this;

		event = event || window.event;

		// 'enter' and 'space' keys expand the details view when a theme is :focused
		if ( event.type === 'keydown' && ( event.which !== 13 && event.which !== 32 ) ) {
			return;
		}

		// Bail if the user scrolled on a touch device
		if ( this.touchDrag === true ) {
			return this.touchDrag = false;
		}

		// Prevent the modal from showing when the user clicks
		// one of the direct action buttons
		if ( $( event.target ).is( '.theme-actions a' ) ) {
			return;
		}

		// Set focused theme to current element
		themes.focusedTheme = this.$el;

		this.trigger( 'theme:expand', self.model.cid );
	},

	preventExpand: function() {
		this.touchDrag = true;
	}
});

// Theme Details view
// Set ups a modal overlay with the expanded theme data
themes.view.Details = wp.Backbone.View.extend({

	// Wrap theme data on a div.theme element
	className: 'theme-overlay',

	events: {
		'click': 'collapse',
		'click .delete-theme': 'deleteTheme',
		'click .left': 'previousTheme',
		'click .right': 'nextTheme'
	},

	// The HTML template for the theme overlay
	html: themes.template( 'theme-single' ),

	render: function() {
		var data = this.model.toJSON();
		this.$el.html( this.html( data ) );
		// Renders active theme styles
		this.activeTheme();
		// Set up navigation events
		this.navigation();
		// Checks screenshot size
		this.screenshotCheck( this.$el );
		// Contain "tabbing" inside the overlay
		this.containFocus( this.$el );
	},

	// Adds a class to the currently active theme
	// and to the overlay in detailed view mode
	activeTheme: function() {
		// Check the model has the active property
		this.$el.toggleClass( 'active', this.model.get( 'active' ) );
	},

	// Keeps :focus within the theme details elements
	containFocus: function( $el ) {
		var $target;

		// Move focus to the primary action
		_.delay( function() {
			$( '.theme-wrap a.button-primary:visible' ).focus();
		}, 500 );

		$el.on( 'keydown.wp-themes', function( event ) {

			// Tab key
			if ( event.which === 9 ) {
				$target = $( event.target );

				// Keep focus within the overlay by making the last link on theme actions
				// switch focus to button.left on tabbing and vice versa
				if ( $target.is( 'button.left' ) && event.shiftKey ) {
					$el.find( '.theme-actions a:last-child' ).focus();
					event.preventDefault();
				} else if ( $target.is( '.theme-actions a:last-child' ) ) {
					$el.find( 'button.left' ).focus();
					event.preventDefault();
				}
			}
		});
	},

	// Single theme overlay screen
	// It's shown when clicking a theme
	collapse: function( event ) {
		var self = this,
			scroll;

		event = event || window.event;

		// Prevent collapsing detailed view when there is only one theme available
		if ( themes.data.themes.length === 1 ) {
			return;
		}

		// Detect if the click is inside the overlay
		// and don't close it unless the target was
		// the div.back button
		if ( $( event.target ).is( '.theme-backdrop' ) || $( event.target ).is( '.close' ) || event.keyCode === 27 ) {

			// Add a temporary closing class while overlay fades out
			$( 'body' ).addClass( 'closing-overlay' );

			// With a quick fade out animation
			this.$el.fadeOut( 130, function() {
				// Clicking outside the modal box closes the overlay
				$( 'body' ).removeClass( 'theme-overlay-open closing-overlay' );
				// Handle event cleanup
				self.closeOverlay();

				// Get scroll position to avoid jumping to the top
				scroll = document.body.scrollTop;

				// Clean the url structure
				themes.router.navigate( themes.router.baseUrl( '' ), { replace: true } );

				// Restore scroll position
				document.body.scrollTop = scroll;

				// Return focus to the theme div
				if ( themes.focusedTheme ) {
					themes.focusedTheme.focus();
				}
			});
		}
	},

	// Handles .disabled classes for next/previous buttons
	navigation: function() {

		// Disable Left/Right when at the start or end of the collection
		if ( this.model.cid === this.model.collection.at(0).cid ) {
			this.$el.find( '.left' ).addClass( 'disabled' );
		}
		if ( this.model.cid === this.model.collection.at( this.model.collection.length - 1 ).cid ) {
			this.$el.find( '.right' ).addClass( 'disabled' );
		}
	},

	// Performs the actions to effectively close
	// the theme details overlay
	closeOverlay: function() {
		this.remove();
		this.unbind();
		this.trigger( 'theme:collapse' );
	},

	// Confirmation dialoge for deleting a theme
	deleteTheme: function() {
		return confirm( themes.data.settings.confirmDelete );
	},

	nextTheme: function() {
		var self = this;
		self.trigger( 'theme:next', self.model.cid );
	},

	previousTheme: function() {
		var self = this;
		self.trigger( 'theme:previous', self.model.cid );
	},

	// Checks if the theme screenshot is the old 300px width version
	// and adds a corresponding class if it's true
	screenshotCheck: function( el ) {
		var screenshot, image;

		screenshot = el.find( '.screenshot img' );
		image = new Image();
		image.src = screenshot.attr( 'src' );

		// Width check
		if ( image.width && image.width <= 300 ) {
			el.addClass( 'small-screenshot' );
		}
	}
});

// Controls the rendering of div.themes,
// a wrapper that will hold all the theme elements
themes.view.Themes = wp.Backbone.View.extend({

	className: 'themes',
	$overlay: $( 'div.theme-overlay' ),

	// Number to keep track of scroll position
	// while in theme-overlay mode
	index: 0,

	// The theme count element
	count: $( '.theme-count' ),

	initialize: function( options ) {
		var self = this;

		// Set up parent
		this.parent = options.parent;

		// Set current view to [grid]
		this.setView( 'grid' );

		// Move the active theme to the beginning of the collection
		self.currentTheme();

		// When the collection is updated by user input...
		this.listenTo( self.collection, 'update', function() {
			self.parent.page = 0;
			self.currentTheme();
			self.render( this );
		});

		this.listenTo( this.parent, 'theme:scroll', function() {
			self.renderThemes( self.parent.page );
		});

		// Bind keyboard events.
		$('body').on( 'keyup', function( event ) {
			if ( ! self.overlay ) {
				return;
			}

			// Pressing the right arrow key fires a theme:next event
			if ( event.keyCode === 39 ) {
				self.overlay.nextTheme();
			}

			// Pressing the left arrow key fires a theme:previous event
			if ( event.keyCode === 37 ) {
				self.overlay.previousTheme();
			}

			// Pressing the escape key fires a theme:collapse event
			if ( event.keyCode === 27 ) {
				self.overlay.collapse( event );
			}
		});
	},

	// Manages rendering of theme pages
	// and keeping theme count in sync
	render: function() {
		// Clear the DOM, please
		this.$el.html( '' );

		// If the user doesn't have switch capabilities
		// or there is only one theme in the collection
		// render the detailed view of the active theme
		if ( themes.data.themes.length === 1 ) {

			// Constructs the view
			this.singleTheme = new themes.view.Details({
				model: this.collection.models[0]
			});

			// Render and apply a 'single-theme' class to our container
			this.singleTheme.render();
			this.$el.addClass( 'single-theme' );
			this.$el.append( this.singleTheme.el );
		}

		// Generate the themes
		// Using page instance
		this.renderThemes( this.parent.page );

		// Display a live theme count for the collection
		this.count.text( this.collection.length );
	},

	// Iterates through each instance of the collection
	// and renders each theme module
	renderThemes: function( page ) {
		var self = this;

		self.instance = self.collection.paginate( page );

		// If we have no more themes bail
		if ( self.instance.length === 0 ) {
			return;
		}

		// Make sure the add-new stays at the end
		if ( page >= 1 ) {
			$( '.add-new-theme' ).remove();
		}

		// Loop through the themes and setup each theme view
		self.instance.each( function( theme ) {
			self.theme = new themes.view.Theme({
				model: theme
			});

			// Render the views...
			self.theme.render();
			// and append them to div.themes
			self.$el.append( self.theme.el );

			// Binds to theme:expand to show the modal box
			// with the theme details
			self.listenTo( self.theme, 'theme:expand', self.expand, self );
		});

		// 'Add new theme' element shown at the end of the grid
		if ( themes.data.settings.canInstall ) {
			this.$el.append( '<div class="theme add-new-theme"><a href="' + themes.data.settings.installURI + '"><div class="theme-screenshot"><span></span></div><h3 class="theme-name">' + l10n.addNew + '</h3></a></div>' );
		}

		this.parent.page++;
	},

	// Grabs current theme and puts it at the beginning of the collection
	currentTheme: function() {
		var self = this,
			current;

		current = self.collection.findWhere({ active: true });

		// Move the active theme to the beginning of the collection
		if ( current ) {
			self.collection.remove( current );
			self.collection.add( current, { at:0 } );
		}
	},

	// Sets current view
	setView: function( view ) {
		return view;
	},

	// Renders the overlay with the ThemeDetails view
	// Uses the current model data
	expand: function( id ) {
		var self = this;

		// Set the current theme model
		this.model = self.collection.get( id );

		// Trigger a route update for the current model
		themes.router.navigate( themes.router.baseUrl( '?theme=' + this.model.id ), { replace: true } );

		// Sets this.view to 'detail'
		this.setView( 'detail' );
		$( 'body' ).addClass( 'theme-overlay-open' );

		// Set up the theme details view
		this.overlay = new themes.view.Details({
			model: self.model
		});

		this.overlay.render();
		this.$overlay.html( this.overlay.el );

		// Bind to theme:next and theme:previous
		// triggered by the arrow keys
		//
		// Keep track of the current model so we
		// can infer an index position
		this.listenTo( this.overlay, 'theme:next', function() {
			// Renders the next theme on the overlay
			self.next( [ self.model.cid ] );

		})
		.listenTo( this.overlay, 'theme:previous', function() {
			// Renders the previous theme on the overlay
			self.previous( [ self.model.cid ] );
		});
	},

	// This method renders the next theme on the overlay modal
	// based on the current position in the collection
	// @params [model cid]
	next: function( args ) {
		var self = this,
			model, nextModel;

		// Get the current theme
		model = self.collection.get( args[0] );
		// Find the next model within the collection
		nextModel = self.collection.at( self.collection.indexOf( model ) + 1 );

		// Sanity check which also serves as a boundary test
		if ( nextModel !== undefined ) {

			// We have a new theme...
			// Close the overlay
			this.overlay.closeOverlay();

			// Trigger a route update for the current model
			self.theme.trigger( 'theme:expand', nextModel.cid );

		}
	},

	// This method renders the previous theme on the overlay modal
	// based on the current position in the collection
	// @params [model cid]
	previous: function( args ) {
		var self = this,
			model, previousModel;

		// Get the current theme
		model = self.collection.get( args[0] );
		// Find the previous model within the collection
		previousModel = self.collection.at( self.collection.indexOf( model ) - 1 );

		if ( previousModel !== undefined ) {

			// We have a new theme...
			// Close the overlay
			this.overlay.closeOverlay();

			// Trigger a route update for the current model
			self.theme.trigger( 'theme:expand', previousModel.cid );

		}
	}
});

// Search input view controller.
themes.view.Search = wp.Backbone.View.extend({

	tagName: 'input',
	className: 'theme-search',
	id: 'theme-search-input',

	attributes: {
		placeholder: l10n.searchPlaceholder,
		type: 'search'
	},

	events: {
		'input':  'search',
		'keyup':  'search',
		'change': 'search',
		'search': 'search'
	},

	// Runs a search on the theme collection.
	search: function( event ) {
		// Clear on escape.
		if ( event.type === 'keyup' && event.which === 27 ) {
			event.target.value = '';
		}

		this.collection.doSearch( event.target.value );

		// Update the URL hash
		if ( event.target.value ) {
			themes.router.navigate( themes.router.baseUrl( '?search=' + event.target.value ), { replace: true } );
		} else {
			themes.router.navigate( themes.router.baseUrl( '' ), { replace: true } );
		}
	}
});

// Sets up the routes events for relevant url queries
// Listens to [theme] and [search] params
themes.routes = Backbone.Router.extend({

	initialize: function() {
		this.routes = _.object([
		]);
	},

	baseUrl: function( url ) {
		return themes.data.settings.root + url;
	}
});

// Execute and setup the application
themes.Run = {
	init: function() {
		// Initializes the blog's theme library view
		// Create a new collection with data
		this.themes = new themes.Collection( themes.data.themes );

		// Set up the view
		this.view = new themes.view.Appearance({
			collection: this.themes
		});

		this.render();
	},

	render: function() {
		// Render results
		this.view.render();
		this.routes();

		// Set the initial theme
		if ( 'undefined' !== typeof themes.data.settings.theme && '' !== themes.data.settings.theme ){
			this.view.view.theme.trigger( 'theme:expand', this.view.collection.findWhere( { id: themes.data.settings.theme } ) );
		}

		// Set the initial search
		if ( 'undefined' !== typeof themes.data.settings.search && '' !== themes.data.settings.search ){
			$( '.theme-search' ).val( themes.data.settings.search );
			this.themes.doSearch( themes.data.settings.search );
		}

		// Start the router if browser supports History API
		if ( window.history && window.history.pushState ) {
			// Calls the routes functionality
			Backbone.history.start({ pushState: true, silent: true });
		}
	},

	routes: function() {
		// Bind to our global thx object
		// so that the object is available to sub-views
		themes.router = new themes.routes();
	}
};

// Ready...
jQuery( document ).ready(

	// Bring on the themes
	_.bind( themes.Run.init, themes.Run )

);

})( jQuery );

// Align theme browser thickbox
var tb_position;
jQuery(document).ready( function($) {
	tb_position = function() {
		var tbWindow = $('#TB_window'),
			width = $(window).width(),
			H = $(window).height(),
			W = ( 1040 < width ) ? 1040 : width,
			adminbar_height = 0;

		if ( $('body.admin-bar').length ) {
			adminbar_height = parseInt( jQuery('#wpadminbar').css('height'), 10 );
		}

		if ( tbWindow.size() ) {
			tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
			tbWindow.css({'margin-left': '-' + parseInt( ( ( W - 50 ) / 2 ), 10 ) + 'px'});
			if ( typeof document.body.style.maxWidth !== 'undefined' ) {
				tbWindow.css({'top': 20 + adminbar_height + 'px', 'margin-top': '0'});
			}
		}
	};

	$(window).resize(function(){ tb_position(); });
});
