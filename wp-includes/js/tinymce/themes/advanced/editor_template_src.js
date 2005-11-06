/* Import theme specific language pack */
tinyMCE.importThemeLanguagePack('advanced');

// Variable declarations
var TinyMCE_advanced_autoImportCSSClasses = true;
var TinyMCE_advanced_resizer = new Object();
var TinyMCE_advanced_buttons = [
	// Control id, button img, button title, command, user_interface, value
	['bold', '{$lang_bold_img}', '{$lang_bold_desc}', 'Bold'],
	['italic', '{$lang_italic_img}', '{$lang_italic_desc}', 'Italic'],
	['underline', '{$lang_underline_img}', '{$lang_underline_desc}', 'Underline'],
	['strikethrough', 'strikethrough.gif', '{$lang_striketrough_desc}', 'Strikethrough'],
	['justifyleft', 'left.gif', '{$lang_justifyleft_desc}', 'JustifyLeft'],
	['justifycenter', 'center.gif', '{$lang_justifycenter_desc}', 'JustifyCenter'],
	['justifyright', 'right.gif', '{$lang_justifyright_desc}', 'JustifyRight'],
	['justifyfull', 'full.gif', '{$lang_justifyfull_desc}', 'JustifyFull'],
	['bullist', 'bullist.gif', '{$lang_bullist_desc}', 'InsertUnorderedList'],
	['numlist', 'numlist.gif', '{$lang_numlist_desc}', 'InsertOrderedList'],
	['outdent', 'outdent.gif', '{$lang_outdent_desc}', 'Outdent'],
	['indent', 'indent.gif', '{$lang_indent_desc}', 'Indent'],
	['cut', 'cut.gif', '{$lang_cut_desc}', 'Cut'],
	['copy', 'copy.gif', '{$lang_copy_desc}', 'Copy'],
	['paste', 'paste.gif', '{$lang_paste_desc}', 'Paste'],
	['undo', 'undo.gif', '{$lang_undo_desc}', 'Undo'],
	['redo', 'redo.gif', '{$lang_redo_desc}', 'Redo'],
	['link', 'link.gif', '{$lang_link_desc}', 'mceLink', true],
	['unlink', 'unlink.gif', '{$lang_unlink_desc}', 'unlink'],
	['image', 'image.gif', '{$lang_image_desc}', 'mceImage', true],
	['cleanup', 'cleanup.gif', '{$lang_cleanup_desc}', 'mceCleanup'],
	['help', 'help.gif', '{$lang_help_desc}', 'mceHelp'],
	['code', 'code.gif', '{$lang_theme_code_desc}', 'mceCodeEditor'],
	['hr', 'hr.gif', '{$lang_theme_hr_desc}', 'inserthorizontalrule'],
	['removeformat', 'removeformat.gif', '{$lang_theme_removeformat_desc}', 'removeformat'],
	['sub', 'sub.gif', '{$lang_theme_sub_desc}', 'subscript'],
	['sup', 'sup.gif', '{$lang_theme_sup_desc}', 'superscript'],
	['forecolor', 'forecolor.gif', '{$lang_theme_forecolor_desc}', 'mceForeColor', true],
	['backcolor', 'backcolor.gif', '{$lang_theme_backcolor_desc}', 'mceBackColor', true],
	['charmap', 'charmap.gif', '{$lang_theme_charmap_desc}', 'mceCharMap'],
	['visualaid', 'visualaid.gif', '{$lang_theme_visualaid_desc}', 'mceToggleVisualAid'],
	['anchor', 'anchor.gif', '{$lang_theme_anchor_desc}', 'mceInsertAnchor'],
	['newdocument', 'newdocument.gif', '{$lang_newdocument_desc}', 'mceNewDocument']
];

/**
 * Returns HTML code for the specificed control.
 */
