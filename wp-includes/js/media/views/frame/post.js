/*globals wp, _ */

/**
 * wp.media.view.MediaFrame.Post
 *
 * The frame for manipulating media on the Edit Post page.
 *
 * @class
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 * @mixes wp.media.controller.StateMachine
 */
var View = require( '../view.js' ),
	Select = require( './select.js' ),
	Library = require( '../../controllers/library.js' ),
	Embed = require( '../embed.js' ),
	EditImage = require( '../edit-image.js' ),
	EditSelection = require( '../attachment/edit-selection.js' ),
	Toolbar = require( '../toolbar.js' ),
	ToolbarEmbed = require( '../toolbar/embed.js' ),
	PlaylistSettings = require( '../settings/playlist.js' ),
	AttachmentsBrowser = require( '../attachments/browser.js' ),
	SelectionView = require( '../selection.js' ),
	EmbedController = require( '../../controllers/embed.js' ),
	EditImageController = require( '../../controllers/edit-image.js' ),
	GalleryEditController = require( '../../controllers/gallery-edit.js' ),
	GalleryAddController = require( '../../controllers/gallery-add.js' ),
	CollectionEditController = require( '../../controllers/collection-edit.js' ),
	CollectionAddController = require( '../../controllers/collection-add.js' ),
	FeaturedImageController = require( '../../controllers/featured-image.js' ),
	l10n = wp.media.view.l10n,
	Post;

