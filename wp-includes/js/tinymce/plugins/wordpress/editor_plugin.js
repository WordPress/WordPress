/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('wordpress', '');

function TinyMCE_wordpress_initInstance(inst) {
	if (!tinyMCE.settings['wordpress_skip_plugin_css'])
		tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/plugins/wordpress/wordpress.css");
}

function TinyMCE_wordpress_getControlHTML(control_name) {
	switch (control_name) {
		case "wordpress":
			var titleMore = tinyMCE.getLang('lang_wordpress_more_button');
			var titlePage = tinyMCE.getLang('lang_wordpress_page_button');
			var titleHelp = tinyMCE.getLang('lang_wordpress_help_button');
			var buttons = '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpressmore\')" target="_self" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpressmore\');return false;"><img id="{$editor_id}_wordpress_more" src="{$pluginurl}/images/more.gif" title="'+titleMore+'" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>'
			+ '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpresshelp\')" target="_self" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpresshelp\');return false;"><img id="{$editor_id}_wordpress_help" src="{$pluginurl}/images/help.gif" title="'+titleHelp+'" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
			// Add this to the buttons var to put the Page button into the toolbar.
			// '<a href="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpresspage\')" target="_self" onclick="javascript:tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpresspage\');return false;"><img id="{$editor_id}_wordpress_page" src="{$pluginurl}/images/page.gif" title="'+titlePage+'" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
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
			+ '<input type="button" accesskey="h" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mcewordpresshelp\',false);" />'
			+ '</div>';
			return buttons+hiddenControls;
    }

    return "";
}

function TinyMCE_wordpress_parseAttributes(attribute_string) {
	var attributeName = "";
	var attributeValue = "";
	var withInName;
	var withInValue;
	var attributes = new Array();
	var whiteSpaceRegExp = new RegExp('^[ \n\r\t]+', 'g');
	var titleText = tinyMCE.getLang('lang_wordpress_more');
	var titleTextPage = tinyMCE.getLang('lang_wordpress_page');

	if (attribute_string == null || attribute_string.length < 2)
		return null;

	withInName = withInValue = false;

	for (var i=0; i<attribute_string.length; i++) {
		var chr = attribute_string.charAt(i);

		if ((chr == '"' || chr == "'") && !withInValue)
			withInValue = true;
		else if ((chr == '"' || chr == "'") && withInValue) {
			withInValue = false;

			var pos = attributeName.lastIndexOf(' ');
			if (pos != -1)
				attributeName = attributeName.substring(pos+1);

			attributes[attributeName.toLowerCase()] = attributeValue.substring(1).toLowerCase();

			attributeName = "";
			attributeValue = "";
		} else if (!whiteSpaceRegExp.test(chr) && !withInName && !withInValue)
			withInName = true;

		if (chr == '=' && withInName)
			withInName = false;

		if (withInName)
			attributeName += chr;

		if (withInValue)
			attributeValue += chr;
	}

	return attributes;
}

function TinyMCE_wordpress_execCommand(editor_id, element, command, user_interface, value) {
	var inst = tinyMCE.getInstanceById(editor_id);
	var focusElm = inst.getFocusElement();
	var doc = inst.getDoc();

	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	}

	// Handle commands
	switch (command) {
			case "mcewordpressmore":
				var flag = "";
				var template = new Array();
				var altMore = tinyMCE.getLang('lang_wordpress_more_alt');

				// Is selection a image
				if (focusElm != null && focusElm.nodeName.toLowerCase() == "img") {
					flag = getAttrib(focusElm, 'class');
	
					if (flag != 'mce_plugin_wordpress_more') // Not a wordpress
						return true;
	
					action = "update";
				}
	
				html = ''
					+ '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" '
					+ ' width="100%" height="10px" '
					+ 'alt="'+altMore+'" title="'+altMore+'" class="mce_plugin_wordpress_more" name="mce_plugin_wordpress_more" />';
				tinyMCE.execCommand("mceInsertContent",true,html);
				tinyMCE.selectedInstance.repaint();
				return true;
			case "mcewordpresspage":
				var flag = "";
				var template = new Array();
				var altPage = tinyMCE.getLang('lang_wordpress_more_alt');
	
				// Is selection a image
				if (focusElm != null && focusElm.nodeName.toLowerCase() == "img") {
					flag = getAttrib(focusElm, 'name');
	
					if (flag != 'mce_plugin_wordpress_page') // Not a wordpress
						return true;
	
					action = "update";
				}
	
				html = ''
					+ '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" '
					+ ' width="100%" height="10px" '
					+ 'alt="'+altPage+'" title="'+altPage+'" class="mce_plugin_wordpress_page" name="mce_plugin_wordpress_page" />';
				tinyMCE.execCommand("mceInsertContent",true,html);
				tinyMCE.selectedInstance.repaint();
				return true;
			case "mcewordpresshelp":
				var helpText = tinyMCE.getLang('lang_wordpress_help_text');
				alert(helpText);
				return true;
	}

	// Pass to next handler in chain
	return false;
}

