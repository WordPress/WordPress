/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 659:
/***/ ((module) => {

var l10n = wp.media.view.l10n,
	EditAttachmentMetadata;

/**
 * wp.media.controller.EditAttachmentMetadata
 *
 * A state for editing an attachment's metadata.
 *
 * @memberOf wp.media.controller
 *
 * @class
 * @augments wp.media.controller.State
 * @augments Backbone.Model
 */
EditAttachmentMetadata = wp.media.controller.State.extend(/** @lends wp.media.controller.EditAttachmentMetadata.prototype */{
	defaults: {
		id:      'edit-attachment',
		// Title string passed to the frame's title region view.
		title:   l10n.attachmentDetails,
		// Region mode defaults.
		content: 'edit-metadata',
		menu:    false,
		toolbar: false,
		router:  false
	}
});

module.exports = EditAttachmentMetadata;


/***/ }),

/***/ 2429:
/***/ ((module) => {

/**
 * wp.media.view.MediaFrame.Manage.Router
 *
 * A router for handling the browser history and application state.
 *
 * @memberOf wp.media.view.MediaFrame.Manage
 *
 * @class
 * @augments Backbone.Router
 */
var Router = Backbone.Router.extend(/** @lends wp.media.view.MediaFrame.Manage.Router.prototype */{
	routes: {
		'upload.php?item=:slug&mode=edit': 'editItem',
		'upload.php?item=:slug':           'showItem',
		'upload.php?search=:query':        'search',
		'upload.php':                      'reset'
	},

	// Map routes against the page URL.
	baseUrl: function( url ) {
		return 'upload.php' + url;
	},

	reset: function() {
		var frame = wp.media.frames.edit;

		if ( frame ) {
			frame.close();
		}
	},

	// Respond to the search route by filling the search field and triggering the input event.
	search: function( query ) {
		jQuery( '#media-search-input' ).val( query ).trigger( 'input' );
	},

	// Show the modal with a specific item.
	showItem: function( query ) {
		var media = wp.media,
			frame = media.frames.browse,
			library = frame.state().get('library'),
			item;

		// Trigger the media frame to open the correct item.
		item = library.findWhere( { id: parseInt( query, 10 ) } );

		if ( item ) {
			item.set( 'skipHistory', true );
			frame.trigger( 'edit:attachment', item );
		} else {
			item = media.attachment( query );
			frame.listenTo( item, 'change', function( model ) {
				frame.stopListening( item );
				frame.trigger( 'edit:attachment', model );
			} );
			item.fetch();
		}
	},

	// Show the modal in edit mode with a specific item.
	editItem: function( query ) {
		this.showItem( query );
		wp.media.frames.edit.content.mode( 'edit-details' );
	}
});

module.exports = Router;


/***/ }),

/***/ 1312:
/***/ ((module) => {

var Details = wp.media.view.Attachment.Details,
	TwoColumn;

/**
 * wp.media.view.Attachment.Details.TwoColumn
 *
 * A similar view to media.view.Attachment.Details
 * for use in the Edit Attachment modal.
 *
 * @memberOf wp.media.view.Attachment.Details
 *
 * @class
 * @augments wp.media.view.Attachment.Details
 * @augments wp.media.view.Attachment
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
TwoColumn = Details.extend(/** @lends wp.media.view.Attachment.Details.TwoColumn.prototype */{
	template: wp.template( 'attachment-details-two-column' ),

	initialize: function() {
		this.controller.on( 'content:activate:edit-details', _.bind( this.editAttachment, this ) );

		Details.prototype.initialize.apply( this, arguments );
	},

	editAttachment: function( event ) {
		if ( event ) {
			event.preventDefault();
		}
		this.controller.content.mode( 'edit-image' );
	},

	/**
	 * Noop this from parent class, doesn't apply here.
	 */
	toggleSelectionHandler: function() {}

});

module.exports = TwoColumn;


/***/ }),

