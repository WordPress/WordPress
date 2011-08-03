/*
 * Quicktags
 * 
 * This is the HTML editor in WordPress. It can be attached to any textarea and will
 * append a toolbar above it. This script is self-contained (does not require external libraries).
 *
 * Run quicktags(settings) to initialize it, where settings is an object containing up to 3 properties:
 * settings = {
 *   quicktags_id: 'myid', // required
 *   buttons: '', // optional
 *   disabled_buttons: '' // optional
 * }
 *
 * The settings can also be a string quicktags_id.
 *
 * quicktags_id The ID of the textarea that will be the editor canvas
 * buttons Comma separated list of the buttons IDs that will be shown. Buttons added by plugins
 * will not show. Default: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,spell,close'
 * disabled_buttons Comma separated list of the buttons IDs that should be excluded. Buttons
 * added by plugins will show unless specifically disabled.
 */

// new edit toolbar used with permission
// by Alex King
// http://www.alexking.org/

var QTags, edButtons, edButton;

function quicktags(settings) {
	return new QTags(settings);
}

(function(){
	// private stuff is prefixed with an underscore
	var _domReady = function(func) {
		var t, i,  DOMContentLoaded;

		if ( typeof jQuery != 'undefined' ) {
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

					(function(){
						try {
							document.documentElement.doScroll("left");
						} catch(e) {
							setTimeout(arguments.callee, 50);
							return;
						}

						t.ready();
					})();
				}

				t.eventAttached = true;
			}
		}
	},

	_datetime = (function() {
		var now = new Date(), zeroise;

		zeroise = function(number) {
			var str = number.toString();

			if ( str.length < 2 )
				str = "0" + str;

			return str;
		}

		return now.getUTCFullYear() + '-' +
			zeroise( now.getUTCMonth() + 1 ) + '-' +
			zeroise( now.getUTCDate() ) + 'T' +
			zeroise( now.getUTCHours() ) + ':' +
			zeroise( now.getUTCMinutes() ) + ':' +
			zeroise( now.getUTCSeconds() ) +
			'+00:00';
	})(),

	_customButtons = {},
	qt;

	qt = QTags = function(settings) {
		if ( typeof(settings) == 'string' )
			settings = {quicktags_id: settings};
		else if ( typeof(settings) != 'object' )
			return false;

		var t = this,
			id = settings.quicktags_id,
			buttons = {},
			theButtons = {},
			canvas = document.getElementById(id),
			name = 'qt_' + id,
			html = '',
			i, tb, qb, btn, onclick;

		if ( !id || !canvas )
			return false;

		t.name = name;
		t.id = id;

		// default buttons
		for ( i in edButtons ) {
			buttons[edButtons[i].id] = edButtons[i];
		}

		if ( id == 'content' && adminpage && ( adminpage == 'post-new-php' || adminpage == 'post-php' ) )
			buttons['fullscreen'] = new qt.FullscreenButton();

		// add custom buttons
		for ( i in t._customButtons ) {
			buttons[i] = new t._customButtons[i]();
		}

		if ( settings.quicktags_buttons ) {
			qb = settings.quicktags_buttons.split(',');

			for ( i in qb ) {
				btn = qb[i];
				if ( buttons[btn] )
					theButtons[btn] = buttons[btn];
			}
		} else {
			theButtons = buttons;
		}

		if ( settings.quicktags_disabled_buttons ) {
			qb = settings.quicktags_disabled_buttons.split(',');

			for ( i in qb ) {
				btn = qb[i];
				if ( theButtons[btn] )
					delete(theButtons[btn]);
			}
		}

		for ( i in theButtons )
			html += theButtons[i].html(name + '_');

		tb = document.createElement('div');
		tb.id = name + '_toolbar';
		tb.className = 'quicktags-toolbar';

		canvas.parentNode.insertBefore(tb, canvas);

		tb.innerHTML = html;
		t.toolbar = tb;

		// listen for click events
		onclick = function(e) {
			e = e || window.event;
			var target = e.target || e.srcElement, i;

			// as long as it has the class ed_button, execute the callback
			if ( /\s+ed_button\s+/.test(' ' + target.className + ' ' ) ) {
				// we have to reassign canvas here
				t.canvas = canvas = document.getElementById(id);
				i = target.id.replace(name + '_', '');

				if ( theButtons[i] )
					theButtons[i].callback.call(theButtons[i], target, canvas, t);
			}
		};

		if ( tb.addEventListener ) {
			tb.addEventListener('click', onclick, false);
		} else if ( tb.attachEvent ) {
			tb.attachEvent('onclick', onclick);
		}

		qt.instances[id] = t;
	};

	qt.instances = {};

	qt.registerButton = function(id, btnClass) {
		_customButtons[id] = btnClass;
	};

	qt.getInstance = function(id) {
		return qt.instances[id];
	};

	qt.insertContent = function(editor_id, content) {
		var sel, startPos, endPos, scrollTop, text, ed = document.getElementById(editor_id);

		if ( document.selection ) { //IE
			ed.focus();
			sel = document.selection.createRange();
			sel.text = content;
			ed.focus();
		} else if ( ed.selectionStart || ed.selectionStart == '0' ) { // all other
			text = ed.value;
			startPos = ed.selectionStart;
			endPos = ed.selectionEnd;
			scrollTop = ed.scrollTop;

			ed.value = text.substring(0, startPos) + content + text.substring(endPos, text.length);

			ed.focus();
			ed.selectionStart = startPos + content.length;
			ed.selectionEnd = startPos + content.length;
			ed.scrollTop = scrollTop;
		} else {
			ed.value += content;
			ed.focus();
		}
	};

	// a plain, dumb button
	qt.Button = function(id, display, access, title) {
		var t = this;
		t.id = id;
		t.display = display;
		t.access = access;
		t.title = title || '';
	};
	qt.Button.prototype.html = function(idPrefix) {
		var access = this.access ? ' accesskey="' + this.access + '"' : '';
		return '<input type="button" id="' + idPrefix + this.id + '"' + access + ' class="ed_button" title="' + this.title + '" value="' + this.display + '" />';
	};
	qt.Button.prototype.callback = function(canvas) {};

	// a button that inserts HTML tag
	qt.TagButton = function(id, display, tagStart, tagEnd, access, open, title) {
		var t = this;
		qt.Button.call(t, id, display, access, title);
		t.tagStart = tagStart;
		t.tagEnd = tagEnd;
		t.open = open;
	};
	qt.TagButton.prototype = new qt.Button();
	qt.TagButton.prototype.openTag = function(e, tb) {
		var t = this;
		if ( ! tb.openTags ) {
			tb.openTags = [];
		}
		if ( t.tagEnd ) {
			tb.openTags.push(t.id);
			e.value = '/' + e.value;
		}
	};
	qt.TagButton.prototype.closeTag = function(e, tb) {
		var t = this,
			i = t.isOpen(tb);

		if ( i !== false ) {
			tb.openTags.splice(i, 1);
		}

		e.value = t.display;
	};
	// whether a tag is open or not. Returns false if not open, or current open depth of the tag
	qt.TagButton.prototype.isOpen = function (tb) {
		var t = this, i = 0, ret = false;
		if ( tb.openTags ) {
			while ( ret === false && i < tb.openTags.length ) {
				ret = tb.openTags[i] == t.id ? i : false;
				i ++;
			}
		} else {
			ret = false;
		}
		return ret;
	};
	qt.TagButton.prototype.callback = function(element, canvas, toolbar) {
		var t = this, startPos, endPos, cursorPos, scrollTop, v, l, r, i, sel;

		v = canvas.value;

		// IE support
		if ( document.selection ) {
			canvas.focus();
			sel = document.selection.createRange();
			if ( sel.text.length > 0 ) {
				sel.text = t.tagStart + sel.text + t.tagEnd;
			} else {
				if ( t.isOpen(toolbar) === false || t.tagEnd === '' ) {
					sel.text = t.tagStart;
					t.openTag(element, toolbar);
				} else {
					sel.text = t.tagEnd;
					t.closeTag(element, toolbar);
				}
			}
			canvas.focus();
		}
		// moz, webkit, opera
		else if ( canvas.selectionStart || canvas.selectionStart == '0' ) {
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			cursorPos = endPos;
			scrollTop = canvas.scrollTop;
			l = v.substring(0, startPos); // left of the selection
			r = v.substring(endPos, v.length); // right of the selection
			i = v.substring(startPos, endPos); // inside the selection
			if ( startPos != endPos ) {
				canvas.value = l + t.tagStart + i + t.tagEnd + r;
				if ( t.tagEnd === '' ) {
					cursorPos = startPos;
				}
				cursorPos += t.tagStart.length + t.tagEnd.length;
			} else {
				if ( t.isOpen(toolbar) === false || t.tagEnd === '' ) {
					canvas.value = l + t.tagStart + r;
					t.openTag(element, toolbar);
					cursorPos = startPos + t.tagStart.length;
				} else {
					canvas.value = l + t.tagEnd + r;
					cursorPos = startPos + t.tagEnd.length;
					t.closeTag(element, toolbar);
				}
			}

			canvas.focus();
			canvas.selectionStart = cursorPos;
			canvas.selectionEnd = cursorPos;
			canvas.scrollTop = scrollTop;
		}
		// other browsers
		else {
			if ( t.isOpen(toolbar) !== false || t.tagEnd === '' ) {
				canvas.value += t.tagStart;
				t.openTag(element, toolbar);
			} else {
				canvas.value += t.tagEnd;
				t.closeTag(element, toolbar);
			}
			canvas.focus();
		}
	};

	// the spell button
	qt.SpellButton = function() {
		qt.Button.call(this, 'spell', quicktagsL10n.lookup, '', quicktagsL10n.dictionaryLookup);
	};
	qt.SpellButton.prototype = new qt.Button();
	qt.SpellButton.prototype.callback = function(element, canvas, toolbar) {
		var word = '', sel, startPos, endPos;

		if ( document.selection ) {
			canvas.focus();
			sel = document.selection.createRange();
			if ( sel.text.length > 0 ) {
				word = sel.text;
			}
		}
		else if ( canvas.selectionStart || canvas.selectionStart == '0' ) {
			startPos = canvas.selectionStart;
			endPos = canvas.selectionEnd;
			if ( startPos != endPos ) {
				word = canvas.value.substring(startPos, endPos);
			}
		}

		if ( word === '' ) {
			word = prompt(quicktagsL10n.wordLookup, '');
		}

		if ( word !== null && /^\w[\w ]*$/.test(word)) {
			window.open('http://www.answers.com/' + encodeURIComponent(word));
		}
	};

	// the close button
	qt.CloseButton = function() {
		qt.Button.call(this, 'close', quicktagsL10n.closeTags, '', quicktagsL10n.closeAllOpenTags);
	};
	qt.CloseButton.prototype = new qt.Button();
	qt.CloseButton.prototype.callback = function(e, c, tb) {
		var button, element, tbo = tb.openTags;
		if ( tbo ) {
			while ( tbo.length > 0 ) {
				button = tb.getButton(tbo[tbo.length - 1]);
				element = document.getElementById(tb.name + '_' + button.id);
				button.callback.call(button, element, c, tb);
			}
		}
	};

	qt.prototype.closeAllTags = function() {
		var btn = this.getButton('close');
		btn.callback.call(btn, '', this.canvas, this.toolbar);
	};

	// the link button
	qt.LinkButton = function() {
		qt.TagButton.call(this, 'link', 'link', '', '</a>', 'a');
	};
	qt.LinkButton.prototype = new qt.TagButton();
	qt.LinkButton.prototype.callback = function(e, c, tb, defaultValue) {
		var URL, t = this;

		if ( typeof(wpLink) != 'undefined' ) {
			wpLink.open();
			return;
		}

		if ( ! defaultValue )
			defaultValue = 'http://';

		if ( t.isOpen(tb) === false ) {
			URL = prompt(quicktagsL10n.enterURL, defaultValue);
			if ( URL ) {
				t.tagStart = '<a href="' + URL + '">';
				qt.TagButton.prototype.callback.call(t, e, c, tb);
			}
		} else {
			qt.TagButton.prototype.callback.call(t, e, c, tb);
		}
	};

	// the img button
	qt.ImgButton = function() {
		qt.TagButton.call(this, 'img', 'img', '', '', 'm', -1);
	};
	qt.ImgButton.prototype = new qt.TagButton();
	qt.ImgButton.prototype.callback = function(e, c, tb, defaultValue) {
		if ( ! defaultValue ) {
			defaultValue = 'http://';
		}
		var src = prompt(quicktagsL10n.enterImageURL, defaultValue), alt;
		if ( src ) {
			alt = prompt(quicktagsL10n.enterImageDescription, '');
			this.tagStart = '<img src="' + src + '" alt="' + alt + '" />';
			qt.TagButton.prototype.callback.call(this, e, c, tb);
		}
	};

	qt.FullscreenButton = function() {
		qt.Button.call(this, 'fullscreen', quicktagsL10n.fullscreen, 'f', quicktagsL10n.toggleFullscreen);
	};
	qt.FullscreenButton.prototype = new qt.Button();
	qt.FullscreenButton.prototype.callback = function(e, c) {
		if ( c.id != 'content' || typeof(fullscreen) == 'undefined' )
			return;

		fullscreen.on();
	};

	// ensure backward compatibility
	edButtons = [
		new qt.TagButton('strong','b','<strong>','</strong>','b'),
		new qt.TagButton('em','i','<em>','</em>','i'),
		new qt.LinkButton(), // special case
		new qt.TagButton('block','b-quote','\n\n<blockquote>','</blockquote>\n\n','q'),
		new qt.TagButton('del','del','<del datetime="' + _datetime + '">','</del>','d'),
		new qt.TagButton('ins','ins','<ins datetime="' + _datetime + '">','</ins>','s'),
		new qt.ImgButton(), // special case
		new qt.TagButton('ul','ul','<ul>\n','</ul>\n\n','u'),
		new qt.TagButton('ol','ol','<ol>\n','</ol>\n\n','o'),
		new qt.TagButton('li','li','\t<li>','</li>\n','l'),
		new qt.TagButton('code','code','<code>','</code>','c'),
		new qt.TagButton('more','more','<!--more-->','','t',-1),
		new qt.SpellButton(),
		new qt.CloseButton()
	];

	edButton = qt.TagButton;
})();