function TinyMCE_advanced_getControlHTML(button_name)
{
	var buttonTileMap = new Array('anchor.gif','backcolor.gif','bullist.gif','center.gif',
											'charmap.gif','cleanup.gif','code.gif','copy.gif','custom_1.gif',
											'cut.gif','forecolor.gif','full.gif','help.gif','hr.gif',
											'image.gif','indent.gif','left.gif','link.gif','numlist.gif',
											'outdent.gif','paste.gif','redo.gif','removeformat.gif',
											'right.gif','strikethrough.gif','sub.gif','sup.gif','undo.gif',
											'unlink.gif','visualaid.gif');

	// Lookup button in button list
	for (var i=0; i<TinyMCE_advanced_buttons.length; i++)
	{
		var but = TinyMCE_advanced_buttons[i];

		if (but[0] == button_name)
		{
			// Check for it in tilemap
			if (tinyMCE.settings['button_tile_map'])
			{
				for (var x=0; !tinyMCE.isMSIE && x<buttonTileMap.length; x++)
				{
					if (buttonTileMap[x] == but[1])
					{
						var cmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'' + but[3] + '\',' + (but.length > 4 ? but[4] : false) + (but.length > 5 ? ',\'' + but[5] + '\'' : '') + ')';
						return '<a href="javascript:' + cmd + '" onclick="' + cmd + ';return false;" onmousedown="return false;" target="_self"><img id="{$editor_id}_' + but[0] +'" src="{$themeurl}/images/spacer.gif" style="background-image:url({$themeurl}/images/buttons.gif); background-position: ' + (0-(x*20)) + 'px 0px" title="' + but[2] + '" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
					}
				}
			}

			// Old style
			var cmd = 'tinyMCE.execInstanceCommand(\'{$editor_id}\',\'' + but[3] + '\',' + (but.length > 4 ? but[4] : false) + (but.length > 5 ? ',\'' + but[5] + '\'' : '') + ')';
			return '<a href="javascript:' + cmd + '" onclick="' + cmd + ';return false;" onmousedown="return false;" target="_self"><img id="{$editor_id}_' + but[0] + '" src="{$themeurl}/images/' + but[1] + '" title="' + but[2] + '" width="20" height="20" class="mceButtonNormal" onmouseover="tinyMCE.switchClass(this,\'mceButtonOver\');" onmouseout="tinyMCE.restoreClass(this);" onmousedown="tinyMCE.restoreAndSwitchClass(this,\'mceButtonDown\');" /></a>';
		}
	}

	// Custom controlls other than buttons
	switch (button_name)
	{
		case "formatselect":
			var html = '<select id="{$editor_id}_formatSelect" name="{$editor_id}_formatSelect" onfocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'FormatBlock\',false,this.options[this.selectedIndex].value);" class="mceSelectList">';
			var formats = tinyMCE.getParam("theme_advanced_blockformats", "p,address,pre,h1,h2,h3,h4,h5,h6", true).split(',');
			var lookup = [
				['p', '{$lang_theme_paragraph}'],
				['address', '{$lang_theme_address}'],
				['pre', '{$lang_theme_pre}'],
				['h1', '{$lang_theme_h1}'],
				['h2', '{$lang_theme_h2}'],
				['h3', '{$lang_theme_h3}'],
				['h4', '{$lang_theme_h4}'],
				['h5', '{$lang_theme_h5}'],
				['h6', '{$lang_theme_h6}']
			];

			html += '<option value="">{$lang_theme_block}</option>';

			// Build format select
			for (var i=0; i<formats.length; i++)
			{
				for (var x=0; x<lookup.length; x++)
				{
					if (formats[i] == lookup[x][0])
					{
						html += '<option value="<' + lookup[x][0] + '>">' + lookup[x][1] + '</option>';
					}
				}
			}

			html += '</select>';
			//formatselect
		return html;

		case "styleselect":
			//styleselect
		return '<select id="{$editor_id}_styleSelect" onmousedown="TinyMCE_advanced_setupCSSClasses(\'{$editor_id}\');" name="{$editor_id}_styleSelect" onfocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'mceSetCSSClass\',false,this.options[this.selectedIndex].value);" class="mceSelectList">{$style_select_options}</select>';

		case "fontselect":
			var fontHTML = '<select id="{$editor_id}_fontNameSelect" name="{$editor_id}_fontNameSelect" onfocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'FontName\',false,this.options[this.selectedIndex].value);" class="mceSelectList"><option value="">{$lang_theme_fontdefault}</option>';
			var iFonts = 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;WingDings=wingdings';
			var nFonts = 'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sand;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats';
			var fonts = tinyMCE.getParam("theme_advanced_fonts", nFonts).split(';');
			for (var i=0; i<fonts.length; i++) {
				if (fonts[i] != '') {
					var parts = fonts[i].split('=');
					fontHTML += '<option value="' + parts[1] + '">' + parts[0] + '</option>';
				}
			}

			fontHTML += '</select>';
			return fontHTML;

		case "fontsizeselect":
			//fontsizeselect
		return '<select id="{$editor_id}_fontSizeSelect" name="{$editor_id}_fontSizeSelect" onfocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="tinyMCE.execInstanceCommand(\'{$editor_id}\',\'FontSize\',false,this.options[this.selectedIndex].value);" class="mceSelectList">\
		<option value="0">{$lang_theme_font_size}</option>\
		<option value="1">1 (8 pt)</option>\
		<option value="2">2 (10 pt)</option>\
		<option value="3">3 (12 pt)</option>\
		<option value="4">4 (14 pt)</option>\
		<option value="5">5 (18 pt)</option>\
		<option value="6">6 (24 pt)</option>\
		<option value="7">7 (36 pt)</option>\
		</select>';

		case "|":
		case "separator":
		return '<img src="{$themeurl}/images/spacer.gif" width="1" height="15" class="mceSeparatorLine">';

		case "spacer":
		return '<img src="{$themeurl}/images/spacer.gif" width="1" height="15" border="0" class="mceSeparatorLine" style="vertical-align: middle" />';

		case "rowseparator":
		return '<br />';
	}

	return "";
}

/**
 * Theme specific exec command handeling.
 */