function TinyMCE_wordpress_cleanup(type, content) {
	switch (type) {
	
		case "insert_to_editor":
			var startPos = 0;
			var altMore = tinyMCE.getLang('lang_wordpress_more_alt');
			var altPage = tinyMCE.getLang('lang_wordpress_page_alt');

			// Parse all <!--more--> tags and replace them with images
			while ((startPos = content.indexOf('<!--more-->', startPos)) != -1) {
				// Insert image
				var contentAfter = content.substring(startPos + 11);
				content = content.substring(0, startPos);
				content += '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" ';
				content += ' width="100%" height="10px" ';
				content += 'alt="'+altMore+'" title="'+altMore+'" class="mce_plugin_wordpress_more" />';
				content += contentAfter;

				startPos++;
			}
			var startPos = 0;

			// Parse all <!--page--> tags and replace them with images
			while ((startPos = content.indexOf('<!--nextpage-->', startPos)) != -1) {
				// Insert image
				var contentAfter = content.substring(startPos + 15);
				content = content.substring(0, startPos);
				content += '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" ';
				content += ' width="100%" height="10px" ';
				content += 'alt="'+altPage+'" title="'+altPage+'" class="mce_plugin_wordpress_page" />';
				content += contentAfter;

				startPos++;
			}

			// It's supposed to be WYSIWYG, right?
			content = content.replace(new RegExp('&', 'g'), '&amp;');

			break;

		case "get_from_editor":
			// Parse all img tags and replace them with <!--more-->
			var startPos = -1;
			while ((startPos = content.indexOf('<img', startPos+1)) != -1) {
				var endPos = content.indexOf('/>', startPos);
				var attribs = TinyMCE_wordpress_parseAttributes(content.substring(startPos + 4, endPos));

				if (attribs['class'] == "mce_plugin_wordpress_more") {
					endPos += 2;
	
					var embedHTML = '<!--more-->';
	
					// Insert embed/object chunk
					chunkBefore = content.substring(0, startPos);
					chunkAfter = content.substring(endPos);
					content = chunkBefore + embedHTML + chunkAfter;
				}
				if (attribs['class'] == "mce_plugin_wordpress_page") {
					endPos += 2;
	
					var embedHTML = '<!--nextpage-->';
	
					// Insert embed/object chunk
					chunkBefore = content.substring(0, startPos);
					chunkAfter = content.substring(endPos);
					content = chunkBefore + embedHTML + chunkAfter;
				}
			}

			// If it says & in the WYSIWYG editor, it should say &amp; in the html.
			content = content.replace(new RegExp('&', 'g'), '&amp;');

			// Pretty it up for the source editor.
			var blocklist = 'blockquote|ul|ol|li|table|thead|tr|th|td|div|h\d|pre|p';
			content = content.replace(new RegExp('\\s*</('+blocklist+')>\\s*', 'mg'), '</$1>\n');
			content = content.replace(new RegExp('\\s*<(('+blocklist+')[^>]*)>\\s*', 'mg'), '\n<$1>');
			content = content.replace(new RegExp('<li>', 'g'), '\t<li>');
			content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'mg'), '<br />\n');
			content = content.replace(new RegExp('^\\s*', ''), '');
			content = content.replace(new RegExp('\\s*$', ''), '');
			break;
	}

	// Pass through to next handler in chain
	return content;
}

function TinyMCE_wordpress_handleNodeChange(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	}

	tinyMCE.switchClassSticky(editor_id + '_wordpress_more', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_wordpress_page', 'mceButtonNormal');

	if (node == null)
		return;

	do {
		if (node.nodeName.toLowerCase() == "img" && getAttrib(node, 'class').indexOf('mce_plugin_wordpress_more') == 0)
			tinyMCE.switchClassSticky(editor_id + '_wordpress_more', 'mceButtonSelected');
		if (node.nodeName.toLowerCase() == "img" && getAttrib(node, 'class').indexOf('mce_plugin_wordpress_page') == 0)
			tinyMCE.switchClassSticky(editor_id + '_wordpress_page', 'mceButtonSelected');
	} while ((node = node.parentNode));

	return true;
}

function wp_save_callback(el, content, body) {
	// We have a TON of cleanup to do.

	// Mark </p> if it has any attributes.
	content = content.replace(new RegExp('(<p[^>]+>.*?)</p>', 'mg'), '$1</p#>');

	// Decode the ampersands of time.
	content = content.replace(new RegExp('&amp;', 'g'), '&');

	// Get it ready for wpautop.
	content = content.replace(new RegExp('[\\s]*<p>[\\s]*', 'mgi'), '');
	content = content.replace(new RegExp('[\\s]*</p>[\\s]*', 'mgi'), '\n\n');
	content = content.replace(new RegExp('\\n\\s*\\n\\s*\\n*', 'mgi'), '\n\n');
	content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

	// Fix some block element newline issues
	var blocklist = 'blockquote|ul|ol|li|table|thead|tr|th|td|div|h\d|pre';
	content = content.replace(new RegExp('\\s*<(('+blocklist+') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
	content = content.replace(new RegExp('\\s*</('+blocklist+')>\\s*', 'mg'), '</$1>\n');
	content = content.replace(new RegExp('<li>', 'g'), '\t<li>');

	// Unmark special paragraph closing tags
	content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
	content = content.replace(new RegExp('\\s*(<p[^>]+>.*</p>)', 'mg'), '\n$1');

	// Trim any whitespace
	content = content.replace(new RegExp('^\\s*', ''), '');
	content = content.replace(new RegExp('\\s*$', ''), '');

	// Hope.
	return content;

}
