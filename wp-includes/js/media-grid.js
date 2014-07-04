(function( $, _, Backbone, wp ) {
	var media = wp.media, l10n;

	// Link any localized strings.
	if ( media.view.l10n ) {
		l10n = media.view.l10n;
	} else {
		l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;
		delete l10n.settings;
	}

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
				uploader:  true
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
				}),

				new media.controller.EditImage( { model: options.editImage } )
			]);
		},

		bindHandlers: function() {
			this.on( 'content:create:browse', this.browseContent, this );
			this.on( 'content:render:edit-image', this.editImageContent, this );
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
	
}( jQuery, _, Backbone, wp ));