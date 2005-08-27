/* Import plugin specific language pack */
//tinyMCE.importPluginLanguagePack('contextmenu', 'en,zh_cn,cs,fa,fr_ca,fr,de');
if (!tinyMCE.settings['contextmenu_skip_plugin_css'])
	tinyMCE.loadCSS(tinyMCE.baseURL + "/plugins/contextmenu/contextmenu.css");

// Global contextmenu class instance
var TinyMCE_contextmenu_contextMenu = null;

function TinyMCE_contextmenu_initInstance(inst) {
	// Is not working on MSIE 5.0
	if (tinyMCE.isMSIE5_0)
		return;

	// Add hide event handles
	tinyMCE.addEvent(inst.getDoc(), "click", TinyMCE_contextmenu_hideContextMenu);
	tinyMCE.addEvent(inst.getDoc(), "keypress", TinyMCE_contextmenu_hideContextMenu);
	tinyMCE.addEvent(inst.getDoc(), "keydown", TinyMCE_contextmenu_hideContextMenu);
	tinyMCE.addEvent(document, "click", TinyMCE_contextmenu_hideContextMenu);
	tinyMCE.addEvent(document, "keypress", TinyMCE_contextmenu_hideContextMenu);
	tinyMCE.addEvent(document, "keydown", TinyMCE_contextmenu_hideContextMenu);

	var contextMenu = new ContextMenu({
		commandhandler : "TinyMCE_contextmenu_commandHandler",
		spacer_image : tinyMCE.baseURL + "/plugins/contextmenu/images/spacer.gif"
	});

	// Register global reference
	TinyMCE_contextmenu_contextMenu = contextMenu;

	// Attach contextmenu event
	if (tinyMCE.isGecko) {
		tinyMCE.addEvent(inst.getDoc(), "contextmenu", function(e) {TinyMCE_contextmenu_showContextMenu(tinyMCE.isMSIE ? inst.contentWindow.event : e, inst);});
	} else
		tinyMCE.addEvent(inst.getDoc(), "contextmenu", TinyMCE_contextmenu_onContextMenu);
}

function TinyMCE_contextmenu_onContextMenu(e) {
	var elm = tinyMCE.isMSIE ? e.srcElement : e.target;
	var targetInst, body;

	// Find instance
	if ((body = tinyMCE.getParentElement(elm, "body")) != null) {
		for (var n in tinyMCE.instances) {
			var inst = tinyMCE.instances[n];

			if (body == inst.getBody()) {
				targetInst = inst;
				break;
			}
		}

		return TinyMCE_contextmenu_showContextMenu(tinyMCE.isMSIE ? targetInst.contentWindow.event : e, targetInst);
	}
}

