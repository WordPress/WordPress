/* global tinymce */
/**
 * WordPress View plugin.
 */

(function() {
	var VK = tinymce.VK,
		TreeWalker = tinymce.dom.TreeWalker,
		selected;

	tinymce.create('tinymce.plugins.wpView', {
		init : function( editor ) {
			var wpView = this;

			// Check if the `wp.mce` API exists.
			if ( typeof wp === 'undefined' || ! wp.mce ) {
				return;
			}

			editor.on( 'PreInit', function() {
				// Add elements so we can set `contenteditable` to false.
				editor.schema.addValidElements('div[*],span[*]');
			});

			// When the editor's content changes, scan the new content for
			// matching view patterns, and transform the matches into
			// view wrappers. Since the editor's DOM is outdated at this point,
			// we'll wait to render the views.
			editor.on( 'BeforeSetContent', function( e ) {
				if ( ! e.content ) {
					return;
				}

				e.content = wp.mce.view.toViews( e.content );
			});

			// When the editor's content has been updated and the DOM has been
			// processed, render the views in the document.
			editor.on( 'SetContent', function() {
				wp.mce.view.render( editor.getDoc() );
			});

			editor.on( 'init', function() {
				var selection = editor.selection;
				// When a view is selected, ensure content that is being pasted
				// or inserted is added to a text node (instead of the view).
				editor.on( 'BeforeSetContent', function() {
					var walker, target,
						view = wpView.getParentView( selection.getNode() );

					// If the selection is not within a view, bail.
					if ( ! view ) {
						return;
					}

					// If there are no additional nodes or the next node is a
					// view, create a text node after the current view.
					if ( ! view.nextSibling || wpView.isView( view.nextSibling ) ) {
						target = editor.getDoc().createTextNode('');
						editor.dom.insertAfter( target, view );

					// Otherwise, find the next text node.
					} else {
						walker = new TreeWalker( view.nextSibling, view.nextSibling );
						target = walker.next();
					}

					// Select the `target` text node.
					selection.select( target );
					selection.collapse( true );
				});

				// When the selection's content changes, scan any new content
				// for matching views and immediately render them.
				//
				// Runs on paste and on inserting nodes/html.
				editor.on( 'SetContent', function( e ) {
					if ( ! e.context ) {
						return;
					}

					var node = selection.getNode();

					if ( ! node.innerHTML ) {
						return;
					}

					node.innerHTML = wp.mce.view.toViews( node.innerHTML );
					wp.mce.view.render( node );
				});
			});

			// When the editor's contents are being accessed as a string,
			// transform any views back to their text representations.
			editor.on( 'PostProcess', function( e ) {
				if ( ( ! e.get && ! e.save ) || ! e.content ) {
					return;
				}

				e.content = wp.mce.view.toText( e.content );
			});

			// Triggers when the selection is changed.
			// Add the event handler to the top of the stack.
			editor.on( 'NodeChange', function( e ) {
				var view = wpView.getParentView( e.element );

				// Update the selected view.
				if ( view ) {
					wpView.select( view );

					// Prevent the selection from propagating to other plugins.
					return false;

				// If we've clicked off of the selected view, deselect it.
				} else {
					wpView.deselect();
				}
			});

			editor.on( 'keydown', function( event ) {
				var keyCode = event.keyCode,
					view, instance;

				// If a view isn't selected, let the event go on its merry way.
				if ( ! selected ) {
					return;
				}

				// If the caret is not within the selected view, deselect the
				// view and bail.
				view = wpView.getParentView( editor.selection.getNode() );
				if ( view !== selected ) {
					wpView.deselect();
					return;
				}

				// If delete or backspace is pressed, delete the view.
				if ( keyCode === VK.DELETE || keyCode === VK.BACKSPACE ) {
					if ( (instance = wp.mce.view.instance( selected )) ) {
						instance.remove();
						wpView.deselect();
					}
				}

				// Let keypresses that involve the command or control keys through.
				// Also, let any of the F# keys through.
				if ( event.metaKey || event.ctrlKey || ( keyCode >= 112 && keyCode <= 123 ) ) {
					return;
				}

				event.preventDefault();
			});
		},

		getParentView : function( node ) {
			while ( node ) {
				if ( this.isView( node ) ) {
					return node;
				}

				node = node.parentNode;
			}
		},

		isView : function( node ) {
			return (/(?:^|\s)wp-view-wrap(?:\s|$)/).test( node.className );
		},

		select : function( view ) {
			if ( view === selected ) {
				return;
			}

			this.deselect();
			selected = view;
			wp.mce.view.select( selected );
		},

		deselect : function() {
			if ( selected ) {
				wp.mce.view.deselect( selected );
			}

			selected = null;
		}
	});

	// Register plugin
	tinymce.PluginManager.add( 'wpview', tinymce.plugins.wpView );
})();
