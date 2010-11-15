(function() {
	tinymce.create('tinymce.plugins.wpLink', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('WP_Link', function() {
				ed.windowManager.open({
					file : tinymce.baseURL + '/wp-mce-link.php',
					width : 480,
					height : 400,
					inline : 1
				}, {
					plugin_url : url // Plugin absolute URL
				});
			});

			// Register example button
			ed.addButton('link', {
				title : ed.getLang('advanced.link_desc'),
				cmd : 'WP_Link'
			});
			
			ed.addShortcut('alt+shift+a', ed.getLang('advanced.link_desc'), 'WP_Link');

			// Add a node change handler, selects the button in the UI when a link is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wplink', n.nodeName == 'A');
			});
		},
		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'WordPress Link Dialog',
				author : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl : '',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wplink', tinymce.plugins.wpLink);
})();