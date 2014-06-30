/* global tinymce, MediaElementPlayer, WPPlaylistView */
/**
 * Note: this API is "experimental" meaning that it will probably change
 * in the next few releases based on feedback from 3.9.0.
 * If you decide to use it, please follow the development closely.
 */

// Ensure the global `wp` object exists.
window.wp = window.wp || {};

( function( $ ) {
	'use strict';

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
	 *
	 * @param {Object} [options={}]
	 */
	wp.mce.View = function( options ) {
		options = options || {};
		this.type = options.type;
		_.extend( this, _.pick( options, viewOptions ) );
		this.initialize.apply( this, arguments );
	};

	_.extend( wp.mce.View.prototype, {
		initialize: function() {},
		getHtml: function() {},
		render: function() {
			this.setContent(
				'<div class="toolbar">' +
					( _.isFunction( views[ this.type ].edit ) ? '<div class="dashicons dashicons-edit edit"></div>' : '' ) +
					'<div class="dashicons dashicons-no-alt remove"></div>' +
				'</div>' +
				'<div class="wpview-content">' +
					this.getHtml() +
				'</div>' +
				( this.overlay ? '<div class="wpview-overlay"></div>' : '' ) +
				// The <ins> is used to mark the end of the wrapper div (has to be the last child node).
				// Needed when comparing the content as string for preventing extra undo levels.
				'<ins data-wpview-end="1"></ins>',
				function( self, editor, node ) {
					$( self ).trigger( 'ready', [ editor, node ] );
				}
			);
		},
		unbind: function() {},
		setContent: function( html, callback, replace ) {
			_.each( tinymce.editors, function( editor ) {
				var self = this;
				if ( editor.plugins.wpview ) {
					$( editor.getBody() )
					.find( '[data-wpview-text="' + this.encodedText + '"]' )
					.each( function ( i, element ) {
						var contentWrap = $( element ).children( '.wpview-content' ),
							wrap = element;

						if ( contentWrap.length ) {
							element = contentWrap = contentWrap[0];
						}

						if ( _.isString( html ) ) {
							if ( replace ) {
								element = editor.dom.replace( editor.dom.createFragment( html ), wrap );
							} else {
								editor.dom.setHTML( element, html );
							}
						} else {
							if ( replace ) {
								element = editor.dom.replace( html, wrap );
							} else {
								element.appendChild( html );
							}
						}

						if ( _.isFunction( callback ) ) {
							callback( self, editor, $( element ).children( '.wpview-content' )[0] );
						}
					} );
				}
			}, this );
		},
		setError: function( message, dashicon ) {
			this.setContent(
				'<div class="wpview-error">' +
					'<div class="dashicons dashicons-' + ( dashicon ? dashicon : 'no' ) + '"></div>' +
					'<p>' + message + '</p>' +
				'</div>'
			);
		}
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
			var defaultConstructor = {
					type: type,
					View: {},
					toView: function( content ) {
						var match = wp.shortcode.next( this.type, content );

						if ( ! match ) {
							return;
						}

						return {
							index: match.index,
							content: match.content,
							options: {
								shortcode: match.shortcode
							}
						};
					}
				};

			constructor = _.defaults( constructor, defaultConstructor );
			constructor.View = wp.mce.View.extend( constructor.View );

			views[ type ] = constructor;
		},

		/**
		 * wp.mce.views.get( id )
		 *
		 * Returns a TinyMCE view constructor.
		 *
		 * @param type
		 */
		get: function( type ) {
			return views[ type ];
		},

		/**
		 * wp.mce.views.unregister( type )
		 *
		 * Unregisters a TinyMCE view.
		 *
		 * @param type
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
		 * @param content
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
				viewOptions.type = viewType;
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
				viewOptions.type = view.type;
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

	wp.mce.views.register( 'gallery', {
		View: {
			template: media.template( 'editor-gallery' ),

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
		},

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
	} );

	/**
	 * These are base methods that are shared by the audio and video shortcode's MCE controller.
	 *
	 * @mixin
	 */
	wp.mce.av = {
		loaded: false,

		View: _.extend( {}, wp.media.mixin, {
			overlay: true,

			initialize: function( options ) {
				this.players = [];
				this.shortcode = options.shortcode;
				_.bindAll( this, 'setPlayer', 'pausePlayers' );
				$( this ).on( 'ready', this.setPlayer );
				$( this ).on( 'ready', function( event, editor ) {
					editor.on( 'hide', this.pausePlayers );
				} );
				$( document ).on( 'media:edit', this.pausePlayers );
			},

			/**
			 * Creates the player instance for the current node
			 *
			 * @global MediaElementPlayer
			 *
			 * @param {Event} event
			 * @param {Object} editor
			 * @param {HTMLElement} node
			 */
			setPlayer: function( event, editor, node ) {
				var self = this,
					media;

				media = $( node ).find( '.wp-' +  this.shortcode.tag + '-shortcode' );

				if ( ! this.isCompatible( media ) ) {
					media.closest( '.wpview-wrap' ).addClass( 'wont-play' );
					media.replaceWith( '<p>' + media.find( 'source' ).eq(0).prop( 'src' ) + '</p>' );
					return;
				} else {
					media.closest( '.wpview-wrap' ).removeClass( 'wont-play' );
					if ( this.ua.is( 'ff' ) ) {
						media.prop( 'preload', 'metadata' );
					} else {
						media.prop( 'preload', 'none' );
					}
				}

				media = wp.media.view.MediaDetails.prepareSrc( media.get(0) );

				setTimeout( function() {
					wp.mce.av.loaded = true;
					self.players.push( new MediaElementPlayer( media, self.mejsSettings ) );
				}, wp.mce.av.loaded ? 10 : 500 );
			},

			/**
			 * Pass data to the View's Underscore template and return the compiled output
			 *
			 * @returns {string}
			 */
			getHtml: function() {
				var attrs = this.shortcode.attrs.named;
				attrs.content = this.shortcode.content;

				return this.template({ model: _.defaults(
					attrs,
					wp.media[ this.shortcode.tag ].defaults )
				});
			},

			unbind: function() {
				this.unsetPlayers();
			}
		} ),

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
			var media = wp.media[ this.type ],
				self = this,
				frame, data, callback;

			$( document ).trigger( 'media:edit' );

			data = window.decodeURIComponent( $( node ).attr('data-wpview-text') );
			frame = media.edit( data );
			frame.on( 'close', function() {
				frame.detach();
			} );

			callback = function( selection ) {
				var shortcode = wp.media[ self.type ].shortcode( selection ).string();
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
	 * TinyMCE handler for the video shortcode
	 *
	 * @mixes wp.mce.av
	 */
	wp.mce.views.register( 'video', _.extend( {}, wp.mce.av, {
		state: 'video-details',
		View: _.extend( {}, wp.mce.av.View, {
			template: media.template( 'editor-video' )
		} )
	} ) );

	/**
	 * TinyMCE handler for the audio shortcode
	 *
	 * @mixes wp.mce.av
	 */
	wp.mce.views.register( 'audio', _.extend( {}, wp.mce.av, {
		state: 'audio-details',
		View: _.extend( {}, wp.mce.av.View, {
			template: media.template( 'editor-audio' )
		} )
	} ) );

	/**
	 * TinyMCE handler for the playlist shortcode
	 *
	 * @mixes wp.mce.av
	 */
	wp.mce.views.register( 'playlist', _.extend( {}, wp.mce.av, {
		state: ['playlist-edit', 'video-playlist-edit'],
		View: _.extend( {}, wp.media.mixin, {
			template:  media.template( 'editor-playlist' ),
			overlay: true,

			initialize: function( options ) {
				this.players = [];
				this.data = {};
				this.attachments = [];
				this.shortcode = options.shortcode;

				$( this ).on( 'ready', function( event, editor ) {
					editor.on( 'hide', this.pausePlayers );
				} );
				$( document ).on( 'media:edit', this.pausePlayers );

				this.fetch();

				$( this ).on( 'ready', this.setPlaylist );
			},

			/**
			 * Asynchronously fetch the shortcode's attachments
			 */
			fetch: function() {
				this.attachments = wp.media.playlist.attachments( this.shortcode );
				this.dfd = this.attachments.more().done( _.bind( this.render, this ) );
			},

			setPlaylist: function( event, editor, element ) {
				if ( ! this.data.tracks ) {
					return;
				}

				this.players.push( new WPPlaylistView( {
					el: $( element ).find( '.wp-playlist' ).get( 0 ),
					metadata: this.data
				} ).player );
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
			},

			unbind: function() {
				this.unsetPlayers();
			}
		} )
	} ) );

	/**
	 * TinyMCE handler for the embed shortcode
	 */
	wp.mce.embedView = _.extend( {}, wp.media.mixin, {
		overlay: true,
		initialize: function( options ) {
			this.players = [];
			this.content = options.content;
			this.fetching = false;
			this.parsed = false;
			this.original = options.url || options.shortcode.string();

			if ( options.url ) {
				this.shortcode = '[embed]' + options.url + '[/embed]';
			} else {
				this.shortcode = options.shortcode.string();
			}

			_.bindAll( this, 'setHtml', 'setNode', 'fetch' );
			$( this ).on( 'ready', this.setNode );
		},
		unbind: function() {
			var self = this;
			_.each( this.players, function ( player ) {
				player.pause();
				self.removePlayer( player );
			} );
			this.players = [];
		},
		setNode: function () {
			if ( this.parsed ) {
				this.setHtml( this.parsed );
				this.parseMediaShortcodes();
			} else if ( ! this.fetching ) {
				this.fetch();
			}
		},
		fetch: function () {
			var self = this;

			this.fetching = true;

			wp.ajax.send( 'parse-embed', {
				data: {
					post_ID: $( '#post_ID' ).val() || 0,
					shortcode: this.shortcode
				}
			} )
			.always( function() {
				self.fetching = false;
			} )
			.done( function( response ) {
				if ( response ) {
					self.parsed = response;
					self.setHtml( response );
				}
			} )
			.fail( function( response ) {
				if ( response && response.message ) {
					if ( ( response.type === 'not-embeddable' && self.type === 'embed' ) ||
						response.type === 'not-ssl' ) {

						self.setError( response.message, 'admin-media' );
					} else {
						self.setContent( '<p>' + self.original + '</p>', null, true );
					}
				} else if ( response && response.statusText ) {
					self.setError( response.statusText, 'admin-media' );
				}
			} );
		},
		/* jshint scripturl: true */
		setHtml: function ( content ) {
			var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
				iframe, iframeDoc, i, resize,
				dom = tinymce.DOM;

			if ( content.indexOf( '<script' ) !== -1 ) {
				iframe = dom.create( 'iframe', {
					src: tinymce.Env.ie ? 'javascript:""' : '',
					frameBorder: '0',
					allowTransparency: 'true',
					style: {
						width: '100%',
						display: 'block'
					}
				} );

				this.setContent( iframe );
				iframeDoc = iframe.contentWindow.document;

				iframeDoc.open();
				iframeDoc.write(
					'<!DOCTYPE html>' +
					'<html>' +
						'<head>' +
							'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' +
						'</head>' +
						'<body>' +
							content +
						'</body>' +
					'</html>'
				);
				iframeDoc.close();

				resize = function() {
					$( iframe ).height( $( iframeDoc ).height() );
				};

				if ( MutationObserver ) {
					new MutationObserver( _.debounce( function() {
						resize();
					}, 100 ) )
					.observe( iframeDoc.body, {
						attributes: true,
						childList: true,
						subtree: true
					} );
				} else {
					for ( i = 1; i < 6; i++ ) {
						setTimeout( resize, i * 700 );
					}
				}
			} else {
				this.setContent( content );
			}

			this.parseMediaShortcodes();
		},
		parseMediaShortcodes: function () {
			var self = this;
			$( '.wp-audio-shortcode, .wp-video-shortcode', this.node ).each( function ( i, element ) {
				self.players.push( new MediaElementPlayer( element, self.mejsSettings ) );
			} );
		},
		getHtml: function() {
			return '';
		}
	} );

	wp.mce.embedMixin = {
		View: wp.mce.embedView,
		edit: function( node ) {
			var embed = media.embed,
				self = this,
				frame,
				data,
				isURL = 'embedURL' === this.type;

			$( document ).trigger( 'media:edit' );

			data = window.decodeURIComponent( $( node ).attr('data-wpview-text') );
			frame = embed.edit( data, isURL );
			frame.on( 'close', function() {
				frame.detach();
			} );
			frame.state( 'embed' ).props.on( 'change:url', function (model, url) {
				if ( ! url ) {
					return;
				}
				frame.state( 'embed' ).metadata = model.toJSON();
			} );
			frame.state( 'embed' ).on( 'select', function() {
				var shortcode;

				if ( isURL ) {
					shortcode = frame.state( 'embed' ).metadata.url;
				} else {
					shortcode = embed.shortcode( frame.state( 'embed' ).metadata ).string();
				}
				$( node ).attr( 'data-wpview-text', window.encodeURIComponent( shortcode ) );
				wp.mce.views.refreshView( self, shortcode );
				frame.detach();
			} );
			frame.open();
		}
	};

	wp.mce.views.register( 'embed', _.extend( {}, wp.mce.embedMixin ) );

	wp.mce.views.register( 'embedURL', _.extend( {}, wp.mce.embedMixin, {
		toView: function( content ) {
			var re = /(?:^|<p>)(https?:\/\/[^\s"]+?)(?:<\/p>\s*|$)/gi,
				match = re.exec( tinymce.trim( content ) );

			if ( ! match ) {
				return;
			}

			return {
				index: match.index,
				content: match[0],
				options: {
					url: match[1]
				}
			};
		}
	} ) );

}(jQuery));