/***/ 5806:
/***/ ((module) => {

var Button = wp.media.view.Button,
	DeleteSelected = wp.media.view.DeleteSelectedButton,
	DeleteSelectedPermanently;

/**
 * wp.media.view.DeleteSelectedPermanentlyButton
 *
 * When MEDIA_TRASH is true, a button that handles bulk Delete Permanently logic
 *
 * @memberOf wp.media.view
 *
 * @class
 * @augments wp.media.view.DeleteSelectedButton
 * @augments wp.media.view.Button
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
DeleteSelectedPermanently = DeleteSelected.extend(/** @lends wp.media.view.DeleteSelectedPermanentlyButton.prototype */{
	initialize: function() {
		DeleteSelected.prototype.initialize.apply( this, arguments );
		this.controller.on( 'select:activate', this.selectActivate, this );
		this.controller.on( 'select:deactivate', this.selectDeactivate, this );
	},

	filterChange: function( model ) {
		this.canShow = ( 'trash' === model.get( 'status' ) );
	},

	selectActivate: function() {
		this.toggleDisabled();
		this.$el.toggleClass( 'hidden', ! this.canShow );
	},

	selectDeactivate: function() {
		this.toggleDisabled();
		this.$el.addClass( 'hidden' );
	},

	render: function() {
		Button.prototype.render.apply( this, arguments );
		this.selectActivate();
		return this;
	}
});

module.exports = DeleteSelectedPermanently;


/***/ }),

/***/ 6606:
/***/ ((module) => {

var Button = wp.media.view.Button,
	l10n = wp.media.view.l10n,
	DeleteSelected;

/**
 * wp.media.view.DeleteSelectedButton
 *
 * A button that handles bulk Delete/Trash logic
 *
 * @memberOf wp.media.view
 *
 * @class
 * @augments wp.media.view.Button
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
DeleteSelected = Button.extend(/** @lends wp.media.view.DeleteSelectedButton.prototype */{
	initialize: function() {
		Button.prototype.initialize.apply( this, arguments );
		if ( this.options.filters ) {
			this.options.filters.model.on( 'change', this.filterChange, this );
		}
		this.controller.on( 'selection:toggle', this.toggleDisabled, this );
		this.controller.on( 'select:activate', this.toggleDisabled, this );
	},

	filterChange: function( model ) {
		if ( 'trash' === model.get( 'status' ) ) {
			this.model.set( 'text', l10n.restoreSelected );
		} else if ( wp.media.view.settings.mediaTrash ) {
			this.model.set( 'text', l10n.trashSelected );
		} else {
			this.model.set( 'text', l10n.deletePermanently );
		}
	},

	toggleDisabled: function() {
		this.model.set( 'disabled', ! this.controller.state().get( 'selection' ).length );
	},

	render: function() {
		Button.prototype.render.apply( this, arguments );
		if ( this.controller.isModeActive( 'select' ) ) {
			this.$el.addClass( 'delete-selected-button' );
		} else {
			this.$el.addClass( 'delete-selected-button hidden' );
		}
		this.toggleDisabled();
		return this;
	}
});

module.exports = DeleteSelected;


/***/ }),

/***/ 682:
/***/ ((module) => {


var Button = wp.media.view.Button,
	l10n = wp.media.view.l10n,
	SelectModeToggle;

/**
 * wp.media.view.SelectModeToggleButton
 *
 * @memberOf wp.media.view
 *
 * @class
 * @augments wp.media.view.Button
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
SelectModeToggle = Button.extend(/** @lends wp.media.view.SelectModeToggle.prototype */{
	initialize: function() {
		_.defaults( this.options, {
			size : ''
		} );

		Button.prototype.initialize.apply( this, arguments );
		this.controller.on( 'select:activate select:deactivate', this.toggleBulkEditHandler, this );
		this.controller.on( 'selection:action:done', this.back, this );
	},

	back: function () {
		this.controller.deactivateMode( 'select' ).activateMode( 'edit' );
	},

	click: function() {
		Button.prototype.click.apply( this, arguments );
		if ( this.controller.isModeActive( 'select' ) ) {
			this.back();
		} else {
			this.controller.deactivateMode( 'edit' ).activateMode( 'select' );
		}
	},

	render: function() {
		Button.prototype.render.apply( this, arguments );
		this.$el.addClass( 'select-mode-toggle-button' );
		return this;
	},

	toggleBulkEditHandler: function() {
		var toolbar = this.controller.content.get().toolbar, children;

		children = toolbar.$( '.media-toolbar-secondary > *, .media-toolbar-primary > *' );

		// @todo The Frame should be doing all of this.
		if ( this.controller.isModeActive( 'select' ) ) {
			this.model.set( {
				size: 'large',
				text: l10n.cancel
			} );
			children.not( '.spinner, .media-button' ).hide();
			this.$el.show();
			toolbar.$el.addClass( 'media-toolbar-mode-select' );
			toolbar.$( '.delete-selected-button' ).removeClass( 'hidden' );
		} else {
			this.model.set( {
				size: '',
				text: l10n.bulkSelect
			} );
			this.controller.content.get().$el.removeClass( 'fixed' );
			toolbar.$el.css( 'width', '' );
			toolbar.$el.removeClass( 'media-toolbar-mode-select' );
			toolbar.$( '.delete-selected-button' ).addClass( 'hidden' );
			children.not( '.media-button' ).show();
			this.controller.state().get( 'selection' ).reset();
		}
	}
});

module.exports = SelectModeToggle;


/***/ }),

