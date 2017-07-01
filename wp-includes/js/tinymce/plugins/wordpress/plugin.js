/* global getUserSetting, setUserSetting */
( function( tinymce ) {
// Set the minimum value for the modals z-index higher than #wpadminbar (100000)
if ( tinymce.ui.FloatPanel.zIndex < 100100 ) {
	tinymce.ui.FloatPanel.zIndex = 100100;
}

tinymce.PluginManager.add( 'wordpress', function( editor ) {
	var wpAdvButton, style,
		DOM = tinymce.DOM,
		each = tinymce.each,
		__ = editor.editorManager.i18n.translate,
		$ = window.jQuery,
		wp = window.wp,
		hasWpautop = ( wp && wp.editor && wp.editor.autop && editor.getParam( 'wpautop', true ) );

	if ( $ ) {
		$( document ).triggerHandler( 'tinymce-editor-setup', [ editor ] );
	}

	function toggleToolbars( state ) {
		var iframe, initial, toolbars,
			pixels = 0;

		initial = ( state === 'hide' );

		if ( editor.theme.panel ) {
			toolbars = editor.theme.panel.find('.toolbar:not(.menubar)');
		}

		if ( ! toolbars || toolbars.length < 2 || ( state === 'hide' && ! toolbars[1].visible() ) ) {
			return;
		}

		if ( ! state && toolbars[1].visible() ) {
			state = 'hide';
		}

		each( toolbars, function( toolbar, i ) {
			if ( i > 0 ) {
				if ( state === 'hide' ) {
					toolbar.hide();
					pixels += 30;
				} else {
					toolbar.show();
					pixels -= 30;
				}
			}
		});

		if ( pixels && ! initial ) {
			// Resize iframe, not needed in iOS
			if ( ! tinymce.Env.iOS ) {
				iframe = editor.getContentAreaContainer().firstChild;
				DOM.setStyle( iframe, 'height', iframe.clientHeight + pixels );
			}

			if ( state === 'hide' ) {
				setUserSetting('hidetb', '0');
				wpAdvButton && wpAdvButton.active( false );
			} else {
				setUserSetting('hidetb', '1');
				wpAdvButton && wpAdvButton.active( true );
			}
		}

		editor.fire( 'wp-toolbar-toggle' );
	}

	// Add the kitchen sink button :)
	editor.addButton( 'wp_adv', {
		tooltip: 'Toolbar Toggle',
		cmd: 'WP_Adv',
		onPostRender: function() {
			wpAdvButton = this;
			wpAdvButton.active( getUserSetting( 'hidetb' ) === '1' ? true : false );
		}
	});

	// Hide the toolbars after loading
	editor.on( 'PostRender', function() {
		if ( editor.getParam( 'wordpress_adv_hidden', true ) && getUserSetting( 'hidetb', '0' ) === '0' ) {
			toggleToolbars( 'hide' );
		}
	});

	editor.addCommand( 'WP_Adv', function() {
		toggleToolbars();
	});

	editor.on( 'focus', function() {
        window.wpActiveEditor = editor.id;
    });

	editor.on( 'BeforeSetContent', function( event ) {
		var title;

		if ( event.content ) {
			if ( event.content.indexOf( '<!--more' ) !== -1 ) {
				title = __( 'Read more...' );

				event.content = event.content.replace( /<!--more(.*?)-->/g, function( match, moretext ) {
					return '<img src="' + tinymce.Env.transparentSrc + '" data-wp-more="more" data-wp-more-text="' + moretext + '" ' +
						'class="wp-more-tag mce-wp-more" alt="" title="' + title + '" data-mce-resize="false" data-mce-placeholder="1" />';
				});
			}

			if ( event.content.indexOf( '<!--nextpage-->' ) !== -1 ) {
				title = __( 'Page break' );

				event.content = event.content.replace( /<!--nextpage-->/g,
					'<img src="' + tinymce.Env.transparentSrc + '" data-wp-more="nextpage" class="wp-more-tag mce-wp-nextpage" ' +
						'alt="" title="' + title + '" data-mce-resize="false" data-mce-placeholder="1" />' );
			}

			if ( event.load && event.format !== 'raw' && hasWpautop ) {
				event.content = wp.editor.autop( event.content );
			}

			if ( event.content.indexOf( '<script' ) !== -1 || event.content.indexOf( '<style' ) !== -1 ) {
				event.content = event.content.replace( /<(script|style)[^>]*>[\s\S]*?<\/\1>/g, function( match, tag ) {
					return '<img ' +
						'src="' + tinymce.Env.transparentSrc + '" ' +
						'data-wp-preserve="' + encodeURIComponent( match ) + '" ' +
						'data-mce-resize="false" ' +
						'data-mce-placeholder="1" '+
						'class="mce-object" ' +
						'width="20" height="20" '+
						'alt="&lt;' + tag + '&gt;" ' +
						'title="&lt;' + tag + '&gt;" ' +
					'/>';
				} );
			}
		}
	});

	editor.on( 'setcontent', function() {
		// Remove spaces from empty paragraphs.
		editor.$( 'p' ).each( function( i, node ) {
			if ( node.innerHTML && node.innerHTML.length < 10 ) {
				var html = tinymce.trim( node.innerHTML );

				if ( ! html || html === '&nbsp;' ) {
					node.innerHTML = ( tinymce.Env.ie && tinymce.Env.ie < 11 ) ? '' : '<br data-mce-bogus="1">';
				}
			}
		} );
	});

	editor.on( 'PostProcess', function( event ) {
		if ( event.get ) {
			event.content = event.content.replace(/<img[^>]+>/g, function( image ) {
				var match,
					string,
					moretext = '';

				if ( image.indexOf( 'data-wp-more="more"' ) !== -1 ) {
					if ( match = image.match( /data-wp-more-text="([^"]+)"/ ) ) {
						moretext = match[1];
					}

					string = '<!--more' + moretext + '-->';
				} else if ( image.indexOf( 'data-wp-more="nextpage"' ) !== -1 ) {
					string = '<!--nextpage-->';
				} else if ( image.indexOf( 'data-wp-preserve' ) !== -1 ) {
					if ( match = image.match( / data-wp-preserve="([^"]+)"/ ) ) {
						string = decodeURIComponent( match[1] );
					}
				}

				return string || image;
			});
		}
	});

	// Display the tag name instead of img in element path
	editor.on( 'ResolveName', function( event ) {
		var attr;

		if ( event.target.nodeName === 'IMG' && ( attr = editor.dom.getAttrib( event.target, 'data-wp-more' ) ) ) {
			event.name = attr;
		}
	});

	// Register commands
	editor.addCommand( 'WP_More', function( tag ) {
		var parent, html, title,
			classname = 'wp-more-tag',
			dom = editor.dom,
			node = editor.selection.getNode();

		tag = tag || 'more';
		classname += ' mce-wp-' + tag;
		title = tag === 'more' ? 'Read more...' : 'Next page';
		title = __( title );
		html = '<img src="' + tinymce.Env.transparentSrc + '" alt="" title="' + title + '" class="' + classname + '" ' +
			'data-wp-more="' + tag + '" data-mce-resize="false" data-mce-placeholder="1" />';

		// Most common case
		if ( node.nodeName === 'BODY' || ( node.nodeName === 'P' && node.parentNode.nodeName === 'BODY' ) ) {
			editor.insertContent( html );
			return;
		}

		// Get the top level parent node
		parent = dom.getParent( node, function( found ) {
			if ( found.parentNode && found.parentNode.nodeName === 'BODY' ) {
				return true;
			}

			return false;
		}, editor.getBody() );

		if ( parent ) {
			if ( parent.nodeName === 'P' ) {
				parent.appendChild( dom.create( 'p', null, html ).firstChild );
			} else {
				dom.insertAfter( dom.create( 'p', null, html ), parent );
			}

			editor.nodeChanged();
		}
	});

	editor.addCommand( 'WP_Code', function() {
		editor.formatter.toggle('code');
	});

	editor.addCommand( 'WP_Page', function() {
		editor.execCommand( 'WP_More', 'nextpage' );
	});

	editor.addCommand( 'WP_Help', function() {
		var access = tinymce.Env.mac ? __( 'Ctrl + Alt + letter:' ) : __( 'Shift + Alt + letter:' ),
			meta = tinymce.Env.mac ? __( 'Cmd + letter:' ) : __( 'Ctrl + letter:' ),
			table1 = [],
			table2 = [],
			row1 = {},
			row2 = {},
			i1 = 0,
			i2 = 0,
			labels = editor.settings.wp_shortcut_labels,
			header, html, dialog, $wrap;

		if ( ! labels ) {
			return;
		}

		function tr( row, columns ) {
			var out = '<tr>';
			var i = 0;

			columns = columns || 1;

			each( row, function( text, key ) {
				out += '<td><kbd>' + key + '</kbd></td><td>' + __( text ) + '</td>';
				i++;
			});

			while ( i < columns ) {
				out += '<td></td><td></td>';
				i++;
			}

			return out + '</tr>';
		}

		each ( labels, function( label, name ) {
			var letter;

			if ( label.indexOf( 'meta' ) !== -1 ) {
				i1++;
				letter = label.replace( 'meta', '' ).toLowerCase();

				if ( letter ) {
					row1[ letter ] = name;

					if ( i1 % 2 === 0 ) {
						table1.push( tr( row1, 2 ) );
						row1 = {};
					}
				}
			} else if ( label.indexOf( 'access' ) !== -1 ) {
				i2++;
				letter = label.replace( 'access', '' ).toLowerCase();

				if ( letter ) {
					row2[ letter ] = name;

					if ( i2 % 2 === 0 ) {
						table2.push( tr( row2, 2 ) );
						row2 = {};
					}
				}
			}
		} );

		// Add remaining single entries.
		if ( i1 % 2 > 0 ) {
			table1.push( tr( row1, 2 ) );
		}

		if ( i2 % 2 > 0 ) {
			table2.push( tr( row2, 2 ) );
		}

		header = [ __( 'Letter' ), __( 'Action' ), __( 'Letter' ), __( 'Action' ) ];
		header = '<tr><th>' + header.join( '</th><th>' ) + '</th></tr>';

		html = '<div class="wp-editor-help">';

		// Main section, default and additional shortcuts
		html = html +
			'<h2>' + __( 'Default shortcuts,' ) + ' ' + meta + '</h2>' +
			'<table class="wp-help-th-center fixed">' +
				header +
				table1.join('') +
			'</table>' +
			'<h2>' + __( 'Additional shortcuts,' ) + ' ' + access + '</h2>' +
			'<table class="wp-help-th-center fixed">' +
				header +
				table2.join('') +
			'</table>';

		if ( editor.plugins.wptextpattern && ( ! tinymce.Env.ie || tinymce.Env.ie > 8 ) ) {
			// Text pattern section
			html = html +
				'<h2>' + __( 'When starting a new paragraph with one of these formatting shortcuts followed by a space, the formatting will be applied automatically. Press Backspace or Escape to undo.' ) + '</h2>' +
				'<table class="wp-help-th-center fixed">' +
					tr({ '*':  'Bullet list', '1.':  'Numbered list' }) +
					tr({ '-':  'Bullet list', '1)':  'Numbered list' }) +
				'</table>';

			html = html +
				'<h2>' + __( 'The following formatting shortcuts are replaced when pressing Enter. Press Escape or the Undo button to undo.' ) + '</h2>' +
				'<table class="wp-help-single">' +
					tr({ '>': 'Blockquote' }) +
					tr({ '##': 'Heading 2' }) +
					tr({ '###': 'Heading 3' }) +
					tr({ '####': 'Heading 4' }) +
					tr({ '#####': 'Heading 5' }) +
					tr({ '######': 'Heading 6' }) +
					tr({ '---': 'Horizontal line' }) +
				'</table>';
		}

		// Focus management section
		html = html +
			'<h2>' + __( 'Focus shortcuts:' ) + '</h2>' +
			'<table class="wp-help-single">' +
				tr({ 'Alt + F8':  'Inline toolbar (when an image, link or preview is selected)' }) +
				tr({ 'Alt + F9':  'Editor menu (when enabled)' }) +
				tr({ 'Alt + F10': 'Editor toolbar' }) +
				tr({ 'Alt + F11': 'Elements path' }) +
			'</table>' +
			'<p>' + __( 'To move focus to other buttons use Tab or the arrow keys. To return focus to the editor press Escape or use one of the buttons.' ) + '</p>';

		html += '</div>';

		dialog = editor.windowManager.open( {
			title: 'Keyboard Shortcuts',
			items: {
				type: 'container',
				classes: 'wp-help',
				html: html
			},
			buttons: {
				text: 'Close',
				onclick: 'close'
			}
		} );

		if ( dialog.$el ) {
			dialog.$el.find( 'div[role="application"]' ).attr( 'role', 'document' );
			$wrap = dialog.$el.find( '.mce-wp-help' );

			if ( $wrap[0] ) {
				$wrap.attr( 'tabindex', '0' );
				$wrap[0].focus();
				$wrap.on( 'keydown', function( event ) {
					// Prevent use of: page up, page down, end, home, left arrow, up arrow, right arrow, down arrow
					// in the dialog keydown handler.
					if ( event.keyCode >= 33 && event.keyCode <= 40 ) {
						event.stopPropagation();
					}
				});
			}
		}
	} );

	editor.addCommand( 'WP_Medialib', function() {
		if ( wp && wp.media && wp.media.editor ) {
			wp.media.editor.open( editor.id );
		}
	});

	// Register buttons
	editor.addButton( 'wp_more', {
		tooltip: 'Insert Read More tag',
		onclick: function() {
			editor.execCommand( 'WP_More', 'more' );
		}
	});

	editor.addButton( 'wp_page', {
		tooltip: 'Page break',
		onclick: function() {
			editor.execCommand( 'WP_More', 'nextpage' );
		}
	});

	editor.addButton( 'wp_help', {
		tooltip: 'Keyboard Shortcuts',
		cmd: 'WP_Help'
	});

	editor.addButton( 'wp_code', {
		tooltip: 'Code',
		cmd: 'WP_Code',
		stateSelector: 'code'
	});

	// Menubar
	// Insert->Add Media
	if ( wp && wp.media && wp.media.editor ) {
		editor.addMenuItem( 'add_media', {
			text: 'Add Media',
			icon: 'wp-media-library',
			context: 'insert',
			cmd: 'WP_Medialib'
		});
	}

	// Insert "Read More..."
	editor.addMenuItem( 'wp_more', {
		text: 'Insert Read More tag',
		icon: 'wp_more',
		context: 'insert',
		onclick: function() {
			editor.execCommand( 'WP_More', 'more' );
		}
	});

	// Insert "Next Page"
	editor.addMenuItem( 'wp_page', {
		text: 'Page break',
		icon: 'wp_page',
		context: 'insert',
		onclick: function() {
			editor.execCommand( 'WP_More', 'nextpage' );
		}
	});

	editor.on( 'BeforeExecCommand', function(e) {
		if ( tinymce.Env.webkit && ( e.command === 'InsertUnorderedList' || e.command === 'InsertOrderedList' ) ) {
			if ( ! style ) {
				style = editor.dom.create( 'style', {'type': 'text/css'},
					'#tinymce,#tinymce span,#tinymce li,#tinymce li>span,#tinymce p,#tinymce p>span{font:medium sans-serif;color:#000;line-height:normal;}');
			}

			editor.getDoc().head.appendChild( style );
		}
	});

	editor.on( 'ExecCommand', function( e ) {
		if ( tinymce.Env.webkit && style &&
			( 'InsertUnorderedList' === e.command || 'InsertOrderedList' === e.command ) ) {

			editor.dom.remove( style );
		}
	});

	editor.on( 'init', function() {
		var env = tinymce.Env,
			bodyClass = ['mceContentBody'], // back-compat for themes that use this in editor-style.css...
			doc = editor.getDoc(),
			dom = editor.dom;

		if ( env.iOS ) {
			dom.addClass( doc.documentElement, 'ios' );
		}

		if ( editor.getParam( 'directionality' ) === 'rtl' ) {
			bodyClass.push('rtl');
			dom.setAttrib( doc.documentElement, 'dir', 'rtl' );
		}

		dom.setAttrib( doc.documentElement, 'lang', editor.getParam( 'wp_lang_attr' ) );

		if ( env.ie ) {
			if ( parseInt( env.ie, 10 ) === 9 ) {
				bodyClass.push('ie9');
			} else if ( parseInt( env.ie, 10 ) === 8 ) {
				bodyClass.push('ie8');
			} else if ( env.ie < 8 ) {
				bodyClass.push('ie7');
			}
		} else if ( env.webkit ) {
			bodyClass.push('webkit');
		}

		bodyClass.push('wp-editor');

		each( bodyClass, function( cls ) {
			if ( cls ) {
				dom.addClass( doc.body, cls );
			}
		});

		// Remove invalid parent paragraphs when inserting HTML
		editor.on( 'BeforeSetContent', function( event ) {
			if ( event.content ) {
				event.content = event.content.replace( /<p>\s*<(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre)( [^>]*)?>/gi, '<$1$2>' )
					.replace( /<\/(p|div|ul|ol|dl|table|blockquote|h[1-6]|fieldset|pre)>\s*<\/p>/gi, '</$1>' );
			}
		});

		if ( $ ) {
			$( document ).triggerHandler( 'tinymce-editor-init', [editor] );
		}

		if ( window.tinyMCEPreInit && window.tinyMCEPreInit.dragDropUpload ) {
			dom.bind( doc, 'dragstart dragend dragover drop', function( event ) {
				if ( $ ) {
					// Trigger the jQuery handlers.
					$( document ).trigger( new $.Event( event ) );
				}
			});
		}

		if ( editor.getParam( 'wp_paste_filters', true ) ) {
			editor.on( 'PastePreProcess', function( event ) {
				// Remove trailing <br> added by WebKit browsers to the clipboard
				event.content = event.content.replace( /<br class="?Apple-interchange-newline"?>/gi, '' );

				// In WebKit this is handled by removeWebKitStyles()
				if ( ! tinymce.Env.webkit ) {
					// Remove all inline styles
					event.content = event.content.replace( /(<[^>]+) style="[^"]*"([^>]*>)/gi, '$1$2' );

					// Put back the internal styles
					event.content = event.content.replace(/(<[^>]+) data-mce-style=([^>]+>)/gi, '$1 style=$2' );
				}
			});

			editor.on( 'PastePostProcess', function( event ) {
				// Remove empty paragraphs
				editor.$( 'p', event.node ).each( function( i, node ) {
					if ( dom.isEmpty( node ) ) {
						dom.remove( node );
					}
				});

				if ( tinymce.isIE ) {
					editor.$( 'a', event.node ).find( 'font, u' ).each( function( i, node ) {
						dom.remove( node, true );
					});
				}
			});
		}

		if ( editor.settings.wp_shortcut_labels && editor.theme.panel ) {
			var labels = {};
			var access = 'Shift+Alt+';
			var meta = 'Ctrl+';

			// For Mac: ctrl = \u2303, cmd = \u2318, alt = \u2325

			if ( tinymce.Env.mac ) {
				access = '\u2303\u2325';
				meta = '\u2318';
			}

			each( editor.settings.wp_shortcut_labels, function( value, name ) {
				labels[ name ] = value.replace( 'access', access ).replace( 'meta', meta );
			} );

			each( editor.theme.panel.find('button'), function( button ) {
				if ( button && button.settings.tooltip && labels.hasOwnProperty( button.settings.tooltip ) ) {
					// Need to translate now. We are changing the string so it won't match and cannot be translated later.
					button.settings.tooltip = editor.translate( button.settings.tooltip ) + ' (' + labels[ button.settings.tooltip ] + ')';
				}
			} );

			// listbox for the "blocks" drop-down
			each( editor.theme.panel.find('listbox'), function( listbox ) {
				if ( listbox && listbox.settings.text === 'Paragraph' ) {
					each( listbox.settings.values, function( item ) {
						if ( item.text && labels.hasOwnProperty( item.text ) ) {
							item.shortcut = '(' + labels[ item.text ] + ')';
						}
					} );
				}
			} );
		}
	});

	editor.on( 'SaveContent', function( event ) {
		// If editor is hidden, we just want the textarea's value to be saved
		if ( ! editor.inline && editor.isHidden() ) {
			event.content = event.element.value;
			return;
		}

		// Keep empty paragraphs :(
		event.content = event.content.replace( /<p>(?:<br ?\/?>|\u00a0|\uFEFF| )*<\/p>/g, '<p>&nbsp;</p>' );

		if ( hasWpautop ) {
			event.content = wp.editor.removep( event.content );
		}
	});

	editor.on( 'preInit', function() {
		var validElementsSetting = '@[id|accesskey|class|dir|lang|style|tabindex|' +
			'title|contenteditable|draggable|dropzone|hidden|spellcheck|translate],' + // Global attributes.
			'i,' + // Don't replace <i> with <em> and <b> with <strong> and don't remove them when empty.
			'b,' +
			'script[src|async|defer|type|charset|crossorigin|integrity]'; // Add support for <script>.

		editor.schema.addValidElements( validElementsSetting );

		if ( tinymce.Env.iOS ) {
			editor.settings.height = 300;
		}

		each( {
			c: 'JustifyCenter',
			r: 'JustifyRight',
			l: 'JustifyLeft',
			j: 'JustifyFull',
			q: 'mceBlockQuote',
			u: 'InsertUnorderedList',
			o: 'InsertOrderedList',
			m: 'WP_Medialib',
			z: 'WP_Adv',
			t: 'WP_More',
			d: 'Strikethrough',
			h: 'WP_Help',
			p: 'WP_Page',
			x: 'WP_Code'
		}, function( command, key ) {
			editor.shortcuts.add( 'access+' + key, '', command );
		} );

		editor.addShortcut( 'meta+s', '', function() {
			if ( wp && wp.autosave ) {
				wp.autosave.server.triggerSave();
			}
		} );

		if ( window.getUserSetting( 'editor_plain_text_paste_warning' ) > 1 ) {
			editor.settings.paste_plaintext_inform = false;
		}

		// Change the editor iframe title on MacOS, add the correct help shortcut.
		if ( tinymce.Env.mac ) {
			tinymce.$( editor.iframeElement ).attr( 'title', __( 'Rich Text Area. Press Control-Option-H for help.' ) );
		}
	} );

	editor.on( 'PastePlainTextToggle', function( event ) {
		// Warn twice, then stop.
		if ( event.state === true ) {
			var times = parseInt( window.getUserSetting( 'editor_plain_text_paste_warning' ), 10 ) || 0;

			if ( times < 2 ) {
				window.setUserSetting( 'editor_plain_text_paste_warning', ++times );
			}
		}
	});

	/**
	 * Experimental: create a floating toolbar.
	 * This functionality will change in the next releases. Not recommended for use by plugins.
	 */
	editor.on( 'preinit', function() {
		var Factory = tinymce.ui.Factory,
			settings = editor.settings,
			activeToolbar,
			currentSelection,
			timeout,
			container = editor.getContainer(),
			wpAdminbar = document.getElementById( 'wpadminbar' ),
			mceIframe = document.getElementById( editor.id + '_ifr' ),
			mceToolbar,
			mceStatusbar,
			wpStatusbar;

			if ( container ) {
				mceToolbar = tinymce.$( '.mce-toolbar-grp', container )[0];
				mceStatusbar = tinymce.$( '.mce-statusbar', container )[0];
			}

			if ( editor.id === 'content' ) {
				wpStatusbar = document.getElementById( 'post-status-info' );
			}

		function create( buttons, bottom ) {
			var toolbar,
				toolbarItems = [],
				buttonGroup;

			each( buttons, function( item ) {
				var itemName;

				function bindSelectorChanged() {
					var selection = editor.selection;

					if ( itemName === 'bullist' ) {
						selection.selectorChanged( 'ul > li', function( state, args ) {
							var i = args.parents.length,
								nodeName;

							while ( i-- ) {
								nodeName = args.parents[ i ].nodeName;

								if ( nodeName === 'OL' || nodeName == 'UL' ) {
									break;
								}
							}

							item.active( state && nodeName === 'UL' );
						} );
					}

					if ( itemName === 'numlist' ) {
						selection.selectorChanged( 'ol > li', function( state, args ) {
							var i = args.parents.length,
								nodeName;

							while ( i-- ) {
								nodeName = args.parents[ i ].nodeName;

								if ( nodeName === 'OL' || nodeName === 'UL' ) {
									break;
								}
							}

							item.active( state && nodeName === 'OL' );
						} );
					}

					if ( item.settings.stateSelector ) {
						selection.selectorChanged( item.settings.stateSelector, function( state ) {
							item.active( state );
						}, true );
					}

					if ( item.settings.disabledStateSelector ) {
						selection.selectorChanged( item.settings.disabledStateSelector, function( state ) {
							item.disabled( state );
						} );
					}
				}

				if ( item === '|' ) {
					buttonGroup = null;
				} else {
					if ( Factory.has( item ) ) {
						item = {
							type: item
						};

						if ( settings.toolbar_items_size ) {
							item.size = settings.toolbar_items_size;
						}

						toolbarItems.push( item );

						buttonGroup = null;
					} else {
						if ( ! buttonGroup ) {
							buttonGroup = {
								type: 'buttongroup',
								items: []
							};

							toolbarItems.push( buttonGroup );
						}

						if ( editor.buttons[ item ] ) {
							itemName = item;
							item = editor.buttons[ itemName ];

							if ( typeof item === 'function' ) {
								item = item();
							}

							item.type = item.type || 'button';

							if ( settings.toolbar_items_size ) {
								item.size = settings.toolbar_items_size;
							}

							item = Factory.create( item );

							buttonGroup.items.push( item );

							if ( editor.initialized ) {
								bindSelectorChanged();
							} else {
								editor.on( 'init', bindSelectorChanged );
							}
						}
					}
				}
			} );

			toolbar = Factory.create( {
				type: 'panel',
				layout: 'stack',
				classes: 'toolbar-grp inline-toolbar-grp',
				ariaRoot: true,
				ariaRemember: true,
				items: [ {
					type: 'toolbar',
					layout: 'flow',
					items: toolbarItems
				} ]
			} );

			toolbar.bottom = bottom;

			function reposition() {
				if ( ! currentSelection ) {
					return this;
				}

				var scrollX = window.pageXOffset || document.documentElement.scrollLeft,
					scrollY = window.pageYOffset || document.documentElement.scrollTop,
					windowWidth = window.innerWidth,
					windowHeight = window.innerHeight,
					iframeRect = mceIframe ? mceIframe.getBoundingClientRect() : {
						top: 0,
						right: windowWidth,
						bottom: windowHeight,
						left: 0,
						width: windowWidth,
						height: windowHeight
					},
					toolbar = this.getEl(),
					toolbarWidth = toolbar.offsetWidth,
					toolbarHeight = toolbar.clientHeight,
					selection = currentSelection.getBoundingClientRect(),
					selectionMiddle = ( selection.left + selection.right ) / 2,
					buffer = 5,
					spaceNeeded = toolbarHeight + buffer,
					wpAdminbarBottom = wpAdminbar ? wpAdminbar.getBoundingClientRect().bottom : 0,
					mceToolbarBottom = mceToolbar ? mceToolbar.getBoundingClientRect().bottom : 0,
					mceStatusbarTop = mceStatusbar ? windowHeight - mceStatusbar.getBoundingClientRect().top : 0,
					wpStatusbarTop = wpStatusbar ? windowHeight - wpStatusbar.getBoundingClientRect().top : 0,
					blockedTop = Math.max( 0, wpAdminbarBottom, mceToolbarBottom, iframeRect.top ),
					blockedBottom = Math.max( 0, mceStatusbarTop, wpStatusbarTop, windowHeight - iframeRect.bottom ),
					spaceTop = selection.top + iframeRect.top - blockedTop,
					spaceBottom = windowHeight - iframeRect.top - selection.bottom - blockedBottom,
					editorHeight = windowHeight - blockedTop - blockedBottom,
					className = '',
					iosOffsetTop = 0,
					iosOffsetBottom = 0,
					top, left;

				if ( spaceTop >= editorHeight || spaceBottom >= editorHeight ) {
					this.scrolling = true;
					this.hide();
					this.scrolling = false;
					return this;
				}

				// Add offset in iOS to move the menu over the image, out of the way of the default iOS menu.
				if ( tinymce.Env.iOS && currentSelection.nodeName === 'IMG' ) {
					iosOffsetTop = 54;
					iosOffsetBottom = 46;
				}

				if ( this.bottom ) {
					if ( spaceBottom >= spaceNeeded ) {
						className = ' mce-arrow-up';
						top = selection.bottom + iframeRect.top + scrollY - iosOffsetBottom;
					} else if ( spaceTop >= spaceNeeded ) {
						className = ' mce-arrow-down';
						top = selection.top + iframeRect.top + scrollY - toolbarHeight + iosOffsetTop;
					}
				} else {
					if ( spaceTop >= spaceNeeded ) {
						className = ' mce-arrow-down';
						top = selection.top + iframeRect.top + scrollY - toolbarHeight + iosOffsetTop;
					} else if ( spaceBottom >= spaceNeeded && editorHeight / 2 > selection.bottom + iframeRect.top - blockedTop ) {
						className = ' mce-arrow-up';
						top = selection.bottom + iframeRect.top + scrollY - iosOffsetBottom;
					}
				}

				if ( typeof top === 'undefined' ) {
					top = scrollY + blockedTop + buffer + iosOffsetBottom;
				}

				left = selectionMiddle - toolbarWidth / 2 + iframeRect.left + scrollX;

				if ( selection.left < 0 || selection.right > iframeRect.width ) {
					left = iframeRect.left + scrollX + ( iframeRect.width - toolbarWidth ) / 2;
				} else if ( toolbarWidth >= windowWidth ) {
					className += ' mce-arrow-full';
					left = 0;
				} else if ( ( left < 0 && selection.left + toolbarWidth > windowWidth ) || ( left + toolbarWidth > windowWidth && selection.right - toolbarWidth < 0 ) ) {
					left = ( windowWidth - toolbarWidth ) / 2;
				} else if ( left < iframeRect.left + scrollX ) {
					className += ' mce-arrow-left';
					left = selection.left + iframeRect.left + scrollX;
				} else if ( left + toolbarWidth > iframeRect.width + iframeRect.left + scrollX ) {
					className += ' mce-arrow-right';
					left = selection.right - toolbarWidth + iframeRect.left + scrollX;
				}

				// No up/down arrows on the menu over images in iOS.
				if ( tinymce.Env.iOS && currentSelection.nodeName === 'IMG' ) {
					className = className.replace( / ?mce-arrow-(up|down)/g, '' );
				}

				toolbar.className = toolbar.className.replace( / ?mce-arrow-[\w]+/g, '' ) + className;

				DOM.setStyles( toolbar, {
					'left': left,
					'top': top
				} );

				return this;
			}

			toolbar.on( 'show', function() {
				this.reposition();
			} );

			toolbar.on( 'keydown', function( event ) {
				if ( event.keyCode === 27 ) {
					this.hide();
					editor.focus();
				}
			} );

			editor.on( 'remove', function() {
				toolbar.remove();
			} );

			toolbar.reposition = reposition;
			toolbar.hide().renderTo( document.body );

			return toolbar;
		}

		editor.shortcuts.add( 'alt+119', '', function() {
			var node;

			if ( activeToolbar ) {
				node = activeToolbar.find( 'toolbar' )[0];
				node && node.focus( true );
			}
		} );

		editor.on( 'nodechange', function( event ) {
			var collapsed = editor.selection.isCollapsed();

			var args = {
				element: event.element,
				parents: event.parents,
				collapsed: collapsed
			};

			editor.fire( 'wptoolbar', args );

			currentSelection = args.selection || args.element;

			if ( activeToolbar && activeToolbar !== args.toolbar ) {
				activeToolbar.hide();
			}

			if ( args.toolbar ) {
				activeToolbar = args.toolbar;

				if ( activeToolbar.visible() ) {
					activeToolbar.reposition();
				} else {
					activeToolbar.show();
				}
			} else {
				activeToolbar = false;
			}
		} );

		editor.on( 'focus', function() {
			if ( activeToolbar ) {
				activeToolbar.show();
			}
		} );

		function hide( event ) {
			if ( activeToolbar ) {
				if ( activeToolbar.tempHide || event.type === 'hide' || event.type === 'blur' ) {
					activeToolbar.hide();
					activeToolbar = false;
				} else if ( (
					event.type === 'resizewindow' ||
					event.type === 'scrollwindow' ||
					event.type === 'resize' ||
					event.type === 'scroll'
				) && ! activeToolbar.blockHide ) {
					clearTimeout( timeout );

					timeout = setTimeout( function() {
						if ( activeToolbar && typeof activeToolbar.show === 'function' ) {
							activeToolbar.scrolling = false;
							activeToolbar.show();
						}
					}, 250 );

					activeToolbar.scrolling = true;
					activeToolbar.hide();
				}
			}
		}

		// For full height editor.
		editor.on( 'resizewindow scrollwindow', hide );
		// For scrollable editor.
		editor.dom.bind( editor.getWin(), 'resize scroll', hide );

		editor.on( 'remove', function() {
			editor.off( 'resizewindow scrollwindow', hide );
			editor.dom.unbind( editor.getWin(), 'resize scroll', hide );
		} );

		editor.on( 'blur hide', hide );

		editor.wp = editor.wp || {};
		editor.wp._createToolbar = create;
	}, true );

	function noop() {}

	// Expose some functions (back-compat)
	return {
		_showButtons: noop,
		_hideButtons: noop,
		_setEmbed: noop,
		_getEmbed: noop
	};
});

}( window.tinymce ));
