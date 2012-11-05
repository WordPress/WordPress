(function($){
	var media       = wp.media,
		Attachment  = media.model.Attachment,
		Attachments = media.model.Attachments,
		Query       = media.model.Query,
		l10n;

	// Link any localized strings.
	l10n = media.view.l10n = _.isUndefined( _wpMediaViewsL10n ) ? {} : _wpMediaViewsL10n;

	// Check if the browser supports CSS 3.0 transitions
	$.support.transition = (function(){
		var style = document.documentElement.style,
			transitions = {
				WebkitTransition: 'webkitTransitionEnd',
				MozTransition:    'transitionend',
				OTransition:      'oTransitionEnd otransitionend',
				transition:       'transitionend'
			}, transition;

		transition = _.find( _.keys( transitions ), function( transition ) {
			return ! _.isUndefined( style[ transition ] );
		});

		return transition && {
			end: transitions[ transition ]
		};
	}());

	// Makes it easier to bind events using transitions.
	media.transition = function( selector ) {
		var deferred = $.Deferred();

		if ( $.support.transition ) {
			if ( ! (selector instanceof $) )
				selector = $( selector );

			// Resolve the deferred when the first element finishes animating.
			selector.first().one( $.support.transition.end, deferred.resolve );

		// Otherwise, execute on the spot.
		} else {
			deferred.resolve();
		}

		return deferred.promise();
	};

	/**
	 * ========================================================================
	 * CONTROLLERS
	 * ========================================================================
	 */

	/**
	 * wp.media.controller.StateMachine
	 */
	media.controller.StateMachine = function( states ) {
		this.states = new Backbone.Collection( states );
	};

	// Use Backbone's self-propagating `extend` inheritance method.
	media.controller.StateMachine.extend = Backbone.Model.extend;

	_.extend( media.controller.StateMachine.prototype, {
		// Fetch a state model.
		//
		// Implicitly creates states.
		get: function( id ) {
			// Ensure that the `states` collection exists so the `StateMachine`
			// can be used as a mixin.
			this.states = this.states || new Backbone.Collection();

			if ( ! this.states.get( id ) )
				this.states.add({ id: id });
			return this.states.get( id );
		},

		// Selects or returns the active state.
		//
		// If a `id` is provided, sets that as the current state.
		// If no parameters are provided, returns the current state object.
		state: function( id ) {
			var previous;

			if ( ! id )
				return this._state ? this.get( this._state ) : null;

			previous = this.state();

			// Bail if we're trying to select the current state, or a state
			// that does not exist.
			if ( previous && id === previous.id || ! this.states.get( id ) )
				return;

			if ( previous )
				previous.trigger('deactivate');

			this._state = id;
			this.state().trigger('activate');
		}
	});

	// Map methods from the `states` collection to the `StateMachine` itself.
	_.each([ 'on', 'off', 'trigger' ], function( method ) {
		media.controller.StateMachine.prototype[ method ] = function() {
			// Ensure that the `states` collection exists so the `StateMachine`
			// can be used as a mixin.
			this.states = this.states || new Backbone.Collection();
			// Forward the method to the `states` collection.
			this.states[ method ].apply( this.states, arguments );
			return this;
		};
	});


	// wp.media.controller.State
	// ---------------------------
	media.controller.State = Backbone.Model.extend({
		initialize: function() {
			this.on( 'activate', this._activate, this );
			this.on( 'activate', this.activate, this );
			this.on( 'deactivate', this._deactivate, this );
			this.on( 'deactivate', this.deactivate, this );
		},

		activate: function() {},
		_activate: function() {
			this.active = true;

			this.menu();
			this.toolbar();
			this.sidebar();
			this.content();
		},

		deactivate: function() {},
		_deactivate: function() {
			this.active = false;
		},

		menu: function() {
			var menu = this.get('menu');

			if ( ! menu )
				return;

			this.frame.menu( menu );
			menu.select( this.id );
		},

		toolbar: function() {},
		sidebar: function() {},
		content: function() {}
	});

	// wp.media.controller.Library
	// ---------------------------
	media.controller.Library = media.controller.State.extend({
		defaults: {
			id:       'library',
			multiple: false,
			describe: false,
			title:    l10n.mediaLibraryTitle
		},

		initialize: function() {
			if ( ! this.get('selection') ) {
				this.set( 'selection', new media.model.Selection( null, {
					multiple: this.get('multiple')
				}) );
			}

			if ( ! this.get('library') )
				this.set( 'library', media.query() );

			if ( ! this.get('edge') )
				this.set( 'edge', 120 );

			if ( ! this.get('gutter') )
				this.set( 'gutter', 8 );

			media.controller.State.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			// If we're in a workflow that supports multiple attachments,
			// automatically select any uploading attachments.
			if ( this.get('multiple') )
				wp.Uploader.queue.on( 'add', this.selectUpload, this );

			this.get('selection').on( 'selection:single', this.buildDetails, this );
			this.get('selection').on( 'selection:unsingle', this.clearDetails, this );

			this._updateEmpty();
			this.get('library').on( 'add remove reset', this._updateEmpty, this );
			this.on( 'change:empty', this.refresh, this );
			this.refresh();
		},

		deactivate: function() {
			var toolbar = this._postLibraryToolbar;

			if ( toolbar )
				this.get('selection').off( 'add remove', toolbar.visibility, toolbar );

			wp.Uploader.queue.off( 'add', this.selectUpload, this );
			this.get('selection').off( 'selection:single', this.buildDetails, this );
			this.get('selection').off( 'selection:unsingle', this.clearDetails, this );
			this.get('library').off( 'add remove reset', this._updateEmpty, this );
			this.off( 'change:empty', this.refresh, this );
		},

		toolbar: function() {
			var frame = this.frame,
				toolbar;

			// Toolbar.
			toolbar = this._postLibraryToolbar = new media.view.Toolbar.PostLibrary({
				controller: frame,
				state:      this
			});

			frame.toolbar( toolbar );
			this.get('selection').on( 'add remove', toolbar.visibility, toolbar );
		},

		sidebar: function() {
			var frame = this.frame;

			// Sidebar.
			frame.sidebar( new media.view.Sidebar({
				controller: frame
			}) );

			this.details();
		},

		content: function() {
			var frame = this.frame,
				library = this.get('library'),
				view;

			// Content.
			if ( this.get('empty') ) {
				// Attempt to fetch any Attachments we don't already have.
				library.more();

				// In the meantime, render an inline uploader.
				view = new media.view.UploaderInline({
					controller: frame
				});
			} else {
				// Browse our library of attachments.
				view = new media.view.AttachmentsBrowser({
					controller: frame,
					collection: library,
					model:      this
				});
			}

			frame.content( view.render() );
		},

		refresh: function() {
			this.frame.$el.toggleClass( 'hide-sidebar hide-toolbar', this.get('empty') );
			this.content();
		},

		_updateEmpty: function() {
			this.set( 'empty', ! this.get('library').length );
		},

		selectUpload: function( attachment ) {
			this.get('selection').add( attachment );
		},

		details: function() {
			var single = this.get('selection').single();
			this[ single ? 'buildDetails' : 'clearDetails' ]( single );
		},

		buildDetails: function( model ) {
			this.frame.sidebar().add( 'details', new media.view.Attachment.Details({
				controller: this.frame,
				model:      model,
				priority:   80
			}).render() );
			return this;
		},

		clearDetails: function( model ) {
			if ( this.get('selection').single() )
				return this;

			this.frame.sidebar().add( 'details', new Backbone.View({
				priority: 80
			}).render() );
			return this;
		},

		toggleSelection: function( model ) {
			var selection = this.get('selection');

			if ( selection.has( model ) ) {
				// If the model is the single model, remove it.
				// If it is not the same as the single model,
				// it now becomes the single model.
				selection[ selection.single() === model ? 'remove' : 'single' ]( model );
			} else {
				selection.add( model ).single();
			}

			return this;
		}
	});


	// wp.media.controller.Upload
	// ---------------------------
	media.controller.Upload = media.controller.Library.extend({
		defaults: _.defaults({
			id: 'upload'
		}, media.controller.Library.prototype.defaults ),

		initialize: function() {
			var library = this.get('library');

			// If a `library` attribute isn't provided, create a new
			// `Attachments` collection that observes (and thereby receives
			// all uploading) attachments.
			if ( ! library ) {
				library = new Attachments();
				library.props.set({
					orderby: 'date',
					order:   'ASC'
				});
				library.observe( wp.Uploader.queue );
				this.set( 'library', library );
			}

			media.controller.Library.prototype.initialize.apply( this, arguments );
		}

	});

	// wp.media.controller.Gallery
	// ---------------------------
	media.controller.Gallery = media.controller.Library.extend({
		defaults: {
			id:         'gallery',
			multiple:   false,
			describe:   true,
			title:      l10n.createGalleryTitle,
			edge:       199,
			editing:    false
		},

		toolbar: function() {
			this.frame.toolbar( new media.view.Toolbar.Gallery({
				controller: this.frame,
				state:      this
			}) );
		},

		sidebar: function() {
			var frame = this.frame;

			// Sidebar.
			frame.sidebar( new media.view.Sidebar({
				controller: frame
			}) );

			this.details();
			frame.sidebar().add({
				settings: new media.view.Settings.Gallery({
					controller: frame,
					model:      this.get('library').props,
					priority:   40
				}).render()
			});
		},

		content: function() {
			this.frame.content( new media.view.Attachments({
				controller: this.frame,
				collection: this.get('library'),
				model:      this,
				sortable:   true,
				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: media.view.Attachment.Gallery
			}).render() );
		}
	});

	media.controller.GalleryAddImages = media.controller.Library.extend({
		defaults: {
			id:       'gallery:add',
			multiple: true,
			title:    l10n.createGalleryTitle
		},

		initialize: function() {
			if ( ! this.get('library') )
				this.set( 'library', media.query({ type: 'image' }) );
			return media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		toolbar: function() {
			var frame = this.frame;

			frame.toolbar( new media.view.Toolbar.GalleryAddImages({
				controller: frame,
				state:      this
			}) );
		},

		// Leave the sidebar.
		sidebar: function() {}
	});

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	/**
	 * wp.media.view.Frame
	 */
	media.view.Frame = Backbone.View.extend({
		tagName:   'div',
		className: 'media-frame',
		template:  media.template('media-frame'),

		initialize: function() {
			_.defaults( this.options, {
				state:     'upload',
				title:     '',
				selection: [],
				library:   {},
				modal:     true,
				multiple:  false,
				uploader:  true,
				editing:   false
			});

			this.createSelection();
			this.createSubviews();
			this.createStates();
		},

		render: function() {
			var els = [  this.menu().el, this.content().el, this.sidebar().el, this.toolbar().el ];

			if ( this.modal )
				this.modal.render();

			// Detach any views that will be rebound to maintain event bindings.
			this.$el.children().filter( els ).detach();
			this.$el.empty().append( els );

			// Render the window uploader if it exists.
			if ( this.uploader )
				this.uploader.render().$el.appendTo( this.$el );

			return this;
		},

		createSelection: function() {
			var controller = this,
				selection = this.options.selection;

			if ( ! (selection instanceof media.model.Selection) ) {
				selection = this.options.selection = new media.model.Selection( selection, {
					multiple: this.options.multiple
				});
			}
		},

		createStates: function() {
			var options = this.options,
				menus = {
					landing: new media.view.Menu.Landing({
						controller: this
					})
				};

			// Create the default `states` collection.
			this.states = new Backbone.Collection();

			// Ensure states have a reference to the frame.
			this.states.on( 'add', function( model ) {
				model.frame = this;
			}, this );

			// Add the default states.
			this.states.add([
				new media.controller.Library({
					selection: options.selection,
					library:   media.query( options.library ),
					multiple:  this.options.multiple,
					menu:      menus.landing
				}),
				new media.controller.Upload({
					multiple: this.options.multiple,
					menu:     menus.landing
				}),
				new media.controller.Gallery({
					library: options.selection,
					editing: options.editing
				}),
				new media.controller.GalleryAddImages()
			]);

			// Set the default state.
			this.state( options.state );
		},

		createSubviews: function() {
			// Initialize a stub view for each subview region.
			_.each(['menu','content','sidebar','toolbar'], function( subview ) {
				this[ '_' + subview ] = new Backbone.View({
					tagName:   'div',
					className: 'media-' + subview
				});
			}, this );

			// Initialize modal container view.
			if ( this.options.modal ) {
				this.modal = new media.view.Modal({
					controller: this,
					$content:   this.$el,
					title:      this.options.title
				});
			}

			// Initialize window-wide uploader.
			if ( this.options.uploader ) {
				this.uploader = new media.view.UploaderWindow({
					uploader: {
						dropzone: this.modal ? this.modal.$el : this.$el
					}
				});
			}
		}
	});

	// Make the `Frame` a `StateMachine`.
	_.extend( media.view.Frame.prototype, media.controller.StateMachine.prototype );

	// Create methods to fetch and replace individual subviews.
	_.each(['menu','content','sidebar','toolbar'], function( subview ) {
		media.view.Frame.prototype[ subview ] = function( view ) {
			var previous = this[ '_' + subview ];

			if ( ! view )
				return previous;

			if ( view === previous )
				return;

			view.$el.addClass( 'media-' + subview );

			// Remove the hide class.
			this.$el.removeClass( 'hide-' + subview );

			if ( previous.destroy )
				previous.destroy();
			previous.undelegateEvents();
			previous.$el.replaceWith( view.$el );
			this[ '_' + subview ] = view;
		};
	});

	// Map some of the modal's methods to the frame.
	_.each(['open','close','attach','detach'], function( method ) {
		media.view.Frame.prototype[ method ] = function( view ) {
			if ( this.modal )
				this.modal[ method ].apply( this.modal, arguments );
			return this;
		};
	});

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
				container: document.body,
				title:     ''
			});
		},

		render: function() {
			// Ensure content div exists.
			this.options.$content = this.options.$content || $('<div />');

			// Detach the content element from the DOM to prevent
			// `this.$el.html()` from garbage collecting its events.
			this.options.$content.detach();

			this.$el.html( this.template({
				title: this.options.title
			}) );

			this.options.$content.addClass('media-modal-content');
			this.$('.media-modal').append( this.options.$content );
			return this;
		},

		attach: function() {
			this.$el.appendTo( this.options.container );
			this.controller.trigger( 'attach', this.controller );
			return this;
		},

		detach: function() {
			this.$el.detach();
			this.controller.trigger( 'detach', this.controller );
			return this;
		},

		open: function() {
			this.$el.show();
			this.controller.trigger( 'open', this.controller );
			return this;
		},

		close: function() {
			this.$el.hide();
			this.controller.trigger( 'close', this.controller );
			return this;
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

	// wp.media.view.UploaderWindow
	// ----------------------------
	media.view.UploaderWindow = Backbone.View.extend({
		tagName:   'div',
		className: 'uploader-window',
		template:  media.template('uploader-window'),

		initialize: function() {
			var uploader;

			this.controller = this.options.controller;

			this.$browser = $('<a href="#" class="browser" />').hide().appendTo('body');

			uploader = this.options.uploader = _.defaults( this.options.uploader || {}, {
				dropzone:  this.$el,
				browser:   this.$browser,
				params:    {}
			});

			if ( uploader.dropzone ) {
				// Ensure the dropzone is a jQuery collection.
				if ( ! (uploader.dropzone instanceof $) )
					uploader.dropzone = $( uploader.dropzone );

				// Attempt to initialize the uploader whenever the dropzone is hovered.
				uploader.dropzone.one( 'mouseenter dragenter', _.bind( this.maybeInitUploader, this ) );
			}
		},

		render: function() {
			this.maybeInitUploader();
			this.$el.html( this.template( this.options ) );
			return this;
		},

		refresh: function() {
			if ( this.uploader )
				this.uploader.refresh();
		},

		maybeInitUploader: function() {
			var $id, dropzone;

			// If the uploader already exists or the body isn't in the DOM, bail.
			if ( this.uploader || ! this.$el.closest('body').length )
				return;

			$id = $('#post_ID');
			if ( $id.length )
				this.options.uploader.params.post_id = $id.val();

			this.uploader = new wp.Uploader( this.options.uploader );

			dropzone = this.uploader.dropzone;
			dropzone.on( 'dropzone:enter', _.bind( this.show, this ) );
			dropzone.on( 'dropzone:leave', _.bind( this.hide, this ) );
		},

		show: function() {
			var $el = this.$el.show();

			// Ensure that the animation is triggered by waiting until
			// the transparent element is painted into the DOM.
			_.defer( function() {
				$el.css({ opacity: 1 });
			});
		},

		hide: function() {
			var $el = this.$el.css({ opacity: 0 });

			media.transition( $el ).done( function() {
				// Transition end events are subject to race conditions.
				// Make sure that the value is set as intended.
				if ( '0' === $el.css('opacity') )
					$el.hide();
			});
		}
	});

	media.view.UploaderInline = Backbone.View.extend({
		tagName:   'div',
		className: 'uploader-inline',
		template:  media.template('uploader-inline'),

		initialize: function() {
			this.controller = this.options.controller;

			if ( ! this.options.$browser )
				this.options.$browser = this.controller.uploader.$browser;

			// Track uploading attachments.
			wp.Uploader.queue.on( 'add remove reset change:percent', this.renderUploadProgress, this );
		},

		destroy: function() {
			wp.Uploader.queue.off( 'add remove reset change:percent', this.renderUploadProgress, this );
		},

		render: function() {
			var $browser = this.options.$browser,
				$placeholder;

			this.renderUploadProgress();
			this.$el.html( this.template( this.options ) );

			$placeholder = this.$('.browser');
			$browser.text( $placeholder.text() );
			$browser[0].className = $placeholder[0].className;
			$placeholder.replaceWith( $browser.show() );

			this.$bar = this.$('.media-progress-bar div');
			return this;
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
	 * wp.media.view.Toolbar
	 */
	media.view.Toolbar = Backbone.View.extend({
		tagName:   'div',
		className: 'media-toolbar',

		initialize: function() {
			this.controller = this.options.controller;

			this._views     = {};
			this.$primary   = $('<div class="media-toolbar-primary" />').prependTo( this.$el );
			this.$secondary = $('<div class="media-toolbar-secondary" />').prependTo( this.$el );

			if ( this.options.items )
				this.add( this.options.items, { silent: true });

			if ( ! this.options.silent )
				this.render();
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
			this.$primary.html( _.pluck( views.primary || [], 'el' ) );
			this.$secondary.html( _.pluck( views.secondary || [], 'el' ) );

			return this;
		},

		add: function( id, view, options ) {
			options = options || {};

			// Accept an object with an `id` : `view` mapping.
			if ( _.isObject( id ) ) {
				_.each( id, function( view, id ) {
					this.add( id, view, { silent: true });
				}, this );

				if ( ! options.silent )
					this.render();
				return this;
			}

			if ( ! ( view instanceof Backbone.View ) ) {
				view.classes = [ id ].concat( view.classes || [] );
				view = new media.view.Button( view ).render();
			}

			view.controller = view.controller || this.controller;

			this._views[ id ] = view;
			if ( ! options.silent )
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

	// wp.media.view.Toolbar.PostLibrary
	// ---------------------------------
	media.view.Toolbar.PostLibrary = media.view.Toolbar.extend({
		initialize: function() {
			var state = this.options.state,
				selection = state.get('selection'),
				controller = this.options.controller;

			this.options.items = {
				selection: new media.view.Selection({
					controller: controller,
					collection: selection,
					priority:   -40
				}).render(),

				'create-new-gallery': {
					style:    'primary',
					text:     l10n.createNewGallery,
					priority: 40,

					click: function() {
						this.controller.state('gallery');
					}
				},

				'insert-into-post': new media.view.ButtonGroup({
					priority: 30,
					classes:  'dropdown-flip-x dropdown-flip-y',
					buttons:  [
						{
							text:  l10n.insertIntoPost,
							click: function() {
								controller.close();
								state.trigger( 'insert', selection );
								selection.clear();
							}
						},
						{
							classes:  ['down-arrow'],
							dropdown: new media.view.Settings.AttachmentDisplay().render().$el,

							click: function( event ) {
								var $el = this.$el;

								if ( ! $( event.target ).closest('.dropdown').length )
									$el.toggleClass('active');

								// Stop the event from propagating further so we can bind
								// a one-time event to the body (and ensure that a click
								// on the dropdown won't trigger said event).
								event.stopPropagation();

								if ( $el.is(':visible') ) {
									$(document.body).one( 'click', function() {
										$el.removeClass('active');
									});
								}
							}
						}
					]
				}).render(),

				'add-to-gallery': {
					text:     l10n.addToGallery,
					priority: 20
				}
			};

			media.view.Toolbar.prototype.initialize.apply( this, arguments );
			this.visibility();
		},

		visibility: function() {
			var state = this.options.state,
				selection = state.get('selection'),
				controller = this.options.controller,
				count = selection.length,
				showGallery;

			// Check if every attachment in the selection is an image.
			showGallery = count > 1 && selection.all( function( attachment ) {
				return 'image' === attachment.get('type');
			});

			this.get('create-new-gallery').$el.toggle( showGallery );
			insert = this.get('insert-into-post');
			_.each( insert.buttons, function( button ) {
				button.model.set( 'style', showGallery ? '' : 'primary' );
			});

			_.first( insert.buttons ).model.set( 'disabled', ! count );
		}
	});

	// wp.media.view.Toolbar.Gallery
	// -----------------------------
	media.view.Toolbar.Gallery = media.view.Toolbar.extend({
		initialize: function() {
			var state = this.options.state,
				editing = state.get('editing'),
				library = state.get('library'),
				controller = this.options.controller;

			this.options.items = {
				update: {
					style:    'primary',
					text:     editing ? l10n.updateGallery : l10n.insertGallery,
					priority: 40,
					click:    function() {
						controller.close();
						state.trigger( 'update', library );
						controller.get('library').get('selection').clear();
						controller.state('library');
					}
				},

				addImages: {
					text:     l10n.addImages,
					priority: -40,

					click: function() {
						controller.get('gallery:add').set( 'selection', new media.model.Selection( library.models, {
							props:    library.props.toJSON(),
							multiple: true
						}) );
						controller.state('gallery:add');
					}
				},

				cancel: {
					text:     l10n.cancel,
					priority: -60,

					click: function() {
						if ( editing )
							controller.close();
						else
							controller.state('library');
					}
				}
			};

			media.view.Toolbar.prototype.initialize.apply( this, arguments );
		}
	});

	// wp.media.view.Toolbar.GalleryAddImages
	// -----------------------------
	media.view.Toolbar.GalleryAddImages = media.view.Toolbar.extend({
		initialize: function() {
			var state = this.options.state,
				editing = state.get('editing'),
				library = state.get('library'),
				controller = this.options.controller;

			this.options.items = {
				update: {
					style:    'primary',
					text:     l10n.continueEditing,
					priority: 40,

					click: function() {
						controller.get('gallery').set( 'library', state.get('selection') );
						controller.state('gallery');
					}
				},

				cancel: {
					text:     l10n.cancel,
					priority: -60,

					click: function() {
						controller.state('gallery');
					}
				}
			};

			media.view.Toolbar.prototype.initialize.apply( this, arguments );
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
			text:     '',
			style:    '',
			size:     'large',
			disabled: false
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

			if ( this.options.dropdown )
				this.options.dropdown.addClass('dropdown');

			this.model.on( 'change', this.render, this );
		},

		render: function() {
			var classes = [ 'button', this.className ],
				model = this.model.toJSON();

			if ( model.style )
				classes.push( 'button-' + model.style );

			if ( model.size )
				classes.push( 'button-' + model.size );

			classes = _.uniq( classes.concat( this.options.classes ) );
			this.el.className = classes.join(' ');

			this.$el.attr( 'disabled', model.disabled );

			// Detach the dropdown.
			if ( this.options.dropdown )
				this.options.dropdown.detach();

			this.$el.text( this.model.get('text') );

			if ( this.options.dropdown )
				this.$el.append( this.options.dropdown );

			return this;
		},

		click: function( event ) {
			event.preventDefault();
			if ( this.options.click && ! this.model.get('disabled') )
				this.options.click.apply( this, arguments );
		}
	});

	/**
	 * wp.media.view.ButtonGroup
	 */
	media.view.ButtonGroup = Backbone.View.extend({
		tagName:   'div',
		className: 'button-group button-large media-button-group',

		initialize: function() {
			this.buttons = _.map( this.options.buttons || [], function( button ) {
				if ( button instanceof Backbone.View )
					return button;
				else
					return new media.view.Button( button ).render();
			});

			delete this.options.buttons;

			if ( this.options.classes )
				this.$el.addClass( this.options.classes );
		},

		render: function() {
			this.$el.html( $( _.pluck( this.buttons, 'el' ) ).detach() );
			return this;
		}
	});

	/**
	 * wp.media.view.PriorityList
	 */

	media.view.PriorityList = Backbone.View.extend({
		tagName:   'div',

		initialize: function() {
			this.controller = this.options.controller;
			this._views     = {};

			this.add( _.extend( {}, this.views, this.options.views ), { silent: true });
			delete this.views;
			delete this.options.views;

			if ( ! this.options.silent )
				this.render();
		},

		render: function() {
			var els = _( this._views ).chain().sortBy( function( view ) {
					return view.options.priority || 10;
				}).pluck('el').value();

			// Make sure to detach the elements we want to reuse.
			// Otherwise, `jQuery.html()` will unbind their events.
			$( els ).detach();

			this.$el.html( els );
			return this;
		},

		add: function( id, view, options ) {
			options = options || {};

			// Accept an object with an `id` : `view` mapping.
			if ( _.isObject( id ) ) {
				_.each( id, function( view, id ) {
					this.add( id, view, { silent: true });
				}, this );

				if ( ! options.silent )
					this.render();
				return this;
			}

			if ( ! (view instanceof Backbone.View) )
				view = this.toView( view, id, options );

			view.controller = view.controller || this.controller;

			this._views[ id ] = view;
			if ( ! options.silent )
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
		},

		toView: function( options ) {
			return new Backbone.View( options );
		}
	});


	/**
	 * wp.media.view.Menu
	 */
	media.view.Menu = media.view.PriorityList.extend({
		tagName:   'ul',
		className: 'media-menu',

		toView: function( options, id ) {
			options = options || {};
			options.id = id;
			return new media.view.MenuItem( options ).render();
		},

		select: function( id ) {
			var view = this.get( id );

			if ( ! view )
				return;

			this.deselect();
			view.$el.addClass('active');
		},

		deselect: function() {
			this.$el.children().removeClass('active');
		}
	});

	media.view.MenuItem = Backbone.View.extend({
		tagName:   'li',
		className: 'media-menu-item',

		events: {
			'click': 'toState'
		},

		toState: function() {
			this.controller.state( this.options.id );
		},

		render: function() {
			var options = this.options;

			if ( options.text )
				this.$el.text( options.text );
			else if ( options.html )
				this.$el.html( options.html );

			return this;
		}
	});

	media.view.Menu.Landing = media.view.Menu.extend({
		views: {
			upload: {
				text: l10n.uploadFilesTitle,
				priority: 20
			},
			library: {
				text: l10n.mediaLibraryTitle,
				priority: 40
			},
			separateLibrary: new Backbone.View({
				className: 'separator',
				priority: 60
			}),
			embed: {
				text: l10n.embedFromUrlTitle,
				priority: 80
			}
		}
	});

	/**
	 * wp.media.view.Sidebar
	 */
	media.view.Sidebar = media.view.PriorityList.extend({
		className: 'media-sidebar'
	});

	/**
	 * wp.media.view.Attachment
	 */
	media.view.Attachment = Backbone.View.extend({
		tagName:   'li',
		className: 'attachment',
		template:  media.template('attachment'),

		events: {
			'mousedown .attachment-preview': 'toggleSelection',
			'change .describe':          'describe'
		},

		buttons: {},

		initialize: function() {
			this.controller = this.options.controller;

			this.model.on( 'change:sizes change:uploading change:caption change:title', this.render, this );
			this.model.on( 'change:percent', this.progress, this );
			this.model.on( 'add', this.select, this );
			this.model.on( 'remove', this.deselect, this );

			// Update the model's details view.
			this.model.on( 'selection:single selection:unsingle', this.details, this );
			this.details( this.model, this.controller.state().get('selection') );

			// Prevent default navigation on all links.
			this.$el.on( 'click', 'a', this.preventDefault );
		},

		destroy: function() {
			this.model.off( null, null, this );
		},

		render: function() {
			var attachment = this.model.toJSON(),
				options = _.defaults( this.model.toJSON(), {
					orientation: 'landscape',
					uploading:   false,
					type:        '',
					subtype:     '',
					icon:        '',
					filename:    '',
					caption:     '',
					title:       ''
				});

			options.buttons  = this.buttons;
			options.describe = this.controller.state().get('describe');

			if ( 'image' === options.type )
				_.extend( options, this.imageSize() );

			this.$el.html( this.template( options ) );

			if ( options.uploading )
				this.$bar = this.$('.media-progress-bar div');
			else
				delete this.$bar;

			// Check if the model is selected.
			if ( this.selected() )
				this.select();

			return this;
		},

		progress: function() {
			if ( this.$bar && this.$bar.length )
				this.$bar.width( this.model.get('percent') + '%' );
		},

		toggleSelection: function( event ) {
			this.controller.state().toggleSelection( this.model );
		},

		selected: function() {
			var selection = this.controller.state().get('selection');
			if ( selection )
				return selection.has( this.model );
		},

		select: function( model, collection ) {
			var selection = this.controller.state().get('selection');

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) )
				return;

			this.$el.addClass('selected');
		},

		deselect: function( model, collection ) {
			var selection = this.controller.state().get('selection');

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) )
				return;

			this.$el.removeClass('selected');
		},

		details: function( model, collection ) {
			var selection = this.controller.state().get('selection'),
				details;

			if ( selection !== collection )
				return;

			details = selection.single();
			this.$el.toggleClass( 'details', details === this.model );
		},

		preventDefault: function( event ) {
			event.preventDefault();
		},

		imageSize: function( size ) {
			var sizes = this.model.get('sizes');

			size = size || 'medium';

			// Use the provided image size if possible.
			if ( sizes && sizes[ size ] ) {
				return _.clone( sizes[ size ] );
			} else {
				return {
					url:         this.model.get('url'),
					width:       this.model.get('width'),
					height:      this.model.get('height'),
					orientation: this.model.get('orientation')
				};
			}
		},

		describe: function( event ) {
			if ( 'image' === this.model.get('type') )
				this.model.save( 'caption', event.target.value );
			else
				this.model.save( 'title', event.target.value );
		}
	});

	/**
	 * wp.media.view.Attachment.Library
	 */
	media.view.Attachment.Library = media.view.Attachment.extend({
		className: 'attachment library'
	});

	/**
	 * wp.media.view.Attachment.Gallery
	 */
	media.view.Attachment.Gallery = media.view.Attachment.extend({
		buttons: {
			close: true
		},

		events: (function() {
			var events = _.clone( media.view.Attachment.prototype.events );
			events['click .close'] = 'removeFromGallery';
			return events;
		}()),

		removeFromGallery: function( event ) {
			// Stop propagation so the model isn't selected.
			event.stopPropagation();

			this.controller.state().get('library').remove( this.model );
		}
	});

	/**
	 * wp.media.view.Attachments
	 */
	media.view.Attachments = Backbone.View.extend({
		tagName:   'ul',
		className: 'attachments',
		template:  media.template('attachments-css'),

		events: {
			'scroll': 'scroll'
		},

		initialize: function() {
			this.controller = this.options.controller;
			this.el.id = _.uniqueId('__attachments-view-');

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

			this.collection.on( 'reset', this.render, this );

			// Throttle the scroll handler.
			this.scroll = _.chain( this.scroll ).bind( this ).throttle( this.options.refreshSensitivity ).value();

			this.initSortable();

			_.bindAll( this, 'css' );
			this.model.on( 'change:edge change:gutter', this.css, this );
			this._resizeCss = _.debounce( _.bind( this.css, this ), this.refreshSensitivity );
			$(window).on( 'resize.attachments', this._resizeCss );
			this.css();
		},

		destroy: function() {
			this.collection.off( 'add remove reset', null, this );
			this.model.off( 'change:edge change:gutter', this.css, this );
			$(window).off( 'resize.attachments', this._resizeCss );
		},

		css: function() {
			var $css = $( '#' + this.el.id + '-css' );

			if ( $css.length )
				$css.remove();

			media.view.Attachments.$head().append( this.template({
				id:     this.el.id,
				edge:   this.edge(),
				gutter: this.model.get('gutter')
			}) );
		},

		edge: function() {
			var edge = this.model.get('edge'),
				gutter, width, columns;

			if ( ! this.$el.is(':visible') )
				return edge;


			gutter  = this.model.get('gutter') * 2;
			width   = this.$el.width() - gutter;
			columns = Math.ceil( width / ( edge + gutter ) );
			edge = Math.floor( ( width - ( columns * gutter ) ) / columns );
			return edge;
		},

		initSortable: function() {
			var collection = this.collection,
				from;

			if ( ! this.options.sortable || ! $.fn.sortable )
				return;

			this.$el.sortable({
				// If the `collection` has a `comparator`, disable sorting.
				disabled: !! collection.comparator,

				// Prevent attachments from being dragged outside the bounding
				// box of the list.
				containment: this.$el,

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
				this.$el.sortable( 'option', 'disabled', !! collection.comparator );
			}, this );
		},

		render: function() {
			// If there are no elements, load some.
			if ( ! this.collection.length ) {
				this.collection.more().done( this.scroll );
				this.$el.empty();
				return this;
			}

			// Otherwise, create all of the Attachment views, and replace
			// the list in a single DOM operation.
			this.$el.html( this.collection.map( function( attachment ) {
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

			children = this.$el.children();

			if ( children.length > index )
				children.eq( index ).before( view.$el );
			else
				this.$el.append( view.$el );
		},

		remove: function( attachment, index ) {
			var children = this.$el.children();
			if ( children.length )
				children.eq( index ).detach();
		},

		scroll: function( event ) {
			// @todo: is this still necessary?
			if ( ! this.$el.is(':visible') )
				return;

			if ( this.el.scrollHeight < this.el.scrollTop + ( this.el.clientHeight * this.options.refreshThreshold ) ) {
				this.collection.more().done( this.scroll );
			}
		}
	}, {
		$head: (function() {
			var $head;
			return function() {
				return $head = $head || $('head');
			};
		}())
	});

	/**
	 * wp.media.view.Search
	 */
	media.view.Search = Backbone.View.extend({
		tagName:   'input',
		className: 'search',

		attributes: {
			type:        'text',
			placeholder: l10n.search
		},

		events: {
			'keyup': 'search'
		},

		render: function() {
			this.el.value = this.model.escape('search');
			return this;
		},

		search: function( event ) {
			if ( event.target.value )
				this.model.set( 'search', event.target.value );
			else
				this.model.unset('search');
		}
	});



	/**
	 * wp.media.view.AttachmentsBrowser
	 */
	media.view.AttachmentsBrowser = Backbone.View.extend({
		tagName:   'div',
		className: 'attachments-browser',

		initialize: function() {
			this.controller = this.options.controller;

			_.defaults( this.options, {
				search: true,
				upload: false,
				total:  true
			});

			this.toolbar = new media.view.Toolbar({
				controller: this.controller
			});

			if ( this.options.search ) {
				this.toolbar.add( 'search', new media.view.Search({
					controller: this.controller,
					model:      this.collection.props,
					priority:   -40
				}) );
			}

			this.attachments = new media.view.Attachments({
				controller: this.controller,
				collection: this.collection,
				model:      this.model,
				sortable:   this.options.sortable,
				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: media.view.Attachment.Library
			});
		},

		render: function() {
			this.toolbar.$el.detach();
			this.attachments.$el.detach();
			this.$el.html([ this.toolbar.render().el, this.attachments.render().el ]);
			return this;
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
			_.defaults( this.options, {
				clearable: true
			});

			this.controller = this.options.controller;
			this.collection.on( 'add change:url remove', this.render, this );
			this.render();
		},

		render: function() {
			var options = _.clone( this.options ),
				last, sizes, amount;

			// If nothing is selected, display nothing.
			if ( ! this.collection.length ) {
				this.$el.empty();
				return this;
			}

			options.count = this.collection.length;
			last  = this.collection.last();
			sizes = last.get('sizes');

			if ( 'image' === last.get('type') )
				options.thumbnail = ( sizes && sizes.thumbnail ) ? sizes.thumbnail.url : last.get('url');
			else
				options.thumbnail =  last.get('icon');

			this.$el.html( this.template( options ) );
			return this;
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.clear();
		}
	});

	/**
	 * wp.media.view.Selection
	 */
	media.view.Selection = Backbone.View.extend({
		tagName:   'div',
		className: 'media-selection',
		template:  media.template('media-selection'),

		events: {
			'click .clear-selection': 'clear'
		},

		initialize: function() {
			_.defaults( this.options, {
				clearable: true
			});

			this.controller = this.options.controller;
			this.attachments = new media.view.Attachments({
				controller: this.controller,
				collection: this.collection,
				sortable:   true,
				model:      new Backbone.Model({
					edge:   40,
					gutter: 5
				}),

				// The single `Attachment` view to be used in the `Attachments` view.
				AttachmentView: media.view.Attachment.Selection
			});

			this.collection.on( 'add remove reset', this.refresh, this );
		},

		destroy: function() {
			this.collection.off( 'add remove reset', this.refresh, this );
		},

		render: function() {
			this.attachments.$el.detach();
			this.attachments.render();

			this.$el.html( this.template( this.options ) );

			this.$('.selection-view').replaceWith( this.attachments.$el );
			this.refresh();
			return this;
		},

		refresh: function() {
			// If the selection hasn't been rendered, bail.
			if ( ! this.$el.children().length )
				return;

			// If nothing is selected, display nothing.
			this.$el.toggleClass( 'empty', ! this.collection.length );
			this.$('.count').text( this.collection.length + ' ' + l10n.selected );
		},

		clear: function( event ) {
			event.preventDefault();
			this.collection.clear();
		}
	});


	/**
	 * wp.media.view.Attachment.Selection
	 */
	media.view.Attachment.Selection = media.view.Attachment.extend({
		// On click, just select the model, instead of removing the model from
		// the selection.
		toggleSelection: function() {
			this.controller.state().get('selection').single( this.model );
		}
	});


	/**
	 * wp.media.view.Settings
	 */
	media.view.Settings = Backbone.View.extend({
		tagName:   'div',
		className: 'attachment-display-settings',
		template:  media.template('attachment-display-settings'),

		events: {
			'click button':    'updateHandler',
			'change input':    'updateHandler',
			'change select':   'updateHandler',
			'change textarea': 'updateHandler'
		},

		settings: {},

		initialize: function() {
			var settings = this.settings;

			this.model = this.model || new Backbone.Model();

			_.each( settings, function( setting, key ) {
				if ( setting.name )
					this.model.set( key, getUserSetting( setting.name, setting.fallback ) );
				else
					this.model.set( key, this.model.get( key ) || setting.fallback );
			}, this );

			this.model.validate = function( attrs ) {
				return _.any( attrs, function( value, key ) {
					return ! settings[ key ] || ! _.contains( settings[ key ].accepts, value );
				});
			};

			this.model.on( 'change', function( model, options ) {
				if ( ! options.changes )
					return;

				_.each( _.keys( options.changes ), function( key ) {
					if ( settings[ key ] && settings[ key ].name )
						setUserSetting( settings[ key ].name, model.get( key ) );
				});
			}, this );

			this.model.on( 'change', this.updateChanges, this );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );

			// Select the correct values.
			_( this.model.attributes ).chain().keys().each( this.update, this );
			return this;
		},

		update: function( key ) {
			var setting = this.settings[ key ],
				$setting = this.$('[data-setting="' + key + '"]'),
				$buttons;

			if ( ! setting )
				return;

			if ( 'select' === setting.type ) {
				$setting.find('[value="' + this.model.get( key ) + '"]').attr( 'selected', true );
			} else {
				$buttons = $setting.find('button').removeClass('active');
				$buttons.filter( '[value="' + this.model.get( key ) + '"]' ).addClass('active');
			}
		},

		updateHandler: function( event ) {
			var $setting = $( event.target ).closest('[data-setting]');

			event.preventDefault();

			if ( $setting.length )
				this.model.set( $setting.data('setting'), event.target.value );
		},

		updateChanges: function( model, options ) {
			if ( options.changes )
				_( options.changes ).chain().keys().each( this.update, this );
		}
	});

	/**
	 * wp.media.view.Settings.AttachmentDisplay
	 */
	media.view.Settings.AttachmentDisplay = media.view.Settings.extend({
		className: 'attachment-display-settings',
		template:  media.template('attachment-display-settings'),

		settings: {
			align: {
				accepts:  ['left','center','right','none'],
				name:     'align',
				fallback: 'none'
			},
			link: {
				accepts:  ['post','file','none'],
				name:     'urlbutton',
				fallback: 'post'
			},
			size: {
				// @todo: Dynamically generate these.
				accepts:  ['thumbnail','medium','large','full'],
				name:     'imgsize',
				fallback: 'medium'
			}
		}
	});

	/**
	 * wp.media.view.Settings.Gallery
	 */
	media.view.Settings.Gallery = media.view.Settings.extend({
		className: 'gallery-settings',
		template:  media.template('gallery-settings'),

		settings: {
			columns: {
				accepts:  _.invoke( _.range( 1, 10 ), 'toString' ),
				fallback: '3',
				type:     'select'
			},
			link: {
				accepts:  ['post','file'],
				fallback: 'post'
			}
		}
	});

	/**
	 * wp.media.view.Attachment.Details
	 */
	media.view.Attachment.Details = media.view.Attachment.extend({
		tagName:   'div',
		className: 'attachment-details',
		template:  media.template('attachment-details'),

		events: {
			'change .describe': 'describe'
		}
	});
}(jQuery));