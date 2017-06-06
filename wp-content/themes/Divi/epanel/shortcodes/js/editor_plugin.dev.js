(function(){
	tinymce.create('tinymce.plugins.etquicktags', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			ed.addButton('et_learn_more', {
				title : et_quicktags_strings.learn_more,
				image : url + '/../images/icon-toggle.gif',
				onclick : function() {
					CustomButtonClick('learn_more');
				}
			});
			ed.addButton('et_box', {
				title : et_quicktags_strings.box,
				image : url + '/../images/icon-boxes.gif',
				onclick : function() {
					CustomButtonClick('box');
				}
			});
			ed.addButton('et_button', {
				title : et_quicktags_strings.button,
				image : url + '/../images/icon-buttons.gif',
				onclick : function() {
					CustomButtonClick('button');
				}
			});
			ed.addButton('et_tabs', {
				title : et_quicktags_strings.tabs,
				image : url + '/../images/icon-tabs.gif',
				onclick : function() {
					CustomButtonClick('tabs');
				}
			});
			ed.addButton('et_author', {
				title : et_quicktags_strings.author,
				image : url + '/../images/icon-author.gif',
				onclick : function() {
					CustomButtonClick('author');
				}
			});
		},
		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : "Elegant Themes " + et_quicktags_strings.shortcodes,
				author : 'Elegant Themes',
				authorurl : 'http://www.elegantthemes.com/',
				infourl : 'http://www.elegantthemes.com/',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('et_quicktags', tinymce.plugins.etquicktags);
})()