Post = Select.extend({
	initialize: function() {
		this.counts = {
			audio: {
				count: wp.media.view.settings.attachmentCounts.audio,
				state: 'playlist'
			},
			video: {
				count: wp.media.view.settings.attachmentCounts.video,
				state: 'video-playlist'
			}
		};

		_.defaults( this.options, {
			multiple:  true,
			editing:   false,
			state:    'insert',
			metadata:  {}
		});

		// Call 'initialize' directly on the parent class.
		Select.prototype.initialize.apply( this, arguments );
		this.createIframeStates();

	},

	/**
	 * Create the default states.
	 */
	createStates: function() {
		var options = this.options;

		this.states.add([
			// Main states.
			new Library({
				id:         'insert',
				title:      l10n.insertMediaTitle,
				priority:   20,
				toolbar:    'main-insert',
				filterable: 'all',
				library:    wp.media.query( options.library ),
				multiple:   options.multiple ? 'reset' : false,
				editable:   true,

				// If the user isn't allowed to edit fields,
				// can they still edit it locally?
				allowLocalEdits: true,

				// Show the attachment display settings.
				displaySettings: true,
				// Update user settings when users adjust the
				// attachment display settings.
				displayUserSettings: true
			}),

			new Library({
				id:         'gallery',
				title:      l10n.createGalleryTitle,
				priority:   40,
				toolbar:    'main-gallery',
				filterable: 'uploaded',
				multiple:   'add',
				editable:   false,

				library:  wp.media.query( _.defaults({
					type: 'image'
				}, options.library ) )
			}),

			// Embed states.
			new EmbedController( { metadata: options.metadata } ),

			new EditImageController( { model: options.editImage } ),

			// Gallery states.
			new GalleryEditController({
				library: options.selection,
				editing: options.editing,
				menu:    'gallery'
			}),

			new GalleryAddController(),

			new Library({
				id:         'playlist',
				title:      l10n.createPlaylistTitle,
				priority:   60,
				toolbar:    'main-playlist',
				filterable: 'uploaded',
				multiple:   'add',
				editable:   false,

				library:  wp.media.query( _.defaults({
					type: 'audio'
				}, options.library ) )
			}),

			// Playlist states.
			new CollectionEditController({
				type: 'audio',
				collectionType: 'playlist',
				title:          l10n.editPlaylistTitle,
				SettingsView:   PlaylistSettings,
				library:        options.selection,
				editing:        options.editing,
				menu:           'playlist',
				dragInfoText:   l10n.playlistDragInfo,
				dragInfo:       false
			}),

			new CollectionAddController({
				type: 'audio',
				collectionType: 'playlist',
				title: l10n.addToPlaylistTitle
			}),

			new Library({
				id:         'video-playlist',
				title:      l10n.createVideoPlaylistTitle,
				priority:   60,
				toolbar:    'main-video-playlist',
				filterable: 'uploaded',
				multiple:   'add',
				editable:   false,

				library:  wp.media.query( _.defaults({
					type: 'video'
				}, options.library ) )
			}),

			new CollectionEditController({
				type: 'video',
				collectionType: 'playlist',
				title:          l10n.editVideoPlaylistTitle,
				SettingsView:   PlaylistSettings,
				library:        options.selection,
				editing:        options.editing,
				menu:           'video-playlist',
				dragInfoText:   l10n.videoPlaylistDragInfo,
				dragInfo:       false
			}),

			new CollectionAddController({
				type: 'video',
				collectionType: 'playlist',
				title: l10n.addToVideoPlaylistTitle
			})
		]);

		if ( wp.media.view.settings.post.featuredImageId ) {
			this.states.add( new FeaturedImageController() );
		}
	},

	bindHandlers: function() {
		var handlers, checkCounts;

		Select.prototype.bindHandlers.apply( this, arguments );

		this.on( 'activate', this.activate, this );

		// Only bother checking media type counts if one of the counts is zero
		checkCounts = _.find( this.counts, function( type ) {
			return type.count === 0;
		} );

		if ( typeof checkCounts !== 'undefined' ) {
			this.listenTo( wp.media.model.Attachments.all, 'change:type', this.mediaTypeCounts );
		}

		this.on( 'menu:create:gallery', this.createMenu, this );
		this.on( 'menu:create:playlist', this.createMenu, this );
		this.on( 'menu:create:video-playlist', this.createMenu, this );
		this.on( 'toolbar:create:main-insert', this.createToolbar, this );
		this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
		this.on( 'toolbar:create:main-playlist', this.createToolbar, this );
		this.on( 'toolbar:create:main-video-playlist', this.createToolbar, this );
		this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
		this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

		handlers = {
			menu: {
				'default': 'mainMenu',
				'gallery': 'galleryMenu',
				'playlist': 'playlistMenu',
				'video-playlist': 'videoPlaylistMenu'
			},

			content: {
				'embed':          'embedContent',
				'edit-image':     'editImageContent',
				'edit-selection': 'editSelectionContent'
			},

			toolbar: {
				'main-insert':      'mainInsertToolbar',
				'main-gallery':     'mainGalleryToolbar',
				'gallery-edit':     'galleryEditToolbar',
				'gallery-add':      'galleryAddToolbar',
				'main-playlist':	'mainPlaylistToolbar',
				'playlist-edit':	'playlistEditToolbar',
				'playlist-add':		'playlistAddToolbar',
				'main-video-playlist': 'mainVideoPlaylistToolbar',
				'video-playlist-edit': 'videoPlaylistEditToolbar',
				'video-playlist-add': 'videoPlaylistAddToolbar'
			}
		};

		_.each( handlers, function( regionHandlers, region ) {
			_.each( regionHandlers, function( callback, handler ) {
				this.on( region + ':render:' + handler, this[ callback ], this );
			}, this );
		}, this );
	},

	activate: function() {
		// Hide menu items for states tied to particular media types if there are no items
		_.each( this.counts, function( type ) {
			if ( type.count < 1 ) {
				this.menuItemVisibility( type.state, 'hide' );
			}
		}, this );
	},

	mediaTypeCounts: function( model, attr ) {
		if ( typeof this.counts[ attr ] !== 'undefined' && this.counts[ attr ].count < 1 ) {
			this.counts[ attr ].count++;
			this.menuItemVisibility( this.counts[ attr ].state, 'show' );
		}
	},

	// Menus
	/**
	 * @param {wp.Backbone.View} view
	 */
	mainMenu: function( view ) {
		view.set({
			'library-separator': new View({
				className: 'separator',
				priority: 100
			})
		});
	},

	menuItemVisibility: function( state, visibility ) {
		var menu = this.menu.get();
		if ( visibility === 'hide' ) {
			menu.hide( state );
		} else if ( visibility === 'show' ) {
			menu.show( state );
		}
	},
	/**
	 * @param {wp.Backbone.View} view
	 */
	galleryMenu: function( view ) {
		var lastState = this.lastState(),
			previous = lastState && lastState.id,
			frame = this;

		view.set({
			cancel: {
				text:     l10n.cancelGalleryTitle,
				priority: 20,
				click:    function() {
					if ( previous ) {
						frame.setState( previous );
					} else {
						frame.close();
					}

					// Keep focus inside media modal
					// after canceling a gallery
					this.controller.modal.focusManager.focus();
				}
			},
			separateCancel: new View({
				className: 'separator',
				priority: 40
			})
		});
	},

	playlistMenu: function( view ) {
		var lastState = this.lastState(),
			previous = lastState && lastState.id,
			frame = this;

		view.set({
			cancel: {
				text:     l10n.cancelPlaylistTitle,
				priority: 20,
				click:    function() {
					if ( previous ) {
						frame.setState( previous );
					} else {
						frame.close();
					}
				}
			},
			separateCancel: new View({
				className: 'separator',
				priority: 40
			})
		});
	},

	videoPlaylistMenu: function( view ) {
		var lastState = this.lastState(),
			previous = lastState && lastState.id,
			frame = this;

		view.set({
			cancel: {
				text:     l10n.cancelVideoPlaylistTitle,
				priority: 20,
				click:    function() {
					if ( previous ) {
						frame.setState( previous );
					} else {
						frame.close();
					}
				}
			},
			separateCancel: new View({
				className: 'separator',
				priority: 40
			})
		});
	},

	// Content
	embedContent: function() {
		var view = new Embed({
			controller: this,
			model:      this.state()
		}).render();

		this.content.set( view );

		if ( ! wp.media.isTouchDevice ) {
			view.url.focus();
		}
	},

	editSelectionContent: function() {
		var state = this.state(),
			selection = state.get('selection'),
			view;

		view = new AttachmentsBrowser({
			controller: this,
			collection: selection,
			selection:  selection,
			model:      state,
			sortable:   true,
			search:     false,
			date:       false,
			dragInfo:   true,

			AttachmentView: EditSelection
		}).render();

		view.toolbar.set( 'backToLibrary', {
			text:     l10n.returnToLibrary,
			priority: -100,

			click: function() {
				this.controller.content.mode('browse');
			}
		});

		// Browse our library of attachments.
		this.content.set( view );

		// Trigger the controller to set focus
		this.trigger( 'edit:selection', this );
	},

	editImageContent: function() {
		var image = this.state().get('image'),
			view = new EditImage( { model: image, controller: this } ).render();

		this.content.set( view );

		// after creating the wrapper view, load the actual editor via an ajax call
		view.loadEditor();

	},

	// Toolbars

	/**
	 * @param {wp.Backbone.View} view
	 */
	selectionStatusToolbar: function( view ) {
		var editable = this.state().get('editable');

		view.set( 'selection', new SelectionView({
			controller: this,
			collection: this.state().get('selection'),
			priority:   -40,

			// If the selection is editable, pass the callback to
			// switch the content mode.
			editable: editable && function() {
				this.controller.content.mode('edit-selection');
			}
		}).render() );
	},

	/**
	 * @param {wp.Backbone.View} view
	 */
	mainInsertToolbar: function( view ) {
		var controller = this;

		this.selectionStatusToolbar( view );

		view.set( 'insert', {
			style:    'primary',
			priority: 80,
			text:     l10n.insertIntoPost,
			requires: { selection: true },

			/**
			 * @fires wp.media.controller.State#insert
			 */
			click: function() {
				var state = controller.state(),
					selection = state.get('selection');

				controller.close();
				state.trigger( 'insert', selection ).reset();
			}
		});
	},

	/**
	 * @param {wp.Backbone.View} view
	 */
	mainGalleryToolbar: function( view ) {
		var controller = this;

		this.selectionStatusToolbar( view );

		view.set( 'gallery', {
			style:    'primary',
			text:     l10n.createNewGallery,
			priority: 60,
			requires: { selection: true },

			click: function() {
				var selection = controller.state().get('selection'),
					edit = controller.state('gallery-edit'),
					models = selection.where({ type: 'image' });

				edit.set( 'library', new wp.media.model.Selection( models, {
					props:    selection.props.toJSON(),
					multiple: true
				}) );

				this.controller.setState('gallery-edit');

				// Keep focus inside media modal
				// after jumping to gallery view
				this.controller.modal.focusManager.focus();
			}
		});
	},

	mainPlaylistToolbar: function( view ) {
		var controller = this;

		this.selectionStatusToolbar( view );

		view.set( 'playlist', {
			style:    'primary',
			text:     l10n.createNewPlaylist,
			priority: 100,
			requires: { selection: true },

			click: function() {
				var selection = controller.state().get('selection'),
					edit = controller.state('playlist-edit'),
					models = selection.where({ type: 'audio' });

				edit.set( 'library', new wp.media.model.Selection( models, {
					props:    selection.props.toJSON(),
					multiple: true
				}) );

				this.controller.setState('playlist-edit');

				// Keep focus inside media modal
				// after jumping to playlist view
				this.controller.modal.focusManager.focus();
			}
		});
	},

	mainVideoPlaylistToolbar: function( view ) {
		var controller = this;

		this.selectionStatusToolbar( view );

		view.set( 'video-playlist', {
			style:    'primary',
			text:     l10n.createNewVideoPlaylist,
			priority: 100,
			requires: { selection: true },

			click: function() {
				var selection = controller.state().get('selection'),
					edit = controller.state('video-playlist-edit'),
					models = selection.where({ type: 'video' });

				edit.set( 'library', new wp.media.model.Selection( models, {
					props:    selection.props.toJSON(),
					multiple: true
				}) );

				this.controller.setState('video-playlist-edit');

				// Keep focus inside media modal
				// after jumping to video playlist view
				this.controller.modal.focusManager.focus();
			}
		});
	},

	featuredImageToolbar: function( toolbar ) {
		this.createSelectToolbar( toolbar, {
			text:  l10n.setFeaturedImage,
			state: this.options.state
		});
	},

	mainEmbedToolbar: function( toolbar ) {
		toolbar.view = new ToolbarEmbed({
			controller: this
		});
	},

	galleryEditToolbar: function() {
		var editing = this.state().get('editing');
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				insert: {
					style:    'primary',
					text:     editing ? l10n.updateGallery : l10n.insertGallery,
					priority: 80,
					requires: { library: true },

					/**
					 * @fires wp.media.controller.State#update
					 */
					click: function() {
						var controller = this.controller,
							state = controller.state();

						controller.close();
						state.trigger( 'update', state.get('library') );

						// Restore and reset the default state.
						controller.setState( controller.options.state );
						controller.reset();
					}
				}
			}
		}) );
	},

	galleryAddToolbar: function() {
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				insert: {
					style:    'primary',
					text:     l10n.addToGallery,
					priority: 80,
					requires: { selection: true },

					/**
					 * @fires wp.media.controller.State#reset
					 */
					click: function() {
						var controller = this.controller,
							state = controller.state(),
							edit = controller.state('gallery-edit');

						edit.get('library').add( state.get('selection').models );
						state.trigger('reset');
						controller.setState('gallery-edit');
					}
				}
			}
		}) );
	},

	playlistEditToolbar: function() {
		var editing = this.state().get('editing');
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				insert: {
					style:    'primary',
					text:     editing ? l10n.updatePlaylist : l10n.insertPlaylist,
					priority: 80,
					requires: { library: true },

					/**
					 * @fires wp.media.controller.State#update
					 */
					click: function() {
						var controller = this.controller,
							state = controller.state();

						controller.close();
						state.trigger( 'update', state.get('library') );

						// Restore and reset the default state.
						controller.setState( controller.options.state );
						controller.reset();
					}
				}
			}
		}) );
	},

	playlistAddToolbar: function() {
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				insert: {
					style:    'primary',
					text:     l10n.addToPlaylist,
					priority: 80,
					requires: { selection: true },

					/**
					 * @fires wp.media.controller.State#reset
					 */
					click: function() {
						var controller = this.controller,
							state = controller.state(),
							edit = controller.state('playlist-edit');

						edit.get('library').add( state.get('selection').models );
						state.trigger('reset');
						controller.setState('playlist-edit');
					}
				}
			}
		}) );
	},

	videoPlaylistEditToolbar: function() {
		var editing = this.state().get('editing');
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				insert: {
					style:    'primary',
					text:     editing ? l10n.updateVideoPlaylist : l10n.insertVideoPlaylist,
					priority: 140,
					requires: { library: true },

					click: function() {
						var controller = this.controller,
							state = controller.state(),
							library = state.get('library');

						library.type = 'video';

						controller.close();
						state.trigger( 'update', library );

						// Restore and reset the default state.
						controller.setState( controller.options.state );
						controller.reset();
					}
				}
			}
		}) );
	},

	videoPlaylistAddToolbar: function() {
		this.toolbar.set( new Toolbar({
			controller: this,
			items: {
				insert: {
					style:    'primary',
					text:     l10n.addToVideoPlaylist,
					priority: 140,
					requires: { selection: true },

					click: function() {
						var controller = this.controller,
							state = controller.state(),
							edit = controller.state('video-playlist-edit');

						edit.get('library').add( state.get('selection').models );
						state.trigger('reset');
						controller.setState('video-playlist-edit');
					}
				}
			}
		}) );
	}
});

module.exports = Post;
