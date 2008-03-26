tinyMCEPopup.requireLangPack();

var AnchorDialog = {
	init : function(ed) {
		var action, elm, f = document.forms[0];

		this.editor = ed;
		elm = ed.dom.getParent(ed.selection.getNode(), 'A,IMG');
		v = ed.dom.getAttrib(elm, 'name');

		if (v) {
			this.action = 'update';
			f.anchorName.value = v;
		}

		f.insert.value = ed.getLang(elm ? 'update' : 'insert');
	},

	update : function() {
		var ed = this.editor;
		
		tinyMCEPopup.restoreSelection();

		if (this.action != 'update')
			ed.selection.collapse(1);

		// Webkit acts weird if empty inline element is inserted so we need to use a image instead
		if (tinymce.isWebKit)
			ed.execCommand('mceInsertContent', 0, ed.dom.createHTML('img', {mce_name : 'a', name : document.forms[0].anchorName.value, 'class' : 'mceItemAnchor'}));
		else
			ed.execCommand('mceInsertContent', 0, ed.dom.createHTML('a', {name : document.forms[0].anchorName.value, 'class' : 'mceItemAnchor'}, ''));

		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(AnchorDialog.init, AnchorDialog);