/***/ 8521:
/***/ ((module) => {

var View = wp.media.View,
	EditImage = wp.media.view.EditImage,
	Details;

/**
 * wp.media.view.EditImage.Details
 *
 * @memberOf wp.media.view.EditImage
 *
 * @class
 * @augments wp.media.view.EditImage
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
Details = EditImage.extend(/** @lends wp.media.view.EditImage.Details.prototype */{
	initialize: function( options ) {
		this.editor = window.imageEdit;
		this.frame = options.frame;
		this.controller = options.controller;
		View.prototype.initialize.apply( this, arguments );
	},

	back: function() {
		this.frame.content.mode( 'edit-metadata' );
	},

	save: function() {
		this.model.fetch().done( _.bind( function() {
			this.frame.content.mode( 'edit-metadata' );
		}, this ) );
	}
});

module.exports = Details;


/***/ }),

/***/ 1003:
/***/ ((module) => {

var Frame = wp.media.view.Frame,
	MediaFrame = wp.media.view.MediaFrame,

	$ = jQuery,
	EditAttachments;

/**
 * wp.media.view.MediaFrame.EditAttachments
 *
 * A frame for editing the details of a specific media item.
 *
 * Opens in a modal by default.
 *
 * Requires an attachment model to be passed in the options hash under `model`.
 *
 * @memberOf wp.media.view.MediaFrame
 *
 * @class
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
EditAttachments = MediaFrame.extend(/** @lends wp.media.view.MediaFrame.EditAttachments.prototype */{

	className: 'edit-attachment-frame',
	template:  wp.template( 'edit-attachment-frame' ),
	regions:   [ 'title', 'content' ],

	events: {
		'click .left':  'previousMediaItem',
		'click .right': 'nextMediaItem'
	},

	initialize: function() {
		Frame.prototype.initialize.apply( this, arguments );

		_.defaults( this.options, {
			modal: true,
			state: 'edit-attachment'
		});

		this.controller = this.options.controller;
		this.gridRouter = this.controller.gridRouter;
		this.library = this.options.library;

		if ( this.options.model ) {
			this.model = this.options.model;
		}

		this.bindHandlers();
		this.createStates();
		this.createModal();

		this.title.mode( 'default' );
		this.toggleNav();
	},

	bindHandlers: function() {
		// Bind default title creation.
		this.on( 'title:create:default', this.createTitle, this );

		this.on( 'content:create:edit-metadata', this.editMetadataMode, this );
		this.on( 'content:create:edit-image', this.editImageMode, this );
		this.on( 'content:render:edit-image', this.editImageModeRender, this );
		this.on( 'refresh', this.rerender, this );
		this.on( 'close', this.detach );

		this.bindModelHandlers();
		this.listenTo( this.gridRouter, 'route:search', this.close, this );
	},

	bindModelHandlers: function() {
		// Close the modal if the attachment is deleted.
		this.listenTo( this.model, 'change:status destroy', this.close, this );
	},

	createModal: function() {
		// Initialize modal container view.
		if ( this.options.modal ) {
			this.modal = new wp.media.view.Modal({
				controller:     this,
				title:          this.options.title,
				hasCloseButton: false
			});

			this.modal.on( 'open', _.bind( function () {
				$( 'body' ).on( 'keydown.media-modal', _.bind( this.keyEvent, this ) );
			}, this ) );

			// Completely destroy the modal DOM element when closing it.
			this.modal.on( 'close', _.bind( function() {
				// Remove the keydown event.
				$( 'body' ).off( 'keydown.media-modal' );
				// Move focus back to the original item in the grid if possible.
				$( 'li.attachment[data-id="' + this.model.get( 'id' ) +'"]' ).trigger( 'focus' );
				this.resetRoute();
			}, this ) );

			// Set this frame as the modal's content.
			this.modal.content( this );
			this.modal.open();
		}
	},

	/**
	 * Add the default states to the frame.
	 */
	createStates: function() {
		this.states.add([
			new wp.media.controller.EditAttachmentMetadata({
				model:   this.model,
				library: this.library
			})
		]);
	},

	/**
	 * Content region rendering callback for the `edit-metadata` mode.
	 *
	 * @param {Object} contentRegion Basic object with a `view` property, which
	 *                               should be set with the proper region view.
	 */
	editMetadataMode: function( contentRegion ) {
		contentRegion.view = new wp.media.view.Attachment.Details.TwoColumn({
			controller: this,
			model:      this.model
		});

		/**
		 * Attach a subview to display fields added via the
		 * `attachment_fields_to_edit` filter.
		 */
		contentRegion.view.views.set( '.attachment-compat', new wp.media.view.AttachmentCompat({
			controller: this,
			model:      this.model
		}) );

		// Update browser url when navigating media details, except on load.
		if ( this.model && ! this.model.get( 'skipHistory' ) ) {
			this.gridRouter.navigate( this.gridRouter.baseUrl( '?item=' + this.model.id ) );
		}
	},

	/**
	 * Render the EditImage view into the frame's content region.
	 *
	 * @param {Object} contentRegion Basic object with a `view` property, which
	 *                               should be set with the proper region view.
	 */
	editImageMode: function( contentRegion ) {
		var editImageController = new wp.media.controller.EditImage( {
			model: this.model,
			frame: this
		} );
		// Noop some methods.
		editImageController._toolbar = function() {};
		editImageController._router = function() {};
		editImageController._menu = function() {};

		contentRegion.view = new wp.media.view.EditImage.Details( {
			model: this.model,
			frame: this,
			controller: editImageController
		} );

		this.gridRouter.navigate( this.gridRouter.baseUrl( '?item=' + this.model.id + '&mode=edit' ) );

	},

	editImageModeRender: function( view ) {
		view.on( 'ready', view.loadEditor );
	},

	toggleNav: function() {
		this.$( '.left' ).prop( 'disabled', ! this.hasPrevious() );
		this.$( '.right' ).prop( 'disabled', ! this.hasNext() );
	},

	/**
	 * Rerender the view.
	 */
	rerender: function( model ) {
		this.stopListening( this.model );

		this.model = model;

		this.bindModelHandlers();

		// Only rerender the `content` region.
		if ( this.content.mode() !== 'edit-metadata' ) {
			this.content.mode( 'edit-metadata' );
		} else {
			this.content.render();
		}

		this.toggleNav();
	},

	/**
	 * Click handler to switch to the previous media item.
	 */
	previousMediaItem: function() {
		if ( ! this.hasPrevious() ) {
			return;
		}

		this.trigger( 'refresh', this.library.at( this.getCurrentIndex() - 1 ) );
		// Move focus to the Previous button. When there are no more items, to the Next button.
		this.focusNavButton( this.hasPrevious() ? '.left' : '.right' );
	},

	/**
	 * Click handler to switch to the next media item.
	 */
	nextMediaItem: function() {
		if ( ! this.hasNext() ) {
			return;
		}

		this.trigger( 'refresh', this.library.at( this.getCurrentIndex() + 1 ) );
		// Move focus to the Next button. When there are no more items, to the Previous button.
		this.focusNavButton( this.hasNext() ? '.right' : '.left' );
	},

	/**
	 * Set focus to the navigation buttons depending on the browsing direction.
	 *
	 * @since 5.3.0
	 *
	 * @param {string} which A CSS selector to target the button to focus.
	 */
	focusNavButton: function( which ) {
		$( which ).trigger( 'focus' );
	},

	getCurrentIndex: function() {
		return this.library.indexOf( this.model );
	},

	hasNext: function() {
		return ( this.getCurrentIndex() + 1 ) < this.library.length;
	},

	hasPrevious: function() {
		return ( this.getCurrentIndex() - 1 ) > -1;
	},
	/**
	 * Respond to the keyboard events: right arrow, left arrow, except when
	 * focus is in a textarea or input field.
	 */
	keyEvent: function( event ) {
		if ( ( 'INPUT' === event.target.nodeName || 'TEXTAREA' === event.target.nodeName ) && ! event.target.disabled ) {
			return;
		}

		// The right arrow key.
		if ( 39 === event.keyCode ) {
			this.nextMediaItem();
		}
		// The left arrow key.
		if ( 37 === event.keyCode ) {
			this.previousMediaItem();
		}
	},

	resetRoute: function() {
		var searchTerm = this.controller.browserView.toolbar.get( 'search' ).$el.val(),
			url = '' !== searchTerm ? '?search=' + searchTerm : '';
		this.gridRouter.navigate( this.gridRouter.baseUrl( url ), { replace: true } );
	}
});

module.exports = EditAttachments;


/***/ }),

