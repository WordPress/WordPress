/**
 * @output wp-includes/js/mce-view.js
 */

/* global tinymce */

/*
 * The TinyMCE view API.
 *
 * Note: this API is "experimental" meaning that it will probably change
 * in the next few releases based on feedback from 3.9.0.
 * If you decide to use it, please follow the development closely.
 *
 * Diagram
 *
 * |- registered view constructor (type)
 * |  |- view instance (unique text)
 * |  |  |- editor 1
 * |  |  |  |- view node
 * |  |  |  |- view node
 * |  |  |  |- ...
 * |  |  |- editor 2
 * |  |  |  |- ...
 * |  |- view instance
 * |  |  |- ...
 * |- registered view
 * |  |- ...
 */
( function( window, wp, shortcode, $ ) {
	'use strict';

	var views = {},
		instances = {};

	wp.mce = wp.mce || {};

	/**
	 * wp.mce.views
	 *
	 * A set of utilities that simplifies adding custom UI within a TinyMCE editor.
	 * At its core, it serves as a series of converters, transforming text to a
	 * custom UI, and back again.
	 */
	wp.mce.views = {

		/**
		 * Registers a new view type.
		 *
		 * @param {String} type   The view type.
		 * @param {Object} extend An object to extend wp.mce.View.prototype with.
		 */
		register: function( type, extend ) {
			views[ type ] = wp.mce.View.extend( _.extend( extend, { type: type } ) );
		},

		/**
		 * Unregisters a view type.
		 *
		 * @param {String} type The view type.
		 */
		unregister: function( type ) {
			delete views[ type ];
		},

		/**
		 * Returns the settings of a view type.
		 *
		 * @param {String} type The view type.
		 *
		 * @return {Function} The view constructor.
		 */
		get: function( type ) {
			return views[ type ];
		},

		/**
		 * Unbinds all view nodes.
		 * Runs before removing all view nodes from the DOM.
		 */
		unbind: function() {
			_.each( instances, function( instance ) {
				instance.unbind();
			} );
		},

		/**
		 * Scans a given string for each view's pattern,
		 * replacing any matches with markers,
		 * and creates a new instance for every match.
		 *
		 * @param {String} content The string to scan.
		 * @param {tinymce.Editor} editor The editor.
		 *
		 * @return {String} The string with markers.
		 */
		setMarkers: function( content, editor ) {
			var pieces = [ { content: content } ],
				self = this,
				instance, current;

			_.each( views, function( view, type ) {
				current = pieces.slice();
				pieces  = [];

				_.each( current, function( piece ) {
					var remaining = piece.content,
						result, text;

					// Ignore processed pieces, but retain their location.
					if ( piece.processed ) {
						pieces.push( piece );
						return;
					}

					// Iterate through the string progressively matching views
					// and slicing the string as we go.
					while ( remaining && ( result = view.prototype.match( remaining ) ) ) {
						// Any text before the match becomes an unprocessed piece.
						if ( result.index ) {
							pieces.push( { content: remaining.substring( 0, result.index ) } );
						}

						result.options.editor = editor;
						instance = self.createInstance( type, result.content, result.options );
						text = instance.loader ? '.' : instance.text;

						// Add the processed piece for the match.
						pieces.push( {
							content: instance.ignore ? text : '<p data-wpview-marker="' + instance.encodedText + '">' + text + '</p>',
							processed: true
						} );

						// Update the remaining content.
						remaining = remaining.slice( result.index + result.content.length );
					}

					// There are no additional matches.
					// If any content remains, add it as an unprocessed piece.
					if ( remaining ) {
						pieces.push( { content: remaining } );
					}
				} );
			} );

			content = _.pluck( pieces, 'content' ).join( '' );
			return content.replace( /<p>\s*<p data-wpview-marker=/g, '<p data-wpview-marker=' ).replace( /<\/p>\s*<\/p>/g, '</p>' );
		},

		/**
		 * Create a view instance.
		 *
		 * @param {String}  type    The view type.
		 * @param {String}  text    The textual representation of the view.
		 * @param {Object}  options Options.
		 * @param {Boolean} force   Recreate the instance. Optional.
		 *
		 * @return {wp.mce.View} The view instance.
		 */
		createInstance: function( type, text, options, force ) {
			var View = this.get( type ),
				encodedText,
				instance;

			if ( text.indexOf( '[' ) !== -1 && text.indexOf( ']' ) !== -1 ) {
				// Looks like a shortcode? Remove any line breaks from inside of shortcodes
				// or autop will replace them with <p> and <br> later and the string won't match.
				text = text.replace( /\[[^\]]+\]/g, function( match ) {
					return match.replace( /[\r\n]/g, '' );
				});
			}

			if ( ! force ) {
				instance = this.getInstance( text );

				if ( instance ) {
					return instance;
				}
			}

			encodedText = encodeURIComponent( text );

			options = _.extend( options || {}, {
				text: text,
				encodedText: encodedText
			} );

			return instances[ encodedText ] = new View( options );
		},

		/**
		 * Get a view instance.
		 *
		 * @param {(String|HTMLElement)} object The textual representation of the view or the view node.
		 *
		 * @return {wp.mce.View} The view instance or undefined.
		 */
		getInstance: function( object ) {
			if ( typeof object === 'string' ) {
				return instances[ encodeURIComponent( object ) ];
			}

			return instances[ $( object ).attr( 'data-wpview-text' ) ];
		},

		/**
		 * Given a view node, get the view's text.
		 *
		 * @param {HTMLElement} node The view node.
		 *
		 * @return {String} The textual representation of the view.
		 */
		getText: function( node ) {
			return decodeURIComponent( $( node ).attr( 'data-wpview-text' ) || '' );
		},

		/**
		 * Renders all view nodes that are not yet rendered.
		 *
		 * @param {Boolean} force Rerender all view nodes.
		 */
		render: function( force ) {
			_.each( instances, function( instance ) {
				instance.render( null, force );
			} );
		},

		/**
		 * Update the text of a given view node.
		 *
		 * @param {String}         text   The new text.
		 * @param {tinymce.Editor} editor The TinyMCE editor instance the view node is in.
		 * @param {HTMLElement}    node   The view node to update.
		 * @param {Boolean}        force  Recreate the instance. Optional.
		 */
		update: function( text, editor, node, force ) {
			var instance = this.getInstance( node );

			if ( instance ) {
				instance.update( text, editor, node, force );
			}
		},

		/**
		 * Renders any editing interface based on the view type.
		 *
		 * @param {tinymce.Editor} editor The TinyMCE editor instance the view node is in.
		 * @param {HTMLElement}    node   The view node to edit.
		 */
		edit: function( editor, node ) {
			var instance = this.getInstance( node );

			if ( instance && instance.edit ) {
				instance.edit( instance.text, function( text, force ) {
					instance.update( text, editor, node, force );
				} );
			}
		},

		/**
		 * Remove a given view node from the DOM.
		 *
		 * @param {tinymce.Editor} editor The TinyMCE editor instance the view node is in.
		 * @param {HTMLElement}    node   The view node to remove.
		 */
		remove: function( editor, node ) {
			var instance = this.getInstance( node );

			if ( instance ) {
				instance.remove( editor, node );
			}
		}
	};

	/**
	 * A Backbone-like View constructor intended for use when rendering a TinyMCE View.
	 * The main difference is that the TinyMCE View is not tied to a particular DOM node.
	 *
	 * @param {Object} options Options.
	 */
	wp.mce.View = function( options ) {
		_.extend( this, options );
		this.initialize();
	};

	wp.mce.View.extend = Backbone.View.extend;

	_.extend( wp.mce.View.prototype, /** @lends wp.mce.View.prototype */{

		/**
		 * The content.
		 *
		 * @type {*}
		 */
		content: null,

		/**
		 * Whether or not to display a loader.
		 *
		 * @type {Boolean}
		 */
		loader: true,

		/**
		 * Runs after the view instance is created.
		 */
		initialize: function() {},

		/**
		 * Returns the content to render in the view node.
		 *
		 * @return {*}
		 */
		getContent: function() {
			return this.content;
		},

		/**
		 * Renders all view nodes tied to this view instance that are not yet rendered.
		 *
		 * @param {String}  content The content to render. Optional.
		 * @param {Boolean} force   Rerender all view nodes tied to this view instance. Optional.
		 */
		render: function( content, force ) {
			if ( content != null ) {
				this.content = content;
			}

			content = this.getContent();

			// If there's nothing to render an no loader needs to be shown, stop.
			if ( ! this.loader && ! content ) {
				return;
			}

			// We're about to rerender all views of this instance, so unbind rendered views.
			force && this.unbind();

			// Replace any left over markers.
			this.replaceMarkers();

			if ( content ) {
				this.setContent( content, function( editor, node ) {
					$( node ).data( 'rendered', true );
					this.bindNode.call( this, editor, node );
				}, force ? null : false );
			} else {
				this.setLoader();
			}
		},

		/**
		 * Binds a given node after its content is added to the DOM.
		 */
		bindNode: function() {},

		/**
		 * Unbinds a given node before its content is removed from the DOM.
		 */
		unbindNode: function() {},

		/**
		 * Unbinds all view nodes tied to this view instance.
		 * Runs before their content is removed from the DOM.
		 */
		unbind: function() {
			this.getNodes( function( editor, node ) {
				this.unbindNode.call( this, editor, node );
			}, true );
		},

		/**
		 * Gets all the TinyMCE editor instances that support views.
		 *
		 * @param {Function} callback A callback.
		 */
		getEditors: function( callback ) {
			_.each( tinymce.editors, function( editor ) {
				if ( editor.plugins.wpview ) {
					callback.call( this, editor );
				}
			}, this );
		},

		/**
		 * Gets all view nodes tied to this view instance.
		 *
		 * @param {Function} callback A callback.
		 * @param {Boolean}  rendered Get (un)rendered view nodes. Optional.
		 */
		getNodes: function( callback, rendered ) {
			this.getEditors( function( editor ) {
				var self = this;

				$( editor.getBody() )
					.find( '[data-wpview-text="' + self.encodedText + '"]' )
					.filter( function() {
						var data;

						if ( rendered == null ) {
							return true;
						}

						data = $( this ).data( 'rendered' ) === true;

						return rendered ? data : ! data;
					} )
					.each( function() {
						callback.call( self, editor, this, this /* back compat */ );
					} );
			} );
		},

		/**
		 * Gets all marker nodes tied to this view instance.
		 *
		 * @param {Function} callback A callback.
		 */
		getMarkers: function( callback ) {
			this.getEditors( function( editor ) {
				var self = this;

				$( editor.getBody() )
					.find( '[data-wpview-marker="' + this.encodedText + '"]' )
					.each( function() {
						callback.call( self, editor, this );
					} );
			} );
		},

		/**
		 * Replaces all marker nodes tied to this view instance.
		 */
		replaceMarkers: function() {
			this.getMarkers( function( editor, node ) {
				var selected = node === editor.selection.getNode();
				var $viewNode;

				if ( ! this.loader && $( node ).text() !== tinymce.DOM.decode( this.text ) ) {
					editor.dom.setAttrib( node, 'data-wpview-marker', null );
					return;
				}

				$viewNode = editor.$(
					'<div class="wpview wpview-wrap" data-wpview-text="' + this.encodedText + '" data-wpview-type="' + this.type + '" contenteditable="false"></div>'
				);

				editor.undoManager.ignore( function() {
					editor.$( node ).replaceWith( $viewNode );
				} );

				if ( selected ) {
					setTimeout( function() {
						editor.undoManager.ignore( function() {
							editor.selection.select( $viewNode[0] );
							editor.selection.collapse();
						} );
					} );
				}
			} );
		},

		/**
		 * Removes all marker nodes tied to this view instance.
		 */
		removeMarkers: function() {
			this.getMarkers( function( editor, node ) {
				editor.dom.setAttrib( node, 'data-wpview-marker', null );
			} );
		},

		/**
		 * Sets the content for all view nodes tied to this view instance.
		 *
		 * @param {*}        content  The content to set.
		 * @param {Function} callback A callback. Optional.
		 * @param {Boolean}  rendered Only set for (un)rendered nodes. Optional.
		 */
		setContent: function( content, callback, rendered ) {
			if ( _.isObject( content ) && ( content.sandbox || content.head || content.body.indexOf( '<script' ) !== -1 ) ) {
				this.setIframes( content.head || '', content.body, callback, rendered );
			} else if ( _.isString( content ) && content.indexOf( '<script' ) !== -1 ) {
				this.setIframes( '', content, callback, rendered );
			} else {
				this.getNodes( function( editor, node ) {
					content = content.body || content;

					if ( content.indexOf( '<iframe' ) !== -1 ) {
						content += '<span class="mce-shim"></span>';
					}

					editor.undoManager.transact( function() {
						node.innerHTML = '';
						node.appendChild( _.isString( content ) ? editor.dom.createFragment( content ) : content );
						editor.dom.add( node, 'span', { 'class': 'wpview-end' } );
					} );

					callback && callback.call( this, editor, node );
				}, rendered );
			}
		},

		/**
		 * Sets the content in an iframe for all view nodes tied to this view instance.
		 *
		 * @param {String}   head     HTML string to be added to the head of the document.
		 * @param {String}   body     HTML string to be added to the body of the document.
		 * @param {Function} callback A callback. Optional.
		 * @param {Boolean}  rendered Only set for (un)rendered nodes. Optional.
		 */
		setIframes: function( head, body, callback, rendered ) {
			var self = this;

			if ( body.indexOf( '[' ) !== -1 && body.indexOf( ']' ) !== -1 ) {
				var shortcodesRegExp = new RegExp( '\\[\\/?(?:' + window.mceViewL10n.shortcodes.join( '|' ) + ')[^\\]]*?\\]', 'g' );
				// Escape tags inside shortcode previews.
				body = body.replace( shortcodesRegExp, function( match ) {
					return match.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
				} );
			}

			this.getNodes( function( editor, node ) {
				var dom = editor.dom,
					styles = '',
					bodyClasses = editor.getBody().className || '',
					editorHead = editor.getDoc().getElementsByTagName( 'head' )[0],
					iframe, iframeWin, iframeDoc, MutationObserver, observer, i, block;

				tinymce.each( dom.$( 'link[rel="stylesheet"]', editorHead ), function( link ) {
					if ( link.href && link.href.indexOf( 'skins/lightgray/content.min.css' ) === -1 &&
						link.href.indexOf( 'skins/wordpress/wp-content.css' ) === -1 ) {

						styles += dom.getOuterHTML( link );
					}
				} );

				if ( self.iframeHeight ) {
					dom.add( node, 'span', {
						'data-mce-bogus': 1,
						style: {
							display: 'block',
							width: '100%',
							height: self.iframeHeight
						}
					}, '\u200B' );
				}

				editor.undoManager.transact( function() {
					node.innerHTML = '';

					iframe = dom.add( node, 'iframe', {
						/* jshint scripturl: true */
						src: tinymce.Env.ie ? 'javascript:""' : '',
						frameBorder: '0',
						allowTransparency: 'true',
						scrolling: 'no',
						'class': 'wpview-sandbox',
						style: {
							width: '100%',
							display: 'block'
						},
						height: self.iframeHeight
					} );

					dom.add( node, 'span', { 'class': 'mce-shim' } );
					dom.add( node, 'span', { 'class': 'wpview-end' } );
				} );

				// Bail if the iframe node is not attached to the DOM.
				// Happens when the view is dragged in the editor.
				// There is a browser restriction when iframes are moved in the DOM. They get emptied.
				// The iframe will be rerendered after dropping the view node at the new location.
				if ( ! iframe.contentWindow ) {
					return;
				}

				iframeWin = iframe.contentWindow;
				iframeDoc = iframeWin.document;
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
								'iframe {' +
									'max-width: 100%;' +
								'}' +
							'</style>' +
						'</head>' +
						'<body id="wpview-iframe-sandbox" class="' + bodyClasses + '">' +
							body +
						'</body>' +
					'</html>'
				);

				iframeDoc.close();

				function resize() {
					var $iframe;

					if ( block ) {
						return;
					}

					// Make sure the iframe still exists.
					if ( iframe.contentWindow ) {
						$iframe = $( iframe );
						self.iframeHeight = $( iframeDoc.body ).height();

						if ( $iframe.height() !== self.iframeHeight ) {
							$iframe.height( self.iframeHeight );
							editor.nodeChanged();
						}
					}
				}

				if ( self.iframeHeight ) {
					block = true;

					setTimeout( function() {
						block = false;
						resize();
					}, 3000 );
				}

				function reload() {
					if ( ! editor.isHidden() ) {
						$( node ).data( 'rendered', null );

						setTimeout( function() {
							wp.mce.views.render();
						} );
					}
				}

				function addObserver() {
					observer = new MutationObserver( _.debounce( resize, 100 ) );

					observer.observe( iframeDoc.body, {
						attributes: true,
						childList: true,
						subtree: true
					} );
				}

				$( iframeWin ).on( 'load', resize ).on( 'unload', reload );

				MutationObserver = iframeWin.MutationObserver || iframeWin.WebKitMutationObserver || iframeWin.MozMutationObserver;

				if ( MutationObserver ) {
					if ( ! iframeDoc.body ) {
						iframeDoc.addEventListener( 'DOMContentLoaded', addObserver, false );
					} else {
						addObserver();
					}
				} else {
					for ( i = 1; i < 6; i++ ) {
						setTimeout( resize, i * 700 );
					}
				}

				callback && callback.call( self, editor, node );
			}, rendered );
		},

		/**
		 * Sets a loader for all view nodes tied to this view instance.
		 */
		setLoader: function( dashicon ) {
			this.setContent(
				'<div class="loading-placeholder">' +
					'<div class="dashicons dashicons-' + ( dashicon || 'admin-media' ) + '"></div>' +
					'<div class="wpview-loading"><ins></ins></div>' +
				'</div>'
			);
		},

		/**
		 * Sets an error for all view nodes tied to this view instance.
		 *
		 * @param {String} message  The error message to set.
		 * @param {String} dashicon A dashicon ID. Optional. {@link https://developer.wordpress.org/resource/dashicons/}
		 */
		setError: function( message, dashicon ) {
			this.setContent(
				'<div class="wpview-error">' +
					'<div class="dashicons dashicons-' + ( dashicon || 'no' ) + '"></div>' +
					'<p>' + message + '</p>' +
				'</div>'
			);
		},

		/**
		 * Tries to find a text match in a given string.
		 *
		 * @param {String} content The string to scan.
		 *
		 * @return {Object}
		 */
		match: function( content ) {
			var match = shortcode.next( this.type, content );

			if ( match ) {
				return {
					index: match.index,
					content: match.content,
					options: {
						shortcode: match.shortcode
					}
				};
			}
		},

		/**
		 * Update the text of a given view node.
		 *
		 * @param {String}         text   The new text.
		 * @param {tinymce.Editor} editor The TinyMCE editor instance the view node is in.
		 * @param {HTMLElement}    node   The view node to update.
		 * @param {Boolean}        force  Recreate the instance. Optional.
		 */
		update: function( text, editor, node, force ) {
			_.find( views, function( view, type ) {
				var match = view.prototype.match( text );

				if ( match ) {
					$( node ).data( 'rendered', false );
					editor.dom.setAttrib( node, 'data-wpview-text', encodeURIComponent( text ) );
					wp.mce.views.createInstance( type, text, match.options, force ).render();

					editor.selection.select( node );
					editor.nodeChanged();
					editor.focus();

					return true;
				}
			} );
		},

		/**
		 * Remove a given view node from the DOM.
		 *
		 * @param {tinymce.Editor} editor The TinyMCE editor instance the view node is in.
		 * @param {HTMLElement}    node   The view node to remove.
		 */
		remove: function( editor, node ) {
			this.unbindNode.call( this, editor, node );
			editor.dom.remove( node );
			editor.focus();
		}
	} );
} )( window, window.wp, window.wp.shortcode, window.jQuery );

