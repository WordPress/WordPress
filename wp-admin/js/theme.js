/**
 * @output wp-admin/js/theme.js
 */

/* global _wpThemeSettings, confirm, tb_position */
window.wp = window.wp || {};

( function($) {

// Set up our namespace...
var themes, l10n;
themes = wp.themes = wp.themes || {};

// Store the theme data and settings for organized and quick access.
// themes.data.settings, themes.data.themes, themes.data.l10n.
themes.data = _wpThemeSettings;
l10n = themes.data.l10n;

// Shortcut for isInstall check.
themes.isInstall = !! themes.data.settings.isInstall;

// Setup app structure.
_.extend( themes, { model: {}, view: {}, routes: {}, router: {}, template: wp.template });

themes.Model = Backbone.Model.extend({
	// Adds attributes to the default data coming through the .org themes api.
	// Map `id` to `slug` for shared code.
	initialize: function() {
		var description;

		if ( this.get( 'slug' ) ) {
			// If the theme is already installed, set an attribute.
			if ( _.indexOf( themes.data.installedThemes, this.get( 'slug' ) ) !== -1 ) {
				this.set({ installed: true });
			}

			// If the theme is active, set an attribute.
			if ( themes.data.activeTheme === this.get( 'slug' ) ) {
				this.set({ active: true });
			}
		}

		// Set the attributes.
		this.set({
			// `slug` is for installation, `id` is for existing.
			id: this.get( 'slug' ) || this.get( 'id' )
		});

		// Map `section.description` to `description`
		// as the API sometimes returns it differently.
		if ( this.has( 'sections' ) ) {
			description = this.get( 'sections' ).description;
			this.set({ description: description });
		}
	}
});

// Main view controller for themes.php.
// Unifies and renders all available views.
themes.view.Appearance = wp.Backbone.View.extend({

	el: '#wpbody-content .wrap .theme-browser',

	window: $( window ),
	// Pagination instance.
	page: 0,

	// Sets up a throttler for binding to 'scroll'.
	initialize: function( options ) {
		// Scroller checks how far the scroll position is.
		_.bindAll( this, 'scroller' );

		this.SearchView = options.SearchView ? options.SearchView : themes.view.Search;
		// Bind to the scroll event and throttle
		// the results from this.scroller.
		this.window.on( 'scroll', _.throttle( this.scroller, 300 ) );
	},

	// Main render control.
	render: function() {
		// Setup the main theme view
		// with the current theme collection.
		this.view = new themes.view.Themes({
			collection: this.collection,
			parent: this
		});

		// Render search form.
		this.search();

		this.$el.removeClass( 'search-loading' );

		// Render and append.
		this.view.render();
		this.$el.empty().append( this.view.el ).addClass( 'rendered' );
	},

	// Defines search element container.
	searchContainer: $( '.search-form' ),

	// Search input and view
	// for current theme collection.
	search: function() {
		var view,
			self = this;

		// Don't render the search if there is only one theme.
		if ( themes.data.themes.length === 1 ) {
			return;
		}

		view = new this.SearchView({
			collection: self.collection,
			parent: this
		});
		self.SearchView = view;

		// Render and append after screen title.
		view.render();
		this.searchContainer
			.append( $.parseHTML( '<label class="screen-reader-text" for="wp-filter-search-input">' + l10n.search + '</label>' ) )
			.append( view.el )
			.on( 'submit', function( event ) {
				event.preventDefault();
			});
	},

	// Checks when the user gets close to the bottom
	// of the mage and triggers a theme:scroll event.
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

// Set up the Collection for our theme data.
// @has 'id' 'name' 'screenshot' 'author' 'authorURI' 'version' 'active' ...
themes.Collection = Backbone.Collection.extend({

	model: themes.Model,

	// Search terms.
	terms: '',

	// Controls searching on the current theme collection
	// and triggers an update event.
	doSearch: function( value ) {

		// Don't do anything if we've already done this search.
		// Useful because the Search handler fires multiple times per keystroke.
		if ( this.terms === value ) {
			return;
		}

		// Updates terms with the value passed.
		this.terms = value;

		// If we have terms, run a search...
		if ( this.terms.length > 0 ) {
			this.search( this.terms );
		}

		// If search is blank, show all themes.
		// Useful for resetting the views when you clean the input.
		if ( this.terms === '' ) {
			this.reset( themes.data.themes );
			$( 'body' ).removeClass( 'no-results' );
		}

		// Trigger a 'themes:update' event.
		this.trigger( 'themes:update' );
	},

	/**
	 * Performs a search within the collection.
	 *
	 * @uses RegExp
	 */
	search: function( term ) {
		var match, results, haystack, name, description, author;

		// Start with a full collection.
		this.reset( themes.data.themes, { silent: true } );

		// Trim the term.
		term = term.trim();

		// Escape the term string for RegExp meta characters.
		term = term.replace( /[-\/\\^$*+?.()|[\]{}]/g, '\\$&' );

		// Consider spaces as word delimiters and match the whole string
		// so matching terms can be combined.
		term = term.replace( / /g, ')(?=.*' );
		match = new RegExp( '^(?=.*' + term + ').+', 'i' );

		// Find results.
		// _.filter() and .test().
		results = this.filter( function( data ) {
			name        = data.get( 'name' ).replace( /(<([^>]+)>)/ig, '' );
			description = data.get( 'description' ).replace( /(<([^>]+)>)/ig, '' );
			author      = data.get( 'author' ).replace( /(<([^>]+)>)/ig, '' );

			haystack = _.union( [ name, data.get( 'id' ), description, author, data.get( 'tags' ) ] );

			if ( match.test( data.get( 'author' ) ) && term.length > 2 ) {
				data.set( 'displayAuthor', true );
			}

			return match.test( haystack );
		});

		if ( results.length === 0 ) {
			this.trigger( 'query:empty' );
		} else {
			$( 'body' ).removeClass( 'no-results' );
		}

		this.reset( results );
	},

	// Paginates the collection with a helper method
	// that slices the collection.
	paginate: function( instance ) {
		var collection = this;
		instance = instance || 0;

		// Themes per instance are set at 20.
		collection = _( collection.rest( 20 * instance ) );
		collection = _( collection.first( 20 ) );

		return collection;
	},

	count: false,

	/*
	 * Handles requests for more themes and caches results.
	 *
	 *
	 * When we are missing a cache object we fire an apiCall()
	 * which triggers events of `query:success` or `query:fail`.
	 */
	query: function( request ) {
		/**
		 * @static
		 * @type Array
		 */
		var queries = this.queries,
			self = this,
			query, isPaginated, count;

		// Store current query request args
		// for later use with the event `theme:end`.
		this.currentQuery.request = request;

		// Search the query cache for matches.
		query = _.find( queries, function( query ) {
			return _.isEqual( query.request, request );
		});

		// If the request matches the stored currentQuery.request
		// it means we have a paginated request.
		isPaginated = _.has( request, 'page' );

		// Reset the internal api page counter for non-paginated queries.
		if ( ! isPaginated ) {
			this.currentQuery.page = 1;
		}

		// Otherwise, send a new API call and add it to the cache.
		if ( ! query && ! isPaginated ) {
			query = this.apiCall( request ).done( function( data ) {

				// Update the collection with the queried data.
				if ( data.themes ) {
					self.reset( data.themes );
					count = data.info.results;
					// Store the results and the query request.
					queries.push( { themes: data.themes, request: request, total: count } );
				}

				// Trigger a collection refresh event
				// and a `query:success` event with a `count` argument.
				self.trigger( 'themes:update' );
				self.trigger( 'query:success', count );

				if ( data.themes && data.themes.length === 0 ) {
					self.trigger( 'query:empty' );
				}

			}).fail( function() {
				self.trigger( 'query:fail' );
			});
		} else {
			// If it's a paginated request we need to fetch more themes...
			if ( isPaginated ) {
				return this.apiCall( request, isPaginated ).done( function( data ) {
					// Add the new themes to the current collection.
					// @todo Update counter.
					self.add( data.themes );
					self.trigger( 'query:success' );

					// We are done loading themes for now.
					self.loadingThemes = false;

				}).fail( function() {
					self.trigger( 'query:fail' );
				});
			}

			if ( query.themes.length === 0 ) {
				self.trigger( 'query:empty' );
			} else {
				$( 'body' ).removeClass( 'no-results' );
			}

			// Only trigger an update event since we already have the themes
			// on our cached object.
			if ( _.isNumber( query.total ) ) {
				this.count = query.total;
			}

			this.reset( query.themes );
			if ( ! query.total ) {
				this.count = this.length;
			}

			this.trigger( 'themes:update' );
			this.trigger( 'query:success', this.count );
		}
	},

	// Local cache array for API queries.
	queries: [],

	// Keep track of current query so we can handle pagination.
	currentQuery: {
		page: 1,
		request: {}
	},

	// Send request to api.wordpress.org/themes.
	apiCall: function( request, paginated ) {
		return wp.ajax.send( 'query-themes', {
			data: {
				// Request data.
				request: _.extend({
					per_page: 100
				}, request)
			},

			beforeSend: function() {
				if ( ! paginated ) {
					// Spin it.
					$( 'body' ).addClass( 'loading-content' ).removeClass( 'no-results' );
				}
			}
		});
	},

	// Static status controller for when we are loading themes.
	loadingThemes: false
});

// This is the view that controls each theme item
// that will be displayed on the screen.
themes.view.Theme = wp.Backbone.View.extend({

	// Wrap theme data on a div.theme element.
	className: 'theme',

	// Reflects which theme view we have.
	// 'grid' (default) or 'detail'.
	state: 'grid',

	// The HTML template for each element to be rendered.
	html: themes.template( 'theme' ),

	events: {
		'click': themes.isInstall ? 'preview': 'expand',
		'keydown': themes.isInstall ? 'preview': 'expand',
		'touchend': themes.isInstall ? 'preview': 'expand',
		'keyup': 'addFocus',
		'touchmove': 'preventExpand',
		'click .theme-install': 'installTheme',
		'click .update-message': 'updateTheme'
	},

	touchDrag: false,

	initialize: function() {
		this.model.on( 'change', this.render, this );
	},

	render: function() {
		var data = this.model.toJSON();

		// Render themes using the html template.
		this.$el.html( this.html( data ) ).attr( 'data-slug', data.id );

		// Renders active theme styles.
		this.activeTheme();

		if ( this.model.get( 'displayAuthor' ) ) {
			this.$el.addClass( 'display-author' );
		}
	},

	// Adds a class to the currently active theme
	// and to the overlay in detailed view mode.
	activeTheme: function() {
		if ( this.model.get( 'active' ) ) {
			this.$el.addClass( 'active' );
		}
	},

	// Add class of focus to the theme we are focused on.
	addFocus: function() {
		var $themeToFocus = ( $( ':focus' ).hasClass( 'theme' ) ) ? $( ':focus' ) : $(':focus').parents('.theme');

		$('.theme.focus').removeClass('focus');
		$themeToFocus.addClass('focus');
	},

	// Single theme overlay screen.
	// It's shown when clicking a theme.
	expand: function( event ) {
		var self = this;

		event = event || window.event;

		// 'Enter' and 'Space' keys expand the details view when a theme is :focused.
		if ( event.type === 'keydown' && ( event.which !== 13 && event.which !== 32 ) ) {
			return;
		}

		// Bail if the user scrolled on a touch device.
		if ( this.touchDrag === true ) {
			return this.touchDrag = false;
		}

		// Prevent the modal from showing when the user clicks
		// one of the direct action buttons.
		if ( $( event.target ).is( '.theme-actions a' ) ) {
			return;
		}

		// Prevent the modal from showing when the user clicks one of the direct action buttons.
		if ( $( event.target ).is( '.theme-actions a, .update-message, .button-link, .notice-dismiss' ) ) {
			return;
		}

		// Set focused theme to current element.
		themes.focusedTheme = this.$el;

		this.trigger( 'theme:expand', self.model.cid );
	},

	preventExpand: function() {
		this.touchDrag = true;
	},

	preview: function( event ) {
		var self = this,
			current, preview;

		event = event || window.event;

		// Bail if the user scrolled on a touch device.
		if ( this.touchDrag === true ) {
			return this.touchDrag = false;
		}

		// Allow direct link path to installing a theme.
		if ( $( event.target ).not( '.install-theme-preview' ).parents( '.theme-actions' ).length ) {
			return;
		}

		// 'Enter' and 'Space' keys expand the details view when a theme is :focused.
		if ( event.type === 'keydown' && ( event.which !== 13 && event.which !== 32 ) ) {
			return;
		}

		// Pressing Enter while focused on the buttons shouldn't open the preview.
		if ( event.type === 'keydown' && event.which !== 13 && $( ':focus' ).hasClass( 'button' ) ) {
			return;
		}

		event.preventDefault();

		event = event || window.event;

		// Set focus to current theme.
		themes.focusedTheme = this.$el;

		// Construct a new Preview view.
		themes.preview = preview = new themes.view.Preview({
			model: this.model
		});

		// Render the view and append it.
		preview.render();
		this.setNavButtonsState();

		// Hide previous/next navigation if there is only one theme.
		if ( this.model.collection.length === 1 ) {
			preview.$el.addClass( 'no-navigation' );
		} else {
			preview.$el.removeClass( 'no-navigation' );
		}

		// Append preview.
		$( 'div.wrap' ).append( preview.el );

		// Listen to our preview object
		// for `theme:next` and `theme:previous` events.
		this.listenTo( preview, 'theme:next', function() {

			// Keep local track of current theme model.
			current = self.model;

			// If we have ventured away from current model update the current model position.
			if ( ! _.isUndefined( self.current ) ) {
				current = self.current;
			}

			// Get next theme model.
			self.current = self.model.collection.at( self.model.collection.indexOf( current ) + 1 );

			// If we have no more themes, bail.
			if ( _.isUndefined( self.current ) ) {
				self.options.parent.parent.trigger( 'theme:end' );
				return self.current = current;
			}

			preview.model = self.current;

			// Render and append.
			preview.render();
			this.setNavButtonsState();
			$( '.next-theme' ).trigger( 'focus' );
		})
		.listenTo( preview, 'theme:previous', function() {

			// Keep track of current theme model.
			current = self.model;

			// Bail early if we are at the beginning of the collection.
			if ( self.model.collection.indexOf( self.current ) === 0 ) {
				return;
			}

			// If we have ventured away from current model update the current model position.
			if ( ! _.isUndefined( self.current ) ) {
				current = self.current;
			}

			// Get previous theme model.
			self.current = self.model.collection.at( self.model.collection.indexOf( current ) - 1 );

			// If we have no more themes, bail.
			if ( _.isUndefined( self.current ) ) {
				return;
			}

			preview.model = self.current;

			// Render and append.
			preview.render();
			this.setNavButtonsState();
			$( '.previous-theme' ).trigger( 'focus' );
		});

		this.listenTo( preview, 'preview:close', function() {
			self.current = self.model;
		});

	},

	// Handles .disabled classes for previous/next buttons in theme installer preview.
	setNavButtonsState: function() {
		var $themeInstaller = $( '.theme-install-overlay' ),
			current = _.isUndefined( this.current ) ? this.model : this.current,
			previousThemeButton = $themeInstaller.find( '.previous-theme' ),
			nextThemeButton = $themeInstaller.find( '.next-theme' );

		// Disable previous at the zero position.
		if ( 0 === this.model.collection.indexOf( current ) ) {
			previousThemeButton
				.addClass( 'disabled' )
				.prop( 'disabled', true );

			nextThemeButton.trigger( 'focus' );
		}

		// Disable next if the next model is undefined.
		if ( _.isUndefined( this.model.collection.at( this.model.collection.indexOf( current ) + 1 ) ) ) {
			nextThemeButton
				.addClass( 'disabled' )
				.prop( 'disabled', true );

			previousThemeButton.trigger( 'focus' );
		}
	},

	installTheme: function( event ) {
		var _this = this;

		event.preventDefault();

		wp.updates.maybeRequestFilesystemCredentials( event );

		$( document ).on( 'wp-theme-install-success', function( event, response ) {
			if ( _this.model.get( 'id' ) === response.slug ) {
				_this.model.set( { 'installed': true } );
			}
		} );

		wp.updates.installTheme( {
			slug: $( event.target ).data( 'slug' )
		} );
	},

	updateTheme: function( event ) {
		var _this = this;

		if ( ! this.model.get( 'hasPackage' ) ) {
			return;
		}

		event.preventDefault();

		wp.updates.maybeRequestFilesystemCredentials( event );

		$( document ).on( 'wp-theme-update-success', function( event, response ) {
			_this.model.off( 'change', _this.render, _this );
			if ( _this.model.get( 'id' ) === response.slug ) {
				_this.model.set( {
					hasUpdate: false,
					version: response.newVersion
				} );
			}
			_this.model.on( 'change', _this.render, _this );
		} );

		wp.updates.updateTheme( {
			slug: $( event.target ).parents( 'div.theme' ).first().data( 'slug' )
		} );
	}
});

// Theme Details view.
// Sets up a modal overlay with the expanded theme data.
themes.view.Details = wp.Backbone.View.extend({

	// Wrap theme data on a div.theme element.
	className: 'theme-overlay',

	events: {
		'click': 'collapse',
		'click .delete-theme': 'deleteTheme',
		'click .left': 'previousTheme',
		'click .right': 'nextTheme',
		'click #update-theme': 'updateTheme',
		'click .toggle-auto-update': 'autoupdateState'
	},

	// The HTML template for the theme overlay.
	html: themes.template( 'theme-single' ),

	render: function() {
		var data = this.model.toJSON();
		this.$el.html( this.html( data ) );
		// Renders active theme styles.
		this.activeTheme();
		// Set up navigation events.
		this.navigation();
		// Checks screenshot size.
		this.screenshotCheck( this.$el );
		// Contain "tabbing" inside the overlay.
		this.containFocus( this.$el );
	},

	// Adds a class to the currently active theme
	// and to the overlay in detailed view mode.
	activeTheme: function() {
		// Check the model has the active property.
		this.$el.toggleClass( 'active', this.model.get( 'active' ) );
	},

	// Set initial focus and constrain tabbing within the theme browser modal.
	containFocus: function( $el ) {

		// Set initial focus on the primary action control.
		_.delay( function() {
			$( '.theme-overlay' ).trigger( 'focus' );
		}, 100 );

		// Constrain tabbing within the modal.
		$el.on( 'keydown.wp-themes', function( event ) {
			var $firstFocusable = $el.find( '.theme-header button:not(.disabled)' ).first(),
				$lastFocusable = $el.find( '.theme-actions a:visible' ).last();

			// Check for the Tab key.
			if ( 9 === event.which ) {
				if ( $firstFocusable[0] === event.target && event.shiftKey ) {
					$lastFocusable.trigger( 'focus' );
					event.preventDefault();
				} else if ( $lastFocusable[0] === event.target && ! event.shiftKey ) {
					$firstFocusable.trigger( 'focus' );
					event.preventDefault();
				}
			}
		});
	},

	// Single theme overlay screen.
	// It's shown when clicking a theme.
	collapse: function( event ) {
		var self = this,
			scroll;

		event = event || window.event;

		// Prevent collapsing detailed view when there is only one theme available.
		if ( themes.data.themes.length === 1 ) {
			return;
		}

		// Detect if the click is inside the overlay and don't close it
		// unless the target was the div.back button.
		if ( $( event.target ).is( '.theme-backdrop' ) || $( event.target ).is( '.close' ) || event.keyCode === 27 ) {

			// Add a temporary closing class while overlay fades out.
			$( 'body' ).addClass( 'closing-overlay' );

			// With a quick fade out animation.
			this.$el.fadeOut( 130, function() {
				// Clicking outside the modal box closes the overlay.
				$( 'body' ).removeClass( 'closing-overlay' );
				// Handle event cleanup.
				self.closeOverlay();

				// Get scroll position to avoid jumping to the top.
				scroll = document.body.scrollTop;

				// Clean the URL structure.
				themes.router.navigate( themes.router.baseUrl( '' ) );

				// Restore scroll position.
				document.body.scrollTop = scroll;

				// Return focus to the theme div.
				if ( themes.focusedTheme ) {
					themes.focusedTheme.find('.more-details').trigger( 'focus' );
				}
			});
		}
	},

	// Handles .disabled classes for next/previous buttons.
	navigation: function() {

		// Disable Left/Right when at the start or end of the collection.
		if ( this.model.cid === this.model.collection.at(0).cid ) {
			this.$el.find( '.left' )
				.addClass( 'disabled' )
				.prop( 'disabled', true );
		}
		if ( this.model.cid === this.model.collection.at( this.model.collection.length - 1 ).cid ) {
			this.$el.find( '.right' )
				.addClass( 'disabled' )
				.prop( 'disabled', true );
		}
	},

	// Performs the actions to effectively close
	// the theme details overlay.
	closeOverlay: function() {
		$( 'body' ).removeClass( 'modal-open' );
		this.remove();
		this.unbind();
		this.trigger( 'theme:collapse' );
	},

	// Set state of the auto-update settings link after it has been changed and saved.
	autoupdateState: function() {
		var callback,
			_this = this;

		// Support concurrent clicks in different Theme Details overlays.
		callback = function( event, data ) {
			var autoupdate;
			if ( _this.model.get( 'id' ) === data.asset ) {
				autoupdate = _this.model.get( 'autoupdate' );
				autoupdate.enabled = 'enable' === data.state;
				_this.model.set( { autoupdate: autoupdate } );
				$( document ).off( 'wp-auto-update-setting-changed', callback );
			}
		};

		// Triggered in updates.js
		$( document ).on( 'wp-auto-update-setting-changed', callback );
	},

	updateTheme: function( event ) {
		var _this = this;
		event.preventDefault();

		wp.updates.maybeRequestFilesystemCredentials( event );

		$( document ).on( 'wp-theme-update-success', function( event, response ) {
			if ( _this.model.get( 'id' ) === response.slug ) {
				_this.model.set( {
					hasUpdate: false,
					version: response.newVersion
				} );
			}
			_this.render();
		} );

		wp.updates.updateTheme( {
			slug: $( event.target ).data( 'slug' )
		} );
	},

	deleteTheme: function( event ) {
		var _this = this,
		    _collection = _this.model.collection,
		    _themes = themes;
		event.preventDefault();

		// Confirmation dialog for deleting a theme.
		if ( ! window.confirm( wp.themes.data.settings.confirmDelete ) ) {
			return;
		}

		wp.updates.maybeRequestFilesystemCredentials( event );

		$( document ).one( 'wp-theme-delete-success', function( event, response ) {
			_this.$el.find( '.close' ).trigger( 'click' );
			$( '[data-slug="' + response.slug + '"]' ).css( { backgroundColor:'#faafaa' } ).fadeOut( 350, function() {
				$( this ).remove();
				_themes.data.themes = _.without( _themes.data.themes, _.findWhere( _themes.data.themes, { id: response.slug } ) );

				$( '.wp-filter-search' ).val( '' );
				_collection.doSearch( '' );
				_collection.remove( _this.model );
				_collection.trigger( 'themes:update' );
			} );
		} );

		wp.updates.deleteTheme( {
			slug: this.model.get( 'id' )
		} );
	},

	nextTheme: function() {
		var self = this;
		self.trigger( 'theme:next', self.model.cid );
		return false;
	},

	previousTheme: function() {
		var self = this;
		self.trigger( 'theme:previous', self.model.cid );
		return false;
	},

	// Checks if the theme screenshot is the old 300px width version
	// and adds a corresponding class if it's true.
	screenshotCheck: function( el ) {
		var screenshot, image;

		screenshot = el.find( '.screenshot img' );
		image = new Image();
		image.src = screenshot.attr( 'src' );

		// Width check.
		if ( image.width && image.width <= 300 ) {
			el.addClass( 'small-screenshot' );
		}
	}
});

// Theme Preview view.
// Sets up a modal overlay with the expanded theme data.
themes.view.Preview = themes.view.Details.extend({

	className: 'wp-full-overlay expanded',
	el: '.theme-install-overlay',

	events: {
		'click .close-full-overlay': 'close',
		'click .collapse-sidebar': 'collapse',
		'click .devices button': 'previewDevice',
		'click .previous-theme': 'previousTheme',
		'click .next-theme': 'nextTheme',
		'keyup': 'keyEvent',
		'click .theme-install': 'installTheme'
	},

	// The HTML template for the theme preview.
	html: themes.template( 'theme-preview' ),

	render: function() {
		var self = this,
			currentPreviewDevice,
			data = this.model.toJSON(),
			$body = $( document.body );

		$body.attr( 'aria-busy', 'true' );

		this.$el.removeClass( 'iframe-ready' ).html( this.html( data ) );

		currentPreviewDevice = this.$el.data( 'current-preview-device' );
		if ( currentPreviewDevice ) {
			self.tooglePreviewDeviceButtons( currentPreviewDevice );
		}

		themes.router.navigate( themes.router.baseUrl( themes.router.themePath + this.model.get( 'id' ) ), { replace: false } );

		this.$el.fadeIn( 200, function() {
			$body.addClass( 'theme-installer-active full-overlay-active' );
		});

		this.$el.find( 'iframe' ).one( 'load', function() {
			self.iframeLoaded();
		});
	},

	iframeLoaded: function() {
		this.$el.addClass( 'iframe-ready' );
		$( document.body ).attr( 'aria-busy', 'false' );
	},

	close: function() {
		this.$el.fadeOut( 200, function() {
			$( 'body' ).removeClass( 'theme-installer-active full-overlay-active' );

			// Return focus to the theme div.
			if ( themes.focusedTheme ) {
				themes.focusedTheme.find('.more-details').trigger( 'focus' );
			}
		}).removeClass( 'iframe-ready' );

		// Restore the previous browse tab if available.
		if ( themes.router.selectedTab ) {
			themes.router.navigate( themes.router.baseUrl( '?browse=' + themes.router.selectedTab ) );
			themes.router.selectedTab = false;
		} else {
			themes.router.navigate( themes.router.baseUrl( '' ) );
		}
		this.trigger( 'preview:close' );
		this.undelegateEvents();
		this.unbind();
		return false;
	},

	collapse: function( event ) {
		var $button = $( event.currentTarget );
		if ( 'true' === $button.attr( 'aria-expanded' ) ) {
			$button.attr({ 'aria-expanded': 'false', 'aria-label': l10n.expandSidebar });
		} else {
			$button.attr({ 'aria-expanded': 'true', 'aria-label': l10n.collapseSidebar });
		}

		this.$el.toggleClass( 'collapsed' ).toggleClass( 'expanded' );
		return false;
	},

	previewDevice: function( event ) {
		var device = $( event.currentTarget ).data( 'device' );

		this.$el
			.removeClass( 'preview-desktop preview-tablet preview-mobile' )
			.addClass( 'preview-' + device )
			.data( 'current-preview-device', device );

		this.tooglePreviewDeviceButtons( device );
	},

	tooglePreviewDeviceButtons: function( newDevice ) {
		var $devices = $( '.wp-full-overlay-footer .devices' );

		$devices.find( 'button' )
			.removeClass( 'active' )
			.attr( 'aria-pressed', false );

		$devices.find( 'button.preview-' + newDevice )
			.addClass( 'active' )
			.attr( 'aria-pressed', true );
	},

	keyEvent: function( event ) {
		// The escape key closes the preview.
		if ( event.keyCode === 27 ) {
			this.undelegateEvents();
			this.close();
		}
		// The right arrow key, next theme.
		if ( event.keyCode === 39 ) {
			_.once( this.nextTheme() );
		}

		// The left arrow key, previous theme.
		if ( event.keyCode === 37 ) {
			this.previousTheme();
		}
	},

	installTheme: function( event ) {
		var _this   = this,
		    $target = $( event.target );
		event.preventDefault();

		if ( $target.hasClass( 'disabled' ) ) {
			return;
		}

		wp.updates.maybeRequestFilesystemCredentials( event );

		$( document ).on( 'wp-theme-install-success', function() {
			_this.model.set( { 'installed': true } );
		} );

		wp.updates.installTheme( {
			slug: $target.data( 'slug' )
		} );
	}
});

// Controls the rendering of div.themes,
// a wrapper that will hold all the theme elements.
themes.view.Themes = wp.Backbone.View.extend({

	className: 'themes wp-clearfix',
	$overlay: $( 'div.theme-overlay' ),

	// Number to keep track of scroll position
	// while in theme-overlay mode.
	index: 0,

	// The theme count element.
	count: $( '.wrap .theme-count' ),

	// The live themes count.
	liveThemeCount: 0,

	initialize: function( options ) {
		var self = this;

		// Set up parent.
		this.parent = options.parent;

		// Set current view to [grid].
		this.setView( 'grid' );

		// Move the active theme to the beginning of the collection.
		self.currentTheme();

		// When the collection is updated by user input...
		this.listenTo( self.collection, 'themes:update', function() {
			self.parent.page = 0;
			self.currentTheme();
			self.render( this );
		} );

		// Update theme count to full result set when available.
		this.listenTo( self.collection, 'query:success', function( count ) {
			if ( _.isNumber( count ) ) {
				self.count.text( count );
				self.announceSearchResults( count );
			} else {
				self.count.text( self.collection.length );
				self.announceSearchResults( self.collection.length );
			}
		});

		this.listenTo( self.collection, 'query:empty', function() {
			$( 'body' ).addClass( 'no-results' );
		});

		this.listenTo( this.parent, 'theme:scroll', function() {
			self.renderThemes( self.parent.page );
		});

		this.listenTo( this.parent, 'theme:close', function() {
			if ( self.overlay ) {
				self.overlay.closeOverlay();
			}
		} );

		// Bind keyboard events.
		$( 'body' ).on( 'keyup', function( event ) {
			if ( ! self.overlay ) {
				return;
			}

			// Bail if the filesystem credentials dialog is shown.
			if ( $( '#request-filesystem-credentials-dialog' ).is( ':visible' ) ) {
				return;
			}

			// Pressing the right arrow key fires a theme:next event.
			if ( event.keyCode === 39 ) {
				self.overlay.nextTheme();
			}

			// Pressing the left arrow key fires a theme:previous event.
			if ( event.keyCode === 37 ) {
				self.overlay.previousTheme();
			}

			// Pressing the escape key fires a theme:collapse event.
			if ( event.keyCode === 27 ) {
				self.overlay.collapse( event );
			}
		});
	},

	// Manages rendering of theme pages
	// and keeping theme count in sync.
	render: function() {
		// Clear the DOM, please.
		this.$el.empty();

		// If the user doesn't have switch capabilities or there is only one theme
		// in the collection, render the detailed view of the active theme.
		if ( themes.data.themes.length === 1 ) {

			// Constructs the view.
			this.singleTheme = new themes.view.Details({
				model: this.collection.models[0]
			});

			// Render and apply a 'single-theme' class to our container.
			this.singleTheme.render();
			this.$el.addClass( 'single-theme' );
			this.$el.append( this.singleTheme.el );
		}

		// Generate the themes using page instance
		// while checking the collection has items.
		if ( this.options.collection.size() > 0 ) {
			this.renderThemes( this.parent.page );
		}

		// Display a live theme count for the collection.
		this.liveThemeCount = this.collection.count ? this.collection.count : this.collection.length;
		this.count.text( this.liveThemeCount );

		/*
		 * In the theme installer the themes count is already announced
		 * because `announceSearchResults` is called on `query:success`.
		 */
		if ( ! themes.isInstall ) {
			this.announceSearchResults( this.liveThemeCount );
		}
	},

	// Iterates through each instance of the collection
	// and renders each theme module.
	renderThemes: function( page ) {
		var self = this;

		self.instance = self.collection.paginate( page );

		// If we have no more themes, bail.
		if ( self.instance.size() === 0 ) {
			// Fire a no-more-themes event.
			this.parent.trigger( 'theme:end' );
			return;
		}

		// Make sure the add-new stays at the end.
		if ( ! themes.isInstall && page >= 1 ) {
			$( '.add-new-theme' ).remove();
		}

		// Loop through the themes and setup each theme view.
		self.instance.each( function( theme ) {
			self.theme = new themes.view.Theme({
				model: theme,
				parent: self
			});

			// Render the views...
			self.theme.render();
			// ...and append them to div.themes.
			self.$el.append( self.theme.el );

			// Binds to theme:expand to show the modal box
			// with the theme details.
			self.listenTo( self.theme, 'theme:expand', self.expand, self );
		});

		// 'Add new theme' element shown at the end of the grid.
		if ( ! themes.isInstall && themes.data.settings.canInstall ) {
			this.$el.append( '<div class="theme add-new-theme"><a href="' + themes.data.settings.installURI + '"><div class="theme-screenshot"><span></span></div><h2 class="theme-name">' + l10n.addNew + '</h2></a></div>' );
		}

		this.parent.page++;
	},

	// Grabs current theme and puts it at the beginning of the collection.
	currentTheme: function() {
		var self = this,
			current;

		current = self.collection.findWhere({ active: true });

		// Move the active theme to the beginning of the collection.
		if ( current ) {
			self.collection.remove( current );
			self.collection.add( current, { at:0 } );
		}
	},

	// Sets current view.
	setView: function( view ) {
		return view;
	},

	// Renders the overlay with the ThemeDetails view.
	// Uses the current model data.
	expand: function( id ) {
		var self = this, $card, $modal;

		// Set the current theme model.
		this.model = self.collection.get( id );

		// Trigger a route update for the current model.
		themes.router.navigate( themes.router.baseUrl( themes.router.themePath + this.model.id ) );

		// Sets this.view to 'detail'.
		this.setView( 'detail' );
		$( 'body' ).addClass( 'modal-open' );

		// Set up the theme details view.
		this.overlay = new themes.view.Details({
			model: self.model
		});

		this.overlay.render();

		if ( this.model.get( 'hasUpdate' ) ) {
			$card  = $( '[data-slug="' + this.model.id + '"]' );
			$modal = $( this.overlay.el );

			if ( $card.find( '.updating-message' ).length ) {
				$modal.find( '.notice-warning h3' ).remove();
				$modal.find( '.notice-warning' )
					.removeClass( 'notice-large' )
					.addClass( 'updating-message' )
					.find( 'p' ).text( wp.updates.l10n.updating );
			} else if ( $card.find( '.notice-error' ).length ) {
				$modal.find( '.notice-warning' ).remove();
			}
		}

		this.$overlay.html( this.overlay.el );

		// Bind to theme:next and theme:previous triggered by the arrow keys.
		// Keep track of the current model so we can infer an index position.
		this.listenTo( this.overlay, 'theme:next', function() {
			// Renders the next theme on the overlay.
			self.next( [ self.model.cid ] );

		})
		.listenTo( this.overlay, 'theme:previous', function() {
			// Renders the previous theme on the overlay.
			self.previous( [ self.model.cid ] );
		});
	},

	/*
	 * This method renders the next theme on the overlay modal
	 * based on the current position in the collection.
	 *
	 * @params [model cid]
	 */
	next: function( args ) {
		var self = this,
			model, nextModel;

		// Get the current theme.
		model = self.collection.get( args[0] );
		// Find the next model within the collection.
		nextModel = self.collection.at( self.collection.indexOf( model ) + 1 );

		// Sanity check which also serves as a boundary test.
		if ( nextModel !== undefined ) {

			// We have a new theme...
			// Close the overlay.
			this.overlay.closeOverlay();

			// Trigger a route update for the current model.
			self.theme.trigger( 'theme:expand', nextModel.cid );

		}
	},

	/*
	 * This method renders the previous theme on the overlay modal
	 * based on the current position in the collection.
	 *
	 * @params [model cid]
	 */
	previous: function( args ) {
		var self = this,
			model, previousModel;

		// Get the current theme.
		model = self.collection.get( args[0] );
		// Find the previous model within the collection.
		previousModel = self.collection.at( self.collection.indexOf( model ) - 1 );

		if ( previousModel !== undefined ) {

			// We have a new theme...
			// Close the overlay.
			this.overlay.closeOverlay();

			// Trigger a route update for the current model.
			self.theme.trigger( 'theme:expand', previousModel.cid );

		}
	},

	// Dispatch audible search results feedback message.
	announceSearchResults: function( count ) {
		if ( 0 === count ) {
			wp.a11y.speak( l10n.noThemesFound );
		} else {
			wp.a11y.speak( l10n.themesFound.replace( '%d', count ) );
		}
	}
});

// Search input view controller.
themes.view.Search = wp.Backbone.View.extend({

	tagName: 'input',
	className: 'wp-filter-search',
	id: 'wp-filter-search-input',
	searching: false,

	attributes: {
		placeholder: l10n.searchPlaceholder,
		type: 'search',
		'aria-describedby': 'live-search-desc'
	},

	events: {
		'input': 'search',
		'keyup': 'search',
		'blur': 'pushState'
	},

	initialize: function( options ) {

		this.parent = options.parent;

		this.listenTo( this.parent, 'theme:close', function() {
			this.searching = false;
		} );

	},

	search: function( event ) {
		// Clear on escape.
		if ( event.type === 'keyup' && event.which === 27 ) {
			event.target.value = '';
		}

		// Since doSearch is debounced, it will only run when user input comes to a rest.
		this.doSearch( event );
	},

	// Runs a search on the theme collection.
	doSearch: function( event ) {
		var options = {};

		this.collection.doSearch( event.target.value.replace( /\+/g, ' ' ) );

		// if search is initiated and key is not return.
		if ( this.searching && event.which !== 13 ) {
			options.replace = true;
		} else {
			this.searching = true;
		}

		// Update the URL hash.
		if ( event.target.value ) {
			themes.router.navigate( themes.router.baseUrl( themes.router.searchPath + event.target.value ), options );
		} else {
			themes.router.navigate( themes.router.baseUrl( '' ) );
		}
	},

	pushState: function( event ) {
		var url = themes.router.baseUrl( '' );

		if ( event.target.value ) {
			url = themes.router.baseUrl( themes.router.searchPath + encodeURIComponent( event.target.value ) );
		}

		this.searching = false;
		themes.router.navigate( url );

	}
});

/**
 * Navigate router.
 *
 * @since 4.9.0
 *
 * @param {string} url - URL to navigate to.
 * @param {Object} state - State.
 * @return {void}
 */
function navigateRouter( url, state ) {
	var router = this;
	if ( Backbone.history._hasPushState ) {
		Backbone.Router.prototype.navigate.call( router, url, state );
	}
}

// Sets up the routes events for relevant url queries.
// Listens to [theme] and [search] params.
themes.Router = Backbone.Router.extend({

	routes: {
		'themes.php?theme=:slug': 'theme',
		'themes.php?search=:query': 'search',
		'themes.php?s=:query': 'search',
		'themes.php': 'themes',
		'': 'themes'
	},

	baseUrl: function( url ) {
		return 'themes.php' + url;
	},

	themePath: '?theme=',
	searchPath: '?search=',

	search: function( query ) {
		$( '.wp-filter-search' ).val( query.replace( /\+/g, ' ' ) );
	},

	themes: function() {
		$( '.wp-filter-search' ).val( '' );
	},

	navigate: navigateRouter

});

// Execute and setup the application.
themes.Run = {
	init: function() {
		// Initializes the blog's theme library view.
		// Create a new collection with data.
		this.themes = new themes.Collection( themes.data.themes );

		// Set up the view.
		this.view = new themes.view.Appearance({
			collection: this.themes
		});

		this.render();

		// Start debouncing user searches after Backbone.history.start().
		this.view.SearchView.doSearch = _.debounce( this.view.SearchView.doSearch, 500 );
	},

	render: function() {

		// Render results.
		this.view.render();
		this.routes();

		if ( Backbone.History.started ) {
			Backbone.history.stop();
		}
		Backbone.history.start({
			root: themes.data.settings.adminUrl,
			pushState: true,
			hashChange: false
		});
	},

	routes: function() {
		var self = this;
		// Bind to our global thx object
		// so that the object is available to sub-views.
		themes.router = new themes.Router();

		// Handles theme details route event.
		themes.router.on( 'route:theme', function( slug ) {
			self.view.view.expand( slug );
		});

		themes.router.on( 'route:themes', function() {
			self.themes.doSearch( '' );
			self.view.trigger( 'theme:close' );
		});

		// Handles search route event.
		themes.router.on( 'route:search', function() {
			$( '.wp-filter-search' ).trigger( 'keyup' );
		});

		this.extraRoutes();
	},

	extraRoutes: function() {
		return false;
	}
};

// Extend the main Search view.
themes.view.InstallerSearch =  themes.view.Search.extend({

	events: {
		'input': 'search',
		'keyup': 'search'
	},

	terms: '',

	// Handles Ajax request for searching through themes in public repo.
	search: function( event ) {

		// Tabbing or reverse tabbing into the search input shouldn't trigger a search.
		if ( event.type === 'keyup' && ( event.which === 9 || event.which === 16 ) ) {
			return;
		}

		this.collection = this.options.parent.view.collection;

		// Clear on escape.
		if ( event.type === 'keyup' && event.which === 27 ) {
			event.target.value = '';
		}

		this.doSearch( event.target.value );
	},

	doSearch: function( value ) {
		var request = {};

		// Don't do anything if the search terms haven't changed.
		if ( this.terms === value ) {
			return;
		}

		// Updates terms with the value passed.
		this.terms = value;

		request.search = value;

		/*
		 * Intercept an [author] search.
		 *
		 * If input value starts with `author:` send a request
		 * for `author` instead of a regular `search`.
		 */
		if ( value.substring( 0, 7 ) === 'author:' ) {
			request.search = '';
			request.author = value.slice( 7 );
		}

		/*
		 * Intercept a [tag] search.
		 *
		 * If input value starts with `tag:` send a request
		 * for `tag` instead of a regular `search`.
		 */
		if ( value.substring( 0, 4 ) === 'tag:' ) {
			request.search = '';
			request.tag = [ value.slice( 4 ) ];
		}

		$( '.filter-links li > a.current' )
			.removeClass( 'current' )
			.removeAttr( 'aria-current' );

		$( 'body' ).removeClass( 'show-filters filters-applied show-favorites-form' );
		$( '.drawer-toggle' ).attr( 'aria-expanded', 'false' );

		// Get the themes by sending Ajax POST request to api.wordpress.org/themes
		// or searching the local cache.
		this.collection.query( request );

		// Set route.
		themes.router.navigate( themes.router.baseUrl( themes.router.searchPath + encodeURIComponent( value ) ), { replace: true } );
	}
});

themes.view.Installer = themes.view.Appearance.extend({

	el: '#wpbody-content .wrap',

	// Register events for sorting and filters in theme-navigation.
	events: {
		'click .filter-links li > a': 'onSort',
		'click .theme-filter': 'onFilter',
		'click .drawer-toggle': 'moreFilters',
		'click .filter-drawer .apply-filters': 'applyFilters',
		'click .filter-group [type="checkbox"]': 'addFilter',
		'click .filter-drawer .clear-filters': 'clearFilters',
		'click .edit-filters': 'backToFilters',
		'click .favorites-form-submit' : 'saveUsername',
		'keyup #wporg-username-input': 'saveUsername'
	},

	// Initial render method.
	render: function() {
		var self = this;

		this.search();
		this.uploader();

		this.collection = new themes.Collection();

		// Bump `collection.currentQuery.page` and request more themes if we hit the end of the page.
		this.listenTo( this, 'theme:end', function() {

			// Make sure we are not already loading.
			if ( self.collection.loadingThemes ) {
				return;
			}

			// Set loadingThemes to true and bump page instance of currentQuery.
			self.collection.loadingThemes = true;
			self.collection.currentQuery.page++;

			// Use currentQuery.page to build the themes request.
			_.extend( self.collection.currentQuery.request, { page: self.collection.currentQuery.page } );
			self.collection.query( self.collection.currentQuery.request );
		});

		this.listenTo( this.collection, 'query:success', function() {
			$( 'body' ).removeClass( 'loading-content' );
			$( '.theme-browser' ).find( 'div.error' ).remove();
		});

		this.listenTo( this.collection, 'query:fail', function() {
			$( 'body' ).removeClass( 'loading-content' );
			$( '.theme-browser' ).find( 'div.error' ).remove();
			$( '.theme-browser' ).find( 'div.themes' ).before( '<div class="error"><p>' + l10n.error + '</p><p><button class="button try-again">' + l10n.tryAgain + '</button></p></div>' );
			$( '.theme-browser .error .try-again' ).on( 'click', function( e ) {
				e.preventDefault();
				$( 'input.wp-filter-search' ).trigger( 'input' );
			} );
		});

		if ( this.view ) {
			this.view.remove();
		}

		// Sets up the view and passes the section argument.
		this.view = new themes.view.Themes({
			collection: this.collection,
			parent: this
		});

		// Reset pagination every time the install view handler is run.
		this.page = 0;

		// Render and append.
		this.$el.find( '.themes' ).remove();
		this.view.render();
		this.$el.find( '.theme-browser' ).append( this.view.el ).addClass( 'rendered' );
	},

	// Handles all the rendering of the public theme directory.
	browse: function( section ) {
		// Create a new collection with the proper theme data
		// for each section.
		this.collection.query( { browse: section } );
	},

	// Sorting navigation.
	onSort: function( event ) {
		var $el = $( event.target ),
			sort = $el.data( 'sort' );

		event.preventDefault();

		$( 'body' ).removeClass( 'filters-applied show-filters' );
		$( '.drawer-toggle' ).attr( 'aria-expanded', 'false' );

		// Bail if this is already active.
		if ( $el.hasClass( this.activeClass ) ) {
			return;
		}

		this.sort( sort );

		// Trigger a router.navigate update.
		themes.router.navigate( themes.router.baseUrl( themes.router.browsePath + sort ) );
	},

	sort: function( sort ) {
		this.clearSearch();

		// Track sorting so we can restore the correct tab when closing preview.
		themes.router.selectedTab = sort;

		$( '.filter-links li > a, .theme-filter' )
			.removeClass( this.activeClass )
			.removeAttr( 'aria-current' );

		$( '[data-sort="' + sort + '"]' )
			.addClass( this.activeClass )
			.attr( 'aria-current', 'page' );

		if ( 'favorites' === sort ) {
			$( 'body' ).addClass( 'show-favorites-form' );
		} else {
			$( 'body' ).removeClass( 'show-favorites-form' );
		}

		this.browse( sort );
	},

	// Filters and Tags.
	onFilter: function( event ) {
		var request,
			$el = $( event.target ),
			filter = $el.data( 'filter' );

		// Bail if this is already active.
		if ( $el.hasClass( this.activeClass ) ) {
			return;
		}

		$( '.filter-links li > a, .theme-section' )
			.removeClass( this.activeClass )
			.removeAttr( 'aria-current' );
		$el
			.addClass( this.activeClass )
			.attr( 'aria-current', 'page' );

		if ( ! filter ) {
			return;
		}

		// Construct the filter request
		// using the default values.
		filter = _.union( [ filter, this.filtersChecked() ] );
		request = { tag: [ filter ] };

		// Get the themes by sending Ajax POST request to api.wordpress.org/themes
		// or searching the local cache.
		this.collection.query( request );
	},

	// Clicking on a checkbox to add another filter to the request.
	addFilter: function() {
		this.filtersChecked();
	},

	// Applying filters triggers a tag request.
	applyFilters: function( event ) {
		var name,
			tags = this.filtersChecked(),
			request = { tag: tags },
			filteringBy = $( '.filtered-by .tags' );

		if ( event ) {
			event.preventDefault();
		}

		if ( ! tags ) {
			wp.a11y.speak( l10n.selectFeatureFilter );
			return;
		}

		$( 'body' ).addClass( 'filters-applied' );
		$( '.filter-links li > a.current' )
			.removeClass( 'current' )
			.removeAttr( 'aria-current' );

		filteringBy.empty();

		_.each( tags, function( tag ) {
			name = $( 'label[for="filter-id-' + tag + '"]' ).text();
			filteringBy.append( '<span class="tag">' + name + '</span>' );
		});

		// Get the themes by sending Ajax POST request to api.wordpress.org/themes
		// or searching the local cache.
		this.collection.query( request );
	},

	// Save the user's WordPress.org username and get his favorite themes.
	saveUsername: function ( event ) {
		var username = $( '#wporg-username-input' ).val(),
			nonce = $( '#wporg-username-nonce' ).val(),
			request = { browse: 'favorites', user: username },
			that = this;

		if ( event ) {
			event.preventDefault();
		}

		// Save username on enter.
		if ( event.type === 'keyup' && event.which !== 13 ) {
			return;
		}

		return wp.ajax.send( 'save-wporg-username', {
			data: {
				_wpnonce: nonce,
				username: username
			},
			success: function () {
				// Get the themes by sending Ajax POST request to api.wordpress.org/themes
				// or searching the local cache.
				that.collection.query( request );
			}
		} );
	},

	/**
	 * Get the checked filters.
	 *
	 * @return {Array} of tags or false
	 */
	filtersChecked: function() {
		var items = $( '.filter-group' ).find( ':checkbox' ),
			tags = [];

		_.each( items.filter( ':checked' ), function( item ) {
			tags.push( $( item ).prop( 'value' ) );
		});

		// When no filters are checked, restore initial state and return.
		if ( tags.length === 0 ) {
			$( '.filter-drawer .apply-filters' ).find( 'span' ).text( '' );
			$( '.filter-drawer .clear-filters' ).hide();
			$( 'body' ).removeClass( 'filters-applied' );
			return false;
		}

		$( '.filter-drawer .apply-filters' ).find( 'span' ).text( tags.length );
		$( '.filter-drawer .clear-filters' ).css( 'display', 'inline-block' );

		return tags;
	},

	activeClass: 'current',

	/**
	 * When users press the "Upload Theme" button, show the upload form in place.
	 */
	uploader: function() {
		var uploadViewToggle = $( '.upload-view-toggle' ),
			$body = $( document.body );

		uploadViewToggle.on( 'click', function() {
			// Toggle the upload view.
			$body.toggleClass( 'show-upload-view' );
			// Toggle the `aria-expanded` button attribute.
			uploadViewToggle.attr( 'aria-expanded', $body.hasClass( 'show-upload-view' ) );
		});
	},

	// Toggle the full filters navigation.
	moreFilters: function( event ) {
		var $body = $( 'body' ),
			$toggleButton = $( '.drawer-toggle' );

		event.preventDefault();

		if ( $body.hasClass( 'filters-applied' ) ) {
			return this.backToFilters();
		}

		this.clearSearch();

		themes.router.navigate( themes.router.baseUrl( '' ) );
		// Toggle the feature filters view.
		$body.toggleClass( 'show-filters' );
		// Toggle the `aria-expanded` button attribute.
		$toggleButton.attr( 'aria-expanded', $body.hasClass( 'show-filters' ) );
	},

	/**
	 * Clears all the checked filters.
	 *
	 * @uses filtersChecked()
	 */
	clearFilters: function( event ) {
		var items = $( '.filter-group' ).find( ':checkbox' ),
			self = this;

		event.preventDefault();

		_.each( items.filter( ':checked' ), function( item ) {
			$( item ).prop( 'checked', false );
			return self.filtersChecked();
		});
	},

	backToFilters: function( event ) {
		if ( event ) {
			event.preventDefault();
		}

		$( 'body' ).removeClass( 'filters-applied' );
	},

	clearSearch: function() {
		$( '#wp-filter-search-input').val( '' );
	}
});

themes.InstallerRouter = Backbone.Router.extend({
	routes: {
		'theme-install.php?theme=:slug': 'preview',
		'theme-install.php?browse=:sort': 'sort',
		'theme-install.php?search=:query': 'search',
		'theme-install.php': 'sort'
	},

	baseUrl: function( url ) {
		return 'theme-install.php' + url;
	},

	themePath: '?theme=',
	browsePath: '?browse=',
	searchPath: '?search=',

	search: function( query ) {
		$( '.wp-filter-search' ).val( query.replace( /\+/g, ' ' ) );
	},

	navigate: navigateRouter
});


themes.RunInstaller = {

	init: function() {
		// Set up the view.
		// Passes the default 'section' as an option.
		this.view = new themes.view.Installer({
			section: 'popular',
			SearchView: themes.view.InstallerSearch
		});

		// Render results.
		this.render();

		// Start debouncing user searches after Backbone.history.start().
		this.view.SearchView.doSearch = _.debounce( this.view.SearchView.doSearch, 500 );
	},

	render: function() {

		// Render results.
		this.view.render();
		this.routes();

		if ( Backbone.History.started ) {
			Backbone.history.stop();
		}
		Backbone.history.start({
			root: themes.data.settings.adminUrl,
			pushState: true,
			hashChange: false
		});
	},

	routes: function() {
		var self = this,
			request = {};

		// Bind to our global `wp.themes` object
		// so that the router is available to sub-views.
		themes.router = new themes.InstallerRouter();

		// Handles `theme` route event.
		// Queries the API for the passed theme slug.
		themes.router.on( 'route:preview', function( slug ) {

			// Remove existing handlers.
			if ( themes.preview ) {
				themes.preview.undelegateEvents();
				themes.preview.unbind();
			}

			// If the theme preview is active, set the current theme.
			if ( self.view.view.theme && self.view.view.theme.preview ) {
				self.view.view.theme.model = self.view.collection.findWhere( { 'slug': slug } );
				self.view.view.theme.preview();
			} else {

				// Select the theme by slug.
				request.theme = slug;
				self.view.collection.query( request );
				self.view.collection.trigger( 'update' );

				// Open the theme preview.
				self.view.collection.once( 'query:success', function() {
					$( 'div[data-slug="' + slug + '"]' ).trigger( 'click' );
				});

			}
		});

		/*
		 * Handles sorting / browsing routes.
		 * Also handles the root URL triggering a sort request
		 * for `popular`, the default view.
		 */
		themes.router.on( 'route:sort', function( sort ) {
			if ( ! sort ) {
				sort = 'popular';
				themes.router.navigate( themes.router.baseUrl( '?browse=popular' ), { replace: true } );
			}
			self.view.sort( sort );

			// Close the preview if open.
			if ( themes.preview ) {
				themes.preview.close();
			}
		});

		// The `search` route event. The router populates the input field.
		themes.router.on( 'route:search', function() {
			$( '.wp-filter-search' ).trigger( 'focus' ).trigger( 'keyup' );
		});

		this.extraRoutes();
	},

	extraRoutes: function() {
		return false;
	}
};

// Ready...
$( function() {
	if ( themes.isInstall ) {
		themes.RunInstaller.init();
	} else {
		themes.Run.init();
	}

	// Update the return param just in time.
	$( document.body ).on( 'click', '.load-customize', function() {
		var link = $( this ), urlParser = document.createElement( 'a' );
		urlParser.href = link.prop( 'href' );
		urlParser.search = $.param( _.extend(
			wp.customize.utils.parseQueryString( urlParser.search.substr( 1 ) ),
			{
				'return': window.location.href
			}
		) );
		link.prop( 'href', urlParser.href );
	});

	$( '.broken-themes .delete-theme' ).on( 'click', function() {
		return confirm( _wpThemeSettings.settings.confirmDelete );
	});
});

})( jQuery );

// Align theme browser thickbox.
jQuery( function($) {
	window.tb_position = function() {
		var tbWindow = $('#TB_window'),
			width = $(window).width(),
			H = $(window).height(),
			W = ( 1040 < width ) ? 1040 : width,
			adminbar_height = 0;

		if ( $('#wpadminbar').length ) {
			adminbar_height = parseInt( $('#wpadminbar').css('height'), 10 );
		}

		if ( tbWindow.length >= 1 ) {
			tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
			tbWindow.css({'margin-left': '-' + parseInt( ( ( W - 50 ) / 2 ), 10 ) + 'px'});
			if ( typeof document.body.style.maxWidth !== 'undefined' ) {
				tbWindow.css({'top': 20 + adminbar_height + 'px', 'margin-top': '0'});
			}
		}
	};

	$(window).on( 'resize', function(){ tb_position(); });
});