function TinyMCE_contextmenu_showContextMenu(e, inst) {
	function getAttrib(elm, name) {
		return elm.getAttribute(name) ? elm.getAttribute(name) : "";
	}

	var x, y, elm, contextMenu;
	var pos = tinyMCE.getAbsPosition(inst.iframeElement);

	x = tinyMCE.isMSIE ? e.screenX : pos.absLeft + (e.pageX - inst.getBody().scrollLeft);
	y = tinyMCE.isMSIE ? e.screenY : pos.absTop + (e.pageY - inst.getBody().scrollTop);
	elm = tinyMCE.isMSIE ? e.srcElement : e.target;
	contextMenu = TinyMCE_contextmenu_contextMenu;
	contextMenu.inst = inst;

	// Mozilla needs some time
	window.setTimeout(function () {
		var theme = tinyMCE.getParam("theme");

		contextMenu.clearAll();
		var sel = inst.getSelectedText().length != 0 || elm.nodeName == "IMG";

		// Default items
		contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/cut.gif", "$lang_cut_desc", "Cut", "", !sel);
		contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/copy.gif", "$lang_copy_desc", "Copy", "", !sel);
		contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/paste.gif", "$lang_paste_desc", "Paste", "", false);

		// Get element
		elm = tinyMCE.getParentElement(elm, "img,table,td");
		if (elm) {
			switch (elm.nodeName) {
				case "IMG":
					contextMenu.addSeparator();

					// If flash
					if (tinyMCE.getAttrib(elm, 'name', '').indexOf('mce_plugin_flash') == 0)
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/flash/images/flash.gif", "$lang_flash_props", "mceFlash");
					else
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/image.gif", "$lang_image_props_desc", "mceImage");
					break;

				case "TABLE":
				case "TD":
					// Is table plugin loaded
					if (typeof(TinyMCE_table_getControlHTML) != "undefined") {
						var colspan = (elm.nodeName == "TABLE") ? "" : getAttrib(elm, "colspan");
						var rowspan = (elm.nodeName == "TABLE") ? "" : getAttrib(elm, "rowspan");

						colspan = colspan == "" ? "1" : colspan;
						rowspan = rowspan == "" ? "1" : rowspan;

						contextMenu.addSeparator();
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/cut.gif", "$lang_table_cut_row_desc", "mceTableCutRow");
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/copy.gif", "$lang_table_copy_row_desc", "mceTableCopyRow");
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/paste.gif", "$lang_table_paste_row_before_desc", "mceTablePasteRowBefore", "", inst.tableRowClipboard == null);
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/paste.gif", "$lang_table_paste_row_after_desc", "mceTablePasteRowAfter", "", inst.tableRowClipboard == null);

/*						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/left.gif", "$lang_justifyleft_desc", "JustifyLeft", "", false);
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/center.gif", "$lang_justifycenter_desc", "JustifyCenter", "", false);
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/right.gif", "$lang_justifyright_desc", "JustifyRight", "", false);
						contextMenu.addItem(tinyMCE.baseURL + "/themes/" + theme + "/images/full.gif", "$lang_justifyfull_desc", "JustifyFull", "", false);*/
						contextMenu.addSeparator();
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table.gif", "$lang_table_insert_desc", "mceInsertTable", "insert");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table.gif", "$lang_table_props_desc", "mceInsertTable");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_cell_props.gif", "$lang_table_cell_desc", "mceTableCellProps");
						contextMenu.addSeparator();
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_row_props.gif", "$lang_table_row_desc", "mceTableRowProps");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_row_before.gif", "$lang_table_insert_row_before_desc", "mceTableInsertRowBefore");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_row_after.gif", "$lang_table_insert_row_after_desc", "mceTableInsertRowAfter");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_delete_row.gif", "$lang_table_delete_row_desc", "mceTableDeleteRow");
						contextMenu.addSeparator();
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_col_before.gif", "$lang_table_insert_col_before_desc", "mceTableInsertColBefore");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_insert_col_after.gif", "$lang_table_insert_col_after_desc", "mceTableInsertColAfter");
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_delete_col.gif", "$lang_table_delete_col_desc", "mceTableDeleteCol");
						contextMenu.addSeparator();
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_split_cells.gif", "$lang_table_split_cells_desc", "mceTableSplitCells", "", (colspan == "1" && rowspan == "1"));
						contextMenu.addItem(tinyMCE.baseURL + "/plugins/table/images/table_merge_cells.gif", "$lang_table_merge_cells_desc", "mceTableMergeCells", "", false);
					}
					break;
			}
		}

		contextMenu.show(x, y);
	}, 10);

	// Cancel default handeling
	tinyMCE.cancelEvent(e);
	return false;
}

function TinyMCE_contextmenu_hideContextMenu() {
	TinyMCE_contextmenu_contextMenu.hide();

	return true;
}

function TinyMCE_contextmenu_commandHandler(command, value) {
	TinyMCE_contextmenu_contextMenu.hide();

	// UI must be true on these
	var ui = false;
	if (command == "mceInsertTable" || command == "mceTableCellProps" || command == "mceTableRowProps" || command == "mceTableMergeCells")
		ui = true;

	if (command == "Paste")
		value = null;

	TinyMCE_contextmenu_contextMenu.inst.execCommand(command, ui, value);
}

// Context menu class

