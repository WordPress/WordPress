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

tinymce.PluginManager.add('directionality', function(editor) {
	function setDir(dir) {
		var dom = editor.dom, curDir, blocks = editor.selection.getSelectedBlocks();

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

			editor.nodeChanged();
		}
	}

	function generateSelector(dir) {
		var selector = [];

		tinymce.each('h1 h2 h3 h4 h5 h6 div p'.split(' '), function(name) {
			selector.push(name + '[dir=' + dir + ']');
		});

		return selector.join(',');
	}

	editor.addCommand('mceDirectionLTR', function() {
		setDir("ltr");
	});

	editor.addCommand('mceDirectionRTL', function() {
		setDir("rtl");
	});

	editor.addButton('ltr', {
		title: 'Left to right',
		cmd: 'mceDirectionLTR',
		stateSelector: generateSelector('ltr')
	});

	editor.addButton('rtl', {
		title: 'Right to left',
		cmd: 'mceDirectionRTL',
		stateSelector: generateSelector('rtl')
	});
});