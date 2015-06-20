/* global adminpage, wpActiveEditor, quicktagsL10n, wpLink, prompt */
/*
 * Quicktags
 *
 * This is the HTML editor in WordPress. It can be attached to any textarea and will
 * append a toolbar above it. This script is self-contained (does not require external libraries).
 *
 * Run quicktags(settings) to initialize it, where settings is an object containing up to 3 properties:
 * settings = {
 *   id : 'my_id',          the HTML ID of the textarea, required
 *   buttons: ''            Comma separated list of the names of the default buttons to show. Optional.
 *                          Current list of default button names: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close';
 * }
 *
 * The settings can also be a string quicktags_id.
 *
 * quicktags_id string The ID of the textarea that will be the editor canvas
 * buttons string Comma separated list of the default buttons names that will be shown in that instance.
 */

// new edit toolbar used with permission
// by Alex King
// http://www.alexking.org/

var QTags, edCanvas,
	edButtons = [];

/* jshint ignore:start */

/**
 * Back-compat
 *
 * Define all former global functions so plugins that hack quicktags.js directly don't cause fatal errors.
 */
var edAddTag = function(){},
edCheckOpenTags = function(){},
edCloseAllTags = function(){},
edInsertImage = function(){},
edInsertLink = function(){},
edInsertTag = function(){},
edLink = function(){},
edQuickLink = function(){},
edRemoveTag = function(){},
edShowButton = function(){},
edShowLinks = function(){},
edSpell = function(){},
edToolbar = function(){};

/**
 * Initialize new instance of the Quicktags editor
 */
function quicktags(settings) {
	return new QTags(settings);
}

/**
 * Inserts content at the caret in the active editor (textarea)
 *
 * Added for back compatibility
 * @see QTags.insertContent()
 */
function edInsertContent(bah, txt) {
	return QTags.insertContent(txt);
}

/**
 * Adds a button to all instances of the editor
 *
 * Added for back compatibility, use QTags.addButton() as it gives more flexibility like type of button, button placement, etc.
 * @see QTags.addButton()
 */
function edButton(id, display, tagStart, tagEnd, access) {
	return QTags.addButton( id, display, tagStart, tagEnd, access, '', -1 );
}

/* jshint ignore:end */