function TinyMCE_advanced_execCommand(editor_id, element, command, user_interface, value)
{
	switch (command)
	{
		case "mceForeColor":
			var template = new Array();
			var elm = tinyMCE.selectedInstance.getFocusElement();
			var inputColor = tinyMCE.getAttrib(elm, "color");

			if (inputColor == '')
				inputColor = elm.style.color;

			if (!inputColor)
				inputColor = "#000000";

			template['file'] = 'color_picker.htm';
			template['width'] = 220;
			template['height'] = 190;

			tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes", command : "forecolor", input_color : inputColor});
		return true;

		case "mceBackColor":
			var template = new Array();
			var elm = tinyMCE.selectedInstance.getFocusElement();
			var inputColor = elm.style.backgroundColor;

			if (!inputColor)
				inputColor = "#000000";

			template['file'] = 'color_picker.htm';
			template['width'] = 220;
			template['height'] = 190;

			template['width'] += tinyMCE.getLang('lang_theme_advanced_backcolor_delta_width', 0);
			template['height'] += tinyMCE.getLang('lang_theme_advanced_backcolor_delta_height', 0);

			tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes", command : "HiliteColor", input_color : inputColor});
			//mceBackColor
		return true;

		case "mceColorPicker":
			if (user_interface) {
				var template = new Array();
				var inputColor = value['document'].getElementById(value['element_id']).value;

				template['file'] = 'color_picker.htm';
				template['width'] = 220;
				template['height'] = 190;
				template['close_previous'] = "no";

				template['width'] += tinyMCE.getLang('lang_theme_advanced_colorpicker_delta_width', 0);
				template['height'] += tinyMCE.getLang('lang_theme_advanced_colorpicker_delta_height', 0);

				if (typeof(value['store_selection']) == "undefined")
					value['store_selection'] = true;

				tinyMCE.lastColorPickerValue = value;
				tinyMCE.openWindow(template, {editor_id : editor_id, mce_store_selection : value['store_selection'], inline : "yes", command : "mceColorPicker", input_color : inputColor});
			} else {
				var savedVal = tinyMCE.lastColorPickerValue;
				var elm = savedVal['document'].getElementById(savedVal['element_id']);
				elm.value = value;
				eval('elm.onchange();');
			}
		return true;

		case "mceCodeEditor":
			var template = new Array();

			template['file'] = 'source_editor.htm';
			template['width'] = parseInt(tinyMCE.getParam("theme_advanced_source_editor_width", 500));
			template['height'] = parseInt(tinyMCE.getParam("theme_advanced_source_editor_height", 400));

			tinyMCE.openWindow(template, {editor_id : editor_id, resizable : "yes", scrollbars : "no", inline : "yes"});
			//mceCodeEditor
		return true;

		case "mceCharMap":
			var template = new Array();

			template['file'] = 'charmap.htm';
			template['width'] = 550 + (tinyMCE.isOpera ? 40 : 0);
			template['height'] = 250;

			template['width'] += tinyMCE.getLang('lang_theme_advanced_charmap_delta_width', 0);
			template['height'] += tinyMCE.getLang('lang_theme_advanced_charmap_delta_height', 0);

			tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});
			//mceCharMap
		return true;

		case "mceInsertAnchor":
			var template = new Array();

			template['file'] = 'anchor.htm';
			template['width'] = 320;
			template['height'] = 90 + (tinyMCE.isNS7 ? 30 : 0);

			template['width'] += tinyMCE.getLang('lang_theme_advanced_anchor_delta_width', 0);
			template['height'] += tinyMCE.getLang('lang_theme_advanced_anchor_delta_height', 0);

			tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes"});
		return true;

		case "mceNewDocument":
			if (confirm(tinyMCE.getLang('lang_newdocument')))
				tinyMCE.execInstanceCommand(editor_id, 'mceSetContent', false, '');

			return true;
	}

	// Default behavior
	return false;
}

/**
 * Editor instance template function.
 */
