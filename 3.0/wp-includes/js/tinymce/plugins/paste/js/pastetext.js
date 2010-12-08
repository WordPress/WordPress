tinyMCEPopup.requireLangPack();

var PasteTextDialog = {
	init : function() {
		this.resize();
	},

	insert : function() {
		var h = tinyMCEPopup.dom.encode(document.getElementById('content').value), lines;

		// Convert linebreaks into paragraphs
		if (document.getElementById('linebreaks').checked) {
			lines = h.split(/\r?\n/);
			if (lines.length > 1) {
				h = '';
				tinymce.each(lines, function(row) {
					h += '<p>' + row + '</p>';
				});
			}
		}

		tinyMCEPopup.editor.execCommand('mceInsertClipboardContent', false, {content : h});
		tinyMCEPopup.close();
	},

	resize : function() {
		var vp = tinyMCEPopup.dom.getViewPort(window), el;

		el = document.getElementById('content');

		el.style.width  = (vp.w - 20) + 'px';
		el.style.height = (vp.h - 90) + 'px';
	}
};

tinyMCEPopup.onInit.add(PasteTextDialog.init, PasteTextDialog);
