/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('iespell', 'cs,el,en,fr_ca,it,ko,sv,zh_cn,fr,de,pl,pt_br,nl');

/**
 * Returns the HTML contents of the iespell control.
 */
function TinyMCE_iespell_getControlHTML(control_name) {
	// Is it the iespell control and is the brower MSIE.
	if (control_name == "iespell" && tinyMCE.isMSIE)
		return '<img id="{$editor_id}_iespell" src="{$pluginurl}/images/iespell.gif" title="{$lang_iespell_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceIESpell\');">';

	return "";
}

/**
 * Executes the mceIESpell command.
 */
function TinyMCE_iespell_execCommand(editor_id, element, command, user_interface, value) {
	// Handle ieSpellCommand
	if (command == "mceIESpell") {
		try {
			var ieSpell = new ActiveXObject("ieSpell.ieSpellExtension");
			ieSpell.CheckDocumentNode(tinyMCE.getInstanceById(editor_id).contentDocument.documentElement);
		} catch (e) {
			if (e.number == -2146827859) {
				if (confirm(tinyMCE.getLang("lang_iespell_download", "", true)))
					window.open('http://www.iespell.com/download.php', 'ieSpellDownload', '');
			} else
				alert("Error Loading ieSpell: Exception " + e.number);
		}

		return true;
	}

	// Pass to next handler in chain
	return false;
}