function TinyMCE_advanced_getEditorTemplate(settings, editorId)
{
	function removeFromArray(in_array, remove_array)
	{
		var outArray = new Array();
		
		for (var i=0; i<in_array.length; i++)
		{
			skip = false;

			for (var j=0; j<remove_array.length; j++)
			{
				if (in_array[i] == remove_array[j])
				{
					skip = true;
				}
			}

			if (!skip)
			{
				outArray[outArray.length] = in_array[i];
			}
		}

		return outArray;
	}

	function addToArray(in_array, add_array)
	{
		for (var i=0; i<add_array.length; i++)
		{
			in_array[in_array.length] = add_array[i];
		}

		return in_array;
	}

	var template = new Array();
	var deltaHeight = 0;

	var resizing = tinyMCE.getParam("theme_advanced_resizing", false);
	var path = tinyMCE.getParam("theme_advanced_path", true);
	var statusbarHTML = '<div id="{$editor_id}_path" class="mceStatusbarPathText" style="display: ' + (path ? "block" : "none") + '">&nbsp;</div><div id="{$editor_id}_resize" class="mceStatusbarResize" style="display: ' + (resizing ? "block" : "none") + '" onmousedown="TinyMCE_advanced_setResizing(event,\'{$editor_id}\',true);"></div><br style="clear: both" />';
	var layoutManager = tinyMCE.getParam("theme_advanced_layout_manager", "SimpleLayout");

	// Setup style select options -- MOVED UP FOR EXTERNAL TOOLBAR COMPATABILITY!
	var styleSelectHTML = '<option value="">{$lang_theme_style_select}</option>';
	if (settings['theme_advanced_styles']) {
		var stylesAr = settings['theme_advanced_styles'].split(';');
		
		for (var i=0; i<stylesAr.length; i++) {
			var key, value;

			key = stylesAr[i].split('=')[0];
			value = stylesAr[i].split('=')[1];

			styleSelectHTML += '<option value="' + value + '">' + key + '</option>';
		}

		TinyMCE_advanced_autoImportCSSClasses = false;
	}

	switch(layoutManager) {
		case "SimpleLayout" : //the default TinyMCE Layout (for backwards compatibility)...
			var toolbarHTML = "";
			var toolbarLocation = tinyMCE.getParam("theme_advanced_toolbar_location", "bottom");
			var toolbarAlign = tinyMCE.getParam("theme_advanced_toolbar_align", "center");
			var pathLocation = tinyMCE.getParam("theme_advanced_path_location", "none"); // Compatiblity
			var statusbarLocation = tinyMCE.getParam("theme_advanced_statusbar_location", pathLocation);
			var defVals = {
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,styleselect,formatselect",
				theme_advanced_buttons2 : "bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,image,cleanup,help,code",
				theme_advanced_buttons3 : "hr,removeformat,visualaid,separator,sub,sup,separator,charmap"
			};

			// Add accessibility control
			toolbarHTML += '<a href="#" accesskey="q" title="' + tinyMCE.getLang("lang_toolbar_focus") + '"></a>';

			// Render rows
			for (var i=1; i<100; i++) {
				var def = defVals["theme_advanced_buttons" + i];

				var buttons = tinyMCE.getParam("theme_advanced_buttons" + i, def == null ? '' : def, true, ',');
				if (buttons.length == 0)
					break;

				buttons = removeFromArray(buttons, tinyMCE.getParam("theme_advanced_disable", "", true, ','));
				buttons = addToArray(buttons, tinyMCE.getParam("theme_advanced_buttons" + i + "_add", "", true, ','));
				buttons = addToArray(tinyMCE.getParam("theme_advanced_buttons" + i + "_add_before", "", true, ','), buttons);

				for (var b=0; b<buttons.length; b++)
					toolbarHTML += tinyMCE.getControlHTML(buttons[b]);

				if (buttons.length > 0) {
					toolbarHTML += "<br />";
					deltaHeight -= 23;
				}
			}

			// Add accessibility control
			toolbarHTML += '<a href="#" accesskey="z" onfocus="tinyMCE.getInstanceById(\'' + editorId + '\').getWin().focus();"></a>';

			// Setup template html
			template['html'] = '<table class="mceEditor" border="0" cellpadding="0" cellspacing="0" width="{$width}" height="{$height}" style="width:{$width}px;height:{$height}px"><tbody>';

			if (toolbarLocation == "top")
			{
				template['html'] += '<tr><td class="mceToolbarTop" align="' + toolbarAlign + '" height="1" nowrap="nowrap">' + toolbarHTML + '</td></tr>';
			}

			if (statusbarLocation == "top")
			{
				template['html'] += '<tr><td class="mceStatusbarTop" height="1">' + statusbarHTML + '</td></tr>';
				deltaHeight -= 23;
			}

			template['html'] += '<tr><td align="center"><span id="{$editor_id}"></span></td></tr>';

			if (toolbarLocation == "bottom")
			{
				template['html'] += '<tr><td class="mceToolbarBottom" align="' + toolbarAlign + '" height="1">' + toolbarHTML + '</td></tr>';
			}

			// External toolbar changes
			if (toolbarLocation == "external")
			{
				var bod = document.body;
				var elm = document.createElement ("div");
				
				toolbarHTML = tinyMCE.replaceVars(toolbarHTML, tinyMCE.settings);
				toolbarHTML = tinyMCE.replaceVars(toolbarHTML, tinyMCELang);
				toolbarHTML = tinyMCE.replaceVar(toolbarHTML, 'style_select_options', styleSelectHTML);
				toolbarHTML = tinyMCE.replaceVar(toolbarHTML, "editor_id", editorId);
				toolbarHTML = tinyMCE.applyTemplate(toolbarHTML);

				elm.className = "mceToolbarExternal";
				elm.id = editorId+"_toolbar";
				elm.innerHTML = '<table width="100%" border="0" align="center"><tr><td align="center">'+toolbarHTML+'</td></tr></table>';
				bod.appendChild (elm);
				// bod.style.marginTop = elm.offsetHeight + "px";

				deltaHeight = 0;
				tinyMCE.getInstanceById(editorId).toolbarElement = elm;

				//template['html'] = '<div id="mceExternalToolbar" align="center" class="mceToolbarExternal"><table width="100%" border="0" align="center"><tr><td align="center">'+toolbarHTML+'</td></tr></table></div>' + template["html"];
			}
			else
			{
				tinyMCE.getInstanceById(editorId).toolbarElement = null;
			}

			if (statusbarLocation == "bottom")
			{
				template['html'] += '<tr><td class="mceStatusbarBottom" height="1">' + statusbarHTML + '</td></tr>';
				deltaHeight -= 23;
			}

			template['html'] += '</tbody></table>';
			//"SimpleLayout"
		break;

		case "RowLayout" : //Container Layout - containers defined in "theme_advanced_containers" are rendered from top to bottom.
			template['html'] = '<table class="mceEditor" border="0" cellpadding="0" cellspacing="0" width="{$width}" height="{$height}" style="width:{$width}px;height:{$height}px"><tbody>';

			var containers = tinyMCE.getParam("theme_advanced_containers", "", true, ",");
			var defaultContainerCSS = tinyMCE.getParam("theme_advanced_containers_default_class", "container");
			var defaultContainerAlign = tinyMCE.getParam("theme_advanced_containers_default_align", "center");

			//Render Containers:
			for (var i = 0; i < containers.length; i++)
			{
				if (containers[i] == "mceEditor") //Exceptions for mceEditor and ...
				{
					template['html'] += '<tr><td align="center" class="mceEditor_border">\
												<span id="{$editor_id}"></span>\
												</td></tr>';
				}
				else if (containers[i] == "mceElementpath" || containers[i] == "mceStatusbar") // ... mceElementpath:
				{
					var pathClass = "mceStatusbar";

					if (i == containers.length-1)
					{
						pathClass = "mceStatusbarBottom";
					}
					else if (i == 0)
					{
						pathClass = "mceStatusbar";
					}
					else
					{
						deltaHeight-=2;
					}

					template['html'] += '<tr><td class="' + pathClass + '" height="1">' + statusbarHTML + '</td></tr>';
					deltaHeight -= 22;
				}
				else //Render normal Container:
				{
					var curContainer = tinyMCE.getParam("theme_advanced_container_"+containers[i], "", true, ',');
					var curContainerHTML = "";
					var curAlign = tinyMCE.getParam("theme_advanced_container_"+containers[i]+"_align", defaultContainerAlign);
					var curCSS = tinyMCE.getParam("theme_advanced_container_"+containers[i]+"_class", defaultContainerCSS);

					for (var j=0; j<curContainer.length; j++)
					{
						curContainerHTML += tinyMCE.getControlHTML(curContainer[j]);
					}

					if (curContainer.length > 0)
					{
						curContainerHTML += "<br />";
						deltaHeight -= 23;
					}

					template['html'] += '<tr><td class="' + curCSS + '" align="' + curAlign + '" height="1">' + curContainerHTML + '</td></tr>';
				}
			}

			template['html'] += '</tbody></table>';
			//RowLayout
		break;

		case "BorderLayout" : //will be like java.awt.BorderLayout of SUN Java...
			// Not implemented yet... 
		break;

		case "CustomLayout" : //User defined layout callback...
			var customLayout = tinyMCE.getParam("theme_advanced_custom_layout","");
			
			if (customLayout != "" && eval("typeof(" + customLayout + ")") != "undefined")
			{
				template = eval(customLayout + "(template);");
			}
		break;
			
		default:
			alert('UNDEFINED LAYOUT MANAGER! PLEASE CHECK YOUR TINYMCE CONFIG!');
			//CustomLayout
		break;
	}

	template['html'] += '<div id="{$editor_id}_resize_box" class="mceResizeBox"></div>';
	template['html'] = tinyMCE.replaceVar(template['html'], 'style_select_options', styleSelectHTML);
	template['delta_width'] = 0;
	template['delta_height'] = deltaHeight;

	return template;
}

