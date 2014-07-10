/* global _wpMediaViewsL10n, setUserSetting, deleteUserSetting, MediaElementPlayer, mediaGridSettings*/
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
	 * A state for editing (cropping, etc.) an image.
	 *
	 * @constructor
	 * @augments wp.media.controller.State
	 * @augments Backbone.Model
	 */
	media.controller.EditImageNoFrame = media.controller.State.extend({
		defaults: {
			id:      'edit-attachment',
			title:   l10n.editImage,
			// Region mode defaults.
			menu:    false,
			router:  'edit-metadata',
			content: 'edit-metadata',

			url:     ''
		},

		_ready: function() {},

		/**
		 * Override media.controller.State._postActivate, since this state doesn't
		 * include the regions expected there.
		 */
		_postActivate: function() {
			this._content();
			this._router();
		},

		/**
		 * @access private
		 */
		_router: function() {
			var router = this.frame.router,
				mode = this.get('router'),
				view;

			this.frame.$el.toggleClass( 'hide-router', ! mode );
			if ( ! mode ) {
				return;
			}

			this.frame.router.render( mode );

			view = router.get();
			if ( view && view.select ) {
				view.select( this.frame.content.mode() );
			}
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
				title:     l10n.mediaLibraryTitle,
				modal:     false,
				selection: [],
				library:   {},
				multiple:  'add',
				state:     'library',
				uploader:  true,
				mode:      [ 'grid', 'edit' ]
			});

			$(document).on( 'click', '.add-new-h2', _.bind( this.addNewClickHandler, this ) );
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

			// Set up the Backbone router after a brief delay
			_.delay( function(){
				wp.media.mediarouter = new media.view.Frame.Router( self );
				// Verify pushState support and activate
				if ( window.history && window.history.pushState ) {
					Backbone.history.start({
						root: mediaGridSettings.adminUrl,
						pushState: true
					});
				}
			}, 250);

			// Update the URL when entering search string (at most once per second)
			$( '#media-search-input' ).on( 'input', _.debounce( function() {
				var $val = $( this ).val();
				wp.media.mediarouter.navigate( wp.media.mediarouter.baseUrl( ( '' === $val ) ? '' : ( '?search=' + $val ) ) );
			}, 1000 ) );
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

			libraryState._renderTitle = function( view ) {
				var text = this.get('title') || '';
				view.$el.addClass( 'wrap' );
				text += '<a class="add-new-h2">Add New</a>';
				view.$el.html( text );
			};
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
			this.on( 'edit:attachment:next', this.editNextAttachment, this );
			this.on( 'edit:attachment:previous', this.editPreviousAttachment, this );
		},

		addNewClickHandler: function() {
			this.trigger( 'show:upload:attachment' );
		},

		editPreviousAttachment: function( currentModel ) {
			var library = this.state().get('library'),
				currentModelIndex = library.indexOf( currentModel );
			this.trigger( 'edit:attachment', library.at( currentModelIndex - 1 ) );
		},

		editNextAttachment: function( currentModel ) {
			var library = this.state().get('library'),
				currentModelIndex = library.indexOf( currentModel );
			this.trigger( 'edit:attachment', library.at( currentModelIndex + 1 ) );
		},

		/**
		 * Open the Edit Attachment modal.
		 */
		editAttachment: function( model ) {
			var self    = this,
				library = this.state().get('library');

			// Create a new EditAttachment frame, passing along the library and the attachment model.
			this.editAttachmentFrame = new media.view.Frame.EditAttachments({
				library:        library,
				model:          model
			});

			// Listen to events on the edit attachment frame for triggering pagination callback handlers.
			this.listenTo( this.editAttachmentFrame, 'edit:attachment:next', this.editNextAttachment );
			this.listenTo( this.editAttachmentFrame, 'edit:attachment:previous', this.editPreviousAttachment );
			// Listen to keyboard events on the modal
			$( 'body' ).on( 'keydown.media-modal', function( e ) {
				self.editAttachmentFrame.keyEvent( e );
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

		initialize: function() {
			this.$el.attr('aria-label', this.model.attributes.title).attr('aria-checked', false);
			this.model.on( 'change:sizes change:uploading', this.render, this );
			this.model.on( 'change:title', this._syncTitle, this );
			this.model.on( 'change:caption', this._syncCaption, this );
			this.model.on( 'change:percent', this.progress, this );

			// Update the selection.
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );
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
	media.view.Frame.Router = Backbone.Router.extend({

		mediaFrame: '',

		initialize: function( mediaFrame ){
			this.mediaFrame = mediaFrame;
		},

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
			var library = this.mediaFrame.state().get('library');

			// Remove existing modal if present
			this.closeModal();
			// Trigger the media frame to open the correct item
			this.mediaFrame.trigger( 'edit:attachment', library.findWhere( { id: parseInt( query, 10 ) } ) );
		},

		// Close the modal if set up
		closeModal: function() {
			if ( 'undefined' !== typeof this.mediaFrame.editAttachmentFrame ) {
				this.mediaFrame.editAttachmentFrame.modal.close();
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
	media.view.Frame.EditAttachments = media.view.Frame.extend({

		className: 'edit-attachment-frame',
		template: media.template( 'edit-attachment-frame' ),
		regions:   [ 'router', 'content' ],

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

			this.library = this.options.library;
			if ( this.options.model ) {
				this.model = this.options.model;
			} else {
				this.model = this.library.at( 0 );
			}

			this.createStates();

			this.on( 'content:render:edit-metadata', this.editMetadataContent, this );
			this.on( 'content:render:edit-image', this.editImageContentUgh, this );

			// Only need a tab to Edit Image for images.
			if ( 'undefined' !== typeof this.model && this.model.get( 'type' ) === 'image' ) {
				this.on( 'router:create', this.createRouter, this );
				this.on( 'router:render', this.browseRouter, this );
			}

			this.options.hasPrevious = ( this.options.library.indexOf( this.options.model ) > 0 ) ? true : false;
			this.options.hasNext = ( this.options.library.indexOf( this.options.model ) < this.options.library.length - 1 ) ? true : false;

			// Initialize modal container view.
			if ( this.options.modal ) {
				this.modal = new media.view.Modal({
					controller: this,
					title:      this.options.title
				});

				// Completely destroy the modal DOM element when closing it.
				this.modal.close = function() {
					self.modal.remove();
					$( 'body' ).off( 'keydown.media-modal' ); /* remove the keydown event */
				};

				this.modal.content( this );
				this.modal.open();
			}
		},

		/**
		 * Add the default states to the frame.
		 */
		createStates: function() {
			this.states.add([
				new media.controller.EditImageNoFrame( { model: this.model } )
			]);
		},

		/**
		 * @returns {wp.media.view.MediaFrame} Returns itself to allow chaining
		 */
		render: function() {
			// Activate the default state if no active state exists.
			if ( ! this.state() && this.options.state ) {
				this.setState( this.options.state );
			}
			/**
			 * call 'render' directly on the parent class
			 */
			return media.view.Frame.prototype.render.apply( this, arguments );
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
			wp.media.mediarouter.navigate( wp.media.mediarouter.baseUrl( '?item=' + this.model.id ) );
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

		/**
		 * Create the router view.
		 *
		 * @param {Object} router
		 * @this wp.media.controller.Region
		 */
		createRouter: function( router ) {
			router.view = new media.view.Router({
				controller: this
			});
		},

		/**
		 * Router rendering callback.
		 *
		 * @param  media.view.Router view Instantiated in this.createRouter()
		 */
		browseRouter: function( view ) {
			view.set({
				'edit-metadata': {
					text:     'Edit Metadata',
					priority: 20
				},
				'edit-image': {
					text:     'Edit Image',
					priority: 40
				}
			});
		},

		/**
		 * Click handler to switch to the previous media item.
		 */
		previousMediaItem: function() {
			if ( ! this.options.hasPrevious )
				return;
			this.modal.close();
			this.trigger( 'edit:attachment:previous', this.model );
		},

		/**
		 * Click handler to switch to the next media item.
		 */
		nextMediaItem: function() {
			if ( ! this.options.hasNext )
				return;
			this.modal.close();
			this.trigger( 'edit:attachment:next', this.model );
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
				if ( ! this.hasNext ) { return; }
				_.debounce( this.nextMediaItem(), 250 );
			}
			// The left arrow key
			if ( event.keyCode === 37 ) {
				if ( ! this.hasPrevious ) { return; }
				_.debounce( this.previousMediaItem(), 250 );
			}
		},

		resetRoute: function() {
			wp.media.mediarouter.navigate( wp.media.mediarouter.baseUrl( '' ) );
			return;
		}
	});

	media.view.GridFieldOptions = media.View.extend({
		className: 'media-grid-field-options',
		template: media.template( 'media-grid-field-options' ),

		events: {
			'change input': 'toggleFields'
		},

		toggleFields: function(e) {
			var $el = $( e.currentTarget ), fields, setting;
			setting = $el.data( 'setting' );
			fields = $( '.data-' + setting, '.data-fields' );
			if ( $el.is( ':checked' ) ) {
				fields.show();
				deleteUserSetting( 'hidegrid' + setting );
			} else {
				fields.hide();
				setUserSetting( 'hidegrid' + setting, 1 );
			}

			if ( $( ':checked', this.$el ).length ) {
				fields.parent().show();
			} else {
				fields.parent().hide();
			}
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
				this.controller.deactivateMode( 'bulk-edit' );
				this.controller.activateMode( 'edit' );
			} else {
				this.controller.deactivateMode( 'edit' );
				this.controller.activateMode( 'bulk-edit' );
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