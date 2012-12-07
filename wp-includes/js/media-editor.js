// WordPress, TinyMCE, and Media
// -----------------------------
(function($){
	// Stores the editors' `wp.media.controller.Frame` instances.
	var workflows = {};

	wp.media.string = {
		// Joins the `props` and `attachment` objects,
		// outputting the proper object format based on the
		// attachment's type.
		props: function( props, attachment ) {
			var link, linkUrl, size, sizes, fallbacks;

			// Final fallbacks run after all processing has been completed.
			fallbacks = function( props ) {
				// Generate alt fallbacks and strip tags.
				if ( 'image' === props.type && ! props.alt ) {
					props.alt = props.caption || props.title || '';
					props.alt = props.alt.replace( /<\/?[^>]+>/g, '' );
				}

				return props;
			};

			props = props ? _.clone( props ) : {};

			if ( attachment && attachment.type )
				props.type = attachment.type;

			if ( 'image' === props.type ) {
				props = _.defaults( props || {}, {
					align:   getUserSetting( 'align', 'none' ),
					size:    getUserSetting( 'imgsize', 'medium' ),
					url:     '',
					classes: []
				});
			}

			// All attachment-specific settings follow.
			if ( ! attachment )
				return fallbacks( props );

			props.title = props.title || attachment.title;

			link = props.link || getUserSetting( 'urlbutton', 'post' );
			if ( 'file' === link )
				linkUrl = attachment.url;
			else if ( 'post' === link )
				linkUrl = attachment.link;
			else if ( 'custom' === link )
				linkUrl = props.linkUrl;
			props.linkUrl = linkUrl || '';

			// Format properties for images.
			if ( 'image' === attachment.type ) {
				props.classes.push( 'wp-image-' + attachment.id );

				sizes = attachment.sizes;
				size = sizes && sizes[ props.size ] ? sizes[ props.size ] : attachment;

				_.extend( props, _.pick( attachment, 'align', 'caption', 'alt' ), {
					width:     size.width,
					height:    size.height,
					src:       size.url,
					captionId: 'attachment_' + attachment.id
				});

			// Format properties for non-images.
			} else {
				props.title = props.title || attachment.filename;
				props.rel = props.rel || 'attachment wp-att-' + attachment.id;
			}

			return fallbacks( props );
		},

		link: function( props, attachment ) {
			var options;

			props = wp.media.string.props( props, attachment );

			options = {
				tag:     'a',
				content: props.title,
				attrs:   {
					href: props.linkUrl
				}
			};

			if ( props.rel )
				options.attrs.rel = props.rel;

			return wp.html.string( options );
		},

		image: function( props, attachment ) {
			var img = {},
				options, classes, shortcode, html;

			props = wp.media.string.props( props, attachment );
			classes = props.classes || [];

			img.src = props.url;
			_.extend( img, _.pick( props, 'width', 'height', 'alt' ) );

			// Only assign the align class to the image if we're not printing
			// a caption, since the alignment is sent to the shortcode.
			if ( props.align && ! props.caption )
				classes.push( 'align' + props.align );

			if ( props.size )
				classes.push( 'size-' + props.size );

			img['class'] = _.compact( classes ).join(' ');

			// Generate `img` tag options.
			options = {
				tag:    'img',
				attrs:  img,
				single: true
			};

			// Generate the `a` element options, if they exist.
			if ( props.linkUrl ) {
				options = {
					tag:   'a',
					attrs: {
						href: props.linkUrl
					},
					content: options
				};
			}

			html = wp.html.string( options );

			// Generate the caption shortcode.
			if ( props.caption ) {
				shortcode = {};

				if ( img.width )
					shortcode.width = img.width;

				if ( props.captionId )
					shortcode.id = props.captionId;

				if ( props.align )
					shortcode.align = 'align' + props.align;

				html = wp.shortcode.string({
					tag:     'caption',
					attrs:   shortcode,
					content: html + ' ' + props.caption
				});
			}

			return html;
		}
	};

	wp.media.gallery = (function() {
		var galleries = {};

		return {
			defaults: {
				order:      'ASC',
				id:         wp.media.view.settings.post.id,
				itemtag:    'dl',
				icontag:    'dt',
				captiontag: 'dd',
				columns:    3,
				size:       'thumbnail',
				orderby:    'menu_order ID'
			},

			attachments: function( shortcode ) {
				var shortcodeString = shortcode.string(),
					result = galleries[ shortcodeString ],
					attrs, args, query, others;

				delete galleries[ shortcodeString ];

				if ( result )
					return result;

				// Fill the default shortcode attributes.
				attrs = _.defaults( shortcode.attrs.named, wp.media.gallery.defaults );
				args  = _.pick( attrs, 'orderby', 'order' );

				args.type    = 'image';
				args.perPage = -1;

				// Mark the `orderby` override attribute.
				if ( 'rand' === attrs.orderby )
					attrs._orderbyRandom = true;

				// Map the `orderby` attribute to the corresponding model property.
				if ( ! attrs.orderby || /^menu_order(?: ID)?$/i.test( attrs.orderby ) )
					args.orderby = 'menuOrder';

				// Map the `ids` param to the correct query args.
				if ( attrs.ids ) {
					args.post__in = attrs.ids.split(',');
					args.orderby  = 'post__in';
				} else if ( attrs.include ) {
					args.post__in = attrs.include.split(',');
				}

				if ( attrs.exclude )
					args.post__not_in = attrs.exclude.split(',');

				if ( ! args.post__in )
					args.uploadedTo = attrs.id;

				// Collect the attributes that were not included in `args`.
				others = _.omit( attrs, 'id', 'ids', 'include', 'exclude', 'orderby', 'order' );

				query = wp.media.query( args );
				query.gallery = new Backbone.Model( others );
				return query;
			},

			shortcode: function( attachments ) {
				var props = attachments.props.toJSON(),
					attrs = _.pick( props, 'orderby', 'order' ),
					shortcode, clone;

				if ( attachments.gallery )
					_.extend( attrs, attachments.gallery.toJSON() );

				// Convert all gallery shortcodes to use the `ids` property.
				// Ignore `post__in` and `post__not_in`; the attachments in
				// the collection will already reflect those properties.
				attrs.ids = attachments.pluck('id');

				// Copy the `uploadedTo` post ID.
				if ( props.uploadedTo )
					attrs.id = props.uploadedTo;

				// Check if the gallery is randomly ordered.
				if ( attrs._orderbyRandom )
					attrs.orderby = 'rand';
				delete attrs._orderbyRandom;

				// If the `ids` attribute is set and `orderby` attribute
				// is the default value, clear it for cleaner output.
				if ( attrs.ids && 'post__in' === attrs.orderby )
					delete attrs.orderby;

				// Remove default attributes from the shortcode.
				_.each( wp.media.gallery.defaults, function( value, key ) {
					if ( value === attrs[ key ] )
						delete attrs[ key ];
				});

				shortcode = new wp.shortcode({
					tag:    'gallery',
					attrs:  attrs,
					type:   'single'
				});

				// Use a cloned version of the gallery.
				clone = new wp.media.model.Attachments( attachments.models, {
					props: props
				});
				clone.gallery = attachments.gallery;
				galleries[ shortcode.string() ] = clone;

				return shortcode;
			},

			edit: function( content ) {
				var shortcode = wp.shortcode.next( 'gallery', content ),
					defaultPostId = wp.media.gallery.defaults.id,
					attachments, selection;

				// Bail if we didn't match the shortcode or all of the content.
				if ( ! shortcode || shortcode.content !== content )
					return;

				// Ignore the rest of the match object.
				shortcode = shortcode.shortcode;

				if ( _.isUndefined( shortcode.get('id') ) && ! _.isUndefined( defaultPostId ) )
					shortcode.set( 'id', defaultPostId );

				attachments = wp.media.gallery.attachments( shortcode );

				selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				});

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});

				// Destroy the previous gallery frame.
				if ( this.frame )
					this.frame.dispose();

				// Store the current gallery frame.
				this.frame = wp.media({
					frame:     'post',
					state:     'gallery-edit',
					title:     wp.media.view.l10n.editGalleryTitle,
					editing:   true,
					multiple:  true,
					selection: selection
				}).open();

				return this.frame;
			}
		};
	}());

	wp.media.featuredImage = {
		get: function() {
			return wp.media.view.settings.post.featuredImageId;
		},

		set: function( id ) {
			var settings = wp.media.view.settings;

			settings.post.featuredImageId = id;

			wp.media.post( 'set-post-thumbnail', {
				json:         true,
				post_id:      settings.post.id,
				thumbnail_id: settings.post.featuredImageId,
				_wpnonce:     settings.post.nonce
			}).done( function( html ) {
				$( '.inside', '#postimagediv' ).html( html );
			});
		},

		frame: function() {
			if ( this._frame )
				return this._frame;

			this._frame = wp.media({
				state: 'featured-image',
				states: [ new wp.media.controller.FeaturedImage() ]
			});

			this._frame.on( 'toolbar:create:featured-image', function( toolbar ) {
				this.createSelectToolbar( toolbar, {
					text: wp.media.view.l10n.setFeaturedImage
				});
			}, this._frame );

			this._frame.state('featured-image').on( 'select', this.select );
			return this._frame;
		},

		select: function() {
			var settings = wp.media.view.settings,
				selection = this.get('selection').single();

			if ( ! settings.post.featuredImageId )
				return;

			wp.media.featuredImage.set( selection ? selection.id : -1 );
		},

		init: function() {
			// Open the content media manager to the 'featured image' tab when
			// the post thumbnail is clicked.
			$('#postimagediv').on( 'click', '#set-post-thumbnail', function( event ) {
				event.preventDefault();
				// Stop propagation to prevent thickbox from activating.
				event.stopPropagation();

				wp.media.featuredImage.frame().open();

			// Update the featured image id when the 'remove' link is clicked.
			}).on( 'click', '#remove-post-thumbnail', function() {
				wp.media.view.settings.post.featuredImageId = -1;
			});
		}
	};

	$( wp.media.featuredImage.init );

	wp.media.editor = {
		insert: function( h ) {
			var mce = typeof(tinymce) != 'undefined',
				qt = typeof(QTags) != 'undefined',
				wpActiveEditor = window.wpActiveEditor,
				ed;

			// Delegate to the global `send_to_editor` if it exists.
			// This attempts to play nice with any themes/plugins that have
			// overridden the insert functionality.
			if ( window.send_to_editor )
				return window.send_to_editor.apply( this, arguments );

			if ( ! wpActiveEditor ) {
				if ( mce && tinymce.activeEditor ) {
					ed = tinymce.activeEditor;
					wpActiveEditor = window.wpActiveEditor = ed.id;
				} else if ( !qt ) {
					return false;
				}
			} else if ( mce ) {
				if ( tinymce.activeEditor && (tinymce.activeEditor.id == 'mce_fullscreen' || tinymce.activeEditor.id == 'wp_mce_fullscreen') )
					ed = tinymce.activeEditor;
				else
					ed = tinymce.get(wpActiveEditor);
			}

			if ( ed && !ed.isHidden() ) {
				// restore caret position on IE
				if ( tinymce.isIE && ed.windowManager.insertimagebookmark )
					ed.selection.moveToBookmark(ed.windowManager.insertimagebookmark);

				if ( h.indexOf('[caption') !== -1 ) {
					if ( ed.wpSetImgCaption )
						h = ed.wpSetImgCaption(h);
				} else if ( h.indexOf('[gallery') !== -1 ) {
					if ( ed.plugins.wpgallery )
						h = ed.plugins.wpgallery._do_gallery(h);
				} else if ( h.indexOf('[embed') === 0 ) {
					if ( ed.plugins.wordpress )
						h = ed.plugins.wordpress._setEmbed(h);
				}

				ed.execCommand('mceInsertContent', false, h);
			} else if ( qt ) {
				QTags.insertContent(h);
			} else {
				document.getElementById(wpActiveEditor).value += h;
			}

			// If the old thickbox remove function exists, call it in case
			// a theme/plugin overloaded it.
			if ( window.tb_remove )
				try { window.tb_remove(); } catch( e ) {}
		},

		add: function( id, options ) {
			var workflow = this.get( id );

			if ( workflow )
				return workflow;

			workflow = workflows[ id ] = wp.media( _.defaults( options || {}, {
				frame:    'post',
				state:    'insert',
				title:    wp.media.view.l10n.addMedia,
				multiple: true
			} ) );

			workflow.on( 'insert', function( selection ) {
				var state = workflow.state();

				selection = selection || state.get('selection');

				if ( ! selection )
					return;

				$.when.apply( $, selection.map( function( attachment ) {
					var display = state.display( attachment ).toJSON();
					return this.send.attachment( display, attachment.toJSON() );
				}, this ) ).done( function() {
					wp.media.editor.insert( _.toArray( arguments ).join("\n\n") );
				});
			}, this );

			workflow.state('gallery-edit').on( 'update', function( selection ) {
				this.insert( wp.media.gallery.shortcode( selection ).string() );
			}, this );

			workflow.state('embed').on( 'select', function() {
				var state = workflow.state(),
					type = state.get('type'),
					embed = state.props.toJSON();

				embed.url = embed.url || '';

				if ( 'link' === type ) {
					_.defaults( embed, {
						title:   embed.url,
						linkUrl: embed.url
					});

					this.send.link( embed ).done( function( resp ) {
						wp.media.editor.insert( resp );
					});

				} else if ( 'image' === type ) {
					_.defaults( embed, {
						title:   embed.url,
						linkUrl: '',
						align:   'none',
						link:    'none'
					});

					if ( 'none' === embed.link )
						embed.linkUrl = '';
					else if ( 'file' === embed.link )
						embed.linkUrl = embed.url;

					this.insert( wp.media.string.image( embed ) );
				}
			}, this );

			workflow.state('featured-image').on( 'select', wp.media.featuredImage.select );
			workflow.setState( workflow.options.state );
			return workflow;
		},

		id: function( id ) {
			if ( id )
				return id;

			// If an empty `id` is provided, default to `wpActiveEditor`.
			id = wpActiveEditor;

			// If that doesn't work, fall back to `tinymce.activeEditor.id`.
			if ( ! id && typeof tinymce !== 'undefined' && tinymce.activeEditor )
				id = tinymce.activeEditor.id;

			// Last but not least, fall back to the empty string.
			id = id || '';
			return id;
		},

		get: function( id ) {
			id = this.id( id );
			return workflows[ id ];
		},

		remove: function( id ) {
			id = this.id( id );
			delete workflows[ id ];
		},

		send: {
			attachment: function( props, attachment ) {
				var caption = attachment.caption,
					options, html;

				// If captions are disabled, clear the caption.
				if ( ! wp.media.view.settings.captions )
					delete attachment.caption;

				props = wp.media.string.props( props, attachment );

				options = {
					id:           attachment.id,
					post_content: attachment.description,
					post_excerpt: caption
				};

				if ( props.linkUrl )
					options.url = props.linkUrl;

				if ( 'image' === attachment.type ) {
					html = wp.media.string.image( props );

					_.each({
						align: 'align',
						size:  'image-size',
						alt:   'image_alt'
					}, function( option, prop ) {
						if ( props[ prop ] )
							options[ option ] = props[ prop ];
					});

				} else {
					html = wp.media.string.link( props );
					options.post_title = props.title;
				}

				return wp.media.post( 'send-attachment-to-editor', {
					nonce:      wp.media.view.settings.nonce.sendToEditor,
					attachment: options,
					html:       html,
					post_id:    wp.media.view.settings.post.id
				});
			},

			link: function( embed ) {
				return wp.media.post( 'send-link-to-editor', {
					nonce:   wp.media.view.settings.nonce.sendToEditor,
					src:     embed.linkUrl,
					title:   embed.title,
					html:    wp.media.string.link( embed ),
					post_id: wp.media.view.settings.post.id
				});
			}
		},

		open: function( id ) {
			var workflow, editor;

			id = this.id( id );

			// Save a bookmark of the caret position in IE.
			if ( typeof tinymce !== 'undefined' ) {
				editor = tinymce.get( id );

				if ( tinymce.isIE && editor && ! editor.isHidden() ) {
					editor.focus();
					editor.windowManager.insertimagebookmark = editor.selection.getBookmark();
				}
			}

			workflow = this.get( id );

			// Initialize the editor's workflow if we haven't yet.
			if ( ! workflow )
				workflow = this.add( id );

			return workflow.open();
		},

		init: function() {
			$(document.body).on( 'click', '.insert-media', function( event ) {
				var $this = $(this),
					editor = $this.data('editor');

				event.preventDefault();

				// Remove focus from the `.insert-media` button.
				// Prevents Opera from showing the outline of the button
				// above the modal.
				//
				// See: http://core.trac.wordpress.org/ticket/22445
				$this.blur();

				wp.media.editor.open( editor );
			});
		}
	};

	_.bindAll( wp.media.editor, 'open' );
	$( wp.media.editor.init );
}(jQuery));
