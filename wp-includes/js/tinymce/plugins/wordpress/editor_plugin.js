/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('wordpress', 'en');

var TinyMCE_wordpressPlugin = {
	getInfo : function() {
		return {
			longname : 'WordPress Plugin',
			author : 'WordPress',
			authorurl : 'http://wordpress.org',
			infourl : 'http://wordpress.org',
			version : '1'
		};
	},

	getControlHTML : function(control_name) {
		switch (control_name) {
			case "wp_more":
				return tinyMCE.getButtonHTML(control_name, 'lang_wordpress_more_button', '{$pluginurl}/images/more.gif', 'wpMore');
			case "wp_page":
				return tinyMCE.getButtonHTML(control_name, 'lang_wordpress_page_button', '{$pluginurl}/images/page.gif', 'wpPage');
			case "wp_help":
				var buttons = tinyMCE.getButtonHTML(control_name, 'lang_help_button_title', '{$pluginurl}/images/help.gif', 'wpHelp');
				var hiddenControls = '<div class="zerosize">'
				+ '<input type="button" accesskey="n" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceSpellCheck\',false);" />'
				+ '<input type="button" accesskey="k" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Strikethrough\',false);" />'
				+ '<input type="button" accesskey="l" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'InsertUnorderedList\',false);" />'
				+ '<input type="button" accesskey="o" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'InsertOrderedList\',false);" />'
				+ '<input type="button" accesskey="w" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Outdent\',false);" />'
				+ '<input type="button" accesskey="q" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Indent\',false);" />'
				+ '<input type="button" accesskey="f" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyLeft\',false);" />'
				+ '<input type="button" accesskey="c" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyCenter\',false);" />'
				+ '<input type="button" accesskey="r" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyRight\',false);" />'
				+ '<input type="button" accesskey="j" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'JustifyFull\',false);" />'
				+ '<input type="button" accesskey="a" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceLink\',true);" />'
				+ '<input type="button" accesskey="s" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'unlink\',false);" />'
				+ '<input type="button" accesskey="m" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceImage\',true);" />'
				+ '<input type="button" accesskey="t" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'wpMore\');" />'
				+ '<input type="button" accesskey="g" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'wpPage\');" />'
				+ '<input type="button" accesskey="u" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Undo\',false);" />'
				+ '<input type="button" accesskey="y" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'Redo\',false);" />'
				+ '<input type="button" accesskey="e" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceCodeEditor\',false);" />'
				+ '<input type="button" accesskey="h" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'wpHelp\',false);" />'
				+ '<input type="button" accesskey="b" onclick="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'wpAdv\',false);" />'
				+ '</div>';
				return buttons+hiddenControls;
			case "wp_adv":
				return tinyMCE.getButtonHTML(control_name, 'lang_wordpress_adv_button', '{$pluginurl}/images/toolbars.gif', 'wpAdv');
			case "wp_adv_start":
				return '<div id="wpadvbar" style="display:none;"><br />';
			case "wp_adv_end":
				return '</div>';
		}
		return '';
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
		var inst = tinyMCE.getInstanceById(editor_id);
		var focusElm = inst.getFocusElement();
		var doc = inst.getDoc();

		function getAttrib(elm, name) {
			return elm.getAttribute(name) ? elm.getAttribute(name) : "";
		}

		// Handle commands
		switch (command) {
			case "wpMore":
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
				tinyMCE.execInstanceCommand(editor_id, 'mceInsertContent', false, html);
				tinyMCE.selectedInstance.repaint();
				return true;

			case "wpPage":
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

			case "wpHelp":
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
			case "wpAdv":
				var adv = document.getElementById('wpadvbar');
				if ( adv.style.display == 'none' ) {
					adv.style.display = 'block';
					tinyMCE.switchClass(editor_id + '_wp_adv', 'mceButtonSelected');
				} else {
					adv.style.display = 'none';
					tinyMCE.switchClass(editor_id + '_wp_adv', 'mceButtonNormal');
				}
				return true;
		}

		// Pass to next handler in chain
		return false;
	},

	cleanup : function(type, content) {
		switch (type) {

			case "insert_to_editor":
				var startPos = 0;
				var altMore = tinyMCE.getLang('lang_wordpress_more_alt');
				var altPage = tinyMCE.getLang('lang_wordpress_page_alt');

				// Parse all <!--more--> tags and replace them with images
				while ((startPos = content.indexOf('<!--more', startPos)) != -1) {
					var endPos = content.indexOf('-->', startPos) + 3;
					// Insert image
					var moreText = content.substring(startPos + 8, endPos - 3);
					var contentAfter = content.substring(endPos);
					content = content.substring(0, startPos);
					content += '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" ';
					content += ' width="100%" height="10px" moretext="'+moreText+'" ';
					content += 'alt="'+altMore+'" title="'+altMore+'" class="mce_plugin_wordpress_more" name="mce_plugin_wordpress_more" />';
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
					content += 'alt="'+altPage+'" title="'+altPage+'" class="mce_plugin_wordpress_page" name="mce_plugin_wordpress_page" />';
					content += contentAfter;

					startPos++;
				}

				// Look for \n in <pre>, replace with <br>
				var startPos = -1;
				while ((startPos = content.indexOf('<pre', startPos+1)) != -1) {
					var endPos = content.indexOf('</pre>', startPos+1);
					var innerPos = content.indexOf('>', startPos+1);
					var chunkBefore = content.substring(0, innerPos);
					var chunkAfter = content.substring(endPos);
					
					var innards = content.substring(innerPos, endPos);
					innards = innards.replace(/\n/g, '<br />');
					content = chunkBefore + innards + chunkAfter;
				}

				break;

			case "get_from_editor":
				// Parse all img tags and replace them with <!--more-->
				var startPos = -1;
				while ((startPos = content.indexOf('<img', startPos+1)) != -1) {
					var endPos = content.indexOf('/>', startPos);
					var attribs = this._parseAttributes(content.substring(startPos + 4, endPos));

					if (attribs['class'] == "mce_plugin_wordpress_more" || attribs['name'] == "mce_plugin_wordpress_more") {
						endPos += 2;

						var moreText = attribs['moretext'] ? attribs['moretext'] : '';
						var embedHTML = '<!--more'+moreText+'-->';

						// Insert embed/object chunk
						chunkBefore = content.substring(0, startPos);
						chunkAfter = content.substring(endPos);
						content = chunkBefore + embedHTML + chunkAfter;
					}
					if (attribs['class'] == "mce_plugin_wordpress_page" || attribs['name'] == "mce_plugin_wordpress_page") {
						endPos += 2;

						var embedHTML = '<!--nextpage-->';

						// Insert embed/object chunk
						chunkBefore = content.substring(0, startPos);
						chunkAfter = content.substring(endPos);
						content = chunkBefore + embedHTML + chunkAfter;
					}
				}

				// Remove normal line breaks
				content = content.replace(/\n|\r/g, ' ');

				// Look for <br> in <pre>, replace with \n
				var startPos = -1;
				while ((startPos = content.indexOf('<pre', startPos+1)) != -1) {
					var endPos = content.indexOf('</pre>', startPos+1);
					var innerPos = content.indexOf('>', startPos+1);
					var chunkBefore = content.substring(0, innerPos);
					var chunkAfter = content.substring(endPos);
					
					var innards = content.substring(innerPos, endPos);
					innards = innards.replace(new RegExp('<br\\s?/?>', 'g'), '\n');
					innards = innards.replace(new RegExp('\\s$', ''), '');
					content = chunkBefore + innards + chunkAfter;
				}

				// Remove anonymous, empty paragraphs.
				content = content.replace(new RegExp('<p>(\\s|&nbsp;)*</p>', 'mg'), '');
	
				// Handle table badness.
				content = content.replace(new RegExp('<(table( [^>]*)?)>.*?<((tr|thead)( [^>]*)?)>', 'mg'), '<$1><$3>');
				content = content.replace(new RegExp('<(tr|thead|tfoot)>.*?<((td|th)( [^>]*)?)>', 'mg'), '<$1><$2>');
				content = content.replace(new RegExp('</(td|th)>.*?<(td( [^>]*)?|th( [^>]*)?|/tr|/thead|/tfoot)>', 'mg'), '</$1><$2>');
				content = content.replace(new RegExp('</tr>.*?<(tr|/table)>', 'mg'), '</tr><$1>');
				content = content.replace(new RegExp('<(/?(table|tbody|tr|th|td)[^>]*)>(\\s*|(<br ?/?>)*)*', 'g'), '<$1>');
	
				// Pretty it up for the source editor.
				var blocklist = 'blockquote|ul|ol|li|table|thead|tr|th|td|div|h\\d|pre|p';
				content = content.replace(new RegExp('\\s*</('+blocklist+')>\\s*', 'mg'), '</$1>\n');
				content = content.replace(new RegExp('\\s*<(('+blocklist+')[^>]*)>\\s*', 'mg'), '\n<$1>');
				content = content.replace(new RegExp('<((li|/?tr|/?thead|/?tfoot)( [^>]*)?)>', 'g'), '\t<$1>');
				content = content.replace(new RegExp('<((td|th)( [^>]*)?)>', 'g'), '\t\t<$1>');
				content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'mg'), '<br />\n');
				content = content.replace(new RegExp('^\\s*', ''), '');
				content = content.replace(new RegExp('\\s*$', ''), '');

				break;
		}

		// Pass through to next handler in chain
		return content;
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {

		tinyMCE.switchClass(editor_id + '_wp_more', 'mceButtonNormal');
		tinyMCE.switchClass(editor_id + '_wp_page', 'mceButtonNormal');

		if (node == null)
			return;

		do {
			if (node.nodeName.toLowerCase() == "img" && tinyMCE.getAttrib(node, 'class').indexOf('mce_plugin_wordpress_more') == 0)
				tinyMCE.switchClass(editor_id + '_wp_more', 'mceButtonSelected');
			if (node.nodeName.toLowerCase() == "img" && tinyMCE.getAttrib(node, 'class').indexOf('mce_plugin_wordpress_page') == 0)
				tinyMCE.switchClass(editor_id + '_wp_page', 'mceButtonSelected');
		} while ((node = node.parentNode));

		return true;
	},

	saveCallback : function(el, content, body) {
		// We have a TON of cleanup to do.

		// Mark </p> if it has any attributes.
		content = content.replace(new RegExp('(<p[^>]+>.*?)</p>', 'mg'), '$1</p#>');

		// Decode the ampersands of time.
		// content = content.replace(new RegExp('&amp;', 'g'), '&');

		// Get it ready for wpautop.
		content = content.replace(new RegExp('[\\s]*<p>[\\s]*', 'mgi'), '');
		content = content.replace(new RegExp('[\\s]*</p>[\\s]*', 'mgi'), '\n\n');
		content = content.replace(new RegExp('\\n\\s*\\n\\s*\\n*', 'mgi'), '\n\n');
		content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

		// Fix some block element newline issues
		var blocklist = 'blockquote|ul|ol|li|table|thead|tr|th|td|div|h\\d|pre';
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

	},

	_parseAttributes : function(attribute_string) {
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

				attributes[attributeName.toLowerCase()] = attributeValue.substring(1);

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
};

tinyMCE.addPlugin("wordpress", TinyMCE_wordpressPlugin);

/* This little hack protects our More and Page placeholders from the removeformat command */
tinyMCE.orgExecCommand = tinyMCE.execCommand;
tinyMCE.execCommand = function (command, user_interface, value) {
	re = this.orgExecCommand(command, user_interface, value);

	if ( command == 'removeformat' ) {
		var inst = tinyMCE.getInstanceById('mce_editor_0');
		doc = inst.getDoc();
		var imgs = doc.getElementsByTagName('img');
		for (i=0;img=imgs[i];i++)
			img.className = img.name;
	}
	return re;
};
