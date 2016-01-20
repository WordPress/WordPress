/* global tinymce */
tinymce.PluginManager.add( 'wpeditimage', function( editor ) {
	var toolbar, serializer, touchOnImage,
		each = tinymce.each,
		trim = tinymce.trim,
		iOS = tinymce.Env.iOS;

	function isPlaceholder( node ) {
		return !! ( editor.dom.getAttrib( node, 'data-mce-placeholder' ) || editor.dom.getAttrib( node, 'data-mce-object' ) );
	}

	editor.addButton( 'wp_img_remove', {
		tooltip: 'Remove',
		icon: 'dashicon dashicons-no',
		onclick: function() {
			removeImage( editor.selection.getNode() );
		}
	} );

	editor.addButton( 'wp_img_edit', {
		tooltip: 'Edit ', // trailing space is needed, used for context
		icon: 'dashicon dashicons-edit',
		onclick: function() {
			editImage( editor.selection.getNode() );
		}
	} );

	each( {
		alignleft: 'Align left',
		aligncenter: 'Align center',
		alignright: 'Align right',
		alignnone: 'No alignment'
	}, function( tooltip, name ) {
		var direction = name.slice( 5 );

		editor.addButton( 'wp_img_' + name, {
			tooltip: tooltip,
			icon: 'dashicon dashicons-align-' + direction,
			cmd: 'alignnone' === name ? 'wpAlignNone' : 'Justify' + direction.slice( 0, 1 ).toUpperCase() + direction.slice( 1 ),
			onPostRender: function() {
				var self = this;

				editor.on( 'NodeChange', function( event ) {
					var node;

					// Don't bother.
					if ( event.element.nodeName !== 'IMG' ) {
						return;
					}

					node = editor.dom.getParent( event.element, '.wp-caption' ) || event.element;

					if ( 'alignnone' === name ) {
						self.active( ! /\balign(left|center|right)\b/.test( node.className ) );
					} else {
						self.active( editor.dom.hasClass( node, name ) );
					}
				} );
			}
		} );
	} );

	editor.once( 'preinit', function() {
		if ( editor.wp && editor.wp._createToolbar ) {
			toolbar = editor.wp._createToolbar( [
				'wp_img_alignleft',
				'wp_img_aligncenter',
				'wp_img_alignright',
				'wp_img_alignnone',
				'wp_img_edit',
				'wp_img_remove'
			] );
		}
	} );

	editor.on( 'wptoolbar', function( event ) {
		if ( event.element.nodeName === 'IMG' && ! isPlaceholder( event.element ) ) {
			event.toolbar = toolbar;
		}
	} );

	// Safari on iOS fails to select images in contentEditoble mode on touch.
	// Select them again.
	if ( iOS ) {
		editor.on( 'init', function() {
			editor.on( 'touchstart', function( event ) {
				if ( event.target.nodeName === 'IMG' ) {
					touchOnImage = true;
				}
			});

			editor.dom.bind( editor.getDoc(), 'touchmove', function( event ) {
				if ( event.target.nodeName === 'IMG' ) {
					touchOnImage = false;
				}
			});

			editor.on( 'touchend', function( event ) {
				if ( touchOnImage && event.target.nodeName === 'IMG' ) {
					var node = event.target;

					touchOnImage = false;

					window.setTimeout( function() {
						editor.selection.select( node );
						editor.nodeChanged();
					}, 200 );
				} else if ( toolbar ) {
					toolbar.hide();
				}
			});
		});
	}

	function parseShortcode( content ) {
		return content.replace( /(?:<p>)?\[(?:wp_)?caption([^\]]+)\]([\s\S]+?)\[\/(?:wp_)?caption\](?:<\/p>)?/g, function( a, b, c ) {
			var id, align, classes, caption, img, width;

			id = b.match( /id=['"]([^'"]*)['"] ?/ );
			if ( id ) {
				b = b.replace( id[0], '' );
			}

			align = b.match( /align=['"]([^'"]*)['"] ?/ );
			if ( align ) {
				b = b.replace( align[0], '' );
			}

			classes = b.match( /class=['"]([^'"]*)['"] ?/ );
			if ( classes ) {
				b = b.replace( classes[0], '' );
			}

			width = b.match( /width=['"]([0-9]*)['"] ?/ );
			if ( width ) {
				b = b.replace( width[0], '' );
			}

			c = trim( c );
			img = c.match( /((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)([\s\S]*)/i );

			if ( img && img[2] ) {
				caption = trim( img[2] );
				img = trim( img[1] );
			} else {
				// old captions shortcode style
				caption = trim( b ).replace( /caption=['"]/, '' ).replace( /['"]$/, '' );
				img = c;
			}

			id = ( id && id[1] ) ? id[1].replace( /[<>&]+/g,  '' ) : '';
			align = ( align && align[1] ) ? align[1] : 'alignnone';
			classes = ( classes && classes[1] ) ? ' ' + classes[1].replace( /[<>&]+/g,  '' ) : '';

			if ( ! width && img ) {
				width = img.match( /width=['"]([0-9]*)['"]/ );
			}

			if ( width && width[1] ) {
				width = width[1];
			}

			if ( ! width || ! caption ) {
				return c;
			}

			width = parseInt( width, 10 );
			if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
				width += 10;
			}

			return '<div class="mceTemp"><dl id="' + id + '" class="wp-caption ' + align + classes + '" style="width: ' + width + 'px">' +
				'<dt class="wp-caption-dt">'+ img +'</dt><dd class="wp-caption-dd">'+ caption +'</dd></dl></div>';
		});
	}

	function getShortcode( content ) {
		return content.replace( /(?:<div [^>]+mceTemp[^>]+>)?\s*(<dl [^>]+wp-caption[^>]+>[\s\S]+?<\/dl>)\s*(?:<\/div>)?/g, function( all, dl ) {
			var out = '';

			if ( dl.indexOf('<img ') === -1 ) {
				// Broken caption. The user managed to drag the image out?
				// Try to return the caption text as a paragraph.
				out = dl.match( /<dd [^>]+>([\s\S]+?)<\/dd>/i );

				if ( out && out[1] ) {
					return '<p>' + out[1] + '</p>';
				}

				return '';
			}

			out = dl.replace( /\s*<dl ([^>]+)>\s*<dt [^>]+>([\s\S]+?)<\/dt>\s*<dd [^>]+>([\s\S]*?)<\/dd>\s*<\/dl>\s*/gi, function( a, b, c, caption ) {
				var id, classes, align, width;

				width = c.match( /width="([0-9]*)"/ );
				width = ( width && width[1] ) ? width[1] : '';

				classes = b.match( /class="([^"]*)"/ );
				classes = ( classes && classes[1] ) ? classes[1] : '';
				align = classes.match( /align[a-z]+/i ) || 'alignnone';

				if ( ! width || ! caption ) {
					if ( 'alignnone' !== align[0] ) {
						c = c.replace( /><img/, ' class="' + align[0] + '"><img' );
					}
					return c;
				}

				id = b.match( /id="([^"]*)"/ );
				id = ( id && id[1] ) ? id[1] : '';

				classes = classes.replace( /wp-caption ?|align[a-z]+ ?/gi, '' );

				if ( classes ) {
					classes = ' class="' + classes + '"';
				}

				caption = caption.replace( /\r\n|\r/g, '\n' ).replace( /<[a-zA-Z0-9]+( [^<>]+)?>/g, function( a ) {
					// no line breaks inside HTML tags
					return a.replace( /[\r\n\t]+/, ' ' );
				});

				// convert remaining line breaks to <br>
				caption = caption.replace( /\s*\n\s*/g, '<br />' );

				return '[caption id="' + id + '" align="' + align + '" width="' + width + '"' + classes + ']' + c + ' ' + caption + '[/caption]';
			});

			if ( out.indexOf('[caption') === -1 ) {
				// the caption html seems broken, try to find the image that may be wrapped in a link
				// and may be followed by <p> with the caption text.
				out = dl.replace( /[\s\S]*?((?:<a [^>]+>)?<img [^>]+>(?:<\/a>)?)(<p>[\s\S]*<\/p>)?[\s\S]*/gi, '<p>$1</p>$2' );
			}

			return out;
		});
	}

	function extractImageData( imageNode ) {
		var classes, extraClasses, metadata, captionBlock, caption, link, width, height,
			captionClassName = [],
			dom = editor.dom,
			isIntRegExp = /^\d+$/;

		// default attributes
		metadata = {
			attachment_id: false,
			size: 'custom',
			caption: '',
			align: 'none',
			extraClasses: '',
			link: false,
			linkUrl: '',
			linkClassName: '',
			linkTargetBlank: false,
			linkRel: '',
			title: ''
		};

		metadata.url = dom.getAttrib( imageNode, 'src' );
		metadata.alt = dom.getAttrib( imageNode, 'alt' );
		metadata.title = dom.getAttrib( imageNode, 'title' );

		width = dom.getAttrib( imageNode, 'width' );
		height = dom.getAttrib( imageNode, 'height' );

		if ( ! isIntRegExp.test( width ) || parseInt( width, 10 ) < 1 ) {
			width = imageNode.naturalWidth || imageNode.width;
		}

		if ( ! isIntRegExp.test( height ) || parseInt( height, 10 ) < 1 ) {
			height = imageNode.naturalHeight || imageNode.height;
		}

		metadata.customWidth = metadata.width = width;
		metadata.customHeight = metadata.height = height;

		classes = tinymce.explode( imageNode.className, ' ' );
		extraClasses = [];

		tinymce.each( classes, function( name ) {

			if ( /^wp-image/.test( name ) ) {
				metadata.attachment_id = parseInt( name.replace( 'wp-image-', '' ), 10 );
			} else if ( /^align/.test( name ) ) {
				metadata.align = name.replace( 'align', '' );
			} else if ( /^size/.test( name ) ) {
				metadata.size = name.replace( 'size-', '' );
			} else {
				extraClasses.push( name );
			}

		} );

		metadata.extraClasses = extraClasses.join( ' ' );

		// Extract caption
		captionBlock = dom.getParents( imageNode, '.wp-caption' );

		if ( captionBlock.length ) {
			captionBlock = captionBlock[0];

			classes = captionBlock.className.split( ' ' );
			tinymce.each( classes, function( name ) {
				if ( /^align/.test( name ) ) {
					metadata.align = name.replace( 'align', '' );
				} else if ( name && name !== 'wp-caption' ) {
					captionClassName.push( name );
				}
			} );

			metadata.captionClassName = captionClassName.join( ' ' );

			caption = dom.select( 'dd.wp-caption-dd', captionBlock );
			if ( caption.length ) {
				caption = caption[0];

				metadata.caption = editor.serializer.serialize( caption )
					.replace( /<br[^>]*>/g, '$&\n' ).replace( /^<p>/, '' ).replace( /<\/p>$/, '' );
			}
		}

		// Extract linkTo
		if ( imageNode.parentNode && imageNode.parentNode.nodeName === 'A' ) {
			link = imageNode.parentNode;
			metadata.linkUrl = dom.getAttrib( link, 'href' );
			metadata.linkTargetBlank = dom.getAttrib( link, 'target' ) === '_blank' ? true : false;
			metadata.linkRel = dom.getAttrib( link, 'rel' );
			metadata.linkClassName = link.className;
		}

		return metadata;
	}

	function hasTextContent( node ) {
		return node && !! ( node.textContent || node.innerText );
	}

	// Verify HTML in captions
	function verifyHTML( caption ) {
		if ( ! caption || ( caption.indexOf( '<' ) === -1 && caption.indexOf( '>' ) === -1 ) ) {
			return caption;
		}

		if ( ! serializer ) {
			serializer = new tinymce.html.Serializer( {}, editor.schema );
		}

		return serializer.serialize( editor.parser.parse( caption, { forced_root_block: false } ) );
	}

	function updateImage( imageNode, imageData ) {
		var classes, className, node, html, parent, wrap, linkNode,
			captionNode, dd, dl, id, attrs, linkAttrs, width, height, align,
			dom = editor.dom;

		classes = tinymce.explode( imageData.extraClasses, ' ' );

		if ( ! classes ) {
			classes = [];
		}

		if ( ! imageData.caption ) {
			classes.push( 'align' + imageData.align );
		}

		if ( imageData.attachment_id ) {
			classes.push( 'wp-image-' + imageData.attachment_id );
			if ( imageData.size && imageData.size !== 'custom' ) {
				classes.push( 'size-' + imageData.size );
			}
		}

		width = imageData.width;
		height = imageData.height;

		if ( imageData.size === 'custom' ) {
			width = imageData.customWidth;
			height = imageData.customHeight;
		}

		attrs = {
			src: imageData.url,
			width: width || null,
			height: height || null,
			alt: imageData.alt,
			title: imageData.title || null,
			'class': classes.join( ' ' ) || null
		};

		dom.setAttribs( imageNode, attrs );

		linkAttrs = {
			href: imageData.linkUrl,
			rel: imageData.linkRel || null,
			target: imageData.linkTargetBlank ? '_blank': null,
			'class': imageData.linkClassName || null
		};

		if ( imageNode.parentNode && imageNode.parentNode.nodeName === 'A' && ! hasTextContent( imageNode.parentNode ) ) {
			// Update or remove an existing link wrapped around the image
			if ( imageData.linkUrl ) {
				dom.setAttribs( imageNode.parentNode, linkAttrs );
			} else {
				dom.remove( imageNode.parentNode, true );
			}
		} else if ( imageData.linkUrl ) {
			if ( linkNode = dom.getParent( imageNode, 'a' ) ) {
				// The image is inside a link together with other nodes,
				// or is nested in another node, move it out
				dom.insertAfter( imageNode, linkNode );
			}

			// Add link wrapped around the image
			linkNode = dom.create( 'a', linkAttrs );
			imageNode.parentNode.insertBefore( linkNode, imageNode );
			linkNode.appendChild( imageNode );
		}

		captionNode = editor.dom.getParent( imageNode, '.mceTemp' );

		if ( imageNode.parentNode && imageNode.parentNode.nodeName === 'A' && ! hasTextContent( imageNode.parentNode ) ) {
			node = imageNode.parentNode;
		} else {
			node = imageNode;
		}

		if ( imageData.caption ) {
			imageData.caption = verifyHTML( imageData.caption );

			id = imageData.attachment_id ? 'attachment_' + imageData.attachment_id : null;
			align = 'align' + ( imageData.align || 'none' );
			className = 'wp-caption ' + align;

			if ( imageData.captionClassName ) {
				className += ' ' + imageData.captionClassName.replace( /[<>&]+/g,  '' );
			}

			if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
				width = parseInt( width, 10 );
				width += 10;
			}

			if ( captionNode ) {
				dl = dom.select( 'dl.wp-caption', captionNode );

				if ( dl.length ) {
					dom.setAttribs( dl, {
						id: id,
						'class': className,
						style: 'width: ' + width + 'px'
					} );
				}

				dd = dom.select( '.wp-caption-dd', captionNode );

				if ( dd.length ) {
					dom.setHTML( dd[0], imageData.caption );
				}

			} else {
				id = id ? 'id="'+ id +'" ' : '';

				// should create a new function for generating the caption markup
				html =  '<dl ' + id + 'class="' + className +'" style="width: '+ width +'px">' +
					'<dt class="wp-caption-dt"></dt><dd class="wp-caption-dd">'+ imageData.caption +'</dd></dl>';

				wrap = dom.create( 'div', { 'class': 'mceTemp' }, html );

				if ( parent = dom.getParent( node, 'p' ) ) {
					parent.parentNode.insertBefore( wrap, parent );
				} else {
					node.parentNode.insertBefore( wrap, node );
				}

				editor.$( wrap ).find( 'dt.wp-caption-dt' ).append( node );

				if ( parent && dom.isEmpty( parent ) ) {
					dom.remove( parent );
				}
			}
		} else if ( captionNode ) {
			// Remove the caption wrapper and place the image in new paragraph
			parent = dom.create( 'p' );
			captionNode.parentNode.insertBefore( parent, captionNode );
			parent.appendChild( node );
			dom.remove( captionNode );
		}

		if ( wp.media.events ) {
			wp.media.events.trigger( 'editor:image-update', {
				editor: editor,
				metadata: imageData,
				image: imageNode
			} );
		}

		editor.nodeChanged();
	}

	function editImage( img ) {
		var frame, callback, metadata;

		if ( typeof wp === 'undefined' || ! wp.media ) {
			editor.execCommand( 'mceImage' );
			return;
		}

		metadata = extractImageData( img );

		// Manipulate the metadata by reference that is fed into the PostImage model used in the media modal
		wp.media.events.trigger( 'editor:image-edit', {
			editor: editor,
			metadata: metadata,
			image: img
		} );

		frame = wp.media({
			frame: 'image',
			state: 'image-details',
			metadata: metadata
		} );

		wp.media.events.trigger( 'editor:frame-create', { frame: frame } );

		callback = function( imageData ) {
			editor.focus();
			editor.undoManager.transact( function() {
				updateImage( img, imageData );
			} );
			frame.detach();
		};

		frame.state('image-details').on( 'update', callback );
		frame.state('replace-image').on( 'replace', callback );
		frame.on( 'close', function() {
			editor.focus();
			frame.detach();
		});

		frame.open();
	}

	function removeImage( node ) {
		var wrap = editor.dom.getParent( node, 'div.mceTemp' );

		if ( ! wrap && node.nodeName === 'IMG' ) {
			wrap = editor.dom.getParent( node, 'a' );
		}

		if ( wrap ) {
			if ( wrap.nextSibling ) {
				editor.selection.select( wrap.nextSibling );
			} else if ( wrap.previousSibling ) {
				editor.selection.select( wrap.previousSibling );
			} else {
				editor.selection.select( wrap.parentNode );
			}

			editor.selection.collapse( true );
			editor.dom.remove( wrap );
		} else {
			editor.dom.remove( node );
		}

		editor.nodeChanged();
		editor.undoManager.add();
	}

	editor.on( 'init', function() {
		var dom = editor.dom,
			captionClass = editor.getParam( 'wpeditimage_html5_captions' ) ? 'html5-captions' : 'html4-captions';

		dom.addClass( editor.getBody(), captionClass );

		// Add caption field to the default image dialog
		editor.on( 'wpLoadImageForm', function( event ) {
			if ( editor.getParam( 'wpeditimage_disable_captions' ) ) {
				return;
			}

			var captionField = {
				type: 'textbox',
				flex: 1,
				name: 'wpcaption',
				minHeight: 60,
				multiline: true,
				scroll: true,
				label: 'Image caption'
			};

			event.data.splice( event.data.length - 1, 0, captionField );
		});

		// Fix caption parent width for images added from URL
		editor.on( 'wpNewImageRefresh', function( event ) {
			var parent, captionWidth;

			if ( parent = dom.getParent( event.node, 'dl.wp-caption' ) ) {
				if ( ! parent.style.width ) {
					captionWidth = parseInt( event.node.clientWidth, 10 ) + 10;
					captionWidth = captionWidth ? captionWidth + 'px' : '50%';
					dom.setStyle( parent, 'width', captionWidth );
				}
			}
		});

		editor.on( 'wpImageFormSubmit', function( event ) {
			var data = event.imgData.data,
				imgNode = event.imgData.node,
				caption = event.imgData.wpcaption,
				captionId = '',
				captionAlign = '',
				captionWidth = '',
				wrap, parent, node, html, imgId;

			// Temp image id so we can find the node later
			data.id = '__wp-temp-img-id';
			// Cancel the original callback
			event.imgData.cancel = true;

			if ( ! data.style ) {
				data.style = null;
			}

			if ( ! data.src ) {
				// Delete the image and the caption
				if ( imgNode ) {
					if ( wrap = dom.getParent( imgNode, 'div.mceTemp' ) ) {
						dom.remove( wrap );
					} else if ( imgNode.parentNode.nodeName === 'A' ) {
						dom.remove( imgNode.parentNode );
					} else {
						dom.remove( imgNode );
					}

					editor.nodeChanged();
				}
				return;
			}

			if ( caption ) {
				caption = caption.replace( /\r\n|\r/g, '\n' ).replace( /<\/?[a-zA-Z0-9]+( [^<>]+)?>/g, function( a ) {
					// No line breaks inside HTML tags
					return a.replace( /[\r\n\t]+/, ' ' );
				});

				// Convert remaining line breaks to <br>
				caption = caption.replace( /(<br[^>]*>)\s*\n\s*/g, '$1' ).replace( /\s*\n\s*/g, '<br />' );
				caption = verifyHTML( caption );
			}

			if ( ! imgNode ) {
				// New image inserted
				html = dom.createHTML( 'img', data );

				if ( caption ) {
					node = editor.selection.getNode();

					if ( data.width ) {
						captionWidth = parseInt( data.width, 10 );

						if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
							captionWidth += 10;
						}

						captionWidth = ' style="width: ' + captionWidth + 'px"';
					}

					html = '<dl class="wp-caption alignnone"' + captionWidth + '>' +
						'<dt class="wp-caption-dt">'+ html +'</dt><dd class="wp-caption-dd">'+ caption +'</dd></dl>';

					if ( node.nodeName === 'P' ) {
						parent = node;
					} else {
						parent = dom.getParent( node, 'p' );
					}

					if ( parent && parent.nodeName === 'P' ) {
						wrap = dom.create( 'div', { 'class': 'mceTemp' }, html );
						parent.parentNode.insertBefore( wrap, parent );
						editor.selection.select( wrap );
						editor.nodeChanged();

						if ( dom.isEmpty( parent ) ) {
							dom.remove( parent );
						}
					} else {
						editor.selection.setContent( '<div class="mceTemp">' + html + '</div>' );
					}
				} else {
					editor.selection.setContent( html );
				}
			} else {
				// Edit existing image

				// Store the original image id if any
				imgId = imgNode.id || null;
				// Update the image node
				dom.setAttribs( imgNode, data );
				wrap = dom.getParent( imgNode, 'dl.wp-caption' );

				if ( caption ) {
					if ( wrap ) {
						if ( parent = dom.select( 'dd.wp-caption-dd', wrap )[0] ) {
							parent.innerHTML = caption;
						}
					} else {
						if ( imgNode.className ) {
							captionId = imgNode.className.match( /wp-image-([0-9]+)/ );
							captionAlign = imgNode.className.match( /align(left|right|center|none)/ );
						}

						if ( captionAlign ) {
							captionAlign = captionAlign[0];
							imgNode.className = imgNode.className.replace( /align(left|right|center|none)/g, '' );
						} else {
							captionAlign = 'alignnone';
						}

						captionAlign = ' class="wp-caption ' + captionAlign + '"';

						if ( captionId ) {
							captionId = ' id="attachment_' + captionId[1] + '"';
						}

						captionWidth = data.width || imgNode.clientWidth;

						if ( captionWidth ) {
							captionWidth = parseInt( captionWidth, 10 );

							if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
								captionWidth += 10;
							}

							captionWidth = ' style="width: '+ captionWidth +'px"';
						}

						if ( imgNode.parentNode && imgNode.parentNode.nodeName === 'A' ) {
							node = imgNode.parentNode;
						} else {
							node = imgNode;
						}

						html = '<dl ' + captionId + captionAlign + captionWidth + '>' +
							'<dt class="wp-caption-dt"></dt><dd class="wp-caption-dd">'+ caption +'</dd></dl>';

						wrap = dom.create( 'div', { 'class': 'mceTemp' }, html );

						if ( parent = dom.getParent( node, 'p' ) ) {
							parent.parentNode.insertBefore( wrap, parent );
						} else {
							node.parentNode.insertBefore( wrap, node );
						}

						editor.$( wrap ).find( 'dt.wp-caption-dt' ).append( node );

						if ( parent && dom.isEmpty( parent ) ) {
							dom.remove( parent );
						}
					}
				} else {
					if ( wrap ) {
						// Remove the caption wrapper and place the image in new paragraph
						if ( imgNode.parentNode.nodeName === 'A' ) {
							html = dom.getOuterHTML( imgNode.parentNode );
						} else {
							html = dom.getOuterHTML( imgNode );
						}

						parent = dom.create( 'p', {}, html );
						dom.insertAfter( parent, wrap.parentNode );
						editor.selection.select( parent );
						editor.nodeChanged();
						dom.remove( wrap.parentNode );
					}
				}
			}

			imgNode = dom.get('__wp-temp-img-id');
			dom.setAttrib( imgNode, 'id', imgId );
			event.imgData.node = imgNode;
		});

		editor.on( 'wpLoadImageData', function( event ) {
			var parent,
				data = event.imgData.data,
				imgNode = event.imgData.node;

			if ( parent = dom.getParent( imgNode, 'dl.wp-caption' ) ) {
				parent = dom.select( 'dd.wp-caption-dd', parent )[0];

				if ( parent ) {
					data.wpcaption = editor.serializer.serialize( parent )
						.replace( /<br[^>]*>/g, '$&\n' ).replace( /^<p>/, '' ).replace( /<\/p>$/, '' );
				}
			}
		});

		// Prevent IE11 from making dl.wp-caption resizable
		if ( tinymce.Env.ie && tinymce.Env.ie > 10 ) {
			// The 'mscontrolselect' event is supported only in IE11+
			dom.bind( editor.getBody(), 'mscontrolselect', function( event ) {
				if ( event.target.nodeName === 'IMG' && dom.getParent( event.target, '.wp-caption' ) ) {
					// Hide the thick border with resize handles around dl.wp-caption
					editor.getBody().focus(); // :(
				} else if ( event.target.nodeName === 'DL' && dom.hasClass( event.target, 'wp-caption' ) ) {
					// Trigger the thick border with resize handles...
					// This will make the caption text editable.
					event.target.focus();
				}
			});
		}
	});

	editor.on( 'ObjectResized', function( event ) {
		var node = event.target;

		if ( node.nodeName === 'IMG' ) {
			editor.undoManager.transact( function() {
				var parent, width,
					dom = editor.dom;

				node.className = node.className.replace( /\bsize-[^ ]+/, '' );

				if ( parent = dom.getParent( node, '.wp-caption' ) ) {
					width = event.width || dom.getAttrib( node, 'width' );

					if ( width ) {
						width = parseInt( width, 10 );

						if ( ! editor.getParam( 'wpeditimage_html5_captions' ) ) {
							width += 10;
						}

						dom.setStyle( parent, 'width', width + 'px' );
					}
				}
			});
		}
    });

	editor.on( 'BeforeExecCommand', function( event ) {
		var node, p, DL, align, replacement,
			cmd = event.command,
			dom = editor.dom;

		if ( cmd === 'mceInsertContent' ) {
			// When inserting content, if the caret is inside a caption create new paragraph under
			// and move the caret there
			if ( node = dom.getParent( editor.selection.getNode(), 'div.mceTemp' ) ) {
				p = dom.create( 'p' );
				dom.insertAfter( p, node );
				editor.selection.setCursorLocation( p, 0 );
				editor.nodeChanged();
			}
		} else if ( cmd === 'JustifyLeft' || cmd === 'JustifyRight' || cmd === 'JustifyCenter' || cmd === 'wpAlignNone' ) {
			node = editor.selection.getNode();
			align = 'align' + cmd.slice( 7 ).toLowerCase();
			DL = editor.dom.getParent( node, '.wp-caption' );

			if ( node.nodeName !== 'IMG' && ! DL ) {
				return;
			}

			node = DL || node;

			if ( editor.dom.hasClass( node, align ) ) {
				replacement = ' alignnone';
			} else {
				replacement = ' ' + align;
			}

			node.className = trim( node.className.replace( / ?align(left|center|right|none)/g, '' ) + replacement );

			editor.nodeChanged();
			event.preventDefault();

			if ( toolbar ) {
				toolbar.reposition();
			}

			editor.fire( 'ExecCommand', {
				command: cmd,
				ui: event.ui,
				value: event.value
			} );
		}
	});

	editor.on( 'keydown', function( event ) {
		var node, wrap, P, spacer,
			selection = editor.selection,
			keyCode = event.keyCode,
			dom = editor.dom,
			VK = tinymce.util.VK;

		if ( keyCode === VK.ENTER ) {
			// When pressing Enter inside a caption move the caret to a new parapraph under it
			node = selection.getNode();
			wrap = dom.getParent( node, 'div.mceTemp' );

			if ( wrap ) {
				dom.events.cancel( event ); // Doesn't cancel all :(

				// Remove any extra dt and dd cleated on pressing Enter...
				tinymce.each( dom.select( 'dt, dd', wrap ), function( element ) {
					if ( dom.isEmpty( element ) ) {
						dom.remove( element );
					}
				});

				spacer = tinymce.Env.ie && tinymce.Env.ie < 11 ? '' : '<br data-mce-bogus="1" />';
				P = dom.create( 'p', null, spacer );

				if ( node.nodeName === 'DD' ) {
					dom.insertAfter( P, wrap );
				} else {
					wrap.parentNode.insertBefore( P, wrap );
				}

				editor.nodeChanged();
				selection.setCursorLocation( P, 0 );
			}
		} else if ( keyCode === VK.DELETE || keyCode === VK.BACKSPACE ) {
			node = selection.getNode();

			if ( node.nodeName === 'DIV' && dom.hasClass( node, 'mceTemp' ) ) {
				wrap = node;
			} else if ( node.nodeName === 'IMG' || node.nodeName === 'DT' || node.nodeName === 'A' ) {
				wrap = dom.getParent( node, 'div.mceTemp' );
			}

			if ( wrap ) {
				dom.events.cancel( event );
				removeImage( node );
				return false;
			}
		}
	});

	// After undo/redo FF seems to set the image height very slowly when it is set to 'auto' in the CSS.
	// This causes image.getBoundingClientRect() to return wrong values and the resize handles are shown in wrong places.
	// Collapse the selection to remove the resize handles.
	if ( tinymce.Env.gecko ) {
		editor.on( 'undo redo', function() {
			if ( editor.selection.getNode().nodeName === 'IMG' ) {
				editor.selection.collapse();
			}
		});
	}

	editor.wpSetImgCaption = function( content ) {
		return parseShortcode( content );
	};

	editor.wpGetImgCaption = function( content ) {
		return getShortcode( content );
	};

	editor.on( 'BeforeSetContent', function( event ) {
		if ( event.format !== 'raw' ) {
			event.content = editor.wpSetImgCaption( event.content );
		}
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = editor.wpGetImgCaption( event.content );
		}
	});

	( function() {
		var wrap;

		editor.on( 'dragstart', function() {
			var node = editor.selection.getNode();

			if ( node.nodeName === 'IMG' ) {
				wrap = editor.dom.getParent( node, '.mceTemp' );

				if ( ! wrap && node.parentNode.nodeName === 'A' && ! hasTextContent( node.parentNode ) ) {
					wrap = node.parentNode;
				}
			}
		} );

		editor.on( 'drop', function( event ) {
			var dom = editor.dom,
				rng = tinymce.dom.RangeUtils.getCaretRangeFromPoint( event.clientX, event.clientY, editor.getDoc() );

			// Don't allow anything to be dropped in a captioned image.
			if ( dom.getParent( rng.startContainer, '.mceTemp' ) ) {
				event.preventDefault();
			} else if ( wrap ) {
				event.preventDefault();

				editor.undoManager.transact( function() {
					editor.selection.setRng( rng );
					editor.selection.setNode( wrap );
					dom.remove( wrap );
				} );
			}

			wrap = null;
		} );
	} )();

	// Add to editor.wp
	editor.wp = editor.wp || {};
	editor.wp.isPlaceholder = isPlaceholder;

	// Back-compat.
	return {
		_do_shcode: parseShortcode,
		_get_shcode: getShortcode
	};
});
