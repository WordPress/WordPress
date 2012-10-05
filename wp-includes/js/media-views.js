(function($){
	var media       = wp.media,
		Attachment  = media.model.Attachment,
		Attachments = media.model.Attachments,
		Query       = media.model.Query,
		l10n;

	// Link any localized strings.
	l10n = media.view.l10n = _.isUndefined( _wpMediaViewsL10n ) ? {} : _wpMediaViewsL10n;

	/**
	 * ========================================================================
	 * CONTROLLERS
	 * ========================================================================
	 */

	/**
	 * wp.media.controller.Workflow
	 */
	media.controller.Workflow = Backbone.Model.extend({
		defaults: {
			title:     '',
			multiple:  false,
			view:      'library',
			library:   {},
			selection: []
		},

		initialize: function() {
			this.createSelection();

			// Initialize view storage.
			this._views        = {};
			this._pendingViews = {};

			// Initialize modal container view.
			this.modal = new media.view.Modal({ controller: this });

			// Add default views.
			//
			// Use the `library` property to initialize the corresponding view,
			// then unset the property.
			this.add( 'library', media.view.Workspace.Library, {
				collection: media.query( this.get('library') )
			} );
			this.unset('library');

			// Add the gallery view.
			this.add( 'gallery', media.view.Workspace.Gallery, { collection: this.selection } );
		},


		// Registers a view.
		//
		// `id` is a unique ID for the view relative to the workflow instance.
		// `constructor` is a `Backbone.View` constructor. `options` are the
		// options to be passed when the view is initialized.
		//
		// Triggers the `add` and `add:VIEW_ID` events.
		add: function( id, constructor, options ) {
			this.remove( id );
			this._pendingViews[ id ] = {
				view:    constructor,
				options: options
			};
			this.trigger( 'add add:' + id, constructor, options );
			return this;
		},

		// Returns a registered view instance. If an `id` is not provided,
		// it will return the active view.
		//
		// Lazily instantiates a registered view.
		//
		// Triggers the `init` and `init:VIEW_ID` events.
		view: function( id ) {
			var pending;

			id = id || this.get('view');
			pending = this._pendingViews[ id ];

			if ( ! this._views[ id ] && pending ) {
				this._views[ id ] = new pending.view( _.extend({ controller: this }, pending.options || {} ) );
				delete this._pendingViews[ id ];
				this.trigger( 'init init:' + id, this._views[ id ] );
			}

			return this._views[ id ];
		},

		// Unregisters a view from the workflow.
		//
		// Triggers the `remove` and `remove:VIEW_ID` events.
		remove: function( id ) {
			delete this._views[ id ];
			delete this._pendingViews[ id ];
			this.trigger( 'remove remove:' + id );
			return this;
		},

		// Renders a view and places it within the modal window.
		// Automatically adds a view if `constructor` is provided.
		render: function( id, constructor, options ) {
			var view;
			id = id || this.get('view');

			if ( constructor )
				this.add( id, constructor, options );

			view = this.view( id );

			if ( ! view )
				return this;

			view.render();
			this.modal.content( view );
			return this;
		},

		update: function( event ) {
			this.close();
			this.trigger( 'update', this.selection );
			this.trigger( 'update:' + event, this.selection );
			this.selection.clear();
		},

		createSelection: function() {
			var controller = this;

			// Initialize workflow-specific models.
			// Use the `selection` property to initialize the Attachments
			// collection, then unset the property.
			this.selection = new Attachments( this.get('selection') );
			this.unset('selection');

			_.extend( this.selection, {
				// Override the selection's add method.
				// If the workflow does not support multiple
				// selected attachments, reset the selection.
				add: function( models, options ) {
					if ( ! controller.get('multiple') ) {
						models = _.isArray( models ) ? _.first( models ) : models;
						this.clear( options );
					}

					return Attachments.prototype.add.call( this, models, options );
				},

				// Removes all models from the selection.
				clear: function( options ) {
					return this.remove( this.models, options );
				},

				// Override the selection's reset method.
				// Always direct items through add and remove,
				// as we need them to fire.
				reset: function( models, options ) {
					return this.clear( options ).add( models, options );
				},

				// Create selection.has, which determines if a model
				// exists in the collection based on cid and id,
				// instead of direct comparison.
				has: function( attachment ) {
					return !! ( this.getByCid( attachment.cid ) || this.get( attachment.id ) );
				}
			});
		}
	});

	// Map modal methods to the workflow.
	_.each(['attach','detach','open','close'], function( method ) {
		media.controller.Workflow.prototype[ method ] = function() {
			this.modal[ method ].apply( this.modal, arguments );
			return this;
		};
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * wp.media.view.Modal
	 */
	media.view.Modal = Backbone.View.extend({
		tagName:  'div',
		template: media.template('media-modal'),

		events: {
			'click .media-modal-backdrop, .media-modal-close' : 'closeHandler'
		},

		initialize: function() {
			this.controller = this.options.controller;
			this.controller.on( 'change:title', this.render, this );

			_.defaults( this.options, {
				container: document.body
			});
		},

		render: function() {
			// Ensure content div exists.
			this.options.$content = this.options.$content || $('<div />');

			// Detach the content element from the DOM to prevent
			// `this.$el.html()` from garbage collecting its events.
			this.options.$content.detach();

			this.$el.html( this.template( this.controller.toJSON() ) );
			this.$('.media-modal-content').append( this.options.$content );
			return this;
		},

		attach: function() {
			this.$el.appendTo( this.options.container );
		},

		detach: function() {
			this.$el.detach();
		},

		open: function() {
			this.$el.show();
		},

		close: function() {
			this.$el.hide();
		},

		closeHandler: function( event ) {
			event.preventDefault();
			this.close();
		},

		content: function( $content ) {
			// Detach any existing content to prevent events from being lost.
			if ( this.options.$content )
				this.options.$content.detach();

			// Set and render the content.
			this.options.$content = ( $content instanceof Backbone.View ) ? $content.$el : $content;
			return this.render();
		}
	});

	/**
	 * wp.media.view.Toolbar
	 */
	media.view.Toolbar = Backbone.View.extend({
		tagName:   'div',
		className: 'media-toolbar',

		initialize: function() {
			this._views    = {};
			this.$primary   = $('<div class="media-toolbar-primary" />').prependTo( this.$el );
			this.$secondary = $('<div class="media-toolbar-secondary" />').prependTo( this.$el );

			if ( this.options.items ) {
				_.each( this.options.items, function( view, id ) {
					this.add( id, view, { silent: true } );
				}, this );
				this.render();
			}
		},

		render: function() {
			var views = _.chain( this._views ).sortBy( function( view ) {
				return view.options.priority || 10;
			}).groupBy( function( view ) {
				return ( view.options.priority || 10 ) > 0 ? 'primary' : 'secondary';
			}).value();

			// Make sure to detach the elements we want to reuse.
			// Otherwise, `jQuery.html()` will unbind their events.
			$( _.pluck( this._views, 'el' ) ).detach();
			this.$primary.html( _.pluck( views.primary, 'el' ) );
			this.$secondary.html( _.pluck( views.secondary, 'el' ) );

			return this;
		},

		add: function( id, view, options ) {
			if ( ! ( view instanceof Backbone.View ) ) {
				view.classes = [ id ].concat( view.classes || [] );
				view = new media.view.Button( view ).render();
			}

			this._views[ id ] = view;
			if ( ! options || ! options.silent )
				this.render();
			return this;
		},

		get: function( id ) {
			return this._views[ id ];
		},

		remove: function( id, options ) {
			delete this._views[ id ];
			if ( ! options || ! options.silent )
				this.render();
			return this;
		}
	});


	/**
	 * wp.media.view.Button
	 */
	media.view.Button = Backbone.View.extend({
		tagName:    'a',
		className:  'media-button',
		attributes: { href: '#' },

		events: {
			'click': 'click'
		},

		defaults: {
			text:  '',
			style: '',
			size:  'large'
		},

		initialize: function() {
			// Create a model with the provided `defaults`.
			this.model = new Backbone.Model( this.defaults );

			// If any of the `options` have a key from `defaults`, apply its
			// value to the `model` and remove it from the `options object.
			_.each( this.defaults, function( def, key ) {
				var value = this.options[ key ];
				if ( _.isUndefined( value ) )
					return;

				this.model.set( key, value );
				delete this.options[ key ];
			}, this );

			this.model.on( 'change', this.render, this );
		},

		render: function() {
			var classes = [ 'button', this.className ];

			if ( this.model.get('style') )
				classes.push( 'button-' + this.model.get('style') );

			if ( this.model.get('size') )
				classes.push( 'button-' + this.model.get('size') );

			classes = classes.concat( this.options.classes );
			this.el.className = classes.join(' ');

			this.$el.text( this.model.get('text') );
			return this;
		},

		click: function( event ) {
			event.preventDefault();
			if ( this.options.click )
				this.options.click.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.Attachment
	 */
	media.view.Attachment = Backbone.View.extend({
		tagName:   'li',
		className: 'attachment',
		template:  media.template('attachment'),

		events: {
			'click': 'toggleSelection'
		},

		buttons: {},

		initialize: function() {
			this.controller = this.options.controller;

			this.model.on( 'change:sizes change:uploading', this.render, this );
			this.model.on( 'change:percent', this.progress, this );
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );

			// Prevent default navigation on all links.
			this.$el.on( 'click', 'a', this.preventDefault );
		},

		render: function() {
			var attachment = this.model.toJSON(),
				options = {
					thumbnail:   'image' === attachment.type ? attachment.url : attachment.icon,
					uploading:   attachment.uploading,
					orientation: attachment.orientation || 'landscape',
					type:        attachment.type,
					subtype:     attachment.subtype,
					buttons:     this.buttons
				};

			// Use the medium image size if possible. If the medium size
			// doesn't exist, then the attachment is too small.
			// In that case, use the attachment itself.
			if ( attachment.sizes && attachment.sizes.medium ) {
				options.orientation = attachment.sizes.medium.orientation;
				options.thumbnail   = attachment.sizes.medium.url;
			}

			this.$el.html( this.template( options ) );

			if ( attachment.uploading )
				this.$bar = this.$('.media-progress-bar div');
			else
				delete this.$bar;

			// Check if the model is selected.
			if ( this.controller.selection.has( this.model ) )
				this.select();

			return this;
		},

		progress: function() {
			if ( this.$bar && this.$bar.length )
				this.$bar.width( this.model.get('percent') + '%' );
		},

		toggleSelection: function( event ) {
			var selection = this.controller.selection;
			selection[ selection.has( this.model ) ? 'remove' : 'add' ]( this.model );
		},

		select: function( model, collection ) {
			// If a collection is provided, check if it's the selection.
			// If it's not, bail; we're in another selection's event loop.
			if ( collection && collection !== this.controller.selection )
				return;

			this.$el.addClass('selected');
		},

		deselect: function( model, collection ) {
			// If a collection is provided, check if it's the selection.
			// If it's not, bail; we're in another selection's event loop.
			if ( collection && collection !== this.controller.selection )
				return;

			this.$el.removeClass('selected');
		},

		preventDefault: function( event ) {
			event.preventDefault();
		}
	});

	/**
	 * wp.media.view.Attachment.Library
	 */
	media.view.Attachment.Library = media.view.Attachment.extend({
		className: 'attachment library',

		buttons: {
			insert: true
		},

		events: _.defaults({
			'click .insert': 'insert'
		}, media.view.Attachment.prototype.events ),

		insert: function() {
			this.controller.selection.reset([ this.model ]);
			this.controller.update();
		}
	});

	/**
	 * wp.media.view.Attachment.Gallery
	 */
	media.view.Attachment.Gallery = media.view.Attachment.extend({
		buttons: {
			close: true
		},

		events: {
			'click .close': 'toggleSelection'
		}
	});

	/**
	 * wp.media.view.Workspace
	 */
	media.view.Workspace = Backbone.View.extend({
		tagName:   'div',
		className: 'media-workspace',
		template:  media.template('media-workspace'),

		// The `options` to be passed to `Attachments` view.
		attachmentsView: {},

		events: {
			'dragenter':  'maybeInitUploader',
			'mouseenter': 'maybeInitUploader'
		},

		initialize: function() {
			this.controller = this.options.controller;

			_.defaults( this.options, {
				selectOne:       false,
				uploader:        {},
				attachmentsView: {}
			});

			this.$content = $('<div class="existing-attachments" />');

			// Generate the `options` passed to the `Attachments` view.
			// Order of priority from lowest to highest: the provided defaults,
			// the prototypal `attachmentsView` property, the `attachmentsView`
			// option for the current instance, and then the `controller` and
			// `collection` keys, to ensure they're correctly set.
			this.attachmentsView = _.extend( {
				directions: this.controller.get('multiple') ? l10n.selectMediaMultiple : l10n.selectMediaSingular
			}, this.attachmentsView, this.options.attachmentsView, {
				controller: this.controller,
				collection: this.collection
			});

			// Initialize the `Attachments` view.
			this.attachmentsView = new media.view.Attachments( this.attachmentsView );
			this.$content.append( this.attachmentsView.$el );

			// Track uploading attachments.
			wp.Uploader.queue.on( 'add remove reset change:percent', this.renderUploadProgress, this );
			wp.Uploader.queue.on( 'add', this.selectUpload, this );
		},

		render: function() {
			this.$content.detach();

			this.attachmentsView.render();
			this.renderUploadProgress();
			this.$el.html( this.template( this.options ) ).append( this.$content );
			this.$bar = this.$('.upload-attachments .media-progress-bar div');
			return this;
		},

		maybeInitUploader: function() {
			var workspace = this;

			// If the uploader already exists or the body isn't in the DOM, bail.
			if ( this.uploader || ! this.$el.closest('body').length )
				return;

			this.uploader = new wp.Uploader( _.extend({
				container: this.$el,
				dropzone:  this.$el,
				browser:   this.$('.upload-attachments a')
			}, this.options.uploader ) );
		},

		selectUpload: function( attachment ) {
			this.controller.selection.add( attachment );
		},

		renderUploadProgress: function() {
			var queue = wp.Uploader.queue;

			this.$el.toggleClass( 'uploading', !! queue.length );

			if ( ! this.$bar || ! queue.length )
				return;

			this.$bar.width( ( queue.reduce( function( memo, attachment ) {
				if ( attachment.get('uploading') )
					return memo + ( attachment.get('percent') || 0 );
				else
					return memo + 100;
			}, 0 ) / queue.length ) + '%' );
		}
	});

	/**
	 * wp.media.view.Workspace.Library
	 */
	media.view.Workspace.Library = media.view.Workspace.extend({

		attachmentsView: {
			// The single `Attachment` view to be used in the `Attachments` view.
			AttachmentView: media.view.Attachment.Library
		},

		initialize: function() {
			media.view.Workspace.prototype.initialize.apply( this, arguments );

			// If this supports multiple attachments, initialize the sample toolbar view.
			if ( this.controller.get('multiple') )
				this.initToolbarView();
		},

		// Initializes the toolbar view. Currently uses defaults set for
		// inserting media into a post. This should be pulled out into the
		// appropriate workflow when the time comes, but is currently here
		// to test multiple selections.
		initToolbarView: function() {
			var controller = this.controller;

			this.toolbarView = new media.view.Toolbar({
				items: {
					'selection-preview': new media.view.SelectionPreview({
						controller: this.controller,
						collection: this.controller.selection,
						priority: -40
					}),

					'create-new-gallery': {
						style:    'primary',
						text:     l10n.createNewGallery,
						priority: 40,

						click: function() {
							controller.render('gallery');
						}
					},

					'insert-into-post': {
						text:     l10n.insertIntoPost,
						priority: 30,
						click:    _.bind( controller.update, controller, 'insert' )
					},

					'add-to-gallery': {
						text:     l10n.addToGallery,
						priority: 20
					}
				}
			});

			this.controller.selection.on( 'add remove', function() {
				var count = this.controller.selection.length,
					showGallery;

				this.$el.toggleClass( 'with-toolbar', !! count );

				// Check if every attachment in the selection is an image.
				showGallery = count > 1 && this.controller.selection.all( function( attachment ) {
					return 'image' === attachment.get('type');
				});

				this.toolbarView.get('create-new-gallery').$el.toggle( showGallery );
				insert = this.toolbarView.get('insert-into-post');
				insert.model.set( 'style', showGallery ? '' : 'primary' );
			}, this );

			this.$content.append( this.toolbarView.$el );
		}
	});

	/**
	 * wp.media.view.Workspace.Gallery
	 */
	media.view.Workspace.Gallery = media.view.Workspace.extend({

		attachmentsView: {
			// The single `Attachment` view to be used in the `Attachments` view.
			AttachmentView: media.view.Attachment.Gallery,
			sortable:       true
		},

		initialize: function() {
			media.view.Workspace.prototype.initialize.apply( this, arguments );
			this.initToolbarView();
		},

		// Initializes the toolbar view. Currently uses defaults set for
		// inserting media into a post. This should be pulled out into the
		// appropriate workflow when the time comes, but is currently here
		// to test multiple selections.
		initToolbarView: function() {
			var controller = this.controller;

			this.toolbarView = new media.view.Toolbar({
				items: {
					'return-to-library': {
						text:     l10n.returnToLibrary,
						priority: -40,

						click:  function() {
							controller.render('library');
						}
					},

					'insert-gallery-into-post': {
						style:    'primary',
						text:     l10n.insertGalleryIntoPost,
						priority: 40,
						click:    _.bind( controller.update, controller, 'gallery' )
					},

					'add-images-from-library': {
						text:     l10n.addImagesFromLibrary,
						priority: 30
					}
				}
			});

			this.$el.addClass('with-toolbar');
			this.$content.append( this.toolbarView.$el );
		}
	});


	/**
	 * wp.media.view.Attachments
	 */
	media.view.Attachments = Backbone.View.extend({
		tagName:   'div',
		className: 'attachments',
		template:  media.template('attachments'),

		events: {
			'keyup input': 'search'
		},

		initialize: function() {
			this.controller = this.options.controller;

			_.defaults( this.options, {
				refreshSensitivity: 200,
				refreshThreshold:   3,
				AttachmentView:     media.view.Attachment,
				sortable:           false
			});

			_.each(['add','remove'], function( method ) {
				this.collection.on( method, function( attachment, attachments, options ) {
					this[ method ]( attachment, options.index );
				}, this );
			}, this );

			this.collection.on( 'reset', this.refresh, this );

			this.$list = $('<ul />');
			this.list  = this.$list[0];

			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();
			this.$list.on( 'scroll.attachments', this.scroll );

			this.initSortable();
		},

		initSortable: function() {
			var collection = this.collection,
				from;

			if ( ! this.options.sortable || ! $.fn.sortable )
				return;

			this.$list.sortable({
				// If the `collection` has a `comparator`, disable sorting.
				disabled: !! collection.comparator,

				// Prevent attachments from being dragged outside the bounding
				// box of the list.
				containment: this.$list,

				// Change the position of the attachment as soon as the
				// mouse pointer overlaps a thumbnail.
				tolerance: 'pointer',

				// Record the initial `index` of the dragged model.
				start: function( event, ui ) {
					from = ui.item.index();
				},

				// Update the model's index in the collection.
				// Do so silently, as the view is already accurate.
				update: function( event, ui ) {
					var model = collection.at( from );

					collection.remove( model, {
						silent: true
					}).add( model, {
						at:     ui.item.index(),
						silent: true
					});
				}
			});

			// If the `orderby` property is changed on the `collection`,
			// check to see if we have a `comparator`. If so, disable sorting.
			collection.props.on( 'change:orderby', function() {
				this.$list.sortable( 'option', 'disabled', !! collection.comparator );
			}, this );
		},

		render: function() {
			// Detach the list from the DOM to prevent event removal.
			this.$list.detach();

			this.$el.html( this.template( this.options ) ).append( this.$list );
			this.refresh();
			return this;
		},

		refresh: function() {
			// If there are no elements, load some.
			if ( ! this.collection.length ) {
				this.collection.more();
				this.$list.empty();
				return this;
			}

			// Otherwise, create all of the Attachment views, and replace
			// the list in a single DOM operation.
			this.$list.html( this.collection.map( function( attachment ) {
				return new this.options.AttachmentView({
					controller: this.controller,
					model:      attachment
				}).render().$el;
			}, this ) );

			// Then, trigger the scroll event to check if we're within the
			// threshold to query for additional attachments.
			this.scroll();
			return this;
		},

		add: function( attachment, index ) {
			var view, children;

			view = new this.options.AttachmentView({
				controller: this.controller,
				model:      attachment
			}).render();

			children = this.$list.children();

			if ( children.length > index )
				children.eq( index ).before( view.$el );
			else
				this.$list.append( view.$el );
		},

		remove: function( attachment, index ) {
			var children = this.$list.children();
			if ( children.length )
				children.eq( index ).detach();
		},

		scroll: function( event ) {
			// @todo: is this still necessary?
			if ( ! this.$list.is(':visible') )
				return;

			if ( this.list.scrollHeight < this.list.scrollTop + ( this.list.clientHeight * this.options.refreshThreshold ) ) {
				this.collection.more();
			}
		},

		search: function( event ) {
			var props = this.collection.props;

			if ( event.target.value )
				props.set( 'search', event.target.value );
			else
				props.unset('search');
		}
	});

	/**
	 * wp.media.view.SelectionPreview
	 */
	media.view.SelectionPreview = Backbone.View.extend({
		tagName:   'div',
		className: 'selection-preview',
		template:  media.template('media-selection-preview'),

		events: {
			'click .clear-selection': 'clear'
		},

		initialize: function() {
			this.controller = this.options.controller;
			this.collection.on( 'add change:url remove', this.render, this );
			this.render();
		},

		render: function() {
			var options = {},
				first, sizes, amount;

			// If nothing is selected, display nothing.
			if ( ! this.collection.length ) {
				this.$el.empty();
				return this;
			}

			options.count = this.collection.length;
			first = this.collection.first();
			sizes = first.get('sizes');

			if ( 'image' === first.get('type') )
				options.thumbnail = ( sizes && sizes.thumbnail ) ? sizes.thumbnail.url : first.get('url');
			else
				options.thumbnail =  first.get('icon');

			this.$el.html( this.template( options ) );
			return this;
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.clear();
		}
	});
}(jQuery));