/***/ 8359:
/***/ ((module) => {

var MediaFrame = wp.media.view.MediaFrame,
	Library = wp.media.controller.Library,

	$ = Backbone.$,
	Manage;

/**
 * wp.media.view.MediaFrame.Manage
 *
 * A generic management frame workflow.
 *
 * Used in the media grid view.
 *
 * @memberOf wp.media.view.MediaFrame
 *
 * @class
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
Manage = MediaFrame.extend(/** @lends wp.media.view.MediaFrame.Manage.prototype */{
	/**
	 * @constructs
	 */
	initialize: function() {
		_.defaults( this.options, {
			title:     '',
			modal:     false,
			selection: [],
			library:   {}, // Options hash for the query to the media library.
			multiple:  'add',
			state:     'library',
			uploader:  true,
			mode:      [ 'grid', 'edit' ]
		});

		this.$body = $( document.body );
		this.$window = $( window );
		this.$adminBar = $( '#wpadminbar' );
		// Store the Add New button for later reuse in wp.media.view.UploaderInline.
		this.$uploaderToggler = $( '.page-title-action' )
			.attr( 'aria-expanded', 'false' )
			.on( 'click', _.bind( this.addNewClickHandler, this ) );

		this.$window.on( 'scroll resize', _.debounce( _.bind( this.fixPosition, this ), 15 ) );

		// Ensure core and media grid view UI is enabled.
		this.$el.addClass('wp-core-ui');

		// Force the uploader off if the upload limit has been exceeded or
		// if the browser isn't supported.
		if ( wp.Uploader.limitExceeded || ! wp.Uploader.browser.supported ) {
			this.options.uploader = false;
		}

		// Initialize a window-wide uploader.
		if ( this.options.uploader ) {
			this.uploader = new wp.media.view.UploaderWindow({
				controller: this,
				uploader: {
					dropzone:  document.body,
					container: document.body
				}
			}).render();
			this.uploader.ready();
			$('body').append( this.uploader.el );

			this.options.uploader = false;
		}

		this.gridRouter = new wp.media.view.MediaFrame.Manage.Router();

		// Call 'initialize' directly on the parent class.
		MediaFrame.prototype.initialize.apply( this, arguments );

		// Append the frame view directly the supplied container.
		this.$el.appendTo( this.options.container );

		this.createStates();
		this.bindRegionModeHandlers();
		this.render();
		this.bindSearchHandler();

		wp.media.frames.browse = this;
	},

	bindSearchHandler: function() {
		var search = this.$( '#media-search-input' ),
			searchView = this.browserView.toolbar.get( 'search' ).$el,
			listMode = this.$( '.view-list' ),

			input  = _.throttle( function (e) {
				var val = $( e.currentTarget ).val(),
					url = '';

				if ( val ) {
					url += '?search=' + val;
					this.gridRouter.navigate( this.gridRouter.baseUrl( url ), { replace: true } );
				}
			}, 1000 );

		// Update the URL when entering search string (at most once per second).
		search.on( 'input', _.bind( input, this ) );

		this.gridRouter
			.on( 'route:search', function () {
				var href = window.location.href;
				if ( href.indexOf( 'mode=' ) > -1 ) {
					href = href.replace( /mode=[^&]+/g, 'mode=list' );
				} else {
					href += href.indexOf( '?' ) > -1 ? '&mode=list' : '?mode=list';
				}
				href = href.replace( 'search=', 's=' );
				listMode.prop( 'href', href );
			})
			.on( 'route:reset', function() {
				searchView.val( '' ).trigger( 'input' );
			});
	},

	/**
	 * Create the default states for the frame.
	 */
	createStates: function() {
		var options = this.options;

		if ( this.options.states ) {
			return;
		}

		// Add the default states.
		this.states.add([
			new Library({
				library:            wp.media.query( options.library ),
				multiple:           options.multiple,
				title:              options.title,
				content:            'browse',
				toolbar:            'select',
				contentUserSetting: false,
				filterable:         'all',
				autoSelect:         false
			})
		]);
	},

	/**
	 * Bind region mode activation events to proper handlers.
	 */
	bindRegionModeHandlers: function() {
		this.on( 'content:create:browse', this.browseContent, this );

		// Handle a frame-level event for editing an attachment.
		this.on( 'edit:attachment', this.openEditAttachmentModal, this );

		this.on( 'select:activate', this.bindKeydown, this );
		this.on( 'select:deactivate', this.unbindKeydown, this );
	},

	handleKeydown: function( e ) {
		if ( 27 === e.which ) {
			e.preventDefault();
			this.deactivateMode( 'select' ).activateMode( 'edit' );
		}
	},

	bindKeydown: function() {
		this.$body.on( 'keydown.select', _.bind( this.handleKeydown, this ) );
	},

	unbindKeydown: function() {
		this.$body.off( 'keydown.select' );
	},

	fixPosition: function() {
		var $browser, $toolbar;
		if ( ! this.isModeActive( 'select' ) ) {
			return;
		}

		$browser = this.$('.attachments-browser');
		$toolbar = $browser.find('.media-toolbar');

		// Offset doesn't appear to take top margin into account, hence +16.
		if ( ( $browser.offset().top + 16 ) < this.$window.scrollTop() + this.$adminBar.height() ) {
			$browser.addClass( 'fixed' );
			$toolbar.css('width', $browser.width() + 'px');
		} else {
			$browser.removeClass( 'fixed' );
			$toolbar.css('width', '');
		}
	},

	/**
	 * Click handler for the `Add New` button.
	 */
	addNewClickHandler: function( event ) {
		event.preventDefault();
		this.trigger( 'toggle:upload:attachment' );

		if ( this.uploader ) {
			this.uploader.refresh();
		}
	},

	/**
	 * Open the Edit Attachment modal.
	 */
	openEditAttachmentModal: function( model ) {
		// Create a new EditAttachment frame, passing along the library and the attachment model.
		if ( wp.media.frames.edit ) {
			wp.media.frames.edit.open().trigger( 'refresh', model );
		} else {
			wp.media.frames.edit = wp.media( {
				frame:       'edit-attachments',
				controller:  this,
				library:     this.state().get('library'),
				model:       model
			} );
		}
	},

	/**
	 * Create an attachments browser view within the content region.
	 *
	 * @param {Object} contentRegion Basic object with a `view` property, which
	 *                               should be set with the proper region view.
	 * @this wp.media.controller.Region
	 */
	browseContent: function( contentRegion ) {
		var state = this.state();

		// Browse our library of attachments.
		this.browserView = contentRegion.view = new wp.media.view.AttachmentsBrowser({
			controller: this,
			collection: state.get('library'),
			selection:  state.get('selection'),
			model:      state,
			sortable:   state.get('sortable'),
			search:     state.get('searchable'),
			filters:    state.get('filterable'),
			date:       state.get('date'),
			display:    state.get('displaySettings'),
			dragInfo:   state.get('dragInfo'),
			sidebar:    'errors',

			suggestedWidth:  state.get('suggestedWidth'),
			suggestedHeight: state.get('suggestedHeight'),

			AttachmentView: state.get('AttachmentView'),

			scrollElement: document
		});
		this.browserView.on( 'ready', _.bind( this.bindDeferred, this ) );

		this.errors = wp.Uploader.errors;
		this.errors.on( 'add remove reset', this.sidebarVisibility, this );
	},

	sidebarVisibility: function() {
		this.browserView.$( '.media-sidebar' ).toggle( !! this.errors.length );
	},

	bindDeferred: function() {
		if ( ! this.browserView.dfd ) {
			return;
		}
		this.browserView.dfd.done( _.bind( this.startHistory, this ) );
	},

	startHistory: function() {
		// Verify pushState support and activate.
		if ( window.history && window.history.pushState ) {
			if ( Backbone.History.started ) {
				Backbone.history.stop();
			}
			Backbone.history.start( {
				root: window._wpMediaGridSettings.adminUrl,
				pushState: true
			} );
		}
	}
});

module.exports = Manage;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/**
 * @output wp-includes/js/media-grid.js
 */

var media = wp.media;

media.controller.EditAttachmentMetadata = __webpack_require__( 659 );
media.view.MediaFrame.Manage = __webpack_require__( 8359 );
media.view.Attachment.Details.TwoColumn = __webpack_require__( 1312 );
media.view.MediaFrame.Manage.Router = __webpack_require__( 2429 );
media.view.EditImage.Details = __webpack_require__( 8521 );
media.view.MediaFrame.EditAttachments = __webpack_require__( 1003 );
media.view.SelectModeToggleButton = __webpack_require__( 682 );
media.view.DeleteSelectedButton = __webpack_require__( 6606 );
media.view.DeleteSelectedPermanentlyButton = __webpack_require__( 5806 );

/******/ })()
;