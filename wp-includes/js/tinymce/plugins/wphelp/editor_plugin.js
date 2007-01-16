/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('wphelp', '');

function TinyMCE_wphelp_getControlHTML(control_name) {
	switch (control_name) {
		case "wphelp":
			var titleHelp = tinyMCE.getLang('lang_help_button_title');
			var buttons = '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceWordPressHelp\')" target="_self" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceWordPressHelp\');return false;"><img id="{$editor_id}_help" src="{$pluginurl}/images/help.gif" title="'+titleHelp+'" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
			var hiddenControls = '<div class="zerosize">'
			+ '<input type="button" accesskey="b" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Bold\',false);" />'
			+ '<input type="button" accesskey="i" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Italic\',false);" />'
			+ '<input type="button" accesskey="d" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Strikethrough\',false);" />'
			+ '<input type="button" accesskey="l" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'InsertUnorderedList\',false);" />'
			+ '<input type="button" accesskey="o" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'InsertOrderedList\',false);" />'
			+ '<input type="button" accesskey="w" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Outdent\',false);" />'
			+ '<input type="button" accesskey="q" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Indent\',false);" />'
			+ '<input type="button" accesskey="f" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyLeft\',false);" />'
			+ '<input type="button" accesskey="c" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyCenter\',false);" />'
			+ '<input type="button" accesskey="r" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyRight\',false);" />'
			+ '<input type="button" accesskey="a" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceLink\',true);" />'
			+ '<input type="button" accesskey="s" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'unlink\',false);" />'
			+ '<input type="button" accesskey="m" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceImage\',true);" />'
			+ '<input type="button" accesskey="t" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpressmore\');" />'
			+ '<input type="button" accesskey="u" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Undo\',false);" />'
			+ '<input type="button" accesskey="y" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Redo\',false);" />'
			+ '<input type="button" accesskey="e" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceCodeEditor\',false);" />'
			+ '<input type="button" accesskey="h" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceWordPressHelp\',false);" />'
			+ '</div>';
			return buttons+hiddenControls;
    }

    return "";
}

function TinyMCE_wphelp_execCommand(editor_id, element, command, user_interface, value) {

	// Handle commands
	switch (command) {
		case "mceWordPressHelp":
			var template = new Array();

			template['file']   = tinyMCE.baseURL + '/wp-mce-help.php';
			template['width']  = 480;
			template['height'] = 380;

			args = {
				resizable : 'yes',
				scrollbars : 'yes'
			};

			tinyMCE.openWindow(template, args);
			return true;
	}

	// Pass to next handler in chain
	return false;
}
