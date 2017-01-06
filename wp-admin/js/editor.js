
( function( $ ) {
	/**
	 * @summary Creates the tinyMCE editors.
	 *
	 * Creates the tinyMCE editor and binds all events used for switching
	 * from visual to text mode.
	 *
	 * @since 4.3.0
	 *
	 * @class
	 */
	function SwitchEditors() {
		var tinymce, $$,
			exports = {};

		/**
		 * @summary Initializes the event binding for switching editors.
		 *
		 * @since 4.3.0
		 *
		 * @returns {void}
		 */
		function init() {
			if ( ! tinymce && window.tinymce ) {
				tinymce = window.tinymce;
				$$ = tinymce.$;

				/**
				 * @summary Handles onclick events for the editor buttons.
				 *
				 * @since 4.3.0
				 *
				 * Handles an onclick event on the document.
				 * Switches the editor between visual and text,
				 * if the clicked element has the 'wp-switch-editor' class.
				 * If the class name is switch-html switches to the HTML editor,
				 * if the class name is switch-tmce
				 * switches to the TMCE editor.
				 *
				 * @returns {void}
				 */
				$$( document ).on( 'click', function( event ) {
					var id, mode,
						target = $$( event.target );

					if ( target.hasClass( 'wp-switch-editor' ) ) {
						id = target.attr( 'data-wp-editor-id' );
						mode = target.hasClass( 'switch-tmce' ) ? 'tmce' : 'html';
						switchEditor( id, mode );
					}
				});
			}
		}

		/**
		 * @summary Retrieves the height of the toolbar based on the container the
		 * editor is placed in.
		 *
		 * @since 3.9.0
		 *
		 * @param {Object} editor The tinyMCE editor.
		 * @returns {number} If the height is between 10 and 200 return the height,
		 * else return 30.
		 */
		function getToolbarHeight( editor ) {
			var node = $$( '.mce-toolbar-grp', editor.getContainer() )[0],
				height = node && node.clientHeight;

			if ( height && height > 10 && height < 200 ) {
				return parseInt( height, 10 );
			}

			return 30;
		}

		/**
		 * @summary Switches the editor between visual and text.
		 *
		 * @since 4.3.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {string} id The id of the editor you want to change the editor mode for.
		 * If no id is given, it defaults to content.
		 * @param {string} mode The mode you want to switch to.
		 * If an undefined mode is given, it defaults to toggle.
		 *
		 * @returns {void}
		 */
		function switchEditor( id, mode ) {
			id = id || 'content';
			mode = mode || 'toggle';

			var editorHeight, toolbarHeight, iframe,
				editor = tinymce.get( id ),
				wrap = $$( '#wp-' + id + '-wrap' ),
				$textarea = $$( '#' + id ),
				textarea = $textarea[0];

			// Toggle the mode between visual and textual representation.
			if ( 'toggle' === mode ) {
				if ( editor && ! editor.isHidden() ) {
					mode = 'html';
				} else {
					mode = 'tmce';
				}
			}


			// If the mode is tmce or tinymce, show the editor.
			if ( 'tmce' === mode || 'tinymce' === mode ) {

				/*
				 * If the editor isn't hidden we are already in tmce mode
				 * and we don't need to switch.
				 * Return false to stop event bubbling.
				 */
				if ( editor && ! editor.isHidden() ) {
					return false;
				}

				// Close the QuickTags toolbars if they are visible.
				if ( typeof( window.QTags ) !== 'undefined' ) {
					window.QTags.closeAllTags( id );
				}

				editorHeight = parseInt( textarea.style.height, 10 ) || 0;

				if ( editor ) {
					editor.show();

					// Don't resize the iframe in iOS.
					if ( ! tinymce.Env.iOS && editorHeight ) {
						toolbarHeight = getToolbarHeight( editor );
						editorHeight = editorHeight - toolbarHeight + 14;

						// Height must be between 50 and 5000.
						if ( editorHeight > 50 && editorHeight < 5000 ) {
							editor.theme.resizeTo( null, editorHeight );
						}
					}
				} else {
					tinymce.init( window.tinyMCEPreInit.mceInit[id] );
				}

				wrap.removeClass( 'html-active' ).addClass( 'tmce-active' );
				$textarea.attr( 'aria-hidden', true );
				window.setUserSetting( 'editor', 'tinymce' );

			// Hide the editor if mode is html.
			} else if ( 'html' === mode ) {

				/*
				 * If the editor is hidden we are already in html mode and
				 * we don't need to switch.
				 * Return false to stop event bubbling.
				 */
				if ( editor && editor.isHidden() ) {
					return false;
				}

				if ( editor ) {

					// Don't resize the iframe in iOS.
					if ( ! tinymce.Env.iOS ) {
						iframe = editor.iframeElement;
						editorHeight = iframe ? parseInt( iframe.style.height, 10 ) : 0;

						if ( editorHeight ) {
							toolbarHeight = getToolbarHeight( editor );
							editorHeight = editorHeight + toolbarHeight - 14;

							// Height must be between 50 and 5000.
							if ( editorHeight > 50 && editorHeight < 5000 ) {
								textarea.style.height = editorHeight + 'px';
							}
						}
					}

					editor.hide();
				} else {
					// The TinyMCE instance doesn't exist, show the textarea.
					$textarea.css({ 'display': '', 'visibility': '' });
				}

				wrap.removeClass( 'tmce-active' ).addClass( 'html-active' );
				$textarea.attr( 'aria-hidden', false );
				window.setUserSetting( 'editor', 'html' );
			}
		}

		/**
		 * @summary Replaces all paragraphs with double line breaks.
		 *
		 * Replaces all paragraphs with double line breaks. Taking into account
		 * the elements where the <p> should be preserved.
		 * Unifies all whitespaces.
		 * Adds indenting with tabs to li, dt and dd elements.
		 * Trims whitespaces from beginning and end of the html input.
		 *
		 * @since 4.3.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {string} html The content from the editor.
		 * @return {string} The formatted html string.
		 */
		function removep( html ) {
			var blocklist = 'blockquote|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|th|td|h[1-6]|fieldset',
				blocklist1 = blocklist + '|div|p',
				blocklist2 = blocklist + '|pre',
				preserve_linebreaks = false,
				preserve_br = false,
				preserve = [];

			if ( ! html ) {
				return '';
			}

			/*
			 * Protect script and style tags by replacing them with <wp-preserve>.
			 * Push matches into the preserve array.
			 */
			if ( html.indexOf( '<script' ) !== -1 || html.indexOf( '<style' ) !== -1 ) {
				html = html.replace( /<(script|style)[^>]*>[\s\S]*?<\/\1>/g, function( match ) {
					preserve.push( match );
					return '<wp-preserve>';
				} );
			}

			/*
			 * Protect pre tags by replacing all newlines and
			 * <br> tags with <wp-line-break>.
			 */
			if ( html.indexOf( '<pre' ) !== -1 ) {
				preserve_linebreaks = true;
				html = html.replace( /<pre[^>]*>[\s\S]+?<\/pre>/g, function( a ) {
					a = a.replace( /<br ?\/?>(\r\n|\n)?/g, '<wp-line-break>' );
					a = a.replace( /<\/?p( [^>]*)?>(\r\n|\n)?/g, '<wp-line-break>' );
					return a.replace( /\r?\n/g, '<wp-line-break>' );
				});
			}

			/*
			 * Keep <br> tags inside captions and remove line breaks by replacing
			 * them with <wp-temp-br>.
			 */
			if ( html.indexOf( '[caption' ) !== -1 ) {
				preserve_br = true;
				html = html.replace( /\[caption[\s\S]+?\[\/caption\]/g, function( a ) {
					return a.replace( /<br([^>]*)>/g, '<wp-temp-br$1>' ).replace( /[\r\n\t]+/, '' );
				});
			}

			// Format the text to be readable in the source editor.
			html = html.replace( new RegExp( '\\s*</(' + blocklist1 + ')>\\s*', 'g' ), '</$1>\n' );
			html = html.replace( new RegExp( '\\s*<((?:' + blocklist1 + ')(?: [^>]*)?)>', 'g' ), '\n<$1>' );

			// Mark </p> if it has any attributes.
			html = html.replace( /(<p [^>]+>.*?)<\/p>/g, '$1</p#>' );

			// If the content of a container starts with a paragraph, replace the <p> tag with 2 newlines.
			html = html.replace( /<div( [^>]*)?>\s*<p>/gi, '<div$1>\n\n' );

			// Remove <p> and </p> tags.
			html = html.replace( /\s*<p>/gi, '' );
			html = html.replace( /\s*<\/p>\s*/gi, '\n\n' );

			// Remove white spaces between newlines. u00a0 is a no breaking space.
			html = html.replace( /\n[\s\u00a0]+\n/g, '\n\n' );

			// Remove <br> tags.
			html = html.replace( /\s*<br ?\/?>\s*/gi, '\n' );

			/*
			 * Fix some block element newline issues.
			 * Replace white spaces with newlines in combination with <div> tags.
			 */
			html = html.replace( /\s*<div/g, '\n<div' );
			html = html.replace( /<\/div>\s*/g, '</div>\n' );

			// Replace white spaces with newlines in combination with [caption] shortcodes.
			html = html.replace( /\s*\[caption([^\[]+)\[\/caption\]\s*/gi, '\n\n[caption$1[/caption]\n\n' );

			/*
			 * Limit the newlines in combination with [caption]'s to a maximum of
			 * two consecutive occurrences.
			 * .
			 */
			html = html.replace( /caption\]\n\n+\[caption/g, 'caption]\n\n[caption' );

			/*
			 * Replace white spaces with newlines in combination with
			 * all elements listed in blocklist2.
			 */
			html = html.replace( new RegExp('\\s*<((?:' + blocklist2 + ')(?: [^>]*)?)\\s*>', 'g' ), '\n<$1>' );
			html = html.replace( new RegExp('\\s*</(' + blocklist2 + ')>\\s*', 'g' ), '</$1>\n' );

			// Add indentation by adding a tab in front of <li>, <dt> and <dd> tags.
			html = html.replace( /<((li|dt|dd)[^>]*)>/g, ' \t<$1>' );

			// Replace white spaces with newlines in combination with <select> and <option> tags.
			if ( html.indexOf( '<option' ) !== -1 ) {
				html = html.replace( /\s*<option/g, '\n<option' );
				html = html.replace( /\s*<\/select>/g, '\n</select>' );
			}

			// Replace white spaces with 2 newlines in combination with <hr> tags.
			if ( html.indexOf( '<hr' ) !== -1 ) {
				html = html.replace( /\s*<hr( [^>]*)?>\s*/g, '\n\n<hr$1>\n\n' );
			}

			// Remove newlines in <object> tags.
			if ( html.indexOf( '<object' ) !== -1 ) {
				html = html.replace( /<object[\s\S]+?<\/object>/g, function( a ) {
					return a.replace( /[\r\n]+/g, '' );
				});
			}

			// Unmark special paragraph closing tags.
			html = html.replace( /<\/p#>/g, '</p>\n' );


			// Add a new line before <p> tags when there is content inside the paragraph
			html = html.replace( /\s*(<p [^>]+>[\s\S]*?<\/p>)/g, '\n$1' );

			/*
			 * Remove whitespaces at the start and end of a string.
			 * u00a0 is a no breaking space.
			 */
			html = html.replace( /^\s+/, '' );
			html = html.replace( /[\s\u00a0]+$/, '' );

			// Replace <wp-line-break> tags with a newline.
			if ( preserve_linebreaks ) {
				html = html.replace( /<wp-line-break>/g, '\n' );
			}

			// Restore the <wp-temp-br> with <br> tags.
			if ( preserve_br ) {
				html = html.replace( /<wp-temp-br([^>]*)>/g, '<br$1>' );
			}

			// Restore preserved tags.
			if ( preserve.length ) {
				html = html.replace( /<wp-preserve>/g, function() {
					return preserve.shift();
				} );
			}

			return html;
		}

		/**
		 * @summary Adds paragraph tags to the text.
		 *
		 * Adds paragraph tags to the text taking into account block level elements.
		 * Normalizes the whitespaces and newlines.
		 *
		 * Similar to `wpautop()` in formatting.php.
		 *
		 * @since 4.3.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {string} text The text input.
		 * @returns {string} The formatted text.
		 */
		function autop( text ) {
			var preserve_linebreaks = false,
				preserve_br = false,

				// A list containing all block level elements.
				blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre' +
					'|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section' +
					'|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary';

			// Normalize line breaks.
			text = text.replace( /\r\n|\r/g, '\n' );

			// If there are no newlines, return the text.
			if ( text.indexOf( '\n' ) === -1 ) {
				return text;
			}

			if ( text.indexOf( '<object' ) !== -1 ) {

				// If there are multiple newlines in an <object>, remove them.
				text = text.replace( /<object[\s\S]+?<\/object>/g, function( a ) {
					return a.replace( /\n+/g, '' );
				});
			}

			// Replace all new lines and tabs with spaces inside tags.
			text = text.replace( /<[^<>]+>/g, function( a ) {
				return a.replace( /[\n\t ]+/g, ' ' );
			});

			// Protect <pre> and <script> tags by replacing them with <wp-line-break>.
			if ( text.indexOf( '<pre' ) !== -1 || text.indexOf( '<script' ) !== -1 ) {
				preserve_linebreaks = true;
				text = text.replace( /<(pre|script)[^>]*>[\s\S]*?<\/\1>/g, function( a ) {
					return a.replace( /\n/g, '<wp-line-break>' );
				});
			}

			// Keep <br> tags inside captions.
			if ( text.indexOf( '[caption' ) !== -1 ) {
				preserve_br = true;

				// Replace all white spaces and <br> tags with <wp-temp-br>.
				text = text.replace( /\[caption[\s\S]+?\[\/caption\]/g, function( a ) {

					// Protect <br> tags by converting them to <wp-temp-br> tags.
					a = a.replace( /<br([^>]*)>/g, '<wp-temp-br$1>' );

					// Replace all new lines and tabs with spaces inside HTML tags.
					a = a.replace( /<[^<>]+>/g, function( b ) {

						// Replace newlines and tabs with a space.
						return b.replace( /[\n\t ]+/, ' ' );
					});

					// Convert remaining line breaks to <wp-temp-br />.
					return a.replace( /\s*\n\s*/g, '<wp-temp-br />' );
				});
			}

			// Append 2 newlines at the end of the text.
			text = text + '\n\n';

			/*
			 * Replace a <br> tag followed by 1 or more spaces
			 * and another <br> tag with 2 newlines.
			 */
			text = text.replace( /<br \/>\s*<br \/>/gi, '\n\n' );

			/*
			 * Replace a block level element open tag with 2 newlines
			 * followed by the captured block level element.
			 */
			text = text.replace( new RegExp( '(<(?:' + blocklist + ')(?: [^>]*)?>)', 'gi' ), '\n\n$1' );

			/*
			 * Replace a block level element closing tag with the captured
			 * block level element followed by 2 newlines.
			 */
			text = text.replace( new RegExp( '(</(?:' + blocklist + ')>)', 'gi' ), '$1\n\n' );

			// Add 2 newlines to a <hr> tag. <hr> is a self closing block element.
			text = text.replace( /<hr( [^>]*)?>/gi, '<hr$1>\n\n' );

			// Remove the spaces before an <option> tag.
			text = text.replace( /\s*<option/gi, '<option' );

			// Remove the spaces after an <option> closing tag.
			text = text.replace( /<\/option>\s*/gi, '</option>' );

			// Remove the spaces between two newlines.
			text = text.replace( /\n\s*\n+/g, '\n\n' );

			// Convert 2 newlines to a paragraph and a single newline.
			text = text.replace( /([\s\S]+?)\n\n/g, '<p>$1</p>\n' );

			// Remove empty paragraphs.
			text = text.replace( /<p>\s*?<\/p>/gi, '');

			// Remove spaces and <p> tags around block level elements.
			text = text.replace( new RegExp( '<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi' ), '$1' );

			// Remove <p> tags around li elements.
			text = text.replace( /<p>(<li.+?)<\/p>/gi, '$1');

			// Remove spaces and <p> tags from blockquotes.
			text = text.replace( /<p>\s*<blockquote([^>]*)>/gi, '<blockquote$1><p>');

			// Place the <blockquote> outside of the paragraph.
			text = text.replace( /<\/blockquote>\s*<\/p>/gi, '</p></blockquote>');

			// Remove spaces at the start and <p> tags from block level elements.
			text = text.replace( new RegExp( '<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)', 'gi' ), '$1' );

			// Remove spaces at the end and <p> tags from block level elements.
			text = text.replace( new RegExp( '(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi' ), '$1' );

			// Remove spaces and newlines after a <br> tag.
			text = text.replace( /(<br[^>]*>)\s*\n/gi, '$1' );

			// Replace spaces followed by a newline with a <br> tag followed by a new line.
			text = text.replace( /\s*\n/g, '<br />\n');

			// Remove <br> tag that follows a block element directly, ignoring spaces.
			text = text.replace( new RegExp( '(</?(?:' + blocklist + ')[^>]*>)\\s*<br />', 'gi' ), '$1' );

			/*
			 * Remove a br tag preceding white spaces followed by a
			 * <p>, <li>, <div>, <dl>, <dd>, <dt>, <th>, <pre>, <td>, <ul>, or <ol> tag.
			 */
			text = text.replace( /<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi, '$1' );

			// Remove white spaces, <p> and <br> tags in captions.
			text = text.replace( /(?:<p>|<br ?\/?>)*\s*\[caption([^\[]+)\[\/caption\]\s*(?:<\/p>|<br ?\/?>)*/gi, '[caption$1[/caption]' );

			/**
			 * @summary Makes sure there is a paragraph open tag for a close tag.
			 *
			 * @since 2.9.0
			 *
			 * Makes sure there is a paragraph open tag when there is a paragraph close tag
			 * in a div, th, td, form, fieldset or dd element.
			 * @param {string} a The complete match.
			 * @param {string} b The first capture group.
			 * @param {string} c The second capture group.
			 * @returns {string} The string in paragraph tags.
			 */
			text = text.replace( /(<(?:div|th|td|form|fieldset|dd)[^>]*>)(.*?)<\/p>/g, function( a, b, c ) {

				/*
				 * Check if the matched group has a p open tag in it. If so, we don't need to
				 * enclose it with a paragraph.
				 */
				if ( c.match( /<p( [^>]*)?>/ ) ) {
					return a;
				}

				/*
				 * If there is no p open tag in the matched string,
				 * add it and return the string including p tags.
				 */
				return b + '<p>' + c + '</p>';
			});

			// Restore the line breaks in <pre> and <script> tags.
			if ( preserve_linebreaks ) {
				text = text.replace( /<wp-line-break>/g, '\n' );
			}

			// Restore the <br> tags in captions.
			if ( preserve_br ) {
				text = text.replace( /<wp-temp-br([^>]*)>/g, '<br$1>' );
			}

			return text;
		}

		/**
		 * @summary Modifies the data when a switch is made from HTML to text.
		 *
		 * Modifies the data when a switch is made.
		 * Remove the <p> tags from text.
		 * Returns the modified text.
		 * Adds a trigger on beforePreWpautop and afterPreWpautop.
		 *
		 * @since 2.5.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {String} html The content from the visual editor.
		 * @returns {String} the modified text.
		 */
		function pre_wpautop( html ) {
			var obj = { o: exports, data: html, unfiltered: html };

			if ( $ ) {
				$( 'body' ).trigger( 'beforePreWpautop', [ obj ] );
			}

			obj.data = removep( obj.data );

			if ( $ ) {
				$( 'body' ).trigger( 'afterPreWpautop', [ obj ] );
			}

			return obj.data;
		}

		/**
		 * @summary Modifies the data when a switch is made from text to HTML.
		 *
		 * Modifies the data when a switch is made. Runs autop to add p tags from text.
		 * Returns the modified text. Adds a trigger on beforeWpautop and afterWpautop.
		 *
		 * @since 2.5.0
		 *
		 * @memberof switchEditors
		 *
		 * @param {String} text The content from the text editor.
		 * @returns {String} the modified text.
		 */
		function wpautop( text ) {
			var obj = { o: exports, data: text, unfiltered: text };

			if ( $ ) {
				$( 'body' ).trigger( 'beforeWpautop', [ obj ] );
			}

			obj.data = autop( obj.data );

			if ( $ ) {
				$( 'body' ).trigger( 'afterWpautop', [ obj ] );
			}

			return obj.data;
		}

		// Bind the init function to be run when the document is loaded.
		if ( $ ) {
			$( document ).ready( init );
		} else if ( document.addEventListener ) {

			// Use the addEventListener to bind the init event on document load.
			document.addEventListener( 'DOMContentLoaded', init, false );
			window.addEventListener( 'load', init, false );

		} else if ( window.attachEvent ) {

			// Use the addEvent to bind the init event on document load.
			window.attachEvent( 'onload', init );
			document.attachEvent( 'onreadystatechange', function() {
				if ( 'complete' === document.readyState ) {
					init();
				}
			} );
		}

		/*
		 * Make sure the window.wp object exists so autop and removep
		 * can be bound to it.
		 */
		window.wp = window.wp || {};
		window.wp.editor = window.wp.editor || {};
		window.wp.editor.autop = wpautop;
		window.wp.editor.removep = pre_wpautop;

		exports = {
			go: switchEditor,
			wpautop: wpautop,
			pre_wpautop: pre_wpautop,
			_wp_Autop: autop,
			_wp_Nop: removep
		};

		return exports;
	}

	/**
	 * @namespace {SwitchEditors} switchEditors
	 * Expose the switch editors to be used globally.
	 */
	window.switchEditors = new SwitchEditors();
}( window.jQuery ));
