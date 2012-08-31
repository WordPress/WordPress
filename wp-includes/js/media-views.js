(function($){
	var media       = wp.media,
		Attachment  = media.model.Attachment,
		Attachments = media.model.Attachments,
		Query       = media.model.Query;


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
			multiple: false
		},

		initialize: function() {
			this.createSelection();

			// Initialize views.
			this.modal     = new media.view.Modal({ controller: this });
			this.workspace = new media.view.Workspace({ controller: this });
		},

		createSelection: function() {
			var controller = this;

			// Initialize workflow-specific models.
			this.selection = new Attachments();

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
		},

		render: function() {
			this.workspace.render();
			this.modal.content( this.workspace ).attach();
			return this;
		}
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

			_.defaults( this.options, {
				title: '',
				container: document.body
			});
		},

		render: function() {
			this.$el.html( this.template( this.options ) );
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
			this.options.$content = ( $content instanceof Backbone.View ) ? $content.$el : $content;
			return this.render();
		},

		title: function( title ) {
			this.options.title = title;
			return this.render();
		}
	});

	/**
	 * wp.media.view.Workspace
	 */
	media.view.Workspace = Backbone.View.extend({
		tagName:   'div',
		className: 'media-workspace',
		template:  media.template('media-workspace'),

		events: {
			'dragenter':  'maybeInitUploader',
			'mouseenter': 'maybeInitUploader'
		},

		initialize: function() {
			this.controller = this.options.controller;

			_.defaults( this.options, {
				selectOne: false,
				uploader:  {}
			});

			this.attachmentsView = new media.view.Attachments({
				controller: this.controller,
				directions: 'Select stuff.',
				collection: new Attachments( null, {
					mirror: media.query()
				})
			});

			this.$content = $('<div class="existing-attachments" />');
			this.$content.append( this.attachmentsView.$el );

			// Track uploading attachments.
			this.pending = new Attachments( [], { query: false });
			this.pending.on( 'add remove reset change:percent', function() {
				this.$el.toggleClass( 'uploading', !! this.pending.length );

				if ( ! this.$bar || ! this.pending.length )
					return;

				this.$bar.width( ( this.pending.reduce( function( memo, attachment ) {
					if ( attachment.get('uploading') )
						return memo + ( attachment.get('percent') || 0 );
					else
						return memo + 100;
				}, 0 ) / this.pending.length ) + '%' );
			}, this );
		},

		render: function() {
			this.attachmentsView.render();
			this.$el.html( this.template( this.options ) ).append( this.$content );
			this.$bar = this.$('.media-progress-bar div');
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
				browser:   this.$('.upload-attachments a'),

				added: function( file ) {
					file.attachment = Attachment.create( _.extend({
						file: file,
						uploading: true,
						date: new Date()
					}, _.pick( file, 'loaded', 'size', 'percent' ) ) );

					workspace.pending.add( file.attachment );
				},

				progress: function( file ) {
					file.attachment.set( _.pick( file, 'loaded', 'percent' ) );
				},

				success: function( resp, file ) {
					var complete;

					_.each(['file','loaded','size','uploading','percent'], function( key ) {
						file.attachment.unset( key );
					});

					file.attachment.set( 'id', resp.id );
					Attachment.get( resp.id, file.attachment ).fetch();

					complete = workspace.pending.all( function( attachment ) {
						return ! attachment.get('uploading');
					});

					if ( complete )
						workspace.pending.reset();
				},

				error: function( message, error, file ) {
					file.attachment.destroy();
				}
			}, this.options.uploader ) );
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
				refreshThreshold:   3
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
		},

		render: function() {
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
				return new media.view.Attachment({
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

			view = new media.view.Attachment({
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
			var args = _.clone( this.collection.mirroring.args );

			// Bail if we're currently searching for the same string.
			if ( args.s === event.target.value )
				return;

			if ( event.target.value )
				args.s = event.target.value;
			else
				delete args.s;

			this.collection.mirror( media.query( args ) );
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

		initialize: function() {
			this.controller = this.options.controller;

			this.model.on( 'change:sizes change:uploading', this.render, this );
			this.model.on( 'change:percent', this.progress, this );
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );
		},

		render: function() {
			var attachment = this.model.toJSON(),
				options = {
					orientation: attachment.orientation || 'landscape',
					thumbnail:   attachment.url || '',
					uploading:   attachment.uploading
				};

			// Use the medium size if possible. If the medium size
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
			if ( collection === this.controller.selection )
				this.$el.addClass('selected');
		},

		deselect: function( model, collection ) {
			if ( collection === this.controller.selection )
				this.$el.removeClass('selected');
		}
	});
}(jQuery));