/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.Directionality', {
		init : function(ed, url) {
			var t = this;

			t.editor = ed;

			function setDir(dir) {
				var dom = ed.dom, curDir, blocks = ed.selection.getSelectedBlocks();

				if (blocks.length) {
					curDir = dom.getAttrib(blocks[0], "dir");

					tinymce.each(blocks, function(block) {
						// Add dir to block if the parent block doesn't already have that dir
						if (!dom.getParent(block.parentNode, "*[dir='" + dir + "']", dom.getRoot())) {
							if (curDir != dir) {
								dom.setAttrib(block, "dir", dir);
							} else {
								dom.setAttrib(block, "dir", null);
							}
						}
					});

					ed.nodeChanged();
				}
			}

			ed.addCommand('mceDirectionLTR', function() {
				setDir("ltr");
			});

			ed.addCommand('mceDirectionRTL', function() {
				setDir("rtl");
			});

			ed.addButton('ltr', {title : 'directionality.ltr_desc', cmd : 'mceDirectionLTR'});
			ed.addButton('rtl', {title : 'directionality.rtl_desc', cmd : 'mceDirectionRTL'});

			ed.onNodeChange.add(t._nodeChange, t);
		},

		getInfo : function() {
			return {
				longname : 'Directionality',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/directionality',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},

		// Private methods

		_nodeChange : function(ed, cm, n) {
			var dom = ed.dom, dir;

			n = dom.getParent(n, dom.isBlock);
			if (!n) {
				cm.setDisabled('ltr', 1);
				cm.setDisabled('rtl', 1);
				return;
			}

			dir = dom.getAttrib(n, 'dir');
			cm.setActive('ltr', dir == "ltr");
			cm.setDisabled('ltr', 0);
			cm.setActive('rtl', dir == "rtl");
			cm.setDisabled('rtl', 0);
		}
	});

	// Register plugin
	tinymce.PluginManager.add('directionality', tinymce.plugins.Directionality);
})();