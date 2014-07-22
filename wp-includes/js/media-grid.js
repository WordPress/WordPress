/* global _wpMediaViewsL10n, MediaElementPlayer, _wpMediaGridSettings, confirm */
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
				mode:      [ 'grid' ]
			});

			$(document).on( 'click', '.add-new-h2', _.bind( this.addNewClickHandler, this ) );

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
						dropzone:  $('body'),
						container: $('body')
					}
				}).render();
				this.uploader.ready();
				$('body').append( this.uploader.el );

				this.options.uploader = false;
			}

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

			// This is problematic.
			_.delay( _.bind( this.createRouter, this ), 1000 );
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
					library:    media.query( options.library ),
					multiple:   options.multiple,
					title:      options.title,
					content:    'browse',

					filterable: 'mime-types'
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
				gridRouter:  this.gridRouter,
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
			contentRegion.view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				filters:    state.get('filterable'),
				display:    state.get('displaySettings'),
				dragInfo:   state.get('dragInfo'),
				sidebar:    false,

				suggestedWidth:  state.get('suggestedWidth'),
				suggestedHeight: state.get('suggestedHeight'),

				AttachmentView: state.get('AttachmentView')
			});
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

		editAttachment: function( event ) {
			event.preventDefault();
			this.controller.setState( 'edit-image' );
		},

		/**
		 * Noop this from parent class, doesn't apply here.
		 */
		toggleSelectionHandler: function() {},

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
			this.$( 'audio, video' ).each( function (i, elem) {
				var el = media.view.MediaDetails.prepareSrc( elem );
				new MediaElementPlayer( el, media.mixin.mejsSettings );
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
		showItem: function( query ) {
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

			this.on( 'content:create:edit-metadata', this.editMetadataMode, this );
			this.on( 'content:create:edit-image', this.editImageMode, this );
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

				// Set this frame as the modal's content.
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
		 *
		 * @param {Object} contentRegion Basic object with a `view` property, which
		 *                               should be set with the proper region view.
		 */
		editMetadataMode: function( contentRegion ) {
			contentRegion.view = new media.view.Attachment.Details.TwoColumn({
				controller: this,
				model:      this.model
			});
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
			contentRegion.view = new media.view.EditImage( { model: this.model, controller: this } );
			// Defer a call to load the editor, which
			// requires DOM elements to exist.
			_.defer( _.bind( contentRegion.view.loadEditor, contentRegion.view ) );
		},

		/**
		 * Close this modal and immediately open another one.
		 *
		 * Allows for quickly swapping out the attachment being edited.
		 */
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

	/**
	 * Controller for bulk selection.
	 */
	media.view.BulkSelection = media.View.extend({
		className: 'bulk-select',

		initialize: function() {
			this.model = new Backbone.Model({
				currentAction: ''

			});

			this.views.add(
				new media.view.BulkSelectionActionDropdown({
					controller: this
				})
			);

			this.views.add(
				new media.view.BulkSelectionActionButton({
					disabled:   true,
					text:       l10n.apply,
					controller: this
				})
			);
		}
	});

	/**
	 * Bulk Selection dropdown view.
	 *
	 * @constructor
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.BulkSelectionActionDropdown = media.View.extend({
		tagName:   'select',

		initialize: function() {
			media.view.Button.prototype.initialize.apply( this, arguments );
			this.listenTo( this.controller.controller.state().get( 'selection' ), 'add remove reset', _.bind( this.enabled, this ) );
			this.$el.append( $('<option></option>').val( '' ).html( l10n.bulkActions ) )
				.append( $('<option></option>').val( 'delete' ).html( l10n.deletePermanently ) );
			this.$el.prop( 'disabled', true );
			this.$el.on( 'change', _.bind( this.changeHandler, this ) );
		},

		/**
		 * Change handler for the dropdown.
		 *
		 * Sets the bulk selection controller's currentAction.
		 */
		changeHandler: function() {
			this.controller.model.set( { 'currentAction': this.$el.val() } );
		},

		/**
		 * Enable or disable the dropdown if attachments have been selected.
		 */
		enabled: function() {
			var disabled = ! this.controller.controller.state().get('selection').length;
			this.$el.prop( 'disabled', disabled );
		}
	});

	/**
	 * Bulk Selection dropdown view.
	 *
	 * @constructor
	 *
	 * @augments wp.media.view.Button
	 * @augments wp.media.View
	 * @augments wp.Backbone.View
	 * @augments Backbone.View
	 */
	media.view.BulkSelectionActionButton = media.view.Button.extend({
		tagName: 'button',

		initialize: function() {
			media.view.Button.prototype.initialize.apply( this, arguments );

			this.listenTo( this.controller.model, 'change', this.enabled, this );
			this.listenTo( this.controller.controller.state().get( 'selection' ), 'add remove reset', _.bind( this.enabled, this ) );
		},
		/**
		 * Button click handler.
		 */
		click: function() {
			var selection = this.controller.controller.state().get('selection');
			media.view.Button.prototype.click.apply( this, arguments );

			// Currently assumes delete is the only action
			if ( confirm( l10n.warnBulkDelete ) ) {
				while ( selection.length > 0) {
					selection.at(0).destroy();
				}
			}

			this.enabled();
		},
		/**
		 * Enable or disable the button depending if a bulk action is selected
		 * in the bulk select dropdown, and if attachments have been selected.
		 */
		enabled: function() {
			var currentAction = this.controller.model.get( 'currentAction' ),
				selection = this.controller.controller.state().get('selection'),
				disabled = ! currentAction || ! selection.length;
			this.$el.prop( 'disabled', disabled );
		}
	});

	/**
	 * A filter dropdown for month/dates.
	 */
	media.view.DateFilter = media.view.AttachmentFilters.extend({
		id: 'media-attachment-date-filters',

		createFilters: function() {
			var filters = {};
			_.each( media.view.settings.months || {}, function( value, index ) {
				filters[ index ] = {
					text: value.text,
					props: {
						year: value.year,
						monthnum: value.month
					}
				};
			});
			filters.all = {
				text:  l10n.allDates,
				props: {
					monthnum: false,
					year:  false
				},
				priority: 10
			};
			this.filters = filters;
		}
	});

}(jQuery, _, Backbone, wp));