/**
 * Starts/stops the editor resizing.
 */
function TinyMCE_advanced_setResizing(e, editor_id, state) {
	e = typeof(e) == "undefined" ? window.event : e;

	var resizer = TinyMCE_advanced_resizer;
	var editorContainer = document.getElementById(editor_id + '_parent');
	var editorArea = document.getElementById(editor_id + '_parent').firstChild;
	var resizeBox = document.getElementById(editor_id + '_resize_box');
	var inst = tinyMCE.getInstanceById(editor_id);

	if (state) {
		// Place box over editor area
		var width = editorArea.clientWidth;
		var height = editorArea.clientHeight;

		resizeBox.style.width = width + "px";
		resizeBox.style.height = height + "px";

		resizer.iframeWidth = inst.iframeElement.clientWidth;
		resizer.iframeHeight = inst.iframeElement.clientHeight;

		// Hide editor and show resize box
		editorArea.style.display = "none";
		resizeBox.style.display = "block";

		// Add event handlers, only once
		if (!resizer.eventHandlers) {
			if (tinyMCE.isMSIE)
				tinyMCE.addEvent(document, "mousemove", TinyMCE_advanced_resizeEventHandler);
			else
				tinyMCE.addEvent(window, "mousemove", TinyMCE_advanced_resizeEventHandler);

			tinyMCE.addEvent(document, "mouseup", TinyMCE_advanced_resizeEventHandler);

			resizer.eventHandlers = true;
		}

		resizer.resizing = true;
		resizer.downX = e.screenX;
		resizer.downY = e.screenY;
		resizer.width = parseInt(resizeBox.style.width);
		resizer.height = parseInt(resizeBox.style.height);
		resizer.editorId = editor_id;
		resizer.resizeBox = resizeBox;
		resizer.horizontal = tinyMCE.getParam("theme_advanced_resize_horizontal", true);
	} else {
		resizer.resizing = false;
		resizeBox.style.display = "none";
		editorArea.style.display = tinyMCE.isMSIE ? "block" : "table";
		tinyMCE.execCommand('mceResetDesignMode');
	}
}

function TinyMCE_advanced_initInstance(inst) {
	if (tinyMCE.getParam("theme_advanced_resizing", false)) {
		if (tinyMCE.getParam("theme_advanced_resizing_use_cookie", true)) {
			var w = TinyMCE_advanced_getCookie("TinyMCE_" + inst.editorId + "_width");
			var h = TinyMCE_advanced_getCookie("TinyMCE_" + inst.editorId + "_height");

			TinyMCE_advanced_resizeTo(inst, w, h, tinyMCE.getParam("theme_advanced_resize_horizontal", true));
		}
	}
}

function TinyMCE_advanced_setCookie(name, value, expires, path, domain, secure) {
	var curCookie = name + "=" + escape(value) +
		((expires) ? "; expires=" + expires.toGMTString() : "") +
		((path) ? "; path=" + escape(path) : "") +
		((domain) ? "; domain=" + domain : "") +
		((secure) ? "; secure" : "");

	document.cookie = curCookie;
}

function TinyMCE_advanced_getCookie(name) {
	var dc = document.cookie;
	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);

	if (begin == -1) {
		begin = dc.indexOf(prefix);

		if (begin != 0)
			return null;
	} else
		begin += 2;

	var end = document.cookie.indexOf(";", begin);

	if (end == -1)
		end = dc.length;

	return unescape(dc.substring(begin + prefix.length, end));
}

function TinyMCE_advanced_resizeTo(inst, w, h, set_w) {
	var editorContainer = document.getElementById(inst.editorId + '_parent');
	var tableElm = editorContainer.firstChild;
	var iframe = inst.iframeElement;

	if (w == null || w == "null") {
		set_w = false;
		w = 0;
	}

	if (h == null || h == "null")
		return;

	w = parseInt(w);
	h = parseInt(h);

	if (tinyMCE.isGecko) {
		w += 2;
		h += 2;
	}

	var dx = w - tableElm.clientWidth;
	var dy = h - tableElm.clientHeight;

	if (set_w)
		tableElm.style.width = w + "px";

	tableElm.style.height = h + "px";

	iw = iframe.clientWidth + dx;
	ih = iframe.clientHeight + dy;

	if (tinyMCE.isGecko) {
		iw -= 2;
		ih -= 2;
	}

	if (set_w)
		iframe.style.width = iw + "px";

	iframe.style.height = ih + "px";

	// Is it to small, make it bigger again
	if (set_w) {
		var tableBodyElm = tableElm.firstChild;
		var minIframeWidth = tableBodyElm.scrollWidth;
		if (inst.iframeElement.clientWidth < minIframeWidth) {
			dx = minIframeWidth - inst.iframeElement.clientWidth;

			inst.iframeElement.style.width = (iw + dx) + "px";
		}
	}
}