/*
 * The WordPress core TinyMCE views.
 * Views for the gallery, audio, video, playlist and embed shortcodes,
 * and a view for embeddable URLs.
 */
( function( window, views, media, $ ) {
	var base, gallery, av, embed,
		schema, parser, serializer;

	function verifyHTML( string ) {
		var settings = {};

		if ( ! window.tinymce ) {
			return string.replace( /<[^>]+>/g, '' );
		}

		if ( ! string || ( string.indexOf( '<' ) === -1 && string.indexOf( '>' ) === -1 ) ) {
			return string;
		}

		schema = schema || new window.tinymce.html.Schema( settings );
		parser = parser || new window.tinymce.html.DomParser( settings, schema );
		serializer = serializer || new window.tinymce.html.Serializer( settings, schema );

		return serializer.serialize( parser.parse( string, { forced_root_block: false } ) );
	}

	base = {
		state: [],

		edit: function( text, update ) {
			var type = this.type,
				frame = media[ type ].edit( text );

			this.pausePlayers && this.pausePlayers();

			_.each( this.state, function( state ) {
				frame.state( state ).on( 'update', function( selection ) {
					update( media[ type ].shortcode( selection ).string(), type === 'gallery' );
				} );
			} );

			frame.on( 'close', function() {
				frame.detach();
			} );

			frame.open();
		}
	};

	gallery = _.extend( {}, base, {
		state: [ 'gallery-edit' ],
		template: media.template( 'editor-gallery' ),

		initialize: function() {
			var attachments = media.gallery.attachments( this.shortcode, media.view.settings.post.id ),
				attrs = this.shortcode.attrs.named,
				self = this;

			attachments.more()
			.done( function() {
				attachments = attachments.toJSON();

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

				self.render( self.template( {
					verifyHTML: verifyHTML,
					attachments: attachments,
					columns: attrs.columns ? parseInt( attrs.columns, 10 ) : media.galleryDefaults.columns
				} ) );
			} )
			.fail( function( jqXHR, textStatus ) {
				self.setError( textStatus );
			} );
		}
	} );

	av = _.extend( {}, base, {
		action: 'parse-media-shortcode',

		initialize: function() {
			var self = this, maxwidth = null;

			if ( this.url ) {
				this.loader = false;
				this.shortcode = media.embed.shortcode( {
					url: this.text
				} );
			}

			// Obtain the target width for the embed.
			if ( self.editor ) {
				maxwidth = self.editor.getBody().clientWidth;
			}

			wp.ajax.post( this.action, {
				post_ID: media.view.settings.post.id,
				type: this.shortcode.tag,
				shortcode: this.shortcode.string(),
				maxwidth: maxwidth
			} )
			.done( function( response ) {
				self.render( response );
			} )
			.fail( function( response ) {
				if ( self.url ) {
					self.ignore = true;
					self.removeMarkers();
				} else {
					self.setError( response.message || response.statusText, 'admin-media' );
				}
			} );

			this.getEditors( function( editor ) {
				editor.on( 'wpview-selected', function() {
					self.pausePlayers();
				} );
			} );
		},

		pausePlayers: function() {
			this.getNodes( function( editor, node, content ) {
				var win = $( 'iframe.wpview-sandbox', content ).get( 0 );

				if ( win && ( win = win.contentWindow ) && win.mejs ) {
					_.each( win.mejs.players, function( player ) {
						try {
							player.pause();
						} catch ( e ) {}
					} );
				}
			} );
		}
	} );

	embed = _.extend( {}, av, {
		action: 'parse-embed',

		edit: function( text, update ) {
			var frame = media.embed.edit( text, this.url ),
				self = this;

			this.pausePlayers();

			frame.state( 'embed' ).props.on( 'change:url', function( model, url ) {
				if ( url && model.get( 'url' ) ) {
					frame.state( 'embed' ).metadata = model.toJSON();
				}
			} );

			frame.state( 'embed' ).on( 'select', function() {
				var data = frame.state( 'embed' ).metadata;

				if ( self.url ) {
					update( data.url );
				} else {
					update( media.embed.shortcode( data ).string() );
				}
			} );

			frame.on( 'close', function() {
				frame.detach();
			} );

			frame.open();
		}
	} );

	views.register( 'gallery', _.extend( {}, gallery ) );

	views.register( 'audio', _.extend( {}, av, {
		state: [ 'audio-details' ]
	} ) );

	views.register( 'video', _.extend( {}, av, {
		state: [ 'video-details' ]
	} ) );

	views.register( 'playlist', _.extend( {}, av, {
		state: [ 'playlist-edit', 'video-playlist-edit' ]
	} ) );

	views.register( 'embed', _.extend( {}, embed ) );

	views.register( 'embedURL', _.extend( {}, embed, {
		match: function( content ) {
			// There may be a "bookmark" node next to the URL...
			var re = /(^|<p>(?:<span data-mce-type="bookmark"[^>]+>\s*<\/span>)?)(https?:\/\/[^\s"]+?)((?:<span data-mce-type="bookmark"[^>]+>\s*<\/span>)?<\/p>\s*|$)/gi;
			var match = re.exec( content );

			if ( match ) {
				return {
					index: match.index + match[1].length,
					content: match[2],
					options: {
						url: true
					}
				};
			}
		}
	} ) );
} )( window, window.wp.mce.views, window.wp.media, window.jQuery );
