var action, element;

function init() {
	tinyMCEPopup.resizeToInnerSize();

	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var anchor = tinyMCE.getParentElement(inst.getFocusElement(), "a", "name");
	var img = inst.getFocusElement();
	action = 'insert';

	if (anchor != null) {
		element = anchor;
		action = "update";
	}

	if (tinyMCE.getAttrib(img, "class") == "mceItemAnchor") {
		element = img;
		action = "update";
	}

	if (action == "update")
		document.forms[0].anchorName.value = element.nodeName == "IMG" ? element.getAttribute("title") : element.getAttribute("name");

	document.forms[0].insert.value = tinyMCE.getLang('lang_' + action, 'Insert', true);
}

function insertAnchor() {
	var inst = tinyMCE.getInstanceById(tinyMCE.getWindowArg('editor_id'));
	var name = document.forms[0].anchorName.value;

	tinyMCEPopup.execCommand("mceBeginUndoLevel");

	if (action == "update") {
		if (element.nodeName == "IMG")
			element.setAttribute("title", name);
		else
			element.setAttribute("name", name);
	} else {
		var rng = inst.getRng();

		if (rng.collapse)
			rng.collapse(false);

		name = name.replace(/&/g, '&amp;');
		name = name.replace(/\"/g, '&quot;');
		name = name.replace(/</g, '&lt;');
		name = name.replace(/>/g, '&gr;');

		html = '<a name="' + name + '"></a>';

		tinyMCEPopup.execCommand("mceInsertContent", false, html);
		tinyMCE.handleVisualAid(inst.getBody(), true, inst.visualAid, inst);
	}

	tinyMCEPopup.execCommand("mceEndUndoLevel");

	tinyMCE.triggerNodeChange();
	tinyMCEPopup.close();
}
