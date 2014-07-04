/* global _wpMediaViewsL10n, setUserSetting, deleteUserSetting, MediaElementPlayer */
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
	media.controller.EditImageNoFrame = media.controller._State.extend({
		defaults: {
			id:      'edit-attachment',
			title:   l10n.editImage,
			// Region mode defaults.
			menu:    false,
			router:  'edit-metadata',
			content: 'edit-metadata',
			toolbar: 'toolbar',

			url:     ''
		},

		initialize: function() {
			media.controller._State.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			this.listenTo( this.frame, 'toolbar:render:edit-image', this.toolbar );
		},

		_postActivate: function() {
			this._content();
			this._router();
		},

		deactivate: function() {
			this.stopListening( this.frame );
		},

		toolbar: function() {
			var frame = this.frame,
				lastState = frame.lastState(),
				previous = lastState && lastState.id;

			frame.toolbar.set( new media.view.Toolbar({
				controller: frame,
				items: {
					back: {
						style: 'primary',
						text:     l10n.back,
						priority: 20,
						click:    function() {
							if ( previous ) {
								frame.setState( previous );
							} else {
								frame.close();
							}
						}
					}
				}
			}) );
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
			_.defaults( this.options, {
				title:     l10n.mediaLibraryTitle,
				modal:     false,
				selection: [],
				library:   {},
				multiple:  false,
				state:     'library',
				uploader:  true,
				mode:      [ 'grid', 'edit' ]
			});

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
			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			// Add the default states.
			this.states.add([
				new media.controller.Library({
					library:    media.query( options.library ),
					multiple:   options.multiple,
					title:      options.title,
					priority:   20,
					toolbar:    false,
					router:     false,
					content:    'browse',
					filterable: 'mime-types'
				})
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
			var library = this.state().get('library'), hasPrevious, hasNext;
			if ( library.indexOf( model ) > 0 ) {
				hasPrevious = true;
			}
			else {
				hasPrevious = false;
			}
			if ( library.indexOf( model ) < library.length - 1 ) {
				hasNext = true;
			}
			else {
				hasNext = false;
			}

			new media.view.Frame.EditAttachment({
				hasPrevious:    hasPrevious,
				hasNext:        hasNext,
				model:          model,
				gridController: this
			});
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
	 * A frame for editing the details of a specific media item.
	 *
	 * Opens in a modal by default.
	 *
	 * Requires an attachment model to be passed in the options hash under `model`.
	 */
	media.view.Frame.EditAttachment = media.view.Frame.extend({

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

			this.createStates();

			this.on( 'content:render:edit-metadata', this.editMetadataContent, this );
			this.on( 'content:render:edit-image', this.editImageContentUgh, this );

			// Only need a tab to Edit Image for images.
			if ( this.model.get( 'type' ) === 'image' ) {
				this.on( 'router:create', this.createRouter, this );
				this.on( 'router:render', this.browseRouter, this );
			}

			// Initialize modal container view.
			if ( this.options.modal ) {
				this.modal = new media.view.Modal({
					controller: this,
					title:      this.options.title
				});

				// Completely destroy the modal DOM element when closing it.
				this.modal.close = function() {
					self.modal.remove();
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
			this.options.gridController.trigger( 'edit:attachment:previous', this.model );
		},

		/**
		 * Click handler to switch to the next media item.
		 */
		nextMediaItem: function() {
			if ( ! this.options.hasNext )
				return;
			this.modal.close();
			this.options.gridController.trigger( 'edit:attachment:next', this.model );
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

}(jQuery, _, Backbone, wp));