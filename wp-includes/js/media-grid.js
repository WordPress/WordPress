/* global _wpMediaViewsL10n, MediaElementPlayer, _wpMediaGridSettings */
(function($, _, Backbone, wp) {
	// Local reference to the WordPress media namespace.
	var media = wp.media, l10n;

	// Link localized strings and settings.
	if ( media.view.l10n ) {
		l10n = media.view.l10n;
	} else {
		l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;
		delete l10n.settings;
	}

	/**
	 * wp.media.controller.EditAttachmentMetadata
	 *
	 * A state for editing an attachment's metadata.
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.EditAttachmentMetadata = media.controller.State.extend({
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

	/**
	 * wp.media.view.MediaFrame.Manage
	 *
	 * A generic management frame workflow.
	 *
	 * Used in the media grid view.
	 *
	 * @constructor
	 * @augments wp.media.view.MediaFrame
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame.Manage = media.view.MediaFrame.extend({
		/**
		 * @global wp.Uploader
		 */
		initialize: function() {
			var self = this;
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
			this.$window.on( 'scroll resize', _.debounce( _.bind( this.fixPosition, this ), 15 ) );
			$( document ).on( 'click', '.add-new-h2', _.bind( this.addNewClickHandler, this ) );

			// Ensure core and media grid view UI is enabled.
			this.$el.addClass('wp-core-ui');

			// Force the uploader off if the upload limit has been exceeded or
			// if the browser isn't supported.
			if ( wp.Uploader.limitExceeded || ! wp.Uploader.browser.supported ) {
				this.options.uploader = false;
			}

			// Initialize a window-wide uploader.
			if ( this.options.uploader ) {
				this.uploader = new media.view.UploaderWindow({
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

			this.gridRouter = new media.view.MediaFrame.Manage.Router();

			// Call 'initialize' directly on the parent class.
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			// Append the frame view directly the supplied container.
			this.$el.appendTo( this.options.container );

			this.createStates();
			this.bindRegionModeHandlers();
			this.render();

			// Update the URL when entering search string (at most once per second)
			$( '#media-search-input' ).on( 'input', _.debounce( function(e) {
				var val = $( e.currentTarget ).val(), url = '';
				if ( val ) {
					url += '?search=' + val;
				}
				self.gridRouter.navigate( self.gridRouter.baseUrl( url ) );
			}, 1000 ) );
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
				new media.controller.Library({
					library:            media.query( options.library ),
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

			// Offset doesn't appear to take top margin into account, hence +16
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
		},

		/**
		 * Open the Edit Attachment modal.
		 */
		openEditAttachmentModal: function( model ) {
			// Create a new EditAttachment frame, passing along the library and the attachment model.
			wp.media( {
				frame:       'edit-attachments',
				controller:  this,
				library:     this.state().get('library'),
				model:       model
			} );
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
			this.browserView = contentRegion.view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				filters:    state.get('filterable'),
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
			// Verify pushState support and activate
			if ( window.history && window.history.pushState ) {
				Backbone.history.start( {
					root: _wpMediaGridSettings.adminUrl,
					pushState: true
				} );
			}
		}
	});

	/**
	 * A similar view to media.view.Attachment.Details
	 * for use in the Edit Attachment modal.
	 *
	 * @constructor
	 * @augments wp.media.view.Attachment.Details
	 * @augments wp.media.view.Attachment
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.Attachment.Details.TwoColumn = media.view.Attachment.Details.extend({
		template: media.template( 'attachment-details-two-column' ),

		editAttachment: function( event ) {
			event.preventDefault();
			this.controller.content.mode( 'edit-image' );
		},

		/**
		 * Noop this from parent class, doesn't apply here.
		 */
		toggleSelectionHandler: function() {},

		render: function() {
			media.view.Attachment.Details.prototype.render.apply( this, arguments );

			media.mixin.removeAllPlayers();
			this.$( 'audio, video' ).each( function (i, elem) {
				var el = media.view.MediaDetails.prepareSrc( elem );
				setTimeout( function() {
					new MediaElementPlayer( el, media.mixin.mejsSettings );
				}, 0 );
			} );
		}
	});

	/**
	 * A router for handling the browser history and application state.
	 *
	 * @constructor
	 * @augments Backbone.Router
	 */
	media.view.MediaFrame.Manage.Router = Backbone.Router.extend({
		routes: {
			'upload.php?item=:slug':    'showItem',
			'upload.php?search=:query': 'search'
		},

		// Map routes against the page URL
		baseUrl: function( url ) {
			return 'upload.php' + url;
		},

		// Respond to the search route by filling the search field and trigggering the input event
		search: function( query ) {
			$( '#media-search-input' ).val( query ).trigger( 'input' );
		},

		// Show the modal with a specific item
		showItem: function( query ) {
			var library = media.frame.state().get('library'), item;

			// Trigger the media frame to open the correct item
			item = library.findWhere( { id: parseInt( query, 10 ) } );
			if ( item ) {
				media.frame.trigger( 'edit:attachment', item );
			} else {
				item = media.attachment( query );
				media.frame.listenTo( item, 'change', function( model ) {
					media.frame.stopListening( item );
					media.frame.trigger( 'edit:attachment', model );
				} );
				item.fetch();
			}
		}
	});

	media.view.EditImage.Details = media.view.EditImage.extend({
		initialize: function( options ) {
			this.editor = window.imageEdit;
			this.frame = options.frame;
			this.controller = options.controller;
			media.View.prototype.initialize.apply( this, arguments );
		},

		back: function() {
			this.frame.content.mode( 'edit-metadata' );
		},

		save: function() {
			var self = this;

			this.model.fetch().done( function() {
				self.frame.content.mode( 'edit-metadata' );
			});
		}
	});

	/**
	 * A frame for editing the details of a specific media item.
	 *
	 * Opens in a modal by default.
	 *
	 * Requires an attachment model to be passed in the options hash under `model`.
	 *
	 * @constructor
	 * @augments wp.media.view.Frame
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 * @mixes wp.media.controller.StateMachine
	 */
	media.view.MediaFrame.EditAttachments = media.view.MediaFrame.extend({

		className: 'edit-attachment-frame',
		template: media.template( 'edit-attachment-frame' ),
		regions:   [ 'title', 'content' ],

		events: {
			'click .left':  'previousMediaItem',
			'click .right': 'nextMediaItem'
		},

		initialize: function() {
			media.view.Frame.prototype.initialize.apply( this, arguments );

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

			// Close the modal if the attachment is deleted.
			this.listenTo( this.model, 'change:status destroy', this.close, this );

			this.on( 'content:create:edit-metadata', this.editMetadataMode, this );
			this.on( 'content:create:edit-image', this.editImageMode, this );
			this.on( 'content:render:edit-image', this.editImageModeRender, this );
			this.on( 'close', this.detach );
		},

		createModal: function() {
			var self = this;

			// Initialize modal container view.
			if ( this.options.modal ) {
				this.modal = new media.view.Modal({
					controller: this,
					title:      this.options.title
				});

				this.modal.on( 'open', function () {
					$( 'body' ).on( 'keydown.media-modal', _.bind( self.keyEvent, self ) );
				} );

				// Completely destroy the modal DOM element when closing it.
				this.modal.on( 'close', function() {
					self.modal.remove();
					$( 'body' ).off( 'keydown.media-modal' ); /* remove the keydown event */
					// Restore the original focus item if possible
					$( 'li.attachment[data-id="' + self.model.get( 'id' ) +'"]' ).focus();
					self.resetRoute();
				} );

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
				new media.controller.EditAttachmentMetadata( { model: this.model } )
			]);
		},

		/**
		 * Content region rendering callback for the `edit-metadata` mode.
		 *
		 * @param {Object} contentRegion Basic object with a `view` property, which
		 *                               should be set with the proper region view.
		 */
		editMetadataMode: function( contentRegion ) {
			contentRegion.view = new media.view.Attachment.Details.TwoColumn({
				controller: this,
				model:      this.model
			});

			/**
			 * Attach a subview to display fields added via the
			 * `attachment_fields_to_edit` filter.
			 */
			contentRegion.view.views.set( '.attachment-compat', new media.view.AttachmentCompat({
				controller: this,
				model:      this.model
			}) );

			// Update browser url when navigating media details
			if ( this.model ) {
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
			var editImageController = new media.controller.EditImage( {
				model: this.model,
				frame: this
			} );
			// Noop some methods.
			editImageController._toolbar = function() {};
			editImageController._router = function() {};
			editImageController._menu = function() {};

			contentRegion.view = new media.view.EditImage.Details( {
				model: this.model,
				frame: this,
				controller: editImageController
			} );
		},

		editImageModeRender: function( view ) {
			view.on( 'ready', view.loadEditor );
		},

		toggleNav: function() {
			this.$('.left').toggleClass( 'disabled', ! this.hasPrevious() );
			this.$('.right').toggleClass( 'disabled', ! this.hasNext() );
		},

		/**
		 * Rerender the view.
		 */
		rerender: function() {
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
				this.$( '.left' ).blur();
				return;
			}
			this.model = this.library.at( this.getCurrentIndex() - 1 );
			this.rerender();
			this.$( '.left' ).focus();
		},

		/**
		 * Click handler to switch to the next media item.
		 */
		nextMediaItem: function() {
			if ( ! this.hasNext() ) {
				this.$( '.right' ).blur();
				return;
			}
			this.model = this.library.at( this.getCurrentIndex() + 1 );
			this.rerender();
			this.$( '.right' ).focus();
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
			if ( ( 'INPUT' === event.target.nodeName || 'TEXTAREA' === event.target.nodeName ) && ! ( event.target.readOnly || event.target.disabled ) ) {
				return;
			}

			// The right arrow key
			if ( 39 === event.keyCode ) {
				this.nextMediaItem();
			}
			// The left arrow key
			if ( 37 === event.keyCode ) {
				this.previousMediaItem();
			}
		},

		resetRoute: function() {
			this.gridRouter.navigate( this.gridRouter.baseUrl( '' ) );
		}
	});

	media.view.SelectModeToggleButton = media.view.Button.extend({
		initialize: function() {
			media.view.Button.prototype.initialize.apply( this, arguments );
			this.listenTo( this.controller, 'select:activate select:deactivate', this.toggleBulkEditHandler );
			this.listenTo( this.controller, 'selection:action:done', this.back );
		},

		back: function () {
			this.controller.deactivateMode( 'select' ).activateMode( 'edit' );
		},

		click: function() {
			media.view.Button.prototype.click.apply( this, arguments );
			if ( this.controller.isModeActive( 'select' ) ) {
				this.back();
			} else {
				this.controller.deactivateMode( 'edit' ).activateMode( 'select' );
			}
		},

		render: function() {
			media.view.Button.prototype.render.apply( this, arguments );
			this.$el.addClass( 'select-mode-toggle-button' );
			return this;
		},

		toggleBulkEditHandler: function() {
			var toolbar = this.controller.content.get().toolbar, children;

			children = toolbar.$( '.media-toolbar-secondary > *, .media-toolbar-primary > *');

			// TODO: the Frame should be doing all of this.
			if ( this.controller.isModeActive( 'select' ) ) {
				this.model.set( 'text', l10n.cancelSelection );
				children.not( '.media-button' ).hide();
				this.$el.show();
				toolbar.$( '.delete-selected-button' ).removeClass( 'hidden' );
			} else {
				this.model.set( 'text', l10n.bulkSelect );
				this.controller.content.get().$el.removeClass( 'fixed' );
				toolbar.$el.css( 'width', '' );
				toolbar.$( '.delete-selected-button' ).addClass( 'hidden' );
				children.not( '.spinner, .media-button' ).show();
				this.controller.state().get( 'selection' ).reset();
			}
		}
	});

	/**
	 * A button that handles bulk Delete/Trash logic
	 *
	 * @constructor
	 * @augments wp.media.view.Button
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.DeleteSelectedButton = media.view.Button.extend({
		initialize: function() {
			media.view.Button.prototype.initialize.apply( this, arguments );
			if ( this.options.filters ) {
				this.listenTo( this.options.filters.model, 'change', this.filterChange );
			}
			this.listenTo( this.controller, 'selection:toggle', this.toggleDisabled );
		},

		filterChange: function( model ) {
			if ( 'trash' === model.get( 'status' ) ) {
				this.model.set( 'text', l10n.untrashSelected );
			} else if ( media.view.settings.mediaTrash ) {
				this.model.set( 'text', l10n.trashSelected );
			} else {
				this.model.set( 'text', l10n.deleteSelected );
			}
		},

		toggleDisabled: function() {
			this.model.set( 'disabled', ! this.controller.state().get( 'selection' ).length );
		},

		render: function() {
			media.view.Button.prototype.render.apply( this, arguments );
			if ( this.controller.isModeActive( 'select' ) ) {
				this.$el.addClass( 'delete-selected-button' );
			} else {
				this.$el.addClass( 'delete-selected-button hidden' );
			}
			this.toggleDisabled();
			return this;
		}
	});

	/**
	 * When MEDIA_TRASH is true, a button that handles bulk Delete Permanently logic
	 *
	 * @constructor
	 * @augments wp.media.view.DeleteSelectedButton
	 * @augments wp.media.view.Button
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.DeleteSelectedPermanentlyButton = media.view.DeleteSelectedButton.extend({
		initialize: function() {
			media.view.DeleteSelectedButton.prototype.initialize.apply( this, arguments );
			this.listenTo( this.controller, 'select:activate', this.selectActivate );
			this.listenTo( this.controller, 'select:deactivate', this.selectDeactivate );
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
			media.view.Button.prototype.render.apply( this, arguments );
			this.selectActivate();
			return this;
		}
	});

}(jQuery, _, Backbone, wp));