function ContextMenu(settings) {
	// Default value function
	function defParam(key, def_val) {
		settings[key] = typeof(settings[key]) != "undefined" ? settings[key] : def_val;
	}

	var self = this;

	this.isMSIE = (navigator.appName == "Microsoft Internet Explorer");

	// Setup contextmenu div
	this.contextMenuDiv = document.createElement("div");
	this.contextMenuDiv.className = "contextMenu";
	this.contextMenuDiv.setAttribute("class", "contextMenu");
	this.contextMenuDiv.style.display = "none";
	this.contextMenuDiv.style.position = 'absolute';
	this.contextMenuDiv.style.zindex = 1000;
	this.contextMenuDiv.style.left = '0px';
	this.contextMenuDiv.style.top = '0px';
	this.contextMenuDiv.unselectable = "on";

	document.body.appendChild(this.contextMenuDiv);

	// Setup default values
	defParam("commandhandler", "");
	defParam("spacer_image", "images/spacer.gif");

	this.items = new Array();
	this.settings = settings;
	this.html = "";

	// IE Popup
	if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0) {
		this.pop = window.createPopup();
		doc = this.pop.document;
		doc.open();
		doc.write('<html><head><link href="' + tinyMCE.baseURL + '/plugins/contextmenu/contextmenu.css" rel="stylesheet" type="text/css" /></head><body unselectable="yes" class="contextMenuIEPopup"></body></html>');
		doc.close();
	}
};

ContextMenu.prototype.clearAll = function() {
	this.html = "";
	this.contextMenuDiv.innerHTML = "";
};

ContextMenu.prototype.addSeparator = function() {
	this.html += '<tr class="contextMenuItem"><td class="contextMenuIcon"><img src="' + this.settings['spacer_image'] + '" width="20" height="1" class="contextMenuImage" /></td><td><img class="contextMenuSeparator" width="1" height="1" src="' + this.settings['spacer_image'] + '" /></td></tr>';
};

ContextMenu.prototype.addItem = function(icon, title, command, value, disabled) {
	if (title.charAt(0) == '$')
		title = tinyMCE.getLang(title.substring(1));

	var onMouseDown = '';
	var html = '';

	if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0)
		onMouseDown = 'contextMenu.execCommand(\'' + command + '\', \'' + value + '\');return false;';
	else
		onMouseDown = this.settings['commandhandler'] + '(\'' + command + '\', \'' + value + '\');return false;';

	if (icon == "")
		icon = this.settings['spacer_image'];

	if (!disabled)
		html += '<tr class="contextMenuItem" onmousedown="' + onMouseDown + '" onmouseover="tinyMCE.switchClass(this,\'contextMenuItemOver\');" onmouseout="tinyMCE.switchClass(this,\'contextMenuItem\');">';
	else
		html += '<tr class="contextMenuItemDisabled">';

	html += '<td class="contextMenuIcon"><img src="' + icon + '" width="20" height="20" class="contextMenuImage" /></td>';
	html += '<td><div class="contextMenuText">';

	// Add text
	html += title;

	html += '</div></td>';
	html += '</tr>';

	// Add to main
	this.html += html;
};

ContextMenu.prototype.show = function(x, y) {
	if (this.html == "")
		return;

	var html = '';

	html += '<table border="0" cellpadding="0" cellspacing="0">';
	html += this.html;
	html += '</table>';

	this.contextMenuDiv.innerHTML = html;

	if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0) {
		var width, height;

		// Get dimensions
		this.contextMenuDiv.style.display = "block";
		width = this.contextMenuDiv.offsetWidth;
		height = this.contextMenuDiv.offsetHeight;
		this.contextMenuDiv.style.display = "none";

		// Setup popup and show
		this.pop.document.body.innerHTML = '<div class="contextMenu">' + html + "</div>";
		this.pop.document.tinyMCE = tinyMCE;
		this.pop.document.contextMenu = this;
		this.pop.show(x, y, width, height);
	} else {
		this.contextMenuDiv.style.left = x + 'px';
		this.contextMenuDiv.style.top = y + 'px';
		this.contextMenuDiv.style.display = "block";
	}
};

ContextMenu.prototype.hide = function() {
	if (tinyMCE.isMSIE && !tinyMCE.isMSIE5_0)
		this.pop.hide();
	else
		this.contextMenuDiv.style.display = "none";
};

ContextMenu.prototype.execCommand = function(command, value) {
	eval(this.settings['commandhandler'] + "(command, value);");
};