/**
 * Handles resizing events.
 */
function TinyMCE_advanced_resizeEventHandler(e) {
	var resizer = TinyMCE_advanced_resizer;

	// Do nothing
	if (!resizer.resizing)
		return;

	e = typeof(e) == "undefined" ? window.event : e;

	var dx = e.screenX - resizer.downX;
	var dy = e.screenY - resizer.downY;
	var resizeBox = resizer.resizeBox;
	var editorId = resizer.editorId;

	switch (e.type) {
		case "mousemove":
			if (resizer.horizontal)
				resizeBox.style.width = (resizer.width + dx) + "px";

			resizeBox.style.height = (resizer.height + dy) + "px";
			break;

		case "mouseup":
			TinyMCE_advanced_setResizing(e, editorId, false);
			TinyMCE_advanced_resizeTo(tinyMCE.getInstanceById(editorId), resizer.width + dx, resizer.height + dy, resizer.horizontal);

			// Expire in a month
			if (tinyMCE.getParam("theme_advanced_resizing_use_cookie", true)) {
				var expires = new Date();
				expires.setTime(expires.getTime() + 3600000 * 24 * 30);

				// Set the cookies
				TinyMCE_advanced_setCookie("TinyMCE_" + editorId + "_width", "" + (resizer.horizontal ? resizer.width + dx : ""), expires);
				TinyMCE_advanced_setCookie("TinyMCE_" + editorId + "_height", "" + (resizer.height + dy), expires);
			}
			break;
	}
}

/**
 * Insert link template function.
 */
function TinyMCE_advanced_getInsertLinkTemplate()
{
	var template = new Array();

	template['file'] = 'link.htm';
	template['width'] = 330;
	template['height'] = 170;

	// Language specific width and height addons
	template['width'] += tinyMCE.getLang('lang_insert_link_delta_width', 0);
	template['height'] += tinyMCE.getLang('lang_insert_link_delta_height', 0);

	return template;
};

/**
 * Insert image template function.
 */
function TinyMCE_advanced_getInsertImageTemplate()
{
	var template = new Array();

	template['file'] = 'image.htm?src={$src}';
	template['width'] = 340;
	template['height'] = 245;

	// Language specific width and height addons
	template['width'] += tinyMCE.getLang('lang_insert_image_delta_width', 0);
	template['height'] += tinyMCE.getLang('lang_insert_image_delta_height', 0);

	return template;
};

/**
 * Node change handler.
 */
