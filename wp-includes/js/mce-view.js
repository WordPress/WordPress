/* global tinymce */
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
		mediaWindows = [],
		windowIdx = 0,
		waitInterval = 50,
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
		getHtml: function() {
			return '';
		},
		loadingPlaceholder: function() {
			return '' +
				'<div class="loading-placeholder">' +
					'<div class="dashicons dashicons-admin-media"></div>' +
					'<div class="wpview-loading"><ins></ins></div>' +
				'</div>';
		},
		render: function( force ) {
			if ( force || ! this.rendered() ) {
				this.unbind();

				this.setContent(
					'<p class="wpview-selection-before">\u00a0</p>' +
					'<div class="wpview-body" contenteditable="false">' +
						'<div class="toolbar mce-arrow-down">' +
							( _.isFunction( views[ this.type ].edit ) ? '<div class="dashicons dashicons-edit edit"></div>' : '' ) +
							'<div class="dashicons dashicons-no remove"></div>' +
						'</div>' +
						'<div class="wpview-content wpview-type-' + this.type + '">' +
							( this.getHtml() || this.loadingPlaceholder() ) +
						'</div>' +
						( this.overlay ? '<div class="wpview-overlay"></div>' : '' ) +
					'</div>' +
					'<p class="wpview-selection-after">\u00a0</p>',
					'wrap'
				);

				$( this ).trigger( 'ready' );

				this.rendered( true );
			}
		},
		unbind: function() {},
		getEditors: function( callback ) {
			var editors = [];

			_.each( tinymce.editors, function( editor ) {
				if ( editor.plugins.wpview ) {
					if ( callback ) {
						callback( editor );
					}

					editors.push( editor );
				}
			}, this );

			return editors;
		},
		getNodes: function( callback ) {
			var nodes = [],
				self = this;

			this.getEditors( function( editor ) {
				$( editor.getBody() )
				.find( '[data-wpview-text="' + self.encodedText + '"]' )
				.each( function ( i, node ) {
					if ( callback ) {
						callback( editor, node, $( node ).find( '.wpview-content' ).get( 0 ) );
					}

					nodes.push( node );
				} );
			} );

			return nodes;
		},
		setContent: function( html, option ) {
			this.getNodes( function ( editor, node, content ) {
				var el = ( option === 'wrap' || option === 'replace' ) ? node : content,
					insert = html;

				if ( _.isString( insert ) ) {
					insert = editor.dom.createFragment( insert );
				}

				if ( option === 'replace' ) {
					editor.dom.replace( insert, el );
				} else {
					el.innerHTML = '';
					el.appendChild( insert );
				}
			} );
		},
		/* jshint scripturl: true */
		setIframes: function ( head, body ) {
			var MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver,
				importStyles = this.type === 'video' || this.type === 'audio' || this.type === 'playlist';

			if ( head || body.indexOf( '<script' ) !== -1 ) {
				if ( body.indexOf( '[' ) !== -1 && body.indexOf( ']' ) !== -1 ) {
					var shortcodesRegExp = new RegExp( '\\[\\/?(?:' + window.mceViewL10n.shortcodes.join( '|' ) + ')[^\\]]*?\\]', 'g' );
					// Escape tags inside shortcode previews.
					body = body.replace( shortcodesRegExp, function( match ) {
						return match.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
					} );
				}

				this.getNodes( function ( editor, node, content ) {
					var dom = editor.dom,
						styles = '',
						bodyClasses = editor.getBody().className || '',
						iframe, iframeDoc, i, resize;

					content.innerHTML = '';
					head = head || '';

					if ( importStyles ) {
						if ( ! wp.mce.views.sandboxStyles ) {
							tinymce.each( dom.$( 'link[rel="stylesheet"]', editor.getDoc().head ), function( link ) {
								if ( link.href && link.href.indexOf( 'skins/lightgray/content.min.css' ) === -1 &&
									link.href.indexOf( 'skins/wordpress/wp-content.css' ) === -1 ) {

									styles += dom.getOuterHTML( link ) + '\n';
								}
							});

							wp.mce.views.sandboxStyles = styles;
						} else {
							styles = wp.mce.views.sandboxStyles;
						}
					}

					// Seems Firefox needs a bit of time to insert/set the view nodes, or the iframe will fail
					// especially when switching Text => Visual.
					setTimeout( function() {
						iframe = dom.add( content, 'iframe', {
							src: tinymce.Env.ie ? 'javascript:""' : '',
							frameBorder: '0',
							allowTransparency: 'true',
							scrolling: 'no',
							'class': 'wpview-sandbox',
							style: {
								width: '100%',
								display: 'block'
							}
						} );

						iframeDoc = iframe.contentWindow.document;

						iframeDoc.open();
						iframeDoc.write(
							'<!DOCTYPE html>' +
							'<html>' +
								'<head>' +
									'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' +
									head +
									styles +
									'<style>' +
										'html {' +
											'background: transparent;' +
											'padding: 0;' +
											'margin: 0;' +
										'}' +
										'body#wpview-iframe-sandbox {' +
											'background: transparent;' +
											'padding: 1px 0 !important;' +
											'margin: -1px 0 0 !important;' +
										'}' +
										'body#wpview-iframe-sandbox:before,' +
										'body#wpview-iframe-sandbox:after {' +
											'display: none;' +
											'content: "";' +
										'}' +
									'</style>' +
								'</head>' +
								'<body id="wpview-iframe-sandbox" class="' + bodyClasses + '">' +
									body +
								'</body>' +
							'</html>'
						);
						iframeDoc.close();

						resize = function() {
							// Make sure the iframe still exists.
							iframe.contentWindow && $( iframe ).height( $( iframeDoc.body ).height() );
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

						if ( importStyles ) {
							editor.on( 'wp-body-class-change', function() {
								iframeDoc.body.className = editor.getBody().className;
							});
						}
					}, waitInterval );
				});
			} else {
				this.setContent( body );
			}
		},
		setError: function( message, dashicon ) {
			this.setContent(
				'<div class="wpview-error">' +
					'<div class="dashicons dashicons-' + ( dashicon ? dashicon : 'no' ) + '"></div>' +
					'<p>' + message + '</p>' +
				'</div>'
			);
		},
		rendered: function( value ) {
			var notRendered;

			this.getNodes( function( editor, node ) {
				if ( value != null ) {
					$( node ).data( 'rendered', value === true );
				} else {
					notRendered = notRendered || ! $( node ).data( 'rendered' );
				}
			} );

			return ! notRendered;
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
					'class': 'wpview-wrap',
					'data-wpview-text': encodedText,
					'data-wpview-type': viewType
				},

				content: '\u00a0'
			});
		},

		/**
		 * Refresh views after an update is made
		 *
		 * @param view {object} being refreshed
		 * @param text {string} textual representation of the view
		 * @param force {Boolean} whether to force rendering
		 */
		refreshView: function( view, text, force ) {
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

			instance.render( force );
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
		render: function( force ) {
			_.each( instances, function( instance ) {
				instance.render( force );
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
				var self = this;

				this.attachments = wp.media.gallery.attachments( this.shortcode, this.postID );
				this.dfd = this.attachments.more().done( function() {
					self.render( true );
				} );
			},

			getHtml: function() {
				var attrs = this.shortcode.attrs.named,
					attachments = false,
					options;

				// Don't render errors while still fetching attachments
				if ( this.dfd && 'pending' === this.dfd.state() && ! this.attachments.length ) {
					return '';
				}

				if ( this.attachments.length ) {
					attachments = this.attachments.toJSON();

					_.each( attachments, function( attachment ) {
						if ( attachment.sizes ) {
							if ( attrs.size && attachment.sizes[ attrs.size ] ) {
								attachment.thumbnail = attachment.sizes[ attrs.size ];
							} else if ( attachment.sizes.thumbnail ) {
								attachment.thumbnail = attachment.sizes.thumbnail;
							} else if ( attachment.sizes.full ) {
								attachment.thumbnail = attachment.sizes.full;
							}
						}
					} );
				}

				options = {
					attachments: attachments,
					columns: attrs.columns ? parseInt( attrs.columns, 10 ) : wp.media.galleryDefaults.columns
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
				var shortcode = gallery.shortcode( selection ).string(), force;
				$( node ).attr( 'data-wpview-text', window.encodeURIComponent( shortcode ) );
				force = ( data !== shortcode );
				wp.mce.views.refreshView( self, shortcode, force );
			});

			frame.on( 'close', function() {
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
		View: {
			overlay: true,

			action: 'parse-media-shortcode',

			initialize: function( options ) {
				var self = this;

				this.shortcode = options.shortcode;

				_.bindAll( this, 'setIframes', 'setNodes', 'fetch', 'stopPlayers' );
				$( this ).on( 'ready', this.setNodes );

				$( document ).on( 'media:edit', this.stopPlayers );

				this.fetch();

				this.getEditors( function( editor ) {
					editor.on( 'hide', function () {
						mediaWindows = [];
						windowIdx = 0;
						self.stopPlayers();
					} );
				});
			},

			pauseOtherWindows: function ( win ) {
				_.each( mediaWindows, function ( mediaWindow ) {
					if ( mediaWindow.sandboxId !== win.sandboxId ) {
						_.each( mediaWindow.mejs.players, function ( player ) {
							player.pause();
						} );
					}
				} );
			},

			iframeLoaded: function (win) {
				return _.bind( function () {
					var callback;
					if ( ! win.mejs || _.isEmpty( win.mejs.players ) ) {
						return;
					}

					win.sandboxId = windowIdx;
					windowIdx++;
					mediaWindows.push( win );

					callback = _.bind( function () {
						this.pauseOtherWindows( win );
					}, this );

					if ( ! _.isEmpty( win.mejs.MediaPluginBridge.pluginMediaElements ) ) {
						_.each( win.mejs.MediaPluginBridge.pluginMediaElements, function ( mediaElement ) {
							mediaElement.addEventListener( 'play', callback );
						} );
					}

					_.each( win.mejs.players, function ( player ) {
						$( player.node ).on( 'play', callback );
					}, this );
				}, this );
			},

			listenToSandboxes: function () {
				_.each( this.getNodes(), function ( node ) {
					var win, iframe = $( '.wpview-sandbox', node ).get( 0 );
					if ( iframe && ( win = iframe.contentWindow ) ) {
						$( win ).load( _.bind( this.iframeLoaded( win ), this ) );
					}
				}, this );
			},

			deferredListen: function () {
				window.setTimeout( _.bind( this.listenToSandboxes, this ), this.getNodes().length * waitInterval );
			},

			setNodes: function () {
				if ( this.parsed ) {
					this.setIframes( this.parsed.head, this.parsed.body );
					this.deferredListen();
				} else {
					this.fail();
				}
			},

			fetch: function () {
				var self = this;

				wp.ajax.send( this.action, {
					data: {
						post_ID: $( '#post_ID' ).val() || 0,
						type: this.shortcode.tag,
						shortcode: this.shortcode.string()
					}
				} )
				.done( function( response ) {
					if ( response ) {
						self.parsed = response;
						self.setIframes( response.head, response.body );
						self.deferredListen();
					} else {
						self.fail( true );
					}
				} )
				.fail( function( response ) {
					self.fail( response || true );
				} );
			},

			fail: function( error ) {
				if ( ! this.error ) {
					if ( error ) {
						this.error = error;
					} else {
						return;
					}
				}

				if ( this.error.message ) {
					if ( ( this.error.type === 'not-embeddable' && this.type === 'embed' ) || this.error.type === 'not-ssl' ||
						this.error.type === 'no-items' ) {

						this.setError( this.error.message, 'admin-media' );
					} else {
						this.setContent( '<p>' + this.original + '</p>', 'replace' );
					}
				} else if ( this.error.statusText ) {
					this.setError( this.error.statusText, 'admin-media' );
				} else if ( this.original ) {
					this.setContent( '<p>' + this.original + '</p>', 'replace' );
				}
			},

			stopPlayers: function( remove ) {
				var rem = remove === 'remove';

				this.getNodes( function( editor, node, content ) {
					var p, win,
						iframe = $( 'iframe.wpview-sandbox', content ).get(0);

					if ( iframe && ( win = iframe.contentWindow ) && win.mejs ) {
						// Sometimes ME.js may show a "Download File" placeholder and player.remove() doesn't exist there.
						try {
							for ( p in win.mejs.players ) {
								win.mejs.players[p].pause();

								if ( rem ) {
									win.mejs.players[p].remove();
								}
							}
						} catch( er ) {}
					}
				});
			},

			unbind: function() {
				this.stopPlayers( 'remove' );
			}
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
		state: 'video-details'
	} ) );

	/**
	 * TinyMCE handler for the audio shortcode
	 *
	 * @mixes wp.mce.av
	 */
	wp.mce.views.register( 'audio', _.extend( {}, wp.mce.av, {
		state: 'audio-details'
	} ) );

	/**
	 * TinyMCE handler for the playlist shortcode
	 *
	 * @mixes wp.mce.av
	 */
	wp.mce.views.register( 'playlist', _.extend( {}, wp.mce.av, {
		state: [ 'playlist-edit', 'video-playlist-edit' ]
	} ) );

	/**
	 * TinyMCE handler for the embed shortcode
	 */
	wp.mce.embedMixin = {
		View: _.extend( {}, wp.mce.av.View, {
			overlay: true,
			action: 'parse-embed',
			initialize: function( options ) {
				this.content = options.content;
				this.original = options.url || options.shortcode.string();

				if ( options.url ) {
					this.shortcode = media.embed.shortcode( {
						url: options.url
					} );
				} else {
					this.shortcode = options.shortcode;
				}

				_.bindAll( this, 'setIframes', 'setNodes', 'fetch' );
				$( this ).on( 'ready', this.setNodes );

				this.fetch();
			}
		} ),
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
