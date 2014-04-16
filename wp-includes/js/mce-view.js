/* global tinymce, MediaElementPlayer, WPPlaylistView */
/**
 * Note: this API is "experimental" meaning that it will probably change
 * in the next few releases based on feedback from 3.9.0.
 * If you decide to use it, please follow the development closely.
 */

// Ensure the global `wp` object exists.
window.wp = window.wp || {};

(function($){
	var views = {},
		instances = {},
		media = wp.media,
		viewOptions = ['encodedText'];

	// Create the `wp.mce` object if necessary.
	wp.mce = wp.mce || {};

	/**
	 * wp.mce.View
	 *
	 * A Backbone-like View constructor intended for use when rendering a TinyMCE View. The main difference is
	 * that the TinyMCE View is not tied to a particular DOM node.
	 */
	wp.mce.View = function( options ) {
		options || (options = {});
		_.extend(this, _.pick(options, viewOptions));
		this.initialize.apply(this, arguments);
	};

	_.extend( wp.mce.View.prototype, {
		initialize: function() {},
		getHtml: function() {},
		render: function() {
			var html = this.getHtml();
			// Search all tinymce editor instances and update the placeholders
			_.each( tinymce.editors, function( editor ) {
				var doc, self = this;
				if ( editor.plugins.wpview ) {
					doc = editor.getDoc();
					$( doc ).find( '[data-wpview-text="' + this.encodedText + '"]' ).each(function (i, elem) {
						var node = $( elem );
						// The <ins> is used to mark the end of the wrapper div. Needed when comparing
						// the content as string for preventing extra undo levels.
						node.html( html ).append( '<ins data-wpview-end="1"></ins>' );
						$( self ).trigger( 'ready', elem );
					});
				}
			}, this );
		},
		unbind: function() {}
	} );

	// take advantage of the Backbone extend method
	wp.mce.View.extend = Backbone.View.extend;

	/**
	 * wp.mce.views
	 *
	 * A set of utilities that simplifies adding custom UI within a TinyMCE editor.
	 * At its core, it serves as a series of converters, transforming text to a
	 * custom UI, and back again.
	 */
	wp.mce.views = {

		/**
		 * wp.mce.views.register( type, view )
		 *
		 * Registers a new TinyMCE view.
		 *
		 * @param type
		 * @param constructor
		 *
		 */
		register: function( type, constructor ) {
			views[ type ] = constructor;
		},

		/**
		 * wp.mce.views.get( id )
		 *
		 * Returns a TinyMCE view constructor.
		 */
		get: function( type ) {
			return views[ type ];
		},

		/**
		 * wp.mce.views.unregister( type )
		 *
		 * Unregisters a TinyMCE view.
		 */
		unregister: function( type ) {
			delete views[ type ];
		},

		/**
		 * wp.mce.views.unbind( editor )
		 *
		 * The editor DOM is being rebuilt, run cleanup.
		 */
		unbind: function() {
			_.each( instances, function( instance ) {
				instance.unbind();
			} );
		},

		/**
		 * toViews( content )
		 * Scans a `content` string for each view's pattern, replacing any
		 * matches with wrapper elements, and creates a new instance for
		 * every match, which triggers the related data to be fetched.
		 *
		 */
		toViews: function( content ) {
			var pieces = [ { content: content } ],
				current;

			_.each( views, function( view, viewType ) {
				current = pieces.slice();
				pieces  = [];

				_.each( current, function( piece ) {
					var remaining = piece.content,
						result;

					// Ignore processed pieces, but retain their location.
					if ( piece.processed ) {
						pieces.push( piece );
						return;
					}

					// Iterate through the string progressively matching views
					// and slicing the string as we go.
					while ( remaining && (result = view.toView( remaining )) ) {
						// Any text before the match becomes an unprocessed piece.
						if ( result.index ) {
							pieces.push({ content: remaining.substring( 0, result.index ) });
						}

						// Add the processed piece for the match.
						pieces.push({
							content: wp.mce.views.toView( viewType, result.content, result.options ),
							processed: true
						});

						// Update the remaining content.
						remaining = remaining.slice( result.index + result.content.length );
					}

					// There are no additional matches. If any content remains,
					// add it as an unprocessed piece.
					if ( remaining ) {
						pieces.push({ content: remaining });
					}
				});
			});

			return _.pluck( pieces, 'content' ).join('');
		},

		/**
		 * Create a placeholder for a particular view type
		 *
		 * @param viewType
		 * @param text
		 * @param options
		 *
		 */
		toView: function( viewType, text, options ) {
			var view = wp.mce.views.get( viewType ),
				encodedText = window.encodeURIComponent( text ),
				instance, viewOptions;


			if ( ! view ) {
				return text;
			}

			if ( ! wp.mce.views.getInstance( encodedText ) ) {
				viewOptions = options;
				viewOptions.encodedText = encodedText;
				instance = new view.View( viewOptions );
				instances[ encodedText ] = instance;
			}

			return wp.html.string({
				tag: 'div',

				attrs: {
					'class': 'wpview-wrap wpview-type-' + viewType,
					'data-wpview-text': encodedText,
					'data-wpview-type': viewType,
					'contenteditable': 'false'
				},

				content: '\u00a0'
			});
		},

		/**
		 * Refresh views after an update is made
		 *
		 * @param view {object} being refreshed
		 * @param text {string} textual representation of the view
		 */
		refreshView: function( view, text ) {
			var encodedText = window.encodeURIComponent( text ),
				viewOptions,
				result, instance;

			instance = wp.mce.views.getInstance( encodedText );

			if ( ! instance ) {
				result = view.toView( text );
				viewOptions = result.options;
				viewOptions.encodedText = encodedText;
				instance = new view.View( viewOptions );
				instances[ encodedText ] = instance;
			}

			wp.mce.views.render();
		},

		getInstance: function( encodedText ) {
			return instances[ encodedText ];
		},

		/**
		 * render( scope )
		 *
		 * Renders any view instances inside a DOM node `scope`.
		 *
		 * View instances are detected by the presence of wrapper elements.
		 * To generate wrapper elements, pass your content through
		 * `wp.mce.view.toViews( content )`.
		 */
		render: function() {
			_.each( instances, function( instance ) {
				instance.render();
			} );
		},

		edit: function( node ) {
			var viewType = $( node ).data('wpview-type'),
				view = wp.mce.views.get( viewType );

			if ( view ) {
				view.edit( node );
			}
		}
	};

	wp.mce.gallery = {
		shortcode: 'gallery',
		toView:  function( content ) {
			var match = wp.shortcode.next( this.shortcode, content );

			if ( ! match ) {
				return;
			}

			return {
				index:   match.index,
				content: match.content,
				options: {
					shortcode: match.shortcode
				}
			};
		},
		View: wp.mce.View.extend({
			className: 'editor-gallery',
			template:  media.template('editor-gallery'),

			// The fallback post ID to use as a parent for galleries that don't
			// specify the `ids` or `include` parameters.
			//
			// Uses the hidden input on the edit posts page by default.
			postID: $('#post_ID').val(),

			initialize: function( options ) {
				this.shortcode = options.shortcode;
				this.fetch();
			},

			fetch: function() {
				this.attachments = wp.media.gallery.attachments( this.shortcode, this.postID );
				this.dfd = this.attachments.more().done( _.bind( this.render, this ) );
			},

			getHtml: function() {
				var attrs = this.shortcode.attrs.named,
					attachments = false,
					options;

				// Don't render errors while still fetching attachments
				if ( this.dfd && 'pending' === this.dfd.state() && ! this.attachments.length ) {
					return;
				}

				if ( this.attachments.length ) {
					attachments = this.attachments.toJSON();

					_.each( attachments, function( attachment ) {
						if ( attachment.sizes ) {
							if ( attachment.sizes.thumbnail ) {
								attachment.thumbnail = attachment.sizes.thumbnail;
							} else if ( attachment.sizes.full ) {
								attachment.thumbnail = attachment.sizes.full;
							}
						}
					} );
				}

				options = {
					attachments: attachments,
					columns: attrs.columns ? parseInt( attrs.columns, 10 ) : 3
				};

				return this.template( options );

			}
		}),

		edit: function( node ) {
			var gallery = wp.media.gallery,
				self = this,
				frame, data;

			data = window.decodeURIComponent( $( node ).attr('data-wpview-text') );
			frame = gallery.edit( data );

			frame.state('gallery-edit').on( 'update', function( selection ) {
				var shortcode = gallery.shortcode( selection ).string();
				$( node ).attr( 'data-wpview-text', window.encodeURIComponent( shortcode ) );
				wp.mce.views.refreshView( self, shortcode );
				frame.detach();
			});
		}

	};
	wp.mce.views.register( 'gallery', wp.mce.gallery );

	/**
	 * Tiny MCE Views for Audio / Video
	 *
	 */

	/**
	 * These are base methods that are shared by each shortcode's MCE controller
	 *
	 * @mixin
	 */
	wp.mce.media = {
		loaded: false,
		/**
		 * @global wp.shortcode
		 *
		 * @param {string} content
		 * @returns {Object}
		 */
		toView:  function( content ) {
			var match = wp.shortcode.next( this.shortcode, content );

			if ( ! match ) {
				return;
			}

			return {
				index:   match.index,
				content: match.content,
				options: {
					shortcode: match.shortcode
				}
			};
		},

		/**
		 * Called when a TinyMCE view is clicked for editing.
		 * - Parses the shortcode out of the element's data attribute
		 * - Calls the `edit` method on the shortcode model
		 * - Launches the model window
		 * - Bind's an `update` callback which updates the element's data attribute
		 *   re-renders the view
		 *
		 * @param {HTMLElement} node
		 */
		edit: function( node ) {
			var media = wp.media[ this.shortcode ],
				self = this,
				frame, data, callback;

			wp.media.mixin.pauseAllPlayers();

			data = window.decodeURIComponent( $( node ).attr('data-wpview-text') );
			frame = media.edit( data );
			frame.on( 'close', function() {
				frame.detach();
			} );

			callback = function( selection ) {
				var shortcode = wp.media[ self.shortcode ].shortcode( selection ).string();
				$( node ).attr( 'data-wpview-text', window.encodeURIComponent( shortcode ) );
				wp.mce.views.refreshView( self, shortcode );
				frame.detach();
			};
			if ( _.isArray( self.state ) ) {
				_.each( self.state, function (state) {
					frame.state( state ).on( 'update', callback );
				} );
			} else {
				frame.state( self.state ).on( 'update', callback );
			}
			frame.open();
		}
	};

	/**
	 * Base View class for audio and video shortcodes
	 *
	 * @constructor
	 * @augments wp.mce.View
	 * @mixes wp.media.mixin
	 */
	wp.mce.media.View = wp.mce.View.extend({
		initialize: function( options ) {
			this.players = [];
			this.shortcode = options.shortcode;
			_.bindAll( this, 'setPlayer' );
			$(this).on( 'ready', this.setPlayer );
		},

		/**
		 * Creates the player instance for the current node
		 *
		 * @global MediaElementPlayer
		 * @global _wpmejsSettings
		 *
		 * @param {Event} e
		 * @param {HTMLElement} node
		 */
		setPlayer: function(e, node) {
			// if the ready event fires on an empty node
			if ( ! node ) {
				return;
			}

			var self = this,
				media,
				firefox = this.ua.is( 'ff' ),
				className = '.wp-' +  this.shortcode.tag + '-shortcode';

			if ( this.player ) {
				this.unsetPlayer();
			}

			media = $( node ).find( className );

			if ( ! this.isCompatible( media ) ) {
				media.closest( '.wpview-wrap' ).addClass( 'wont-play' );
				if ( ! media.parent().hasClass( 'wpview-wrap' ) ) {
					media.parent().replaceWith( media );
				}
				media.replaceWith( '<p>' + media.find( 'source' ).eq(0).prop( 'src' ) + '</p>' );
				return;
			} else {
				media.closest( '.wpview-wrap' ).removeClass( 'wont-play' );
				if ( firefox ) {
					media.prop( 'preload', 'metadata' );
				} else {
					media.prop( 'preload', 'none' );
				}
			}

			media = wp.media.view.MediaDetails.prepareSrc( media.get(0) );

			setTimeout( function() {
				wp.mce.media.loaded = true;
				self.players.push( new MediaElementPlayer( media, self.mejsSettings ) );
			}, wp.mce.media.loaded ? 10 : 500 );
		},

		/**
		 * Pass data to the View's Underscore template and return the compiled output
		 *
		 * @returns {string}
		 */
		getHtml: function() {
			var attrs = _.defaults(
				this.shortcode.attrs.named,
				wp.media[ this.shortcode.tag ].defaults
			);
			return this.template({ model: attrs });
		},

		unbind: function() {
			var self = this;
			this.pauseAllPlayers();
			_.each( this.players, function (player) {
				self.removePlayer( player );
			} );
			this.players = [];
		}
	});
	_.extend( wp.mce.media.View.prototype, wp.media.mixin );

	/**
	 * TinyMCE handler for the video shortcode
	 *
	 * @mixes wp.mce.media
	 */
	wp.mce.video = _.extend( {}, wp.mce.media, {
		shortcode: 'video',
		state: 'video-details',
		View: wp.mce.media.View.extend({
			className: 'editor-video',
			template:  media.template('editor-video')
		})
	} );
	wp.mce.views.register( 'video', wp.mce.video );

	/**
	 * TinyMCE handler for the audio shortcode
	 *
	 * @mixes wp.mce.media
	 */
	wp.mce.audio = _.extend( {}, wp.mce.media, {
		shortcode: 'audio',
		state: 'audio-details',
		View: wp.mce.media.View.extend({
			className: 'editor-audio',
			template:  media.template('editor-audio')
		})
	} );
	wp.mce.views.register( 'audio', wp.mce.audio );

	/**
	 * Base View class for playlist shortcodes
	 *
	 * @constructor
	 * @augments wp.mce.View
	 * @mixes wp.media.mixin
	 */
	wp.mce.media.PlaylistView = wp.mce.View.extend({
		className: 'editor-playlist',
		template:  media.template('editor-playlist'),

		initialize: function( options ) {
			this.data = {};
			this.attachments = [];
			this.shortcode = options.shortcode;
			_.bindAll( this, 'setPlayer' );
			$(this).on('ready', this.setNode);
		},

		/**
		 * Set the element context for the view, and then fetch the playlist's
		 *   associated attachments.
		 *
		 * @param {Event} e
		 * @param {HTMLElement} node
		 */
		setNode: function(e, node) {
			this.node = node;
			this.fetch();
		},

		/**
		 * Asynchronously fetch the shortcode's attachments
		 */
		fetch: function() {
			this.attachments = wp.media.playlist.attachments( this.shortcode );
			this.attachments.more().done( this.setPlayer );
		},

		/**
		 * Get the HTML for the view (which also set's the data), replace the
		 *   current HTML, and then invoke the WPPlaylistView instance to render
		 *   the playlist in the editor
		 *
		 * @global WPPlaylistView
		 * @global tinymce.editors
		 */
		setPlayer: function() {
			var p,
				html = this.getHtml(),
				t = this.encodedText,
				self = this;

			this.unsetPlayer();

			_.each( tinymce.editors, function( editor ) {
				var doc;
				if ( editor.plugins.wpview ) {
					doc = editor.getDoc();
					$( doc ).find( '[data-wpview-text="' + t + '"]' ).each(function(i, elem) {
						var node = $( elem );
						node.html( html );
						self.node = elem;
					});
				}
			}, this );

			if ( ! this.data.tracks ) {
				return;
			}

			p = new WPPlaylistView({
				el: $( self.node ).find( '.wp-playlist' ).get(0),
				metadata: this.data
			});

			this.player = p._player;
		},

		/**
		 * Set the data that will be used to compile the Underscore template,
		 *  compile the template, and then return it.
		 *
		 * @returns {string}
		 */
		getHtml: function() {
			var data = this.shortcode.attrs.named,
				model = wp.media.playlist,
				options,
				attachments,
				tracks = [];

			// Don't render errors while still fetching attachments
			if ( this.dfd && 'pending' === this.dfd.state() && ! this.attachments.length ) {
				return;
			}

			_.each( model.defaults, function( value, key ) {
				data[ key ] = model.coerce( data, key );
			});

			options = {
				type: data.type,
				style: data.style,
				tracklist: data.tracklist,
				tracknumbers: data.tracknumbers,
				images: data.images,
				artists: data.artists
			};

			if ( ! this.attachments.length ) {
				return this.template( options );
			}

			attachments = this.attachments.toJSON();

			_.each( attachments, function( attachment ) {
				var size = {}, resize = {}, track = {
					src : attachment.url,
					type : attachment.mime,
					title : attachment.title,
					caption : attachment.caption,
					description : attachment.description,
					meta : attachment.meta
				};

				if ( 'video' === data.type ) {
					size.width = attachment.width;
					size.height = attachment.height;
					if ( media.view.settings.contentWidth ) {
						resize.width = media.view.settings.contentWidth - 22;
						resize.height = Math.ceil( ( size.height * resize.width ) / size.width );
						if ( ! options.width ) {
							options.width = resize.width;
							options.height = resize.height;
						}
					} else {
						if ( ! options.width ) {
							options.width = attachment.width;
							options.height = attachment.height;
						}
					}
					track.dimensions = {
						original : size,
						resized : _.isEmpty( resize ) ? size : resize
					};
				} else {
					options.width = 400;
				}

				track.image = attachment.image;
				track.thumb = attachment.thumb;

				tracks.push( track );
			} );

			options.tracks = tracks;
			this.data = options;

			return this.template( options );
		}
	});
	_.extend( wp.mce.media.PlaylistView.prototype, wp.media.mixin );

	/**
	 * TinyMCE handler for the playlist shortcode
	 *
	 * @mixes wp.mce.media
	 */
	wp.mce.playlist = _.extend( {}, wp.mce.media, {
		shortcode: 'playlist',
		state: ['playlist-edit', 'video-playlist-edit'],
		View: wp.mce.media.PlaylistView
	} );
	wp.mce.views.register( 'playlist', wp.mce.playlist );
}(jQuery));