(function(){
	// private stuff is prefixed with an underscore
	var _domReady = function(func) {
		var t, i, DOMContentLoaded, _tryReady;

		if ( typeof jQuery !== 'undefined' ) {
			jQuery(document).ready(func);
		} else {
			t = _domReady;
			t.funcs = [];

			t.ready = function() {
				if ( ! t.isReady ) {
					t.isReady = true;
					for ( i = 0; i < t.funcs.length; i++ ) {
						t.funcs[i]();
					}
				}
			};

			if ( t.isReady ) {
				func();
			} else {
				t.funcs.push(func);
			}

			if ( ! t.eventAttached ) {
				if ( document.addEventListener ) {
					DOMContentLoaded = function(){document.removeEventListener('DOMContentLoaded', DOMContentLoaded, false);t.ready();};
					document.addEventListener('DOMContentLoaded', DOMContentLoaded, false);
					window.addEventListener('load', t.ready, false);
				} else if ( document.attachEvent ) {
					DOMContentLoaded = function(){if (document.readyState === 'complete'){ document.detachEvent('onreadystatechange', DOMContentLoaded);t.ready();}};
					document.attachEvent('onreadystatechange', DOMContentLoaded);
					window.attachEvent('onload', t.ready);

					_tryReady = function() {
						try {
							document.documentElement.doScroll('left');
						} catch(e) {
							setTimeout(_tryReady, 50);
							return;
						}

						t.ready();
					};
					_tryReady();
				}

				t.eventAttached = true;
			}
		}
	},

	_datetime = (function() {
		var now = new Date(), zeroise;

		zeroise = function(number) {
			var str = number.toString();

			if ( str.length < 2 ) {
				str = '0' + str;
			}

			return str;
		};

		return now.getUTCFullYear() + '-' +
			zeroise( now.getUTCMonth() + 1 ) + '-' +
			zeroise( now.getUTCDate() ) + 'T' +
			zeroise( now.getUTCHours() ) + ':' +
			zeroise( now.getUTCMinutes() ) + ':' +
			zeroise( now.getUTCSeconds() ) +
			'+00:00';
	})(),
	qt;

	qt = QTags = function(settings) {
		if ( typeof(settings) === 'string' ) {
			settings = {id: settings};
		} else if ( typeof(settings) !== 'object' ) {
			return false;
		}

		var t = this,
			id = settings.id,
			canvas = document.getElementById(id),
			name = 'qt_' + id,
			tb, onclick, toolbar_id;

		if ( !id || !canvas ) {
			return false;
		}

		t.name = name;
		t.id = id;
		t.canvas = canvas;
		t.settings = settings;

		if ( id === 'content' && typeof(adminpage) === 'string' && ( adminpage === 'post-new-php' || adminpage === 'post-php' ) ) {
			// back compat hack :-(
			edCanvas = canvas;
			toolbar_id = 'ed_toolbar';
		} else {
			toolbar_id = name + '_toolbar';
		}

		tb = document.createElement('div');
		tb.id = toolbar_id;
		tb.className = 'quicktags-toolbar';
		tb.onclick = function() {
			window.wpActiveEditor = id;
		};

		canvas.parentNode.insertBefore(tb, canvas);
		t.toolbar = tb;

		// listen for click events
		onclick = function(e) {
			e = e || window.event;
			var target = e.target || e.srcElement, visible = target.clientWidth || target.offsetWidth, i;

			// don't call the callback on pressing the accesskey when the button is not visible
			if ( !visible ) {
				return;
			}

			// as long as it has the class ed_button, execute the callback
			if ( / ed_button /.test(' ' + target.className + ' ') ) {
				// we have to reassign canvas here
				t.canvas = canvas = document.getElementById(id);
				i = target.id.replace(name + '_', '');

				if ( t.theButtons[i] ) {
					t.theButtons[i].callback.call(t.theButtons[i], target, canvas, t);
				}
			}
		};

		if ( tb.addEventListener ) {
			tb.addEventListener('click', onclick, false);
		} else if ( tb.attachEvent ) {
			tb.attachEvent('onclick', onclick);
		}

		t.getButton = function(id) {
			return t.theButtons[id];
		};

		t.getButtonElement = function(id) {
			return document.getElementById(name + '_' + id);
		};

		qt.instances[id] = t;

		if ( ! qt.instances['0'] ) {
			qt.instances['0'] = qt.instances[id];
			_domReady( function(){ qt._buttonsInit(); } );
		}
	};

	function _escape( text ) {
		text = text || '';
		text = text.replace( /&([^#])(?![a-z1-4]{1,8};)/gi, '&#038;$1' );
		return text.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' ).replace( /'/g, '&#039;' );
	}

	qt.instances = {};

	qt.getInstance = function(id) {
		return qt.instances[id];
	};

	qt._buttonsInit = function() {
		var t = this, canvas, name, settings, theButtons, html, inst, ed, id, i, use,
			defaults = ',strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,';

		for ( inst in t.instances ) {
			if ( '0' === inst ) {
				continue;
			}

			ed = t.instances[inst];
			canvas = ed.canvas;
			name = ed.name;
			settings = ed.settings;
			html = '';
			theButtons = {};
			use = '';

			// set buttons
			if ( settings.buttons ) {
				use = ','+settings.buttons+',';
			}

			for ( i in edButtons ) {
				if ( !edButtons[i] ) {
					continue;
				}

				id = edButtons[i].id;
				if ( use && defaults.indexOf( ',' + id + ',' ) !== -1 && use.indexOf( ',' + id + ',' ) === -1 ) {
					continue;
				}

				if ( !edButtons[i].instance || edButtons[i].instance === inst ) {
					theButtons[id] = edButtons[i];

					if ( edButtons[i].html ) {
						html += edButtons[i].html(name + '_');
					}
				}
			}

			if ( use && use.indexOf(',dfw,') !== -1 ) {
				theButtons.dfw = new qt.DFWButton();
				html += theButtons.dfw.html( name + '_' );
			}

			if ( 'rtl' === document.getElementsByTagName('html')[0].dir ) {
				theButtons.textdirection = new qt.TextDirectionButton();
				html += theButtons.textdirection.html(name + '_');
			}

			ed.toolbar.innerHTML = html;
			ed.theButtons = theButtons;

			if ( typeof jQuery !== 'undefined' ) {
				jQuery( document ).triggerHandler( 'quicktags-init', [ ed ] );
			}
		}
		t.buttonsInitDone = true;
	};

	/**
	 * Main API function for adding a button to Quicktags
	 *
	 * Adds qt.Button or qt.TagButton depending on the args. The first three args are always required.
	 * To be able to add button(s) to Quicktags, your script should be enqueued as dependent
	 * on "quicktags" and outputted in the footer. If you are echoing JS directly from PHP,
	 * use add_action( 'admin_print_footer_scripts', 'output_my_js', 100 ) or add_action( 'wp_footer', 'output_my_js', 100 )
	 *
	 * Minimum required to add a button that calls an external function:
	 *     QTags.addButton( 'my_id', 'my button', my_callback );
	 *     function my_callback() { alert('yeah!'); }
	 *
	 * Minimum required to add a button that inserts a tag:
	 *     QTags.addButton( 'my_id', 'my button', '<span>', '</span>' );
	 *     QTags.addButton( 'my_id2', 'my button', '<br />' );
	 *
	 * @param string id Required. Button HTML ID
	 * @param string display Required. Button's value="..."
	 * @param string|function arg1 Required. Either a starting tag to be inserted like "<span>" or a callback that is executed when the button is clicked.
	 * @param string arg2 Optional. Ending tag like "</span>"
	 * @param string access_key Deprecated Not used
	 * @param string title Optional. Button's title="..."
	 * @param int priority Optional. Number representing the desired position of the button in the toolbar. 1 - 9 = first, 11 - 19 = second, 21 - 29 = third, etc.
	 * @param string instance Optional. Limit the button to a specific instance of Quicktags, add to all instances if not present.
	 * @param attr object Optional. Used to pass additional attributes. Currently supports `ariaLabel` and `ariaLabelClose` (for "close tag" state)
	 * @return mixed null or the button object that is needed for back-compat.
	 */
	qt.addButton = function( id, display, arg1, arg2, access_key, title, priority, instance, attr ) {
		var btn;

		if ( !id || !display ) {
			return;
		}

		priority = priority || 0;
		arg2 = arg2 || '';
		attr = attr || {};

		if ( typeof(arg1) === 'function' ) {
			btn = new qt.Button( id, display, access_key, title, instance, attr );
			btn.callback = arg1;
		} else if ( typeof(arg1) === 'string' ) {
			btn = new qt.TagButton( id, display, arg1, arg2, access_key, title, instance, attr );
		} else {
			return;
		}

		if ( priority === -1 ) { // back-compat
			return btn;
		}

		if ( priority > 0 ) {
			while ( typeof(edButtons[priority]) !== 'undefined' ) {
				priority++;
			}

			edButtons[priority] = btn;
		} else {
			edButtons[edButtons.length] = btn;
		}

		if ( this.buttonsInitDone ) {
			this._buttonsInit(); // add the button HTML to all instances toolbars if addButton() was called too late
		}
	};

	qt.insertContent = function(content) {
		var sel, startPos, endPos, scrollTop, text, canvas = document.getElementById(wpActiveEditor);

		if ( !canvas ) {
			return false;
		}

		if ( document.selection ) { //IE
			canvas.focus();
			sel = document.selection.createRange();
			sel.text = content;
			canvas.focus();
		} else if ( canvas.selectionStart || canvas.selectionStart === 0 ) { // FF, WebKit, Opera
			text = canvas.value;
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			scrollTop = canvas.scrollTop;

			canvas.value = text.substring(0, startPos) + content + text.substring(endPos, text.length);

			canvas.selectionStart = startPos + content.length;
			canvas.selectionEnd = startPos + content.length;
			canvas.scrollTop = scrollTop;
			canvas.focus();
		} else {
			canvas.value += content;
			canvas.focus();
		}
		return true;
	};

	// a plain, dumb button
	qt.Button = function( id, display, access, title, instance, attr ) {
		this.id = id;
		this.display = display;
		this.access = '';
		this.title = title || '';
		this.instance = instance || '';
		this.attr = attr || {};
	};
	qt.Button.prototype.html = function(idPrefix) {
		var active, on, wp,
			title = this.title ? ' title="' + _escape( this.title ) + '"' : '',
			ariaLabel = this.attr && this.attr.ariaLabel ? ' aria-label="' + _escape( this.attr.ariaLabel ) + '"' : '',
			val = this.display ? ' value="' + _escape( this.display ) + '"' : '',
			id = this.id ? ' id="' + _escape( idPrefix + this.id ) + '"' : '',
			dfw = ( wp = window.wp ) && wp.editor && wp.editor.dfw;

		if ( this.id === 'fullscreen' ) {
			return '<button type="button"' + id + ' class="ed_button qt-dfw qt-fullscreen"' + title + ariaLabel + '></button>';
		} else if ( this.id === 'dfw' ) {
			active = dfw && dfw.isActive() ? '' : ' disabled="disabled"';
			on = dfw && dfw.isOn() ? ' active' : '';

			return '<button type="button"' + id + ' class="ed_button qt-dfw' + on + '"' + title + ariaLabel + active + '></button>';
		}

		return '<input type="button"' + id + ' class="ed_button button button-small"' + title + ariaLabel + val + ' />';
	};
	qt.Button.prototype.callback = function(){};

	// a button that inserts HTML tag
	qt.TagButton = function( id, display, tagStart, tagEnd, access, title, instance, attr ) {
		var t = this;
		qt.Button.call( t, id, display, access, title, instance, attr );
		t.tagStart = tagStart;
		t.tagEnd = tagEnd;
	};
	qt.TagButton.prototype = new qt.Button();
	qt.TagButton.prototype.openTag = function( element, ed ) {
		if ( ! ed.openTags ) {
			ed.openTags = [];
		}

		if ( this.tagEnd ) {
			ed.openTags.push( this.id );
			element.value = '/' + element.value;

			if ( this.attr.ariaLabelClose ) {
				element.setAttribute( 'aria-label', this.attr.ariaLabelClose );
			}
		}
	};
	qt.TagButton.prototype.closeTag = function( element, ed ) {
		var i = this.isOpen(ed);

		if ( i !== false ) {
			ed.openTags.splice( i, 1 );
		}

		element.value = this.display;

		if ( this.attr.ariaLabel ) {
			element.setAttribute( 'aria-label', this.attr.ariaLabel );
		}
	};
	// whether a tag is open or not. Returns false if not open, or current open depth of the tag
	qt.TagButton.prototype.isOpen = function (ed) {
		var t = this, i = 0, ret = false;
		if ( ed.openTags ) {
			while ( ret === false && i < ed.openTags.length ) {
				ret = ed.openTags[i] === t.id ? i : false;
				i ++;
			}
		} else {
			ret = false;
		}
		return ret;
	};
	qt.TagButton.prototype.callback = function(element, canvas, ed) {
		var t = this, startPos, endPos, cursorPos, scrollTop, v = canvas.value, l, r, i, sel, endTag = v ? t.tagEnd : '';

		if ( document.selection ) { // IE
			canvas.focus();
			sel = document.selection.createRange();
			if ( sel.text.length > 0 ) {
				if ( !t.tagEnd ) {
					sel.text = sel.text + t.tagStart;
				} else {
					sel.text = t.tagStart + sel.text + endTag;
				}
			} else {
				if ( !t.tagEnd ) {
					sel.text = t.tagStart;
				} else if ( t.isOpen(ed) === false ) {
					sel.text = t.tagStart;
					t.openTag(element, ed);
				} else {
					sel.text = endTag;
					t.closeTag(element, ed);
				}
			}
			canvas.focus();
		} else if ( canvas.selectionStart || canvas.selectionStart === 0 ) { // FF, WebKit, Opera
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			cursorPos = endPos;
			scrollTop = canvas.scrollTop;
			l = v.substring(0, startPos); // left of the selection
			r = v.substring(endPos, v.length); // right of the selection
			i = v.substring(startPos, endPos); // inside the selection
			if ( startPos !== endPos ) {
				if ( !t.tagEnd ) {
					canvas.value = l + i + t.tagStart + r; // insert self closing tags after the selection
					cursorPos += t.tagStart.length;
				} else {
					canvas.value = l + t.tagStart + i + endTag + r;
					cursorPos += t.tagStart.length + endTag.length;
				}
			} else {
				if ( !t.tagEnd ) {
					canvas.value = l + t.tagStart + r;
					cursorPos = startPos + t.tagStart.length;
				} else if ( t.isOpen(ed) === false ) {
					canvas.value = l + t.tagStart + r;
					t.openTag(element, ed);
					cursorPos = startPos + t.tagStart.length;
				} else {
					canvas.value = l + endTag + r;
					cursorPos = startPos + endTag.length;
					t.closeTag(element, ed);
				}
			}

			canvas.selectionStart = cursorPos;
			canvas.selectionEnd = cursorPos;
			canvas.scrollTop = scrollTop;
			canvas.focus();
		} else { // other browsers?
			if ( !endTag ) {
				canvas.value += t.tagStart;
			} else if ( t.isOpen(ed) !== false ) {
				canvas.value += t.tagStart;
				t.openTag(element, ed);
			} else {
				canvas.value += endTag;
				t.closeTag(element, ed);
			}
			canvas.focus();
		}
	};

	// removed
	qt.SpellButton = function() {};

	// the close tags button
	qt.CloseButton = function() {
		qt.Button.call( this, 'close', quicktagsL10n.closeTags, '', quicktagsL10n.closeAllOpenTags );
	};

	qt.CloseButton.prototype = new qt.Button();

	qt._close = function(e, c, ed) {
		var button, element, tbo = ed.openTags;

		if ( tbo ) {
			while ( tbo.length > 0 ) {
				button = ed.getButton(tbo[tbo.length - 1]);
				element = document.getElementById(ed.name + '_' + button.id);

				if ( e ) {
					button.callback.call(button, element, c, ed);
				} else {
					button.closeTag(element, ed);
				}
			}
		}
	};

	qt.CloseButton.prototype.callback = qt._close;

	qt.closeAllTags = function(editor_id) {
		var ed = this.getInstance(editor_id);
		qt._close('', ed.canvas, ed);
	};

	// the link button
	qt.LinkButton = function() {
		var attr = {
			ariaLabel: quicktagsL10n.link
		};

		qt.TagButton.call( this, 'link', 'link', '', '</a>', '', '', '', attr );
	};
	qt.LinkButton.prototype = new qt.TagButton();
	qt.LinkButton.prototype.callback = function(e, c, ed, defaultValue) {
		var URL, t = this;

		if ( typeof wpLink !== 'undefined' ) {
			wpLink.open( ed.id );
			return;
		}

		if ( ! defaultValue ) {
			defaultValue = 'http://';
		}

		if ( t.isOpen(ed) === false ) {
			URL = prompt( quicktagsL10n.enterURL, defaultValue );
			if ( URL ) {
				t.tagStart = '<a href="' + URL + '">';
				qt.TagButton.prototype.callback.call(t, e, c, ed);
			}
		} else {
			qt.TagButton.prototype.callback.call(t, e, c, ed);
		}
	};

	// the img button
	qt.ImgButton = function() {
		var attr = {
			ariaLabel: quicktagsL10n.image
		};

		qt.TagButton.call( this, 'img', 'img', '', '', '', '', '', attr );
	};
	qt.ImgButton.prototype = new qt.TagButton();
	qt.ImgButton.prototype.callback = function(e, c, ed, defaultValue) {
		if ( ! defaultValue ) {
			defaultValue = 'http://';
		}
		var src = prompt(quicktagsL10n.enterImageURL, defaultValue), alt;
		if ( src ) {
			alt = prompt(quicktagsL10n.enterImageDescription, '');
			this.tagStart = '<img src="' + src + '" alt="' + alt + '" />';
			qt.TagButton.prototype.callback.call(this, e, c, ed);
		}
	};

	qt.DFWButton = function() {
		qt.Button.call( this, 'dfw', '', 'f', quicktagsL10n.dfw );
	};
	qt.DFWButton.prototype = new qt.Button();
	qt.DFWButton.prototype.callback = function() {
		var wp;

		if ( ! ( wp = window.wp ) || ! wp.editor || ! wp.editor.dfw ) {
			return;
		}

		window.wp.editor.dfw.toggle();
	};

	qt.TextDirectionButton = function() {
		qt.Button.call( this, 'textdirection', quicktagsL10n.textdirection, '', quicktagsL10n.toggleTextdirection );
	};
	qt.TextDirectionButton.prototype = new qt.Button();
	qt.TextDirectionButton.prototype.callback = function(e, c) {
		var isRTL = ( 'rtl' === document.getElementsByTagName('html')[0].dir ),
			currentDirection = c.style.direction;

		if ( ! currentDirection ) {
			currentDirection = ( isRTL ) ? 'rtl' : 'ltr';
		}

		c.style.direction = ( 'rtl' === currentDirection ) ? 'ltr' : 'rtl';
		c.focus();
	};

	// ensure backward compatibility
	edButtons[10]  = new qt.TagButton( 'strong', 'b', '<strong>', '</strong>', '', '', '', { ariaLabel: quicktagsL10n.strong, ariaLabelClose: quicktagsL10n.strongClose } );
	edButtons[20]  = new qt.TagButton( 'em', 'i', '<em>', '</em>', '', '', '', { ariaLabel: quicktagsL10n.em, ariaLabelClose: quicktagsL10n.emClose } );
	edButtons[30]  = new qt.LinkButton(); // special case
	edButtons[40]  = new qt.TagButton( 'block', 'b-quote', '\n\n<blockquote>', '</blockquote>\n\n', '', '', '', { ariaLabel: quicktagsL10n.blockquote, ariaLabelClose: quicktagsL10n.blockquoteClose } );
	edButtons[50]  = new qt.TagButton( 'del', 'del', '<del datetime="' + _datetime + '">', '</del>', '', '', '', { ariaLabel: quicktagsL10n.del, ariaLabelClose: quicktagsL10n.delClose } );
	edButtons[60]  = new qt.TagButton( 'ins', 'ins', '<ins datetime="' + _datetime + '">', '</ins>', '', '', '', { ariaLabel: quicktagsL10n.ins, ariaLabelClose: quicktagsL10n.insClose } );
	edButtons[70]  = new qt.ImgButton(); // special case
	edButtons[80]  = new qt.TagButton( 'ul', 'ul', '<ul>\n', '</ul>\n\n', '', '', '', { ariaLabel: quicktagsL10n.ul, ariaLabelClose: quicktagsL10n.ulClose } );
	edButtons[90]  = new qt.TagButton( 'ol', 'ol', '<ol>\n', '</ol>\n\n', '', '', '', { ariaLabel: quicktagsL10n.ol, ariaLabelClose: quicktagsL10n.olClose } );
	edButtons[100] = new qt.TagButton( 'li', 'li', '\t<li>', '</li>\n', '', '', '', { ariaLabel: quicktagsL10n.li, ariaLabelClose: quicktagsL10n.liClose } );
	edButtons[110] = new qt.TagButton( 'code', 'code', '<code>', '</code>', '', '', '', { ariaLabel: quicktagsL10n.code, ariaLabelClose: quicktagsL10n.codeClose } );
	edButtons[120] = new qt.TagButton( 'more', 'more', '<!--more-->\n\n', '', '', '', '', { ariaLabel: quicktagsL10n.more } );
	edButtons[140] = new qt.CloseButton();

})();
