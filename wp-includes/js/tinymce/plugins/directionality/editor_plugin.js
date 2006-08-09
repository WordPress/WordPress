/**
 * $RCSfile: editor_plugin_src.js,v $
 * $Revision: 1.16 $
 * $Date: 2006/02/10 21:34:28 $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2006, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('directionality', 'en,tr,sv,fr_ca,zh_cn,cs,da,he,nb,de,hu,ru,ru_KOI8-R,ru_UTF-8,nn,es,cy,is,pl,nl,fr,pt_br');

var TinyMCE_DirectionalityPlugin = {
	getInfo : function() {
		return {
			longname : 'Directionality',
			author : 'Moxiecode Systems',
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
