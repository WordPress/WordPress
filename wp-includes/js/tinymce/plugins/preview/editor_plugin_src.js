/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('preview', 'cs,de,el,en,fr_ca,it,ko,pt,sv,zh_cn,fa,fr,pl,pt_br,nl');

/**
 * Returns the HTML contents of the preview control.
 */
function TinyMCE_preview_getControlHTML(control_name) {
	switch (control_name) {
		case "preview":
			return '<img id="{$editor_id}_preview" src="{$pluginurl}/images/preview.gif" title="{$lang_preview_desc}" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcePreview\');" />';
	}

	return "";
}

/**
 * Executes the mcePreview command.
 */
function TinyMCE_preview_execCommand(editor_id, element, command, user_interface, value) {
	// Handle commands
	switch (command) {
		case "mcePreview":
			var previewPage = tinyMCE.getParam("plugin_preview_pageurl", null);
			var previewWidth = tinyMCE.getParam("plugin_preview_width", "550");
			var previewHeight = tinyMCE.getParam("plugin_preview_height", "600");

			// Use a custom preview page
			if (previewPage) {
				var template = new Array();

				template['file'] = previewPage;
				template['width'] = previewWidth;
				template['height'] = previewHeight;

				tinyMCE.openWindow(template, {editor_id : editor_id, resizable : "yes", scrollbars : "yes", content : tinyMCE.getContent(), content_css : tinyMCE.getParam("content_css")});
			} else {
				var win = window.open("", "mcePreview", "menubar=no,toolbar=no,scrollbars=yes,resizable=yes,left=20,top=20,width=" + previewWidth + ",height="  + previewHeight);
				var html = "";

				html += '<!doctype html public "-//w3c//dtd html 4.0 transitional//en">';
				html += '<html>';
				html += '<head>';
				html += '<title>' + tinyMCE.getLang('lang_preview_desc') + '</title>';
				html += '<base href="' + tinyMCE.getParam("document_base_url") + '">';
				html += '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
				html += '<link href="' + tinyMCE.getParam("content_css") + '" rel="stylesheet" type="text/css">';
				html += '</head>';
				html += '<body>';
				html += tinyMCE.getContent();
				html += '</body>';
				html += '</html>';

				win.document.write(html);
				win.document.close();
			}

			return true;
	}

	// Pass to next handler in chain
	return false;
}
