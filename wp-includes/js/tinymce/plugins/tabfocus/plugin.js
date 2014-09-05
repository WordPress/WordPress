/**
 * plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('tabfocus', function(editor) {
	var DOM = tinymce.DOM, each = tinymce.each, explode = tinymce.explode;

	function tabCancel(e) {
		if (e.keyCode === 9 && !e.ctrlKey && !e.altKey && !e.metaKey) {
			e.preventDefault();
		}
	}

	function tabHandler(e) {
		var x, el, v, i;

		if (e.keyCode !== 9 || e.ctrlKey || e.altKey || e.metaKey || e.isDefaultPrevented()) {
			return;
		}

		function find(direction) {
			el = DOM.select(':input:enabled,*[tabindex]:not(iframe)');

			function canSelectRecursive(e) {
				return e.nodeName === "BODY" || (e.type != 'hidden' &&
					e.style.display != "none" &&
					e.style.visibility != "hidden" && canSelectRecursive(e.parentNode));
			}

			function canSelectInOldIe(el) {
				return el.tabIndex || el.nodeName == "INPUT" || el.nodeName == "TEXTAREA";
			}

			function canSelect(el) {
				return ((!canSelectInOldIe(el))) && el.getAttribute("tabindex") != '-1' && canSelectRecursive(el);
			}

			each(el, function(e, i) {
				if (e.id == editor.id) {
					x = i;
					return false;
				}
			});
			if (direction > 0) {
				for (i = x + 1; i < el.length; i++) {
					if (canSelect(el[i])) {
						return el[i];
					}
				}
			} else {
				for (i = x - 1; i >= 0; i--) {
					if (canSelect(el[i])) {
						return el[i];
					}
				}
			}

			return null;
		}

		v = explode(editor.getParam('tab_focus', editor.getParam('tabfocus_elements', ':prev,:next')));

		if (v.length == 1) {
			v[1] = v[0];
			v[0] = ':prev';
		}

		// Find element to focus
		if (e.shiftKey) {
			if (v[0] == ':prev') {
				el = find(-1);
			} else {
				el = DOM.get(v[0]);
			}
		} else {
			if (v[1] == ':next') {
				el = find(1);
			} else {
				el = DOM.get(v[1]);
			}
		}

		if (el) {
			var focusEditor = tinymce.get(el.id || el.name);

			if (el.id && focusEditor) {
				focusEditor.focus();
			} else {
				window.setTimeout(function() {
					if (!tinymce.Env.webkit) {
						window.focus();
					}

					el.focus();
				}, 10);
			}

			e.preventDefault();
		}
	}

	editor.on('init', function() {
		if (editor.inline) {
			// Remove default tabIndex in inline mode
			tinymce.DOM.setAttrib(editor.getBody(), 'tabIndex', null);
		}

		editor.on('keyup', tabCancel);

		// Add later so other plugins can preventDefault()
		if (tinymce.Env.gecko) {
			editor.on('keypress keydown', tabHandler);
		} else {
			editor.on('keydown', tabHandler);
		}
	});
});
