/**
 * WordPress View plugin.
 */

(function() {


	tinymce.create('tinymce.plugins.wpView', {
		init : function( editor, url ) {
			var wpView = this,
				selected;

			// Check if the `wp.mce` API exists.
			if ( typeof wp === 'undefined' || ! wp.mce )
				return;

			editor.onPreInit.add( function( editor ) {
				// Add elements so we can set `contenteditable` to false.
				editor.schema.addValidElements('div[*],span[*]');
			});

			// When the editor's content changes, scan the new content for
			// matching view patterns, and transform the matches into
			// view wrappers. Since the editor's DOM is outdated at this point,
			// we'll wait to render the views.
			editor.onBeforeSetContent.add( function( editor, o ) {
				if ( ! o.content )
					return;

				o.content = wp.mce.view.toViews( o.content );
			});

			// When the editor's content has been updated and the DOM has been
			// processed, render the views in the document.
			editor.onSetContent.add( function( editor, o ) {
				wp.mce.view.render( editor.getDoc() );
			});

			editor.onInit.add( function( editor ) {

				// When the selection's content changes, scan any new content
				// for matching views and immediately render them.
				//
				// Runs on paste and on inserting nodes/html.
				editor.selection.onSetContent.add( function( selection, o ) {
					if ( ! o.context )
						return;

					var node = selection.getNode();

					if ( ! node.innerHTML )
						return;

					node.innerHTML = wp.mce.view.toViews( node.innerHTML );
					wp.mce.view.render( node );
				});
			});

			// When the editor's contents are being accessed as a string,
			// transform any views back to their text representations.
			editor.onPostProcess.add( function( editor, o ) {
				if ( ( ! o.get && ! o.save ) || ! o.content )
					return;

				o.content = wp.mce.view.toText( o.content );
			});

			// Triggers when the selection is changed.
			editor.onNodeChange.add( function( editor, controlManager, node, collapsed, o ) {
				var view = wpView.getParentView( node );

				// If we've clicked off of the selected view, deselect it.
				if ( selected && selected !== view )
					wp.mce.view.deselect( selected );

				// Bail if we're not selecting another view.
				if ( ! view )
					return;

				// Update the selected view.
				selected = view;
				wp.mce.view.select( selected );

				// Prevent the selection from propagating to other plugins.
				return false;
			});
		},

		getParentView : function( node ) {
			while ( node ) {
				if ( /(?:^|\s)wp-view-wrap(?:\s|$)/.test( node.className ) )
					return node;

				node = node.parentNode;
			}
		},

		getInfo : function() {
			return {
				longname  : 'WordPress Views',
				author    : 'WordPress',
				authorurl : 'http://wordpress.org',
				infourl   : 'http://wordpress.org',
				version   : '1.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add( 'wpview', tinymce.plugins.wpView );
})();