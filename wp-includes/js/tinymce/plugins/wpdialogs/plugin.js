/* global tinymce */
/**
 * Included for back-compat.
 * The default WindowManager in TinyMCE 4.0 supports three types of dialogs:
 *	- With HTML created from JS.
 *	- With inline HTML (like WPWindowManager).
 *	- Old type iframe based dialogs.
 * For examples see the default plugins: https://github.com/tinymce/tinymce/tree/master/js/tinymce/plugins
 */
tinymce.WPWindowManager = tinymce.InlineWindowManager = function( editor ) {

	this.parent = editor.windowManager;
	this.editor = editor;

	tinymce.extend( this, this.parent );

	this.open = function( args, params ) {
		var self = this, $element;

		if ( ! args.wpDialog ) {
			return this.parent.open( args, params );
		} else if ( ! args.id ) {
			return;
		}

		self.element = $element = jQuery( '#' + args.id );

		if ( ! $element.length ) {
			return;
		}

		if ( window && window.console ) {
			window.console.log('tinymce.WPWindowManager is deprecated. Use the default editor.windowManager to open dialogs with inline HTML.');
		}

		self.features = args;
		self.params = params;
		self.windows.push( $element );

		// Store selection. Takes a snapshot in the FocusManager of the selection before focus is moved to the dialog.
		editor.nodeChanged();

		// Create the dialog if necessary
		if ( ! $element.data('wpdialog') ) {
			$element.wpdialog({
				title: args.title,
				width: args.width,
				height: args.height,
				modal: true,
				dialogClass: 'wp-dialog',
				zIndex: 300000
			});
		}

		$element.wpdialog('open');

		$element.on( 'wpdialogclose', function() {
			var i = self.windows.length;

			while ( i-- && i > -1 ) {
				if ( self.windows[i] === self.element ) {
					self.windows.splice( i, 1 );
				}
			}
		});
	};

	this.close = function() {
		if ( ! this.features.wpDialog ) {
			return this.parent.close.apply( this, arguments );
		}

		this.element.wpdialog('close');
	};
}

tinymce.PluginManager.add( 'wpdialogs', function( editor ) {
	// Replace window manager
	editor.on( 'init', function() {
		editor.windowManager = new tinymce.WPWindowManager( editor );
	});
});
