/* global _wpMediaViewsL10n, MediaElementPlayer, _wpMediaGridSettings */
(function($, _, Backbone, wp) {
	var media = wp.media, l10n;

	// Link any localized strings.
	if ( media.view.l10n ) {
		l10n = media.view.l10n;
	} else {
		l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;
		delete l10n.settings;
	}

	/**
	 * A state for editing an attachment's metadata.
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.EditAttachmentMetadata = media.controller.State.extend({
		defaults: {
			id:      'edit-attachment',
			title:   l10n.attachmentDetails,
			// Region mode defaults.
			menu:    false,
			content: 'edit-metadata',

			url:     ''
		},

		_ready: function() {},

		/**
		 * Override media.controller.State._postActivate, since this state doesn't
		 * include the regions expected there.
		 */
		_postActivate: function() {
			this.frame.on( 'title:render:default', this._renderTitle, this );

			this._title();
			this._content();
		},

		/**
		 * @access private
		 */
		_title: function() {
			this.frame.title.render( this.get('titleMode') || 'default' );
		},
		/**
		 * @access private
		 */
		_renderTitle: function( view ) {
			view.$el.text( this.get('title') || '' );
		},

		_content: function() {
			var mode = this.get( 'content' );
			if ( mode ) {
				this.frame.content.render( mode );
			}
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
				library:   {},
				multiple:  'add',
				state:     'library',
				uploader:  true,
				mode:      [ 'grid', 'edit' ]
			});

			$(document).on( 'click', '.add-new-h2', _.bind( this.addNewClickHandler, this ) );
			$(document).on( 'screen:options:open', _.bind( this.screenOptionsOpen, this ) );
			$(document).on( 'screen:options:close', _.bind( this.screenOptionsClose, this ) );

			// Ensure core and media grid view UI is enabled.
			this.$el.addClass('wp-core-ui media-grid-view');

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
						dropzone:  $('body'),
						container: $('body')
					}
				}).render();
				this.uploader.ready();
				$('body').append( this.uploader.el );

				this.options.uploader = false;
			}

			/**
			 * call 'initialize' directly on the parent class
			 */
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			// Since we're not using the default modal built into
			// a media frame, append our $element to the supplied container.
			this.$el.appendTo( this.options.container );

			this.createSelection();
			this.createStates();
			this.bindHandlers();
			this.render();

			// Update the URL when entering search string (at most once per second)
			$( '#media-search-input' ).on( 'input', _.debounce( function(e) {
				var val = $( e.currentTarget ).val(), url = '';
				if ( val ) {
					url += '?search=' + val;
				}
				self.gridRouter.navigate( self.gridRouter.baseUrl( url ) );
			}, 1000 ) );

			_.delay( _.bind( this.createRouter, this ), 1000 );
		},

		screenOptionsOpen: function() {
			this.$el.addClass( 'media-grid-view-options' );
		},

		screenOptionsClose: function() {
			this.$el.removeClass( 'media-grid-view-options' );
		},

		createRouter: function() {
			this.gridRouter = new media.view.MediaFrame.Manage.Router();

			// Verify pushState support and activate
			if ( window.history && window.history.pushState ) {
				Backbone.history.start({
					root: _wpMediaGridSettings.adminUrl,
					pushState: true
				});
			}
		},

		createSelection: function() {
			var selection = this.options.selection;

			if ( ! (selection instanceof media.model.Selection) ) {
				this.options.selection = new media.model.Selection( selection, {
					multiple: this.options.multiple
				});
			}

			this._selection = {
				attachments: new media.model.Attachments(),
				difference: []
			};
		},

		createStates: function() {
			var options = this.options,
				libraryState;

			if ( this.options.states ) {
				return;
			}

			libraryState = new media.controller.Library({
				library:    media.query( options.library ),
				multiple:   options.multiple,
				title:      options.title,
				priority:   20,
				toolbar:    false,
				router:     false,
				content:    'browse',
				filterable: 'mime-types'
			});

			// Add the default states.
			this.states.add([
				libraryState
			]);
		},

		bindHandlers: function() {
			this.on( 'content:create:browse', this.browseContent, this );
			this.on( 'content:render:edit-image', this.editImageContent, this );

			// Handle a frame-level event for editing an attachment.
			this.on( 'edit:attachment', this.editAttachment, this );
		},

		addNewClickHandler: function( event ) {
			event.preventDefault();
			this.trigger( 'toggle:upload:attachment' );
		},

		/**
		 * Open the Edit Attachment modal.
		 */
		editAttachment: function( model ) {
			// Create a new EditAttachment frame, passing along the library and the attachment model.
			wp.media( {
				frame:       'edit-attachments',
				gridRouter:  this.gridRouter,
				library:     this.state().get('library'),
				model:       model
			} );
		},

		/**
		 * Content
		 *
		 * @param {Object} content
		 * @this wp.media.controller.Region
		 */
		browseContent: function( content ) {
			var state = this.state();

			// Browse our library of attachments.
			content.view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				filters:    state.get('filterable'),
				display:    state.get('displaySettings'),
				dragInfo:   state.get('dragInfo'),
				bulkEdit:   true,
				sidebar:    false,

				suggestedWidth:  state.get('suggestedWidth'),
				suggestedHeight: state.get('suggestedHeight'),

				AttachmentView: state.get('AttachmentView')
			});
		},

		editImageContent: function() {
			var image = this.state().get('image'),
				view = new media.view.EditImage( { model: image, controller: this } ).render();

			this.content.set( view );

			// after creating the wrapper view, load the actual editor via an ajax call
			view.loadEditor();

		}
	});

	media.view.Attachment.Details.TwoColumn = media.view.Attachment.Details.extend({
		template: wp.template( 'attachment-details-two-column' ),

		events: {
			'change [data-setting]':          'updateSetting',
			'change [data-setting] input':    'updateSetting',
			'change [data-setting] select':   'updateSetting',
			'change [data-setting] textarea': 'updateSetting',
			'click .delete-attachment':       'deleteAttachment',
			'click .trash-attachment':        'trashAttachment',
			'click .edit-attachment':         'editAttachment',
			'click .refresh-attachment':      'refreshAttachment',
			'click .edit-image':              'handleEditImageClick'
		},

		initialize: function() {
			if ( ! this.model ) {
				return;
			}

			this.$el.attr('aria-label', this.model.get( 'title' ) ).attr( 'aria-checked', false );

			this.model.on( 'change:title',   this._syncTitle, this );
			this.model.on( 'change:caption', this._syncCaption, this );
			this.model.on( 'change:percent', this.progress, this );
			this.model.on( 'change:album',   this._syncAlbum, this );
			this.model.on( 'change:artist',  this._syncArtist, this );

			// Update the selection.
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );
			this.model.on( 'sync', this.afterDelete, this );
		},

		preDestroy: function( event ) {
			event.preventDefault();

			this.lastIndex = this.controller.getCurrentIndex();
			this.hasNext = this.controller.hasNext();
		},

		trashAttachment: function( event ) {
			this.preDestroy( event );
			media.view.Attachment.Details.prototype.trashAttachment.apply( this, arguments );
		},

		deleteAttachment: function( event ) {
			this.preDestroy( event );
			media.view.Attachment.Details.prototype.deleteAttachment.apply( this, arguments );
		},

		handleEditImageClick: function() {
			this.controller.setState( 'edit-image' );
		},

		afterDelete: function( model ) {
			if ( ! model.destroyed ) {
				return;
			}

			var frame = this.controller, index = this.lastIndex;

			if ( ! frame.library.length ) {
				media.frame.modal.close();
				return;
			}

			if ( this.hasNext ) {
				index -= 1;
			}
			frame.model = frame.library.at( index );
			frame.nextMediaItem();
		},

		render: function() {
			media.view.Attachment.Details.prototype.render.apply( this, arguments );

			media.mixin.removeAllPlayers();
			$( 'audio, video', this.$el ).each( function (i, elem) {
				var el = media.view.MediaDetails.prepareSrc( elem );
				new MediaElementPlayer( el, media.mixin.mejsSettings );
			} );
		}
	});

	/**
	 * A router for handling the browser history and application state
	 */
	media.view.MediaFrame.Manage.Router = Backbone.Router.extend({
		routes: {
			'upload.php?item=:slug':    'showitem',
			'upload.php?search=:query': 'search',
			':default':                 'defaultRoute'
		},

		// Map routes against the page URL
		baseUrl: function( url ) {
			return 'upload.php' + url;
		},

		// Respond to the search route by filling the search field and trigggering the input event
		search: function( query ) {
			// Ensure modal closed, see back button
			this.closeModal();
			$( '#media-search-input' ).val( query ).trigger( 'input' );
		},

		// Show the modal with a specific item
		showitem: function( query ) {
			var library = media.frame.state().get('library');

			// Remove existing modal if present
			this.closeModal();
			// Trigger the media frame to open the correct item
			media.frame.trigger( 'edit:attachment', library.findWhere( { id: parseInt( query, 10 ) } ) );
		},

		// Close the modal if set up
		closeModal: function() {
			if ( media.frame.modal ) {
				media.frame.modal.close();
			}
		},

		// Default route: make sure the modal and search are reset
		defaultRoute: function() {
			this.closeModal();
			$( '#media-search-input' ).val( '' ).trigger( 'input' );
		}
	});

	/**
	 * A frame for editing the details of a specific media item.
	 *
	 * Opens in a modal by default.
	 *
	 * Requires an attachment model to be passed in the options hash under `model`.
	 */
	media.view.MediaFrame.EditAttachments = media.view.MediaFrame.extend({

		className: 'edit-attachment-frame',
		template: media.template( 'edit-attachment-frame' ),
		regions:   [ 'title', 'content' ],

		events: {
			'click':                    'collapse',
			'click .delete-media-item': 'deleteMediaItem',
			'click .left':              'previousMediaItem',
			'click .right':             'nextMediaItem'
		},

		initialize: function() {
			var self = this;
			media.view.Frame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				modal: true,
				state: 'edit-attachment'
			});

			this.gridRouter = this.options.gridRouter;
			this.library = this.options.library;
			if ( this.options.model ) {
				this.model = this.options.model;
			} else {
				this.model = this.library.at( 0 );
			}

			this.createStates();

			this.on( 'content:render:edit-metadata', this.editMetadataContent, this );
			this.on( 'content:render:edit-image', this.editImageContentUgh, this );
			this.on( 'close', this.detach );

			// Bind default title creation.
			this.on( 'title:create:default', this.createTitle, this );
			this.title.mode('default');

			this.options.hasPrevious = this.hasPrevious();
			this.options.hasNext = this.hasNext();

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

					self.resetRoute();
				} );

				this.modal.content( this );
				this.modal.open();
			}
		},

		/**
		 * Add the default states to the frame.
		 */
		createStates: function() {
			var editImageState = new media.controller.EditImage( { model: this.model } );
			// Noop some methods.
			editImageState._toolbar = function() {};
			editImageState._router = function() {};
			editImageState._menu = function() {};
			this.states.add([
				new media.controller.EditAttachmentMetadata( { model: this.model } ),
				editImageState

			]);
		},

		/**
		 * Content region rendering callback for the `edit-metadata` mode.
		 */
		editMetadataContent: function() {
			var view = new media.view.Attachment.Details.TwoColumn({
				controller: this,
				model:      this.model
			});
			this.content.set( view );
			// Update browser url when navigating media details
			if ( this.model ) {
				this.gridRouter.navigate( this.gridRouter.baseUrl( '?item=' + this.model.id ) );
			} else {
				this.resetRoute();
			}
		},

		/**
		 * For some reason the view doesn't exist in the DOM yet, don't have the
		 * patience to track this down right now.
		 */
		editImageContentUgh: function() {
			_.defer( _.bind( this.editImageContent, this ) );
		},

		/**
		 * Render the EditImage view into the frame's content region.
		 */
		editImageContent: function() {
			var view = new media.view.EditImage( { model: this.model, controller: this } ).render();

			this.content.set( view );

			// after creating the wrapper view, load the actual editor via an ajax call
			view.loadEditor();
		},

		resetContent: function() {
			this.modal.close();
			wp.media( {
				frame:       'edit-attachments',
				gridRouter:  this.gridRouter,
				library:     this.library,
				model:       this.model
			} );
		},

		/**
		 * Click handler to switch to the previous media item.
		 */
		previousMediaItem: function() {
			if ( ! this.hasPrevious() ) {
				return;
			}
			this.model = this.library.at( this.getCurrentIndex() - 1 );
			this.resetContent();
		},

		/**
		 * Click handler to switch to the next media item.
		 */
		nextMediaItem: function() {
			if ( ! this.hasNext() ) {
				return;
			}
			this.model = this.library.at( this.getCurrentIndex() + 1 );
			this.resetContent();
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
		 * Respond to the keyboard events: right arrow, left arrow, escape.
		 */
		keyEvent: function( event ) {
			var $target = $( event.target );
			// Pressing the escape key routes back to main url
			if ( event.keyCode === 27 ) {
				this.resetRoute();
				return event;
			}
			//Don't go left/right if we are in a textarea or input field
			if ( $target.is( 'input' ) || $target.is( 'textarea' ) ) {
				return event;
			}
			// The right arrow key
			if ( event.keyCode === 39 ) {
				this.nextMediaItem();
			}
			// The left arrow key
			if ( event.keyCode === 37 ) {
				this.previousMediaItem();
			}
		},

		resetRoute: function() {
			this.gridRouter.navigate( this.gridRouter.baseUrl( '' ) );
		}
	});

	media.view.BulkSelectionToggleButton = media.view.Button.extend({
		initialize: function() {
			media.view.Button.prototype.initialize.apply( this, arguments );
			this.listenTo( this.controller, 'bulk-edit:activate bulk-edit:deactivate', _.bind( this.toggleBulkEditHandler, this ) );
		},

		click: function() {
			var bulkEditActive = this.controller.activeModes.where( { id: 'bulk-edit' } ).length;
			media.view.Button.prototype.click.apply( this, arguments );

			if ( bulkEditActive ) {
				this.controller.deactivateMode( 'bulk-edit' ).activateMode( 'edit' );
			} else {
				this.controller.deactivateMode( 'edit' ).activateMode( 'bulk-edit' );
			}
		},

		toggleBulkEditHandler: function() {
			var bulkEditActive = this.controller.activeModes.where( { id: 'bulk-edit' } ).length;
			if ( bulkEditActive ) {
				this.$el.addClass( 'button-primary' );
			} else {
				this.$el.removeClass( 'button-primary' );
				this.controller.state().get('selection').reset();
			}
		}
	});

	media.view.BulkDeleteButton = media.view.Button.extend({
		initialize: function() {
			media.view.Button.prototype.initialize.apply( this, arguments );
			this.$el.hide();
			this.listenTo( this.controller, 'bulk-edit:activate bulk-edit:deactivate', _.bind( this.visibility, this ) );
		},

		click: function() {
			media.view.Button.prototype.click.apply( this, arguments );
			while (this.controller.state().get('selection').length > 0) {
				this.controller.state().get('selection').at(0).destroy();
			}
		},

		visibility: function() {
			var bulkEditActive = this.controller.activeModes.where( { id: 'bulk-edit' } ).length;
			if ( bulkEditActive ) {
				this.$el.show();
			} else {
				this.$el.hide();
			}
		}
	});

}(jQuery, _, Backbone, wp));
