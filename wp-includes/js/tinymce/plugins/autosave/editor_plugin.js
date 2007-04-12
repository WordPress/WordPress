/**
 * $Id: editor_plugin_src.js 201 2007-02-12 15:56:56Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('autosave');

var TinyMCE_AutoSavePlugin = {
	getInfo : function() {
		return {
			longname : 'Auto save',
			author : 'Moxiecode Systems AB',
			authorurl : 'http://tinymce.moxiecode.com',
			infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/autosave',
			version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
		};
	},

	// Private plugin internal methods

	_beforeUnloadHandler : function() {
		var n, inst, anyDirty = false, msg = tinyMCE.getLang("lang_autosave_unload_msg");

		if (tinyMCE.getParam("fullscreen_is_enabled"))
			return;

		for (n in tinyMCE.instances) {
			inst = tinyMCE.instances[n];

			if (!tinyMCE.isInstance(inst))
				continue;

			if (inst.isDirty())
				return msg;
		}

		return;
	}
};

window.onbeforeunload = TinyMCE_AutoSavePlugin._beforeUnloadHandler;

tinyMCE.addPlugin("autosave", TinyMCE_AutoSavePlugin);