function TinyMCE_advanced_handleNodeChange (editor_id, node, undo_index, undo_levels, visual_aid, any_selection, setup_content) {
	function selectByValue(select_elm, value, first_index) {
		first_index = typeof(first_index) == "undefined" ? false : true;

		if (select_elm) {
			for (var i=0; i<select_elm.options.length; i++) {
				var ov = "" + select_elm.options[i].value;

				if (first_index && ov.toLowerCase().indexOf(value.toLowerCase()) == 0) {
					select_elm.selectedIndex = i;
					return true;
				}

				if (ov == value) {
					select_elm.selectedIndex = i;
					return true;
				}
			}
		}

		return false;
	};

	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	};

	// No node provided
	if (node == null)
	{
		return;
	}

	// Update path
	var pathElm = document.getElementById(editor_id + "_path");
	var inst = tinyMCE.getInstanceById(editor_id);
	var doc = inst.getDoc();

	if (pathElm) {
		// Get node path
		var parentNode = node;
		var path = new Array();
		
		while (parentNode != null) {
			if (parentNode.nodeName.toUpperCase() == "BODY") {
				break;
			}

			// Only append element nodes to path
			if (parentNode.nodeType == 1) {
				path[path.length] = parentNode;
			}

			parentNode = parentNode.parentNode;
		}

		// Setup HTML
		var html = "";
		for (var i=path.length-1; i>=0; i--) {
			var nodeName = path[i].nodeName.toLowerCase();
			var nodeData = "";

			if (nodeName == "b") {
				nodeName = "strong";
			}

			if (nodeName == "i") {
				nodeName = "em";
			}

			if (nodeName == "span") {
				var cn = tinyMCE.getAttrib(path[i], "class");
				if (cn != "" && cn.indexOf('mceItem') == -1)
					nodeData += "class: " + cn + " ";

				var st = tinyMCE.getAttrib(path[i], "style");
				if (st != "") {
					st = tinyMCE.serializeStyle(tinyMCE.parseStyle(st));
					nodeData += "style: " + st + " ";
				}
			}

			if (nodeName == "font") {
				if (tinyMCE.getParam("convert_fonts_to_spans"))
					nodeName = "span";

				var face = tinyMCE.getAttrib(path[i], "face");
				if (face != "")
					nodeData += "font: " + face + " ";

				var size = tinyMCE.getAttrib(path[i], "size");
				if (size != "")
					nodeData += "size: " + size + " ";

				var color = tinyMCE.getAttrib(path[i], "color");
				if (color != "")
					nodeData += "color: " + color + " ";
			}

			if (getAttrib(path[i], 'id') != "") {
				nodeData += "id: " + path[i].getAttribute('id') + " ";
			}

			var className = tinyMCE.getVisualAidClass(tinyMCE.getAttrib(path[i], "class"), false);
			if (className != "" && className.indexOf('mceItem') == -1)
				nodeData += "class: " + className + " ";

			if (getAttrib(path[i], 'src') != "") {
				nodeData += "src: " + path[i].getAttribute('src') + " ";
			}

			if (getAttrib(path[i], 'href') != "") {
				nodeData += "href: " + path[i].getAttribute('href') + " ";
			}

			if (nodeName == "img" && tinyMCE.getAttrib(path[i], "class").indexOf('mceItemFlash') != -1) {
				nodeName = "flash";
				nodeData = "src: " + path[i].getAttribute('title');
			}

			if (nodeName == "a" && (anchor = tinyMCE.getAttrib(path[i], "name")) != "") {
				nodeName = "a";
				nodeName += "#" + anchor;
				nodeData = "";
			}

			if (getAttrib(path[i], 'name').indexOf("mce_") != 0) {
				var className = tinyMCE.getVisualAidClass(tinyMCE.getAttrib(path[i], "class"), false);
				if (className != "" && className.indexOf('mceItem') == -1) {
					nodeName += "." + className;
				}
			}

			var cmd = 'tinyMCE.execInstanceCommand(\'' + editor_id + '\',\'mceSelectNodeDepth\',false,\'' + i + '\');';
			html += '<a title="' + nodeData + '" href="javascript:' + cmd + '" onclick="' + cmd + 'return false;" onmousedown="return false;" target="_self" class="mcePathItem">' + nodeName + '</a>';

			if (i > 0) {
				html += " &raquo; ";
			}
		}

		pathElm.innerHTML = '<a href="#" accesskey="x"></a>' + tinyMCE.getLang('lang_theme_path') + ": " + html + '&nbsp;';
	}

	// Reset old states
	tinyMCE.switchClassSticky(editor_id + '_justifyleft', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_justifyright', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_justifycenter', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_justifyfull', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_bold', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_italic', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_underline', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_strikethrough', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_bullist', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_numlist', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_sub', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_sup', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_anchor', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_link', 'mceButtonDisabled', true);
	tinyMCE.switchClassSticky(editor_id + '_unlink', 'mceButtonDisabled', true);
	tinyMCE.switchClassSticky(editor_id + '_outdent', 'mceButtonDisabled', true);
	tinyMCE.switchClassSticky(editor_id + '_image', 'mceButtonNormal');
	tinyMCE.switchClassSticky(editor_id + '_hr', 'mceButtonNormal');

	if (node.nodeName == "A" && tinyMCE.getAttrib(node, "class").indexOf('mceItemAnchor') != -1)
		tinyMCE.switchClassSticky(editor_id + '_anchor', 'mceButtonSelected');

	// Get link
	var anchorLink = tinyMCE.getParentElement(node, "a", "href");

	if (anchorLink || any_selection)
	{
		tinyMCE.switchClassSticky(editor_id + '_link', anchorLink ? 'mceButtonSelected' : 'mceButtonNormal', false);
		tinyMCE.switchClassSticky(editor_id + '_unlink', anchorLink ? 'mceButtonSelected' : 'mceButtonNormal', false);
	}

	// Handle visual aid
	tinyMCE.switchClassSticky(editor_id + '_visualaid', visual_aid ? 'mceButtonSelected' : 'mceButtonNormal', false);

	if (undo_levels != -1)
	{
		tinyMCE.switchClassSticky(editor_id + '_undo', 'mceButtonDisabled', true);
		tinyMCE.switchClassSticky(editor_id + '_redo', 'mceButtonDisabled', true);
	}

	// Within li, blockquote
	if (tinyMCE.getParentElement(node, "li,blockquote"))
	{
		tinyMCE.switchClassSticky(editor_id + '_outdent', 'mceButtonNormal', false);
	}

	// Has redo levels
	if (undo_index != -1 && (undo_index < undo_levels-1 && undo_levels > 0))
	{
		tinyMCE.switchClassSticky(editor_id + '_redo', 'mceButtonNormal', false);
	}

	// Has undo levels
	if (undo_index != -1 && (undo_index > 0 && undo_levels > 0))
	{
		tinyMCE.switchClassSticky(editor_id + '_undo', 'mceButtonNormal', false);
	}

	// Select class in select box
	var selectElm = document.getElementById(editor_id + "_styleSelect");
	
	if (selectElm)
	{
		TinyMCE_advanced_setupCSSClasses(editor_id);

		classNode = node;
		breakOut = false;
		var index = 0;

		do
		{
			if (classNode && classNode.className)
			{
				for (var i=0; i<selectElm.options.length; i++)
				{
					if (selectElm.options[i].value == classNode.className)
					{
						index = i;
						breakOut = true;
						break;
					}
				}
			}
		} while (!breakOut && classNode != null && (classNode = classNode.parentNode) != null);

		selectElm.selectedIndex = index;
	}

	// Select formatblock
	var selectElm = document.getElementById(editor_id + "_formatSelect");
	
	if (selectElm)
	{
		var elm = tinyMCE.getParentElement(node, "p,div,h1,h2,h3,h4,h5,h6,pre,address");
		
		if (elm)
		{
			selectByValue(selectElm, "<" + elm.nodeName.toLowerCase() + ">");
		}
		else
		{
			selectByValue(selectElm, "");
		}
	}

	// Select fontselect
	var selectElm = document.getElementById(editor_id + "_fontNameSelect");
	if (selectElm) {
		if (!tinyMCE.isSafari && !(tinyMCE.isMSIE && !tinyMCE.isOpera)) {
			var face = doc.queryCommandValue('FontName');

			face = face == null || face == "" ? "" : face;

			selectByValue(selectElm, face, face != "");
		} else {
			var elm = tinyMCE.getParentElement(node, "font", "face");

			if (elm) {
				var family = tinyMCE.getAttrib(elm, "face");

				if (family == '')
					family = '' + elm.style.fontFamily;

				if (!selectByValue(selectElm, family, family != ""))
					selectByValue(selectElm, "");
			} else
				selectByValue(selectElm, "");
		}
	}

	// Select fontsize
	var selectElm = document.getElementById(editor_id + "_fontSizeSelect");
	if (selectElm) {
		if (!tinyMCE.isSafari && !tinyMCE.isOpera) {
			var size = doc.queryCommandValue('FontSize');
			selectByValue(selectElm, size == null || size == "" ? "0" : size);
		} else {
			var elm = tinyMCE.getParentElement(node, "font", "size");
			if (elm) {
				var size = tinyMCE.getAttrib(elm, "size");

				if (size == '') {
					var sizes = new Array('', '8px', '10px', '12px', '14px', '18px', '24px', '36px');

					size = '' + elm.style.fontSize;

					for (var i=0; i<sizes.length; i++) {
						if (('' + sizes[i]) == size) {
							size = i;
							break;
						}
					}
				}

				if (!selectByValue(selectElm, size))
					selectByValue(selectElm, "");
			} else
				selectByValue(selectElm, "0");
		}
	}

	// Handle align attributes
	alignNode = node;
	breakOut = false;
	do {
		if (!alignNode.getAttribute || !alignNode.getAttribute('align')) {
			continue;
		}

		switch (alignNode.getAttribute('align').toLowerCase()) {
			case "left":
				tinyMCE.switchClassSticky(editor_id + '_justifyleft', 'mceButtonSelected');
				breakOut = true;
			break;

			case "right":
				tinyMCE.switchClassSticky(editor_id + '_justifyright', 'mceButtonSelected');
				breakOut = true;
			break;

			case "middle":
			case "center":
				tinyMCE.switchClassSticky(editor_id + '_justifycenter', 'mceButtonSelected');
				breakOut = true;
			break;

			case "justify":
				tinyMCE.switchClassSticky(editor_id + '_justifyfull', 'mceButtonSelected');
				breakOut = true;
			break;
		}
	} while (!breakOut && (alignNode = alignNode.parentNode) != null);

	// Div justification
	var div = tinyMCE.getParentElement(node, "div");
	if (div && div.style.textAlign == "center")
		tinyMCE.switchClassSticky(editor_id + '_justifycenter', 'mceButtonSelected');

	// Do special text
	if (!setup_content) {
		// , "JustifyLeft", "_justifyleft", "JustifyCenter", "justifycenter", "JustifyRight", "justifyright", "JustifyFull", "justifyfull", "InsertUnorderedList", "bullist", "InsertOrderedList", "numlist", "InsertUnorderedList", "bullist", "Outdent", "outdent", "Indent", "indent", "subscript", "sub"
		var ar = new Array("Bold", "_bold", "Italic", "_italic", "Strikethrough", "_strikethrough", "superscript", "_sup", "subscript", "_sub");
		for (var i=0; i<ar.length; i+=2) {
			if (doc.queryCommandState(ar[i]))
				tinyMCE.switchClassSticky(editor_id + ar[i+1], 'mceButtonSelected');
		}

		if (doc.queryCommandState("Underline") && (node.parentNode == null || node.parentNode.nodeName != "A")) {
			tinyMCE.switchClassSticky(editor_id + '_underline', 'mceButtonSelected');
		}
	}

	// Handle elements
	do {
		switch (node.nodeName) {
/*			case "B":
			case "STRONG":
				tinyMCE.switchClassSticky(editor_id + '_bold', 'mceButtonSelected');
			break;

			case "I":
			case "EM":
				tinyMCE.switchClassSticky(editor_id + '_italic', 'mceButtonSelected');
			break;

			case "U":
				tinyMCE.switchClassSticky(editor_id + '_underline', 'mceButtonSelected');
			break;

			case "STRIKE":
				tinyMCE.switchClassSticky(editor_id + '_strikethrough', 'mceButtonSelected');
			break;*/

			case "UL":
				tinyMCE.switchClassSticky(editor_id + '_bullist', 'mceButtonSelected');
			break;

			case "OL":
				tinyMCE.switchClassSticky(editor_id + '_numlist', 'mceButtonSelected');
			break;

			case "HR":
				 tinyMCE.switchClassSticky(editor_id + '_hr', 'mceButtonSelected');
			break;

			case "IMG":
			if (getAttrib(node, 'name').indexOf('mce_') != 0) {
				tinyMCE.switchClassSticky(editor_id + '_image', 'mceButtonSelected');
			}
			break;
		}
	} while ((node = node.parentNode) != null);
};

// This function auto imports CSS classes into the class selection droplist
function TinyMCE_advanced_setupCSSClasses(editor_id) {
	if (!TinyMCE_advanced_autoImportCSSClasses)	{
		return;
	}

	var selectElm = document.getElementById(editor_id + '_styleSelect');

	if (selectElm && selectElm.getAttribute('cssImported') != 'true') {
		var csses = tinyMCE.getCSSClasses(editor_id);
		if (csses && selectElm)	{
			for (var i=0; i<csses.length; i++) {
				selectElm.options[selectElm.length] = new Option(csses[i], csses[i]);
			}
		}

		// Only do this once
		if (csses != null && csses.length > 0) {
			selectElm.setAttribute('cssImported', 'true');
		}
	}
};
