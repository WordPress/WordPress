/**
 * $Id: editor_plugin_src.js 162 2007-01-03 16:16:52Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('directionality');

var TinyMCE_DirectionalityPlugin = {
	getInfo : function() {
		return {
			longname : 'Directionality',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://tinymce.moxiecode.com/tinymce/docs/plugin_directionality.html',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	getControlHTML : function(cn) {
		switch (cn) {
			case "ltr":
				return tinyMCE.getButtonHTML(cn, 'lang_directionality_ltr_desc', '{$pluginurl}/images/ltr.gif', 'mceDirectionLTR');

			case "rtl":
				return tinyMCE.getButtonHTML(cn, 'lang_directionality_rtl_desc', '{$pluginurl}/images/rtl.gif', 'mceDirectionRTL');
		}

		return "";
	},

	execCommand : function(editor_id, element, command, user_interface, value) {
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
	},

	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
		function getAttrib(elm, name) {
			return elm.getAttribute(name) ? elm.getAttribute(name) : "";
		}

		if (node == null)
			return;

		var elm = tinyMCE.getParentElement(node, "p,div,td,h1,h2,h3,h4,h5,h6,pre,address");
		if (!elm) {
			tinyMCE.switchClass(editor_id + '_ltr', 'mceButtonDisabled');
			tinyMCE.switchClass(editor_id + '_rtl', 'mceButtonDisabled');
			return true;
		}

		tinyMCE.switchClass(editor_id + '_ltr', 'mceButtonNormal');
		tinyMCE.switchClass(editor_id + '_rtl', 'mceButtonNormal');

		var dir = getAttrib(elm, "dir");
		if (dir == "ltr" || dir == "")
			tinyMCE.switchClass(editor_id + '_ltr', 'mceButtonSelected');
		else
			tinyMCE.switchClass(editor_id + '_rtl', 'mceButtonSelected');

		return true;
	}
};

tinyMCE.addPlugin("directionality", TinyMCE_DirectionalityPlugin);
