/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('directionality', 'en,sv,fr_ca,zh_cn,cs,da,he,no,de,hu,ru,ru_KOI8-R,ru_UTF-8,es,cy,is,pl');

function TinyMCE_directionality_getInfo() {
	return {
		longname : 'Directionality',
		author : 'Moxiecode Systems',
		authorurl : 'http://tinymce.moxiecode.com',
		infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_directionality.html',
		version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
	};
};

function TinyMCE_directionality_getControlHTML(control_name) {
    switch (control_name) {
        case "ltr":
			var cmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceDirectionLTR\');return false;';
            return '<a href="javascript:' + cmd + '" onclick="' + cmd + '" target="_self" onmousedown="return false;"><img id="{$editor_id}_ltr" src="{$pluginurl}/images/ltr.gif" title="{$lang_directionality_ltr_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>'
			+ '<div class="zerosize"><input type="button" accesskey="." onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceDirectionLTR\',false);" /></div>';

        case "rtl":
			var cmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceDirectionRTL\');return false;';
            return '<a href="javascript:' + cmd + '" onclick="' + cmd + '" target="_self" onmousedown="return false;"><img id="{$editor_id}_rtl" src="{$pluginurl}/images/rtl.gif" title="{$lang_directionality_rtl_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>'
			+ '<div class="zerosize"><input type="button" accesskey="," onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceDirectionRTL\',false);" /></div>';
    }

    return "";
}

function TinyMCE_directionality_execCommand(editor_id, element, command, user_interface, value) {
	// Handle commands
	switch (command) {
		case "mceDirectionLTR":
			var inst = tinyMCE.getInstanceById(editor_id);
			var elm = tinyMCE.getParentElement(inst.getFocusElement(), "p,div,td,h1,h2,h3,h4,h5,h6,pre,address");

			if (elm)
				elm.setAttribute("dir", "ltr");

			tinyMCE.triggerNodeChange(false);
			return true;

		case "mceDirectionRTL":
			var inst = tinyMCE.getInstanceById(editor_id);
			var elm = tinyMCE.getParentElement(inst.getFocusElement(), "p,div,td,h1,h2,h3,h4,h5,h6,pre,address");

			if (elm)
				elm.setAttribute("dir", "rtl");

			tinyMCE.triggerNodeChange(false);
			return true;
	}

	// Pass to next handler in chain
	return false;
}

function TinyMCE_directionality_handleNodeChange(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	}

	tinyMCE.switchClassSticky(editor_id + '_ltr', 'mceButtonNormal', false);
	tinyMCE.switchClassSticky(editor_id + '_rtl', 'mceButtonNormal', false);

	if (node == null)
		return;

	var elm = tinyMCE.getParentElement(node, "p,div,td,h1,h2,h3,h4,h5,h6,pre,address");
	if (!elm) {
		tinyMCE.switchClassSticky(editor_id + '_ltr', 'mceButtonDisabled', true);
		tinyMCE.switchClassSticky(editor_id + '_rtl', 'mceButtonDisabled', true);
		return;
	}

	var dir = getAttrib(elm, "dir");
	if (dir == "ltr" || dir == "")
		tinyMCE.switchClassSticky(editor_id + '_ltr', 'mceButtonSelected', false);
	else
		tinyMCE.switchClassSticky(editor_id + '_rtl', 'mceButtonSelected', false);

	return true;
}
