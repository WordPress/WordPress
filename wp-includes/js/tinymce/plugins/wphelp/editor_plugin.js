/* WordPress Help plugin for TinyMCE 3.x */

(function() {

//    tinymce.PluginManager.requireLangPack('wphelp');
    
    tinymce.create('tinymce.plugins.WP_Help', {
    
        init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('wpHelp', function() {
				ed.windowManager.open({
					file : tinymce.baseURL + '/wp-mce-help.php',
					width : 450,
					height : 420,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					resizable : 'yes',
				    scrollbars : 'yes'
				});
			});

			// Register example button
			ed.addButton('wphelp', {
				title : ed.getLang('advanced.help_desc'),
				cmd : 'wpHelp',
				image : url + '/images/help.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wphelp', n.nodeName == 'IMG');
			});
		},
		
		getInfo : function() {
			return {
				longname : 'WordPress Help plugin',
				author : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl : 'http://wordpress.org',
				version : "3.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wphelp', tinymce.plugins.WP_Help);